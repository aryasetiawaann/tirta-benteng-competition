# Laporan Extra Stats Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add finance, operational, and participation stats to each card on the Laporan Kompetisi Aktif page via an expandable detail panel, without touching the export pipeline.

**Architecture:** Enrich `LaporanReportService::summaries()` with new keys. Demographics reuse the rows already loaded by `baseRows()`; revenue and quota are two new lightweight DB-side aggregates. The Blade view renders a native `<details>` panel from the enriched summary array.

**Tech Stack:** Laravel 10, PHPUnit, Blade, MySQL (Laragon). Carbon for date/age math.

## Global Constraints

- Revenue source of truth is `pembayaran.total_harga` summed over **distinct** payments (one payment covers many entries) — never `acara.harga`. Status: `Berhasil` = collected, `Menunggu` = pending.
- `atlet.umur` is a **DATE (birthdate)**; age = `Carbon::parse($umur)->age`.
- `atlet.jenis_kelamin` enum values are exactly `'Pria'` and `'Wanita'`.
- Entry-level status (`acara_atlet.status_pembayaran`) values are `'Selesai'` / `'Menunggu'`.
- The Excel/zip export (`LaporanExportService`, `LaporanSheetExport`, `clubPaymentRows`, `daftarRows`) MUST remain byte-for-byte unchanged. Only `summaries()`, `baseRows()`'s select list, and the view change.
- Existing keys returned by `summaries()` (`peserta`, `nomor`, `club`, `selesai`, `menunggu`) keep their current meaning and values.
- All new ratios guard against zero denominators.

---

## File Structure

- Modify `app/Services/LaporanReportService.php` — extend `baseRows()` select; add `revenueByComp()` and `quotaByComp()` private helpers; add new keys in `summaries()`.
- Modify `resources/views/admin/admin-laporan.blade.php` — add the `<details>` panel and supporting CSS.
- Modify `tests/Unit/LaporanReportServiceTest.php` — extend the seed helper; add stat tests.
- Modify `tests/Feature/LaporanPageTest.php` — assert a new stat label renders.

---

## Task 1: Service — participation demographics & rates

**Files:**
- Modify: `app/Services/LaporanReportService.php` (`baseRows()` select ~L42-53; `summaries()` ~L120-140)
- Test: `tests/Unit/LaporanReportServiceTest.php`

**Interfaces:**
- Consumes: existing `baseRows(array $kompetisiIds)`, `orderedCompetitionIds()`.
- Produces: `summaries()` returns, per competition, the existing keys plus: `tingkat_pelunasan` (float %), `nomor_lomba_count` (int), `gender_l` (int), `gender_p` (int), `umur_rata` (float), `nomor_per_atlet` (float), `club_terbanyak` (?string).

- [ ] **Step 1: Extend the seed helper to vary gender/age and dedup athletes**

In `tests/Unit/LaporanReportServiceTest.php`, replace the `seedKompetisi` helper with this version (adds optional `jenis_kelamin`, `umur`, `kuota`, `pembayaran_id` per reg, and reuses one Atlet per logical athlete so each athlete is counted once):

```php
    private function seedKompetisi(array $attrs, array $regs): Kompetisi
    {
        $k = Kompetisi::factory()->create($attrs);
        $users = [];
        $atlets = [];
        foreach ($regs as $r) {
            if (!isset($users[$r['email']])) {
                $users[$r['email']] = User::factory()->create([
                    'name' => $r['user_name'], 'email' => $r['email'],
                    'club' => $r['club'], 'phone' => $r['phone'], 'role' => 'user',
                ]);
            }
            $u = $users[$r['email']];
            $atletKey = $r['email'] . '|' . $r['atlet'];
            if (!isset($atlets[$atletKey])) {
                $atlets[$atletKey] = Atlet::create([
                    'user_id' => $u->id, 'name' => $r['atlet'],
                    'umur' => $r['umur'] ?? '2010-01-01',
                    'jenis_kelamin' => $r['jenis_kelamin'] ?? 'Pria',
                ]);
            }
            $atlet = $atlets[$atletKey];
            $acara = Acara::factory()->create([
                'kompetisi_id' => $k->id, 'nomor_lomba' => $r['nomor'],
                'kuota' => $r['kuota'] ?? 50,
            ]);
            $pivot = ['status_pembayaran' => $r['status']];
            if (isset($r['pembayaran_id'])) {
                $pivot['pembayaran_id'] = $r['pembayaran_id'];
            }
            $atlet->acara()->attach($acara->id, $pivot);
        }
        return $k;
    }
```

- [ ] **Step 2: Write the failing demographics test**

Add to `tests/Unit/LaporanReportServiceTest.php`:

```php
    public function test_summaries_includes_participation_stats(): void
    {
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba D', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [
                // Alpha: Andi (Pria, born 2010) with 2 entries on nomor 1 & 2
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai', 'jenis_kelamin' => 'Pria', 'umur' => '2010-01-01'],
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 2, 'status' => 'Selesai', 'jenis_kelamin' => 'Pria', 'umur' => '2010-01-01'],
                // Beta: Cici (Wanita, born 2014) with 1 entry on nomor 1
                ['club' => 'Beta', 'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Cici', 'nomor' => 1, 'status' => 'Menunggu', 'jenis_kelamin' => 'Wanita', 'umur' => '2014-01-01'],
            ]
        );

        $s = collect((new LaporanReportService())->summaries([$k->id]))->firstWhere('kompetisi_id', $k->id);

        $this->assertSame(1, $s['gender_l']);                 // Andi
        $this->assertSame(1, $s['gender_p']);                 // Cici
        $this->assertSame(2, $s['nomor_lomba_count']);        // nomor 1 & 2 distinct
        $this->assertSame(1.5, $s['nomor_per_atlet']);        // 3 entries / 2 peserta
        $this->assertEqualsWithDelta(66.7, $s['tingkat_pelunasan'], 0.1); // 2 Selesai / 3
        $expectedAvg = round((\Carbon\Carbon::parse('2010-01-01')->age + \Carbon\Carbon::parse('2014-01-01')->age) / 2, 1);
        $this->assertSame($expectedAvg, $s['umur_rata']);
        $this->assertSame('Alpha', $s['club_terbanyak']);     // Alpha 1 athlete vs Beta 1 -> tie -> alphabetical
    }
```

- [ ] **Step 3: Run it to verify it fails**

Run: `php artisan test --filter=test_summaries_includes_participation_stats`
Expected: FAIL — undefined array key `gender_l`.

- [ ] **Step 4: Extend the `baseRows()` select**

In `app/Services/LaporanReportService.php`, add three columns to the `->select([...])` in `baseRows()` (after `'at.name as atlet_name',`):

```php
                'at.id as atlet_id',
                'at.jenis_kelamin as jenis_kelamin',
                'at.umur as umur',
```

- [ ] **Step 5: Add the new keys in `summaries()`**

Replace the body of the `foreach` loop in `summaries()` with:

```php
        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];

            $athletes = $compRows->groupBy('atlet_id')->map(fn ($g) => $g->first());
            $nomor = $compRows->count();
            $selesai = $compRows->where('status_pembayaran', 'Selesai')->count();
            $peserta = $compRows->pluck('atlet_name')->unique()->count();

            $ages = $athletes->map(fn ($a) => \Carbon\Carbon::parse($a->umur)->age);

            // Top club by unique athletes; ties broken alphabetically.
            $pairs = $athletes->groupBy('club')
                ->map(fn ($g, $club) => ['club' => (string) $club, 'count' => $g->count()])
                ->values()->all();
            usort($pairs, fn ($a, $b) => [$b['count'], $a['club']] <=> [$a['count'], $b['club']]);

            $out[] = [
                'kompetisi_id' => $compId,
                'nama' => $compRows->first()->kompetisi_nama,
                'peserta' => $peserta,
                'nomor' => $nomor,
                'club' => $compRows->pluck('user_id')->unique()->count(),
                'selesai' => $selesai,
                'menunggu' => $compRows->where('status_pembayaran', 'Menunggu')->count(),
                'tingkat_pelunasan' => $nomor > 0 ? round($selesai / $nomor * 100, 1) : 0.0,
                'nomor_lomba_count' => $compRows->pluck('nomor_lomba')->unique()->count(),
                'gender_l' => $athletes->where('jenis_kelamin', 'Pria')->count(),
                'gender_p' => $athletes->where('jenis_kelamin', 'Wanita')->count(),
                'umur_rata' => $ages->isEmpty() ? 0.0 : round($ages->avg(), 1),
                'nomor_per_atlet' => $peserta > 0 ? round($nomor / $peserta, 1) : 0.0,
                'club_terbanyak' => $pairs[0]['club'] ?? null,
            ];
        }
```

- [ ] **Step 6: Run the demographics test + existing service tests**

Run: `php artisan test tests/Unit/LaporanReportServiceTest.php`
Expected: PASS (new test + all four existing tests).

- [ ] **Step 7: Commit**

```bash
git add app/Services/LaporanReportService.php tests/Unit/LaporanReportServiceTest.php
git commit -m "feat(laporan): add participation stats to summaries"
```

---

## Task 2: Service — revenue aggregate

**Files:**
- Modify: `app/Services/LaporanReportService.php` (new `revenueByComp()`; use in `summaries()`)
- Test: `tests/Unit/LaporanReportServiceTest.php`

**Interfaces:**
- Consumes: `acara_atlet.pembayaran_id` → `pembayaran(total_harga, status)`.
- Produces: `summaries()` gains `pendapatan_terkumpul` (int) and `pendapatan_tertunda` (int).

- [ ] **Step 1: Write the failing revenue test**

Add to `tests/Unit/LaporanReportServiceTest.php` (note `use App\Models\Pembayaran;` at the top of the file):

```php
    public function test_summaries_sums_distinct_payments_for_revenue(): void
    {
        $k = Kompetisi::factory()->create([
            'nama' => 'Lomba E', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay(),
        ]);
        $u = User::factory()->create(['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'role' => 'user']);
        $atlet = Atlet::create(['user_id' => $u->id, 'name' => 'Andi', 'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria']);

        // One Berhasil payment of 150000 covering TWO entries -> counted once.
        $paid = Pembayaran::create([
            'user_id' => $u->id, 'midtrans_order_id' => 'ORD-1', 'metode_pembayaran' => 'qris',
            'total_harga' => 150000, 'status' => 'Berhasil',
        ]);
        // One Menunggu payment of 50000.
        $pending = Pembayaran::create([
            'user_id' => $u->id, 'midtrans_order_id' => 'ORD-2', 'metode_pembayaran' => 'qris',
            'total_harga' => 50000, 'status' => 'Menunggu',
        ]);
        // One Gagal payment of 99999 -> excluded entirely.
        $failed = Pembayaran::create([
            'user_id' => $u->id, 'midtrans_order_id' => 'ORD-3', 'metode_pembayaran' => 'qris',
            'total_harga' => 99999, 'status' => 'Gagal',
        ]);

        foreach ([[1, $paid], [2, $paid], [3, $pending], [4, $failed]] as [$nomor, $pay]) {
            $acara = Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => $nomor]);
            $atlet->acara()->attach($acara->id, [
                'status_pembayaran' => $pay->status === 'Berhasil' ? 'Selesai' : 'Menunggu',
                'pembayaran_id' => $pay->id,
            ]);
        }

        $s = collect((new LaporanReportService())->summaries([$k->id]))->firstWhere('kompetisi_id', $k->id);

        $this->assertSame(150000, $s['pendapatan_terkumpul']); // distinct Berhasil payment, once
        $this->assertSame(50000, $s['pendapatan_tertunda']);   // Menunggu payment
    }
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --filter=test_summaries_sums_distinct_payments_for_revenue`
Expected: FAIL — undefined array key `pendapatan_terkumpul`.

- [ ] **Step 3: Add the `revenueByComp()` helper**

In `app/Services/LaporanReportService.php`, add this private method (place it just above `summaries()`):

```php
    /** @return array<int|string, array{terkumpul:int, tertunda:int}> */
    private function revenueByComp(array $kompetisiIds): array
    {
        if (empty($kompetisiIds)) {
            return [];
        }

        $payments = DB::table('acara_atlet as aa')
            ->join('acara as ac', 'aa.acara_id', '=', 'ac.id')
            ->join('pembayaran as p', 'aa.pembayaran_id', '=', 'p.id')
            ->whereIn('ac.kompetisi_id', $kompetisiIds)
            ->select('ac.kompetisi_id as kompetisi_id', 'p.id as pembayaran_id', 'p.total_harga', 'p.status')
            ->distinct()
            ->get();

        $out = [];
        foreach ($payments->groupBy('kompetisi_id') as $compId => $rows) {
            $out[$compId] = [
                'terkumpul' => (int) $rows->where('status', 'Berhasil')->sum('total_harga'),
                'tertunda' => (int) $rows->where('status', 'Menunggu')->sum('total_harga'),
            ];
        }
        return $out;
    }
```

- [ ] **Step 4: Use it in `summaries()`**

In `summaries()`, after `$byComp = $base->groupBy('kompetisi_id');`, add:

```php
        $revenue = $this->revenueByComp($kompetisiIds);
```

Then inside the loop, just before building `$out[] = [...]`, add:

```php
            $rev = $revenue[$compId] ?? ['terkumpul' => 0, 'tertunda' => 0];
```

And add these two keys to the `$out[]` array:

```php
                'pendapatan_terkumpul' => $rev['terkumpul'],
                'pendapatan_tertunda' => $rev['tertunda'],
```

- [ ] **Step 5: Run the revenue test + full service suite**

Run: `php artisan test tests/Unit/LaporanReportServiceTest.php`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Services/LaporanReportService.php tests/Unit/LaporanReportServiceTest.php
git commit -m "feat(laporan): add collected/pending revenue to summaries"
```

---

## Task 3: Service — quota fill rate

**Files:**
- Modify: `app/Services/LaporanReportService.php` (new `quotaByComp()`; use in `summaries()`)
- Test: `tests/Unit/LaporanReportServiceTest.php`

**Interfaces:**
- Consumes: `acara.kuota` summed per competition (includes events with zero entries).
- Produces: `summaries()` gains `keterisian_kuota` (?float — null when total quota is 0).

- [ ] **Step 1: Write the failing quota test**

Add to `tests/Unit/LaporanReportServiceTest.php`:

```php
    public function test_summaries_computes_quota_fill_rate(): void
    {
        // Two events, kuota 50 each = 100 total. One entry on nomor 1 -> 1/100 = 1.0%.
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba F', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai', 'kuota' => 50],
            ]
        );
        // A second event with no entries, kuota 50, so total quota = 100.
        Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => 99, 'kuota' => 50]);

        $s = collect((new LaporanReportService())->summaries([$k->id]))->firstWhere('kompetisi_id', $k->id);

        $this->assertEqualsWithDelta(1.0, $s['keterisian_kuota'], 0.01);
    }
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --filter=test_summaries_computes_quota_fill_rate`
Expected: FAIL — undefined array key `keterisian_kuota`.

- [ ] **Step 3: Add the `quotaByComp()` helper**

In `app/Services/LaporanReportService.php`, add (next to `revenueByComp()`):

```php
    /** @return array<int|string, int> total kuota per competition */
    private function quotaByComp(array $kompetisiIds): array
    {
        if (empty($kompetisiIds)) {
            return [];
        }

        return DB::table('acara')
            ->whereIn('kompetisi_id', $kompetisiIds)
            ->groupBy('kompetisi_id')
            ->selectRaw('kompetisi_id, SUM(kuota) as total_kuota')
            ->pluck('total_kuota', 'kompetisi_id')
            ->map(fn ($v) => (int) $v)
            ->all();
    }
```

- [ ] **Step 4: Use it in `summaries()`**

After the `$revenue = $this->revenueByComp($kompetisiIds);` line add:

```php
        $quota = $this->quotaByComp($kompetisiIds);
```

Inside the loop, after the `$rev = ...` line add:

```php
            $totalKuota = $quota[$compId] ?? 0;
```

And add this key to the `$out[]` array:

```php
                'keterisian_kuota' => $totalKuota > 0 ? round($nomor / $totalKuota * 100, 1) : null,
```

- [ ] **Step 5: Run the full service suite**

Run: `php artisan test tests/Unit/LaporanReportServiceTest.php`
Expected: PASS (all tests).

- [ ] **Step 6: Commit**

```bash
git add app/Services/LaporanReportService.php tests/Unit/LaporanReportServiceTest.php
git commit -m "feat(laporan): add quota fill rate to summaries"
```

---

## Task 4: View — expandable detail panel

**Files:**
- Modify: `resources/views/admin/admin-laporan.blade.php` (CSS in `@section('style')`; markup inside the `@foreach` card, after the `.laporan-stats` div ~L123)
- Test: `tests/Feature/LaporanPageTest.php`

**Interfaces:**
- Consumes: the enriched `$s` summary array from Tasks 1-3; `$k->tutup_pendaftaran`.
- Produces: rendered `<details>` panel; no new keys.

- [ ] **Step 1: Write the failing feature test**

In `tests/Feature/LaporanPageTest.php`, add an assertion to `test_admin_sees_active_competition_summary` (after the existing `->assertSee('Lomba Aktif')`):

```php
            ->assertSee('Keuangan')
            ->assertSee('Tingkat Pelunasan');
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --filter=test_admin_sees_active_competition_summary`
Expected: FAIL — page does not contain "Keuangan".

- [ ] **Step 3: Add the detail-panel CSS**

In `resources/views/admin/admin-laporan.blade.php`, inside `@section('style')` before the closing `</style>`, add:

```css
    .laporan-detail { margin-top: 14px; }
    .laporan-detail > summary {
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        list-style: none;
        user-select: none;
    }
    .laporan-detail > summary::-webkit-details-marker { display: none; }
    .laporan-detail > summary::before { content: '\25B8'; margin-right: 6px; }
    .laporan-detail[open] > summary::before { content: '\25BE'; }
    .laporan-detail-body { margin-top: 12px; display: grid; gap: 14px; }
    .laporan-detail-group .grp-title {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
        margin-bottom: 6px;
    }
    .laporan-detail-group dl {
        margin: 0;
        display: grid;
        grid-template-columns: 1fr auto;
        row-gap: 4px;
        column-gap: 12px;
        font-size: 0.85rem;
    }
    .laporan-detail-group dt { color: #4b5563; }
    .laporan-detail-group dd { margin: 0; font-weight: 600; color: #111827; text-align: right; }
```

- [ ] **Step 4: Add the detail-panel markup**

In the same file, immediately after the closing `</div>` of `.laporan-stats` (before the `<a class="laporan-export" ...>` link), insert:

```blade
                            @php
                                $tutup = $k->tutup_pendaftaran ? \Carbon\Carbon::parse($k->tutup_pendaftaran) : null;
                                $sisaHari = $tutup ? now()->startOfDay()->diffInDays($tutup->copy()->startOfDay(), false) : null;
                                $rp = fn ($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
                            @endphp
                            <details class="laporan-detail">
                                <summary>Detail</summary>
                                <div class="laporan-detail-body">
                                    <div class="laporan-detail-group">
                                        <div class="grp-title">Keuangan</div>
                                        <dl>
                                            <dt>Pendapatan Terkumpul</dt><dd>{{ $rp($s['pendapatan_terkumpul'] ?? 0) }}</dd>
                                            <dt>Pendapatan Tertunda</dt><dd>{{ $rp($s['pendapatan_tertunda'] ?? 0) }}</dd>
                                            <dt>Tingkat Pelunasan</dt><dd>{{ $s['tingkat_pelunasan'] ?? 0 }}%</dd>
                                        </dl>
                                    </div>
                                    <div class="laporan-detail-group">
                                        <div class="grp-title">Operasional</div>
                                        <dl>
                                            <dt>Sisa Hari Pendaftaran</dt>
                                            <dd>{{ $sisaHari === null ? '—' : ($sisaHari < 0 ? 'Ditutup' : $sisaHari . ' hari') }}</dd>
                                            <dt>Keterisian Kuota</dt>
                                            <dd>{{ isset($s['keterisian_kuota']) ? $s['keterisian_kuota'] . '%' : '—' }}</dd>
                                            <dt>Jumlah Nomor Lomba</dt><dd>{{ $s['nomor_lomba_count'] ?? 0 }}</dd>
                                        </dl>
                                    </div>
                                    <div class="laporan-detail-group">
                                        <div class="grp-title">Partisipasi</div>
                                        <dl>
                                            <dt>Komposisi Gender (L / P)</dt><dd>{{ $s['gender_l'] ?? 0 }} / {{ $s['gender_p'] ?? 0 }}</dd>
                                            <dt>Rata-rata Umur</dt><dd>{{ $s['umur_rata'] ?? 0 }} th</dd>
                                            <dt>Rata-rata Nomor/Atlet</dt><dd>{{ $s['nomor_per_atlet'] ?? 0 }}</dd>
                                            <dt>Club Terbanyak</dt><dd>{{ $s['club_terbanyak'] ?? '—' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </details>
```

- [ ] **Step 5: Run the feature test + full laporan suite**

Run: `php artisan test --filter=Laporan`
Expected: PASS — including the unchanged export tests.

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/admin-laporan.blade.php tests/Feature/LaporanPageTest.php
git commit -m "feat(laporan): show extra stats in expandable detail panel"
```

---

## Task 5: Full-suite verification

**Files:** none (verification only)

- [ ] **Step 1: Run the entire test suite**

Run: `php artisan test`
Expected: PASS — no regressions, especially `LaporanExportTest` and `LaporanSheetExportTest` confirming the export is unaffected.

- [ ] **Step 2: Update the knowledge graph**

Run: `graphify update .`
Expected: AST rebuild completes with no errors.

---

## Self-Review

- **Spec coverage:** Finance (Task 2 + Task 4), Operational — days left/quota/distinct events (Task 1 `nomor_lomba_count`, Task 3 quota, Task 4 days-left), Participation (Task 1), expandable detail view (Task 4), edge cases (zero participants → Task 1 guards; null quota → Task 3 null; multi-entry payment → Task 2 distinct; closed registration → Task 4 "Ditutup"; tie club → Task 1 usort), tests (Tasks 1-5), export untouched (Task 5). All covered.
- **Placeholder scan:** none — every step has concrete code/commands.
- **Type consistency:** `revenueByComp` returns `terkumpul`/`tertunda`, consumed as `$rev['terkumpul']`/`$rev['tertunda']`; `quotaByComp` returns int map consumed as `$totalKuota`; summary keys used in Task 4 match those produced in Tasks 1-3.
