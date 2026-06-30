# Admin Laporan Page & Export Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add an admin page that previews active-competition report data and exports ZIP report bundles (single competition or all active) that mirror the Python `report-automation` `laporan_dir` output, with date+time folder names.

**Architecture:** A `LaporanReportService` replicates the `list_club_payment` / `list_daftar` MySQL views in portable PHP (base join query + in-PHP grouping/row-numbering/totals), parameterized by competition id(s) — no dependency on the hardcoded `view_acara_atlet` (`kompetisi_id = 29`). A generic `LaporanSheetExport` reproduces the Excel formatting. A `LaporanExportService` writes each sheet to a temp dir one at a time and assembles a ZIP. `LaporanController` exposes the page and two download endpoints.

**Tech Stack:** Laravel 10, `maatwebsite/excel` ^3.1 (PhpSpreadsheet), PHP `ZipArchive`, Blade, PHPUnit (SQLite in-memory for tests).

## Global Constraints

- Active competition = `buka_pendaftaran <= now AND waktu_kompetisi >= now`. (Verbatim rule already used in `MainPageController`.)
- Report data computed in **portable PHP/query-builder** — NO MySQL-only SQL (no window functions, no CTEs). Tests run on SQLite in-memory.
- Export is **synchronous + optimized**: generate one `.xlsx` at a time, release it before the next; NO `ShouldAutoSize` — column widths precomputed from data (`maxlen + 2`, matching `excel.py`).
- Folder/file timestamp format: `d-m-Y H-i-s` (colon-free), e.g. `laporan 30-06-2026 14-22-05`.
- Phone columns keep a leading apostrophe (`'`) so Excel stores them as text (matches the views' `concat('\'', phone)`).
- Competition names sanitized for filenames: replace `/` and `\` with `-`.
- Column layouts (verbatim, order matters):
  - `list_club_payment`: `No, Club, Email, Nomor Telepon, Total Peserta per Club, Total Nomor per Club, Total Selesai per Club, Total Menunggu per Club, Nama Kompetisi`
  - `list_daftar`: `No, Nama Atlet, Nomor Lomba, Club, Nomor Telepon, Status Pembayaran, Nama Kompetisi`
- Left-aligned columns: club_payment → `Club, Email`; daftar → `Nama Atlet, Club, Nomor Telepon`. (Verbatim from `excel.py`.)
- New routes go in the existing `auth` + `role:admin` middleware group in `routes/web.php`.

---

## File Structure

- Create `app/Services/LaporanReportService.php` — data: active competitions, club-payment rows, daftar rows, summaries.
- Create `app/Exports/LaporanSheetExport.php` — generic formatted sheet (headings + rows + left-align cols).
- Create `app/Services/LaporanExportService.php` — temp-file generation + ZIP assembly.
- Create `app/Http/Controllers/LaporanController.php` — page + two download endpoints.
- Create `resources/views/admin/admin-laporan.blade.php` — preview cards + export buttons.
- Modify `routes/web.php` — add 3 routes in the admin group.
- Modify `resources/views/admin/admin-dashboard-sidebar.blade.php` — add "Laporan" nav item.
- Test `tests/Unit/LaporanReportServiceTest.php`
- Test `tests/Feature/LaporanExportTest.php`
- Test `tests/Feature/LaporanPageTest.php`

### Test data helper (used across tests)

All tests seed data with this shape. `Atlet` and `User` factories have minimal definitions, so attributes are passed explicitly. The `Atlet::acara()` relation uses the `acara_atlet` pivot with a `status_pembayaran` column.

```php
// Build one competition with registrations.
// Returns the Kompetisi. $regs = [['club','email','phone','user_name','atlet','nomor','status'], ...]
function seedKompetisi(array $attrs, array $regs): \App\Models\Kompetisi {
    $k = \App\Models\Kompetisi::factory()->create($attrs);
    foreach ($regs as $r) {
        $u = \App\Models\User::factory()->create([
            'name' => $r['user_name'], 'email' => $r['email'],
            'club' => $r['club'], 'phone' => $r['phone'], 'role' => 'user',
        ]);
        $atlet = \App\Models\Atlet::create([
            'user_id' => $u->id, 'name' => $r['atlet'],
            'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria',
        ]);
        $acara = \App\Models\Acara::factory()->create([
            'kompetisi_id' => $k->id, 'nomor_lomba' => $r['nomor'],
        ]);
        $atlet->acara()->attach($acara->id, ['status_pembayaran' => $r['status']]);
    }
    return $k;
}
```

---

### Task 1: LaporanReportService — headings constants + activeCompetitions + baseRows

**Files:**
- Create: `app/Services/LaporanReportService.php`
- Test: `tests/Unit/LaporanReportServiceTest.php`

**Interfaces:**
- Produces:
  - `const CLUB_PAYMENT_HEADINGS = ['No','Club','Email','Nomor Telepon','Total Peserta per Club','Total Nomor per Club','Total Selesai per Club','Total Menunggu per Club','Nama Kompetisi']`
  - `const DAFTAR_HEADINGS = ['No','Nama Atlet','Nomor Lomba','Club','Nomor Telepon','Status Pembayaran','Nama Kompetisi']`
  - `activeCompetitions(): \Illuminate\Support\Collection` of `Kompetisi`
  - `baseRows(array $kompetisiIds): \Illuminate\Support\Collection` (private; rows with: `kompetisi_id, kompetisi_nama, user_id, club, email, phone, user_name, atlet_name, nomor_lomba, status_pembayaran`)

- [ ] **Step 1: Write the failing test**

```php
<?php // tests/Unit/LaporanReportServiceTest.php
namespace Tests\Unit;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Kompetisi;
use App\Models\User;
use App\Services\LaporanReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function seedKompetisi(array $attrs, array $regs): Kompetisi
    {
        $k = Kompetisi::factory()->create($attrs);
        foreach ($regs as $r) {
            $u = User::factory()->create([
                'name' => $r['user_name'], 'email' => $r['email'],
                'club' => $r['club'], 'phone' => $r['phone'], 'role' => 'user',
            ]);
            $atlet = Atlet::create([
                'user_id' => $u->id, 'name' => $r['atlet'],
                'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria',
            ]);
            $acara = Acara::factory()->create([
                'kompetisi_id' => $k->id, 'nomor_lomba' => $r['nomor'],
            ]);
            $atlet->acara()->attach($acara->id, ['status_pembayaran' => $r['status']]);
        }
        return $k;
    }

    public function test_active_competitions_filters_by_date_window(): void
    {
        Kompetisi::factory()->create([ // active
            'nama' => 'Active', 'buka_pendaftaran' => now()->subDay(),
            'waktu_kompetisi' => now()->addDays(5),
        ]);
        Kompetisi::factory()->create([ // not open yet
            'nama' => 'Future', 'buka_pendaftaran' => now()->addDays(2),
            'waktu_kompetisi' => now()->addDays(9),
        ]);
        Kompetisi::factory()->create([ // already held
            'nama' => 'Past', 'buka_pendaftaran' => now()->subDays(10),
            'waktu_kompetisi' => now()->subDay(),
        ]);

        $names = (new LaporanReportService())->activeCompetitions()->pluck('nama')->all();

        $this->assertSame(['Active'], $names);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_active_competitions_filters_by_date_window`
Expected: FAIL — class `App\Services\LaporanReportService` not found.

- [ ] **Step 3: Write minimal implementation**

```php
<?php // app/Services/LaporanReportService.php
namespace App\Services;

use App\Models\Kompetisi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanReportService
{
    public const CLUB_PAYMENT_HEADINGS = [
        'No', 'Club', 'Email', 'Nomor Telepon',
        'Total Peserta per Club', 'Total Nomor per Club',
        'Total Selesai per Club', 'Total Menunggu per Club', 'Nama Kompetisi',
    ];

    public const DAFTAR_HEADINGS = [
        'No', 'Nama Atlet', 'Nomor Lomba', 'Club',
        'Nomor Telepon', 'Status Pembayaran', 'Nama Kompetisi',
    ];

    public function activeCompetitions(): Collection
    {
        $now = now();
        return Kompetisi::where('buka_pendaftaran', '<=', $now)
            ->where('waktu_kompetisi', '>=', $now)
            ->orderBy('waktu_kompetisi')
            ->get();
    }

    private function baseRows(array $kompetisiIds): Collection
    {
        if (empty($kompetisiIds)) {
            return collect();
        }

        return DB::table('acara_atlet as aa')
            ->join('atlets as at', 'aa.atlet_id', '=', 'at.id')
            ->join('users as u', 'at.user_id', '=', 'u.id')
            ->join('acara as ac', 'aa.acara_id', '=', 'ac.id')
            ->join('kompetisi as k', 'ac.kompetisi_id', '=', 'k.id')
            ->whereIn('ac.kompetisi_id', $kompetisiIds)
            ->select([
                'ac.kompetisi_id as kompetisi_id',
                'k.nama as kompetisi_nama',
                'u.id as user_id',
                'u.club as club',
                'u.email as email',
                'u.phone as phone',
                'u.name as user_name',
                'at.name as atlet_name',
                'ac.nomor_lomba as nomor_lomba',
                'aa.status_pembayaran as status_pembayaran',
            ])
            ->get();
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=test_active_competitions_filters_by_date_window`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Services/LaporanReportService.php tests/Unit/LaporanReportServiceTest.php
git commit -m "feat(laporan): report service scaffold with active-competition filter"
```

---

### Task 2: LaporanReportService — clubPaymentRows + summaries

**Files:**
- Modify: `app/Services/LaporanReportService.php`
- Test: `tests/Unit/LaporanReportServiceTest.php`

**Interfaces:**
- Consumes: `baseRows()`, the constants from Task 1.
- Produces:
  - `clubPaymentRows(array $kompetisiIds): array` — list of associative rows keyed by `CLUB_PAYMENT_HEADINGS`; per-user rows ordered by `club, email` and numbered per competition (starting at 1), followed by one `Total Semua` row per competition; competitions ordered by name ascending.
  - `summaries(array $kompetisiIds): array` — list of `['kompetisi_id','nama','peserta','nomor','club','selesai','menunggu']`, one per competition that has registrations, ordered by name ascending.

- [ ] **Step 1: Write the failing test**

```php
// add to tests/Unit/LaporanReportServiceTest.php
public function test_club_payment_rows_aggregate_per_user_with_total_semua(): void
{
    $k = $this->seedKompetisi(
        ['nama' => 'Lomba A', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
        [
            // Club Alpha user: 2 athletes, 3 entries (2 selesai, 1 menunggu)
            ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai'],
            ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 2, 'status' => 'Selesai'],
            ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Budi', 'nomor' => 1, 'status' => 'Menunggu'],
        ]
    );

    $rows = (new LaporanReportService())->clubPaymentRows([$k->id]);

    // First data row
    $this->assertSame(1, $rows[0]['No']);
    $this->assertSame('Alpha', $rows[0]['Club']);
    $this->assertSame("'0811", $rows[0]['Nomor Telepon']);
    $this->assertSame(2, $rows[0]['Total Peserta per Club']); // Andi, Budi
    $this->assertSame(3, $rows[0]['Total Nomor per Club']);   // 3 entries
    $this->assertSame(2, $rows[0]['Total Selesai per Club']);
    $this->assertSame(1, $rows[0]['Total Menunggu per Club']);

    // Total Semua row
    $total = $rows[1];
    $this->assertNull($total['No']);
    $this->assertSame('Total Semua', $total['Club']);
    $this->assertSame(2, $total['Total Peserta per Club']);
    $this->assertSame(3, $total['Total Nomor per Club']);
    $this->assertSame('Lomba A', $total['Nama Kompetisi']);
}

public function test_summaries_counts_clubs_and_participants(): void
{
    $k = $this->seedKompetisi(
        ['nama' => 'Lomba B', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
        [
            ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai'],
            ['club' => 'Beta',  'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Cici', 'nomor' => 1, 'status' => 'Menunggu'],
        ]
    );

    $s = collect((new LaporanReportService())->summaries([$k->id]))->firstWhere('kompetisi_id', $k->id);

    $this->assertSame('Lomba B', $s['nama']);
    $this->assertSame(2, $s['peserta']);
    $this->assertSame(2, $s['nomor']);
    $this->assertSame(2, $s['club']);
    $this->assertSame(1, $s['selesai']);
    $this->assertSame(1, $s['menunggu']);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LaporanReportServiceTest`
Expected: FAIL — `clubPaymentRows()` / `summaries()` undefined.

- [ ] **Step 3: Write minimal implementation**

Add these methods to `LaporanReportService` (and a private helper to order competitions by name):

```php
    /** @return array<int, array<int, string>> ordered competition ids by name asc */
    private function orderedCompetitionIds(Collection $base): array
    {
        $names = $base->groupBy('kompetisi_id')->map(fn ($g) => $g->first()->kompetisi_nama);
        return $names->sort()->keys()->all();
    }

    public function clubPaymentRows(array $kompetisiIds): array
    {
        $base = $this->baseRows($kompetisiIds);
        $byComp = $base->groupBy('kompetisi_id');
        $rows = [];

        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];
            $compNama = $compRows->first()->kompetisi_nama;

            $users = [];
            foreach ($compRows->groupBy('user_id') as $urows) {
                $first = $urows->first();
                $users[] = [
                    'club' => $first->club,
                    'email' => $first->email,
                    'phone' => $first->phone,
                    'peserta' => $urows->pluck('atlet_name')->unique()->count(),
                    'nomor' => $urows->count(),
                    'selesai' => $urows->where('status_pembayaran', 'Selesai')->count(),
                    'menunggu' => $urows->where('status_pembayaran', 'Menunggu')->count(),
                ];
            }
            usort($users, fn ($a, $b) => [$a['club'], $a['email']] <=> [$b['club'], $b['email']]);

            $no = 1;
            foreach ($users as $u) {
                $rows[] = [
                    'No' => $no++,
                    'Club' => $u['club'],
                    'Email' => $u['email'],
                    'Nomor Telepon' => "'" . $u['phone'],
                    'Total Peserta per Club' => $u['peserta'],
                    'Total Nomor per Club' => $u['nomor'],
                    'Total Selesai per Club' => $u['selesai'],
                    'Total Menunggu per Club' => $u['menunggu'],
                    'Nama Kompetisi' => $compNama,
                ];
            }

            $rows[] = [
                'No' => null,
                'Club' => 'Total Semua',
                'Email' => '',
                'Nomor Telepon' => '',
                'Total Peserta per Club' => $compRows->pluck('atlet_name')->unique()->count(),
                'Total Nomor per Club' => $compRows->count(),
                'Total Selesai per Club' => $compRows->where('status_pembayaran', 'Selesai')->count(),
                'Total Menunggu per Club' => $compRows->where('status_pembayaran', 'Menunggu')->count(),
                'Nama Kompetisi' => $compNama,
            ];
        }

        return $rows;
    }

    public function summaries(array $kompetisiIds): array
    {
        $base = $this->baseRows($kompetisiIds);
        $byComp = $base->groupBy('kompetisi_id');
        $out = [];

        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];
            $out[] = [
                'kompetisi_id' => $compId,
                'nama' => $compRows->first()->kompetisi_nama,
                'peserta' => $compRows->pluck('atlet_name')->unique()->count(),
                'nomor' => $compRows->count(),
                'club' => $compRows->pluck('user_id')->unique()->count(),
                'selesai' => $compRows->where('status_pembayaran', 'Selesai')->count(),
                'menunggu' => $compRows->where('status_pembayaran', 'Menunggu')->count(),
            ];
        }

        return $out;
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LaporanReportServiceTest`
Expected: PASS (all three tests).

- [ ] **Step 5: Commit**

```bash
git add app/Services/LaporanReportService.php tests/Unit/LaporanReportServiceTest.php
git commit -m "feat(laporan): club-payment rows and per-competition summaries"
```

---

### Task 3: LaporanReportService — daftarRows

**Files:**
- Modify: `app/Services/LaporanReportService.php`
- Test: `tests/Unit/LaporanReportServiceTest.php`

**Interfaces:**
- Consumes: `baseRows()`, `orderedCompetitionIds()`.
- Produces: `daftarRows(array $kompetisiIds): array` — associative rows keyed by `DAFTAR_HEADINGS`; per athlete-event rows ordered by `club, atlet_name, nomor_lomba` and numbered per competition, followed by a `Total Atlet` row (distinct athlete names) and a `Total Club` row (distinct user_name) per competition. Both total rows put their count in the `Status Pembayaran` column.

- [ ] **Step 1: Write the failing test**

```php
// add to tests/Unit/LaporanReportServiceTest.php
public function test_daftar_rows_list_with_total_atlet_and_total_club(): void
{
    $k = $this->seedKompetisi(
        ['nama' => 'Lomba C', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
        [
            ['club' => 'Beta',  'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Cici', 'nomor' => 2, 'status' => 'Selesai'],
            ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Menunggu'],
        ]
    );

    $rows = (new LaporanReportService())->daftarRows([$k->id]);

    // Ordered by club asc -> Alpha first
    $this->assertSame('Andi', $rows[0]['Nama Atlet']);
    $this->assertSame(1, $rows[0]['No']);
    $this->assertSame("'0811", $rows[0]['Nomor Telepon']);
    $this->assertSame('Cici', $rows[1]['Nama Atlet']);

    // Total rows
    $totalAtlet = $rows[2];
    $this->assertSame('Total Atlet', $totalAtlet['Nama Atlet']);
    $this->assertSame(2, $totalAtlet['Status Pembayaran']);
    $totalClub = $rows[3];
    $this->assertSame('Total Club', $totalClub['Nama Atlet']);
    $this->assertSame(2, $totalClub['Status Pembayaran']);
    $this->assertSame('Lomba C', $totalClub['Nama Kompetisi']);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_daftar_rows_list_with_total_atlet_and_total_club`
Expected: FAIL — `daftarRows()` undefined.

- [ ] **Step 3: Write minimal implementation**

Add to `LaporanReportService`:

```php
    public function daftarRows(array $kompetisiIds): array
    {
        $base = $this->baseRows($kompetisiIds);
        $byComp = $base->groupBy('kompetisi_id');
        $rows = [];

        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];
            $compNama = $compRows->first()->kompetisi_nama;

            $list = $compRows->map(fn ($r) => [
                'name' => $r->atlet_name,
                'nomor' => $r->nomor_lomba,
                'club' => $r->club,
                'phone' => $r->phone,
                'status' => $r->status_pembayaran,
            ])->values()->all();
            usort($list, fn ($a, $b) => [$a['club'], $a['name'], $a['nomor']] <=> [$b['club'], $b['name'], $b['nomor']]);

            $no = 1;
            foreach ($list as $r) {
                $rows[] = [
                    'No' => $no++,
                    'Nama Atlet' => $r['name'],
                    'Nomor Lomba' => $r['nomor'],
                    'Club' => $r['club'],
                    'Nomor Telepon' => "'" . $r['phone'],
                    'Status Pembayaran' => $r['status'],
                    'Nama Kompetisi' => $compNama,
                ];
            }

            $rows[] = [
                'No' => null, 'Nama Atlet' => 'Total Atlet', 'Nomor Lomba' => '', 'Club' => '',
                'Nomor Telepon' => '', 'Status Pembayaran' => $compRows->pluck('atlet_name')->unique()->count(),
                'Nama Kompetisi' => $compNama,
            ];
            $rows[] = [
                'No' => null, 'Nama Atlet' => 'Total Club', 'Nomor Lomba' => '', 'Club' => '',
                'Nomor Telepon' => '', 'Status Pembayaran' => $compRows->pluck('user_name')->unique()->count(),
                'Nama Kompetisi' => $compNama,
            ];
        }

        return $rows;
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LaporanReportServiceTest`
Expected: PASS (all tests).

- [ ] **Step 5: Commit**

```bash
git add app/Services/LaporanReportService.php tests/Unit/LaporanReportServiceTest.php
git commit -m "feat(laporan): registration (daftar) rows with total atlet/club"
```

---

### Task 4: LaporanSheetExport (generic formatted sheet)

**Files:**
- Create: `app/Exports/LaporanSheetExport.php`
- Test: `tests/Unit/LaporanSheetExportTest.php`

**Interfaces:**
- Consumes: associative rows (keyed by headings) from `LaporanReportService`.
- Produces: `new LaporanSheetExport(array $headings, array $rows, array $leftAlignHeadings)` implementing `FromArray, WithHeadings, WithEvents`.
  - `headings(): array` returns `$headings`.
  - `array(): array` returns rows mapped to ordered scalar arrays (missing keys → `''`).

- [ ] **Step 1: Write the failing test**

```php
<?php // tests/Unit/LaporanSheetExportTest.php
namespace Tests\Unit;

use App\Exports\LaporanSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class LaporanSheetExportTest extends TestCase
{
    public function test_headings_and_ordered_rows(): void
    {
        $headings = ['No', 'Club', 'Nama Kompetisi'];
        $rows = [
            ['No' => 1, 'Club' => 'Alpha', 'Nama Kompetisi' => 'X'],
            ['No' => null, 'Club' => 'Total Semua', 'Nama Kompetisi' => 'X'], // missing handled
        ];

        $export = new LaporanSheetExport($headings, $rows, ['Club']);

        $this->assertSame($headings, $export->headings());
        $this->assertSame([
            [1, 'Alpha', 'X'],
            [null, 'Total Semua', 'X'],
        ], $export->array());
    }

    public function test_store_writes_a_real_xlsx_without_error(): void
    {
        $export = new LaporanSheetExport(
            ['No', 'Club', 'Email'],
            [['No' => 1, 'Club' => 'Alpha', 'Email' => 'a@x.com']],
            ['Club', 'Email'],
        );

        Excel::store($export, 'test-laporan-sheet.xlsx', 'local');

        $this->assertFileExists(storage_path('app/test-laporan-sheet.xlsx'));
        @unlink(storage_path('app/test-laporan-sheet.xlsx'));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LaporanSheetExportTest`
Expected: FAIL — `App\Exports\LaporanSheetExport` not found.

- [ ] **Step 3: Write minimal implementation**

```php
<?php // app/Exports/LaporanSheetExport.php
namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class LaporanSheetExport implements FromArray, WithHeadings, WithEvents
{
    public function __construct(
        private array $headings,
        private array $rows,
        private array $leftAlignHeadings = [],
    ) {
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return array_map(
            fn ($row) => array_map(fn ($h) => $row[$h] ?? '', $this->headings),
            $this->rows,
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->array();
                $colCount = count($this->headings);
                $lastRow = count($data) + 1; // + header
                $lastCol = Coordinate::stringFromColumnIndex($colCount);

                // Precomputed column widths (no ShouldAutoSize): maxlen + 2.
                foreach ($this->headings as $i => $heading) {
                    $maxLen = strlen((string) $heading);
                    foreach ($data as $r) {
                        $maxLen = max($maxLen, strlen((string) ($r[$i] ?? '')));
                    }
                    $letter = Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->getColumnDimension($letter)->setWidth($maxLen + 2);
                }

                // Header centered.
                $sheet->getStyle("A1:{$lastCol}1")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Data: center all, then left-align specified columns.
                if ($lastRow > 1) {
                    $sheet->getStyle("A2:{$lastCol}{$lastRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    foreach ($this->leftAlignHeadings as $h) {
                        $idx = array_search($h, $this->headings, true);
                        if ($idx !== false) {
                            $letter = Coordinate::stringFromColumnIndex($idx + 1);
                            $sheet->getStyle("{$letter}2:{$letter}{$lastRow}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        }
                    }
                }

                // Excel table style with row stripes.
                $table = new Table("A1:{$lastCol}{$lastRow}", 'Table_' . Str::random(8));
                $table->setStyle(
                    (new TableStyle())
                        ->setTheme(TableStyle::TABLE_STYLE_MEDIUM2)
                        ->setShowRowStripes(true)
                );
                $sheet->addTable($table);

                // Print: fit to width, repeat header row.
                $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
            },
        ];
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LaporanSheetExportTest`
Expected: PASS.

> Note: if `PhpOffice\PhpSpreadsheet\Worksheet\Table` does not exist (older PhpSpreadsheet < 1.25), run `composer show phpoffice/phpspreadsheet` to confirm version. The bundled `maatwebsite/excel ^3.1` pulls `^1.29`, which has the Table API. If unexpectedly older, replace the table block with `setShowGridlines` styling — but verify the version first; do not assume.

- [ ] **Step 5: Commit**

```bash
git add app/Exports/LaporanSheetExport.php tests/Unit/LaporanSheetExportTest.php
git commit -m "feat(laporan): generic formatted excel sheet export"
```

---

### Task 5: LaporanExportService — single-competition ZIP

**Files:**
- Create: `app/Services/LaporanExportService.php`
- Test: `tests/Feature/LaporanExportTest.php`

**Interfaces:**
- Consumes: `LaporanReportService` (constructor-injected), `LaporanSheetExport`, constants.
- Produces:
  - `exportCompetition(\App\Models\Kompetisi $k): string` — returns absolute path to a generated `.zip` containing the two split files for that one competition under a `laporan {ts}` folder.
  - `exportActive(): string` — (implemented in Task 6).

- [ ] **Step 1: Write the failing test**

```php
<?php // tests/Feature/LaporanExportTest.php
namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Kompetisi;
use App\Models\User;
use App\Services\LaporanExportService;
use App\Services\LaporanReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    private function seedKompetisi(array $attrs, array $regs): Kompetisi
    {
        $k = Kompetisi::factory()->create($attrs);
        foreach ($regs as $r) {
            $u = User::factory()->create([
                'name' => $r['user_name'], 'email' => $r['email'],
                'club' => $r['club'], 'phone' => $r['phone'], 'role' => 'user',
            ]);
            $atlet = Atlet::create([
                'user_id' => $u->id, 'name' => $r['atlet'],
                'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria',
            ]);
            $acara = Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => $r['nomor']]);
            $atlet->acara()->attach($acara->id, ['status_pembayaran' => $r['status']]);
        }
        return $k;
    }

    private function zipEntries(string $path): array
    {
        $zip = new \ZipArchive();
        $zip->open($path);
        $names = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $names[] = $zip->getNameIndex($i);
        }
        $zip->close();
        sort($names);
        return $names;
    }

    public function test_export_single_competition_zip_contains_two_split_files(): void
    {
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba A', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai']]
        );

        $service = new LaporanExportService(new LaporanReportService());
        $path = $service->exportCompetition($k);

        $this->assertFileExists($path);
        $entries = $this->zipEntries($path);
        $this->assertCount(2, $entries);
        foreach ($entries as $e) {
            $this->assertStringStartsWith('laporan ', $e);
            $this->assertStringContainsString('Lomba A', $e);
            $this->assertStringEndsWith('.xlsx', $e);
        }
        $this->assertTrue((bool) preg_grep('/list_club_payment .* Lomba A\.xlsx$/', $entries));
        $this->assertTrue((bool) preg_grep('/list_daftar .* Lomba A\.xlsx$/', $entries));

        @unlink($path);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_export_single_competition_zip_contains_two_split_files`
Expected: FAIL — `App\Services\LaporanExportService` not found.

- [ ] **Step 3: Write minimal implementation**

```php
<?php // app/Services/LaporanExportService.php
namespace App\Services;

use App\Exports\LaporanSheetExport;
use App\Models\Kompetisi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class LaporanExportService
{
    private const CLUB_LEFT = ['Club', 'Email'];
    private const DAFTAR_LEFT = ['Nama Atlet', 'Club', 'Nomor Telepon'];

    public function __construct(private LaporanReportService $reports)
    {
    }

    public function exportCompetition(Kompetisi $k): string
    {
        return $this->buildZip([$k], combined: false);
    }

    /**
     * @param  array<int, Kompetisi>  $competitions
     */
    private function buildZip(array $competitions, bool $combined): string
    {
        $ts = now()->format('d-m-Y H-i-s');
        $folder = "laporan {$ts}";
        $ids = array_map(fn ($k) => $k->id, $competitions);

        $relDir = 'laporan-tmp/' . Str::uuid();
        $tmpDir = storage_path('app/' . $relDir);
        File::ensureDirectoryExists($tmpDir);

        // [zip entry name => absolute source path]
        $files = [];

        try {
            if ($combined) {
                $files["{$folder}/list_club_payment {$ts}.xlsx"] = $this->writeSheet(
                    $relDir, "list_club_payment {$ts}.xlsx",
                    LaporanReportService::CLUB_PAYMENT_HEADINGS,
                    $this->reports->clubPaymentRows($ids), self::CLUB_LEFT,
                );
                $files["{$folder}/list_daftar {$ts}.xlsx"] = $this->writeSheet(
                    $relDir, "list_daftar {$ts}.xlsx",
                    LaporanReportService::DAFTAR_HEADINGS,
                    $this->reports->daftarRows($ids), self::DAFTAR_LEFT,
                );
            }

            foreach ($competitions as $k) {
                $safe = str_replace(['/', '\\'], '-', $k->nama);
                $files["{$folder}/list_club_payment {$ts} {$safe}.xlsx"] = $this->writeSheet(
                    $relDir, "list_club_payment {$ts} {$safe}.xlsx",
                    LaporanReportService::CLUB_PAYMENT_HEADINGS,
                    $this->reports->clubPaymentRows([$k->id]), self::CLUB_LEFT,
                );
                $files["{$folder}/list_daftar {$ts} {$safe}.xlsx"] = $this->writeSheet(
                    $relDir, "list_daftar {$ts} {$safe}.xlsx",
                    LaporanReportService::DAFTAR_HEADINGS,
                    $this->reports->daftarRows([$k->id]), self::DAFTAR_LEFT,
                );
            }

            $zipDir = storage_path('app/laporan-zip/' . Str::uuid());
            File::ensureDirectoryExists($zipDir);
            $zipPath = $zipDir . '/' . $folder . '.zip';

            $zip = new \ZipArchive();
            $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach ($files as $entry => $abs) {
                $zip->addFile($abs, $entry);
            }
            $zip->close();

            return $zipPath;
        } finally {
            // Remove the temp xlsx files (zip is already closed and lives elsewhere).
            File::deleteDirectory($tmpDir);
        }
    }

    private function writeSheet(string $relDir, string $filename, array $headings, array $rows, array $leftAlign): string
    {
        Excel::store(new LaporanSheetExport($headings, $rows, $leftAlign), $relDir . '/' . $filename, 'local');

        return storage_path('app/' . $relDir . '/' . $filename);
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=test_export_single_competition_zip_contains_two_split_files`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Services/LaporanExportService.php tests/Feature/LaporanExportTest.php
git commit -m "feat(laporan): single-competition zip export"
```

---

### Task 6: LaporanExportService — all-active ZIP (combined + split)

**Files:**
- Modify: `app/Services/LaporanExportService.php`
- Test: `tests/Feature/LaporanExportTest.php`

**Interfaces:**
- Consumes: `LaporanReportService::activeCompetitions()`, `buildZip()`.
- Produces: `exportActive(): string` — returns absolute path to a `.zip` containing combined `list_club_payment {ts}.xlsx` + `list_daftar {ts}.xlsx` plus one split pair per active competition. Throws `\RuntimeException` when there are no active competitions.

- [ ] **Step 1: Write the failing test**

```php
// add to tests/Feature/LaporanExportTest.php
public function test_export_active_includes_combined_plus_split_and_excludes_inactive(): void
{
    $a = $this->seedKompetisi(
        ['nama' => 'CompA', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
        [['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai']]
    );
    $b = $this->seedKompetisi(
        ['nama' => 'CompB', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDays(2)],
        [['club' => 'Beta', 'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Budi', 'nomor' => 1, 'status' => 'Menunggu']]
    );
    // Inactive (already held) — must not appear.
    $this->seedKompetisi(
        ['nama' => 'CompPast', 'buka_pendaftaran' => now()->subDays(10), 'waktu_kompetisi' => now()->subDay()],
        [['club' => 'Gamma', 'email' => 'g@x.com', 'phone' => '0833', 'user_name' => 'UG', 'atlet' => 'Gita', 'nomor' => 1, 'status' => 'Selesai']]
    );

    $service = new LaporanExportService(new LaporanReportService());
    $path = $service->exportActive();
    $entries = $this->zipEntries($path);

    // 2 combined + 2 per active competition (2 actives) = 6
    $this->assertCount(6, $entries);
    $this->assertTrue((bool) preg_grep('/list_club_payment [0-9 :-]+\.xlsx$/', $entries)); // combined (no name suffix)
    $this->assertTrue((bool) preg_grep('/CompA\.xlsx$/', $entries));
    $this->assertTrue((bool) preg_grep('/CompB\.xlsx$/', $entries));
    $this->assertEmpty(preg_grep('/CompPast/', $entries));

    @unlink($path);
}

public function test_export_active_throws_when_none_active(): void
{
    $this->expectException(\RuntimeException::class);
    (new LaporanExportService(new LaporanReportService()))->exportActive();
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_export_active`
Expected: FAIL — `exportActive()` undefined.

- [ ] **Step 3: Write minimal implementation**

Add to `LaporanExportService`:

```php
    public function exportActive(): string
    {
        $active = $this->reports->activeCompetitions();
        if ($active->isEmpty()) {
            throw new \RuntimeException('No active competitions to export.');
        }

        return $this->buildZip($active->all(), combined: true);
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LaporanExportTest`
Expected: PASS (all export tests).

- [ ] **Step 5: Commit**

```bash
git add app/Services/LaporanExportService.php tests/Feature/LaporanExportTest.php
git commit -m "feat(laporan): all-active zip export (combined + per-competition)"
```

---

### Task 7: Controller, routes, page view, and sidebar link

**Files:**
- Create: `app/Http/Controllers/LaporanController.php`
- Create: `resources/views/admin/admin-laporan.blade.php`
- Modify: `routes/web.php` (add 3 routes in the `auth`+`role:admin` group)
- Modify: `resources/views/admin/admin-dashboard-sidebar.blade.php` (nav item)
- Test: `tests/Feature/LaporanPageTest.php`

**Interfaces:**
- Consumes: `LaporanReportService`, `LaporanExportService`.
- Produces routes: `admin.laporan`, `admin.laporan.export`, `admin.laporan.export-all`.

- [ ] **Step 1: Write the failing test**

```php
<?php // tests/Feature/LaporanPageTest.php
namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Kompetisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPageTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_laporan_page(): void
    {
        $this->get(route('admin.laporan'))->assertRedirect();
    }

    public function test_non_admin_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('admin.laporan'))
            ->assertForbidden();
    }

    public function test_admin_sees_active_competition_summary(): void
    {
        $k = Kompetisi::factory()->create([
            'nama' => 'Lomba Aktif',
            'buka_pendaftaran' => now()->subDay(),
            'waktu_kompetisi' => now()->addDay(),
        ]);
        $u = User::factory()->create(['club' => 'Alpha', 'phone' => '0811', 'role' => 'user']);
        $atlet = Atlet::create(['user_id' => $u->id, 'name' => 'Andi', 'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria']);
        $acara = Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => 1]);
        $atlet->acara()->attach($acara->id, ['status_pembayaran' => 'Selesai']);

        $this->actingAs($this->admin())
            ->get(route('admin.laporan'))
            ->assertOk()
            ->assertSee('Lomba Aktif');
    }

    public function test_admin_can_download_single_export_zip(): void
    {
        $k = Kompetisi::factory()->create([
            'nama' => 'Lomba Aktif',
            'buka_pendaftaran' => now()->subDay(),
            'waktu_kompetisi' => now()->addDay(),
        ]);

        $res = $this->actingAs($this->admin())->get(route('admin.laporan.export', $k->id));
        $res->assertOk();
        $this->assertStringContainsString('.zip', $res->headers->get('content-disposition'));
    }

    public function test_export_all_with_no_active_redirects_with_error(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.laporan.export-all'))
            ->assertRedirect();
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LaporanPageTest`
Expected: FAIL — route `admin.laporan` not defined.

- [ ] **Step 3a: Create the controller**

```php
<?php // app/Http/Controllers/LaporanController.php
namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Services\LaporanExportService;
use App\Services\LaporanReportService;

class LaporanController extends Controller
{
    public function __construct(
        private LaporanReportService $reports,
        private LaporanExportService $exporter,
    ) {
    }

    public function index()
    {
        $competitions = $this->reports->activeCompetitions();
        $summaries = collect($this->reports->summaries($competitions->pluck('id')->all()))
            ->keyBy('kompetisi_id');

        return view('admin.admin-laporan', compact('competitions', 'summaries'));
    }

    public function exportOne($id)
    {
        $k = Kompetisi::findOrFail($id);
        $path = $this->exporter->exportCompetition($k);

        return response()->download($path, basename($path))->deleteFileAfterSend(true);
    }

    public function exportAllActive()
    {
        try {
            $path = $this->exporter->exportActive();
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Tidak ada kompetisi aktif untuk diekspor.');
        }

        return response()->download($path, basename($path))->deleteFileAfterSend(true);
    }
}
```

- [ ] **Step 3b: Add routes**

In `routes/web.php`, inside the existing `Route::middleware(['auth','role:admin'])->group(function () { ... })` block, add (and add `use App\Http\Controllers\LaporanController;` near the other controller imports at the top):

```php
    Route::get('/admin/dashboard/laporan', [LaporanController::class, 'index'])->name('admin.laporan');
    Route::get('/admin/dashboard/laporan/export-all', [LaporanController::class, 'exportAllActive'])->name('admin.laporan.export-all');
    Route::get('/admin/dashboard/laporan/{id}/export', [LaporanController::class, 'exportOne'])->name('admin.laporan.export');
```

> Order matters: declare `export-all` before the `{id}/export` route is not strictly required (different path prefixes), but keep `export-all` as a literal segment distinct from `{id}`.

- [ ] **Step 3c: Create the view**

```blade
{{-- resources/views/admin/admin-laporan.blade.php --}}
@extends('admin.admin-dashboard-layout')
@section('content')
    <div class="main-content">
        @if (session('error'))
            <x-error-list>
                <x-error-item>{{ session('error') }}</x-error-item>
            </x-error-list>
        @endif

        <section class="all-container all-card w100">
            <header class="divider flex" style="justify-content: space-between; align-items: center;">
                <h1>Laporan Kompetisi Aktif</h1>
                @if ($competitions->isNotEmpty())
                    <a href="{{ route('admin.laporan.export-all') }}">
                        <button type="button">Export Semua Aktif</button>
                    </a>
                @endif
            </header>

            @if ($competitions->isEmpty())
                <p class="m10">Tidak ada kompetisi aktif saat ini.</p>
            @else
                <div class="m10">
                    @foreach ($competitions as $k)
                        @php $s = $summaries->get($k->id); @endphp
                        <div class="all-card mtopbot" style="padding: 16px; border: 1px solid #e5e5e5; border-radius: 8px;">
                            <div class="flex" style="justify-content: space-between; align-items: center;">
                                <div>
                                    <h2 style="margin-bottom: 4px;">{{ $k->nama }}</h2>
                                    <p class="smaller">Tanggal lomba: {{ \Carbon\Carbon::parse($k->waktu_kompetisi)->format('d/m/Y') }}</p>
                                </div>
                                <a href="{{ route('admin.laporan.export', $k->id) }}">
                                    <button type="button">Export</button>
                                </a>
                            </div>
                            <div class="flex" style="gap: 24px; flex-wrap: wrap; margin-top: 10px;">
                                <span>Peserta: <strong>{{ $s['peserta'] ?? 0 }}</strong></span>
                                <span>Nomor: <strong>{{ $s['nomor'] ?? 0 }}</strong></span>
                                <span>Club: <strong>{{ $s['club'] ?? 0 }}</strong></span>
                                <span>Selesai: <strong>{{ $s['selesai'] ?? 0 }}</strong></span>
                                <span>Menunggu: <strong>{{ $s['menunggu'] ?? 0 }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
```

- [ ] **Step 3d: Add sidebar nav item**

In `resources/views/admin/admin-dashboard-sidebar.blade.php`, add this `<li>` inside the `<ul>` of the "Main" menu, right after the "Kejuaraan" `<li>` (before the closing `</ul>` at line ~89):

```blade
                    <li>
                        <a href="{{ route('admin.laporan') }}">
                            <i class="icon ph-bold ph-file-text"></i>
                            <span class="text">Laporan</span>
                        </a>
                    </li>
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=LaporanPageTest`
Expected: PASS (all five tests).

> If `test_non_admin_forbidden` returns 404/redirect instead of 403, check what the `role` middleware does on failure (open `app/Http/Middleware` or `bootstrap`/`Kernel` alias for `role`) and assert the status that middleware actually returns. Match the test to the real behavior; do not weaken access control.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/LaporanController.php resources/views/admin/admin-laporan.blade.php routes/web.php resources/views/admin/admin-dashboard-sidebar.blade.php tests/Feature/LaporanPageTest.php
git commit -m "feat(laporan): admin report page, export routes, and sidebar link"
```

---

### Task 8: Full suite + graphify update

**Files:** none (verification only)

- [ ] **Step 1: Run the whole test suite**

Run: `php artisan test`
Expected: PASS, including the new `LaporanReportServiceTest`, `LaporanSheetExportTest`, `LaporanExportTest`, `LaporanPageTest`.

- [ ] **Step 2: Manual smoke (optional but recommended)**

Start the app (Laragon MySQL running), log in as admin, open `/admin/dashboard/laporan`, confirm active competitions render with summary numbers, click "Export" and "Export Semua Aktif", and open the downloaded ZIP to confirm the folder name `laporan {dd-mm-YYYY HH-MM-SS}/` and the expected `.xlsx` files with `TableStyleMedium2` formatting.

- [ ] **Step 3: Update the knowledge graph**

Run: `graphify update .`
Expected: completes (AST-only, no API cost), per project CLAUDE.md.

- [ ] **Step 4: Commit any graph changes**

```bash
git add -A
git commit -m "chore: update graphify graph after laporan feature" || echo "nothing to commit"
```

---

## Self-Review

**1. Spec coverage:**
- Active definition (`buka_pendaftaran <= now <= waktu_kompetisi`) → Task 1 `activeCompetitions` + test. ✓
- `LaporanReportService` (clubPaymentRows / daftarRows / summaries) replicating views without hardcoded id → Tasks 1–3. ✓
- Generic Excel export with `excel.py` formatting, no auto-size, precomputed widths, table style, alignment, phone leading-quote → Task 4 + Global Constraints. ✓
- ZIP delivery, single + all-active, combined + per-comp split, date+time folder, name sanitizing → Tasks 5–6. ✓
- Page: live preview cards + export buttons, empty state → Task 7 view + tests. ✓
- Routes in admin group, sidebar link → Task 7. ✓
- Synchronous + optimized (one file at a time via sequential `Excel::store`, no `ShouldAutoSize`) → Tasks 4–5 + Global Constraints. ✓
- Error handling: 404 on bad id (findOrFail), empty-active redirect with error, temp cleanup in `finally` → Tasks 5–7. ✓
- Testing: unit (service) + feature (export zip entries, page access/content) → Tasks 1–7. ✓

**2. Placeholder scan:** No TBD/TODO; every code step contains full code and exact commands. ✓

**3. Type consistency:** `CLUB_PAYMENT_HEADINGS` / `DAFTAR_HEADINGS` used identically in service, export service, and tests. `LaporanSheetExport(headings, rows, leftAlignHeadings)` signature consistent across Tasks 4–5. `buildZip(array, bool $combined)` and `writeSheet(...)` consistent within Task 5–6. Row associative keys match heading strings everywhere. ✓

> Note vs. spec: the spec named two export classes (`ListClubPaymentExport`, `ListDaftarExport`); this plan uses one parameterized `LaporanSheetExport` for DRY, since the two differ only by headings/left-align columns. Behavior is identical.
