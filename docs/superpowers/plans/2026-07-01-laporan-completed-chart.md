# Laporan Completed-Competitions Trend Chart Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a "Tren Kompetisi Selesai" section below the active-competitions section, showing a Chart.js line chart of Peserta, Nomor, and Revenue across completed competitions.

**Architecture:** A new `LaporanReportService::completedTrend()` reuses the existing `baseRows()` + `revenueByComp()` helpers to return per-competition data in date order. The controller passes it to the Blade view, which embeds it as JSON and renders it with Chart.js (loaded via CDN, scoped to the page) using a dual Y-axis and a Last-10 / Semua toggle.

**Tech Stack:** Laravel 10, PHPUnit, Blade, Chart.js 4 (CDN), Carbon.

## Global Constraints

- Completed competition = `waktu_kompetisi < now`; ordered oldest → newest by `waktu_kompetisi`.
- Revenue = collected only: sum of distinct `pembayaran.total_harga` where `status = Berhasil` (via existing `revenueByComp()`).
- Peserta counted by unique `atlet_name` (matches the card stats).
- Chart.js loaded via CDN `<script>` on this page only, not in the global layout.
- Left Y-axis (`y`): Peserta + Nomor. Right Y-axis (`y1`): Revenue, grid off, ticks compact `Rp …jt`, full rupiah in tooltip.
- Toggle default = Last 10; all data embedded once, sliced client-side.
- No change to the active-competitions section or the export pipeline.

---

## File Structure

- Modify `app/Services/LaporanReportService.php` — add public `completedTrend(): array`.
- Modify `app/Http/Controllers/LaporanController.php` — pass `$completedTrend` to the view.
- Modify `resources/views/admin/admin-laporan.blade.php` — new section, CSS, Chart.js CDN + init JS.
- Modify `tests/Unit/LaporanReportServiceTest.php` — test `completedTrend()`.
- Modify `tests/Feature/LaporanPageTest.php` — test section render + empty state.

---

## Task 1: Service — completedTrend()

**Files:**
- Modify: `app/Services/LaporanReportService.php` (add method after `summaries()`)
- Test: `tests/Unit/LaporanReportServiceTest.php`

**Interfaces:**
- Consumes: existing private `baseRows(array $ids)`, `revenueByComp(array $ids)`; `Kompetisi` model.
- Produces: `completedTrend(): array` — ordered list of `['nama'=>string,'tanggal'=>string(Y-m-d),'peserta'=>int,'nomor'=>int,'revenue'=>int]`.

- [ ] **Step 1: Write the failing test**

Add to `tests/Unit/LaporanReportServiceTest.php`:

```php
    public function test_completed_trend_returns_only_past_in_date_order(): void
    {
        // Completed, older
        $this->seedKompetisi(
            ['nama' => 'Selesai Lama', 'buka_pendaftaran' => now()->subDays(30), 'waktu_kompetisi' => now()->subDays(10)],
            [
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai'],
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 2, 'status' => 'Selesai'],
            ]
        );
        // Completed, newer
        $this->seedKompetisi(
            ['nama' => 'Selesai Baru', 'buka_pendaftaran' => now()->subDays(20), 'waktu_kompetisi' => now()->subDays(2)],
            [
                ['club' => 'Beta', 'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Cici', 'nomor' => 1, 'status' => 'Menunggu'],
            ]
        );
        // Active (excluded) and future (excluded)
        Kompetisi::factory()->create(['nama' => 'Aktif', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()]);
        Kompetisi::factory()->create(['nama' => 'Nanti', 'buka_pendaftaran' => now()->addDay(), 'waktu_kompetisi' => now()->addDays(5)]);

        $trend = (new LaporanReportService())->completedTrend();

        $this->assertCount(2, $trend);
        $this->assertSame('Selesai Lama', $trend[0]['nama']); // ascending by date
        $this->assertSame('Selesai Baru', $trend[1]['nama']);
        $this->assertSame(1, $trend[0]['peserta']);           // Andi (unique)
        $this->assertSame(2, $trend[0]['nomor']);             // 2 entries
        $this->assertSame(0, $trend[0]['revenue']);           // no payments linked
        $this->assertArrayHasKey('tanggal', $trend[0]);
    }
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --filter=test_completed_trend_returns_only_past_in_date_order`
Expected: FAIL — `Call to undefined method ...::completedTrend()`.

- [ ] **Step 3: Add the method**

In `app/Services/LaporanReportService.php`, add after the `summaries()` method (before `daftarRows()`):

```php
    /**
     * @return array<int, array{nama:string, tanggal:string, peserta:int, nomor:int, revenue:int}>
     */
    public function completedTrend(): array
    {
        $comps = Kompetisi::where('waktu_kompetisi', '<', now())
            ->orderBy('waktu_kompetisi')
            ->get();

        $ids = $comps->pluck('id')->all();
        if (empty($ids)) {
            return [];
        }

        $byComp = $this->baseRows($ids)->groupBy('kompetisi_id');
        $revenue = $this->revenueByComp($ids);

        $out = [];
        foreach ($comps as $k) {
            $rows = $byComp->get($k->id, collect());
            $out[] = [
                'nama' => $k->nama,
                'tanggal' => \Carbon\Carbon::parse($k->waktu_kompetisi)->format('Y-m-d'),
                'peserta' => $rows->pluck('atlet_name')->unique()->count(),
                'nomor' => $rows->count(),
                'revenue' => (int) ($revenue[$k->id]['terkumpul'] ?? 0),
            ];
        }
        return $out;
    }
```

- [ ] **Step 4: Run it to verify it passes**

Run: `php artisan test tests/Unit/LaporanReportServiceTest.php`
Expected: PASS (new test + all existing service tests).

- [ ] **Step 5: Commit**

```bash
git add app/Services/LaporanReportService.php tests/Unit/LaporanReportServiceTest.php
git commit -m "feat(laporan): add completedTrend() for the trend chart"
```

---

## Task 2: Controller + view — trend chart section

**Files:**
- Modify: `app/Http/Controllers/LaporanController.php` (`index()`)
- Modify: `resources/views/admin/admin-laporan.blade.php` (CSS in `@section('style')`; new `<section>` after the existing one; Chart.js CDN + init `<script>` before `@endsection`)
- Test: `tests/Feature/LaporanPageTest.php`

**Interfaces:**
- Consumes: `LaporanReportService::completedTrend()` from Task 1.
- Produces: `$completedTrend` array in the view; a `#trendChart` canvas with a `.trend-toggle`.

- [ ] **Step 1: Write the failing feature tests**

Add to `tests/Feature/LaporanPageTest.php`:

```php
    public function test_admin_sees_completed_competition_trend_section(): void
    {
        $this->withoutVite();

        $k = Kompetisi::factory()->create([
            'nama' => 'Lomba Selesai',
            'buka_pendaftaran' => now()->subDays(10),
            'waktu_kompetisi' => now()->subDay(),
        ]);
        $u = User::factory()->create(['club' => 'Alpha', 'phone' => '0811', 'role' => 'user']);
        $atlet = Atlet::create(['user_id' => $u->id, 'name' => 'Andi', 'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria']);
        $acara = Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => 1]);
        $atlet->acara()->attach($acara->id, ['status_pembayaran' => 'Selesai']);

        $this->actingAs($this->admin())
            ->get(route('admin.laporan'))
            ->assertOk()
            ->assertSee('Tren Kompetisi Selesai')
            ->assertSee('trendChart');
    }

    public function test_trend_section_shows_empty_state_without_completed(): void
    {
        $this->withoutVite();

        Kompetisi::factory()->create([
            'nama' => 'Aktif', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay(),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.laporan'))
            ->assertOk()
            ->assertSee('Belum ada kompetisi selesai.');
    }
```

- [ ] **Step 2: Run them to verify they fail**

Run: `php artisan test --filter="trend"`
Expected: FAIL — page does not contain "Tren Kompetisi Selesai" / "Belum ada kompetisi selesai.".

- [ ] **Step 3: Pass the data from the controller**

In `app/Http/Controllers/LaporanController.php`, replace the body of `index()` with:

```php
    public function index()
    {
        $competitions = $this->reports->activeCompetitions();
        $summaries = collect($this->reports->summaries($competitions->pluck('id')->all()))
            ->keyBy('kompetisi_id');
        $completedTrend = $this->reports->completedTrend();

        return view('admin.admin-laporan', compact('competitions', 'summaries', 'completedTrend'));
    }
```

- [ ] **Step 4: Add the section CSS**

In `resources/views/admin/admin-laporan.blade.php`, inside `@section('style')` before `</style>`, add:

```css
    .trend-chart-wrap { position: relative; height: 340px; }
    .trend-toggle button {
        font-size: 0.75rem;
        padding: 4px 10px;
        border: 1px solid #d4d9e0;
        background: #fff;
        border-radius: 6px;
        cursor: pointer;
        margin-left: 6px;
    }
    .trend-toggle button.is-active { background: #111827; color: #fff; border-color: #111827; }
```

- [ ] **Step 5: Add the section markup**

In the same file, find the closing `</section>` of the active-competitions section (the one immediately before `</div>` that closes `.main-content`). Insert this new section between that `</section>` and the `</div>`:

```blade
        <section class="all-container all-card w100 mtopbot">
            <header class="divider flex" style="justify-content: space-between; align-items: center;">
                <h1>Tren Kompetisi Selesai</h1>
                @if (!empty($completedTrend))
                    <div class="trend-toggle">
                        <button type="button" data-range="10" class="is-active">10 Terakhir</button>
                        <button type="button" data-range="all">Semua</button>
                    </div>
                @endif
            </header>

            @if (empty($completedTrend))
                <p class="m10">Belum ada kompetisi selesai.</p>
            @else
                <div class="m10 trend-chart-wrap">
                    <canvas id="trendChart"></canvas>
                </div>
            @endif
        </section>
```

- [ ] **Step 6: Add the Chart.js CDN + init script**

In the same file, immediately before `@endsection` (after the existing export `<script>`), add:

```blade
    @if (!empty($completedTrend))
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var raw = @json($completedTrend);
            var canvas = document.getElementById('trendChart');
            if (!canvas || !window.Chart || !raw.length) {
                return;
            }

            function rpShort(v) {
                if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1).replace('.', ',') + 'jt';
                if (v >= 1000) return 'Rp ' + Math.round(v / 1000) + 'rb';
                return 'Rp ' + v;
            }
            function rpFull(v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }

            var chart = null;
            function render(range) {
                var data = range === 'all' ? raw : raw.slice(-10);
                var cfg = {
                    type: 'line',
                    data: {
                        labels: data.map(function (d) { return d.nama; }),
                        datasets: [
                            { label: 'Peserta', data: data.map(function (d) { return d.peserta; }), yAxisID: 'y', borderColor: '#2563eb', backgroundColor: '#2563eb', tension: 0.25 },
                            { label: 'Nomor', data: data.map(function (d) { return d.nomor; }), yAxisID: 'y', borderColor: '#16a34a', backgroundColor: '#16a34a', tension: 0.25 },
                            { label: 'Revenue', data: data.map(function (d) { return d.revenue; }), yAxisID: 'y1', borderColor: '#d97706', backgroundColor: '#d97706', tension: 0.25 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    afterTitle: function (items) {
                                        var d = data[items[0].dataIndex];
                                        return d ? d.tanggal : '';
                                    },
                                    label: function (ctx) {
                                        if (ctx.dataset.label === 'Revenue') {
                                            return 'Revenue: ' + rpFull(ctx.parsed.y);
                                        }
                                        return ctx.dataset.label + ': ' + ctx.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { type: 'linear', position: 'left', beginAtZero: true, title: { display: true, text: 'Peserta / Nomor' } },
                            y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue' }, ticks: { callback: function (v) { return rpShort(v); } } }
                        }
                    }
                };
                if (chart) { chart.destroy(); }
                chart = new Chart(canvas, cfg);
            }

            render('10');

            document.querySelectorAll('.trend-toggle button').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.trend-toggle button').forEach(function (b) { b.classList.remove('is-active'); });
                    btn.classList.add('is-active');
                    render(btn.getAttribute('data-range'));
                });
            });
        });
    </script>
    @endif
```

- [ ] **Step 7: Run the feature tests + full Laporan suite**

Run: `php artisan test --filter=Laporan`
Expected: PASS — new trend tests plus all existing Laporan tests (export unaffected).

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/LaporanController.php resources/views/admin/admin-laporan.blade.php tests/Feature/LaporanPageTest.php
git commit -m "feat(laporan): completed-competitions trend chart section"
```

---

## Task 3: Verification

**Files:** none (verification only)

- [ ] **Step 1: Run the Laporan suites**

Run: `php artisan test --filter=Laporan`
Expected: PASS. (Unrelated pre-existing failures in Auth/Profile/Example/AtletImport are out of scope.)

- [ ] **Step 2: Update the knowledge graph**

Run: `graphify update .`
Expected: AST rebuild completes without error.

---

## Self-Review

- **Spec coverage:** completed definition + ordering + revenue source (Task 1 method & test), reuse of baseRows/revenueByComp (Task 1 Step 3), controller wiring (Task 2 Step 3), section + empty state (Task 2 Step 5), dual Y-axis + compact rupiah ticks + full-rupiah tooltip (Task 2 Step 6), Last 10 / Semua toggle client-side slice (Task 2 Step 6), Chart.js via page-scoped CDN (Task 2 Step 6), tests (Tasks 1-2), verification (Task 3). All covered.
- **Placeholder scan:** none — every step has concrete code/commands.
- **Type consistency:** `completedTrend()` returns keys `nama/tanggal/peserta/nomor/revenue`, consumed identically in the JS (`d.nama`, `d.peserta`, `d.nomor`, `d.revenue`, `d.tanggal`) and asserted in Task 1's test. Toggle values `'10'`/`'all'` match the `data-range` attributes and the `render()` slice logic.
