# Atlet Import Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add an admin page that bulk-imports club registrations from the official multi-sheet XLSX template (Info Klub + Referensi + Input Atlet sheets), creating the club user account, athlete records, and event registrations with payment marked as complete.

**Architecture:** A plain `AtletImportService` uses PhpSpreadsheet `IOFactory` directly to open the XLSX, reads the three sheets in dependency order (Info Klub → Referensi → Input Atlet), and writes to the DB. The controller calls the service and flashes the result summary. No Maatwebsite Excel concerns are needed; PhpSpreadsheet is already in `composer.lock` and installed.

**Tech Stack:** Laravel 10, PhpSpreadsheet (`PhpOffice\PhpSpreadsheet`), PHPUnit feature tests with `RefreshDatabase`.

---

## File Map

| Action | Path | Responsibility |
|---|---|---|
| Create | `app/Services/AtletImportService.php` | All parsing and DB writes |
| Create | `tests/Feature/AtletImportTest.php` | Feature tests with test XLSX fixture |
| Modify | `app/Http/Controllers/AdminController.php` | Add `importAtletForm()` + `importAtlet()` |
| Create | `resources/views/admin/admin-import-atlet.blade.php` | Upload form + result summary |
| Modify | `routes/web.php` | Two new admin routes |
| Modify | `resources/views/admin/admin-dashboard-sidebar.blade.php` | Nav link |

---

## Task 1: AtletImportService — Info Klub sheet

**Files:**
- Create: `app/Services/AtletImportService.php`
- Create: `tests/Feature/AtletImportTest.php`

- [ ] **Step 1: Create the test file with a helper to build test XLSX fixtures**

Create `tests/Feature/AtletImportTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Kompetisi;
use App\Models\User;
use App\Services\AtletImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsxDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class AtletImportTest extends TestCase
{
    use RefreshDatabase;

    private function buildXlsx(int $acaraId1, int $acaraId2, string $path): void
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nama Club', 'PIC', 'Nomor HP', 'Email', 'Alamat'],
            ['Test Club SC', 'Budi Santoso', '081234567890', 'testclub@example.com', 'Jakarta'],
        ]);

        $ref = $spreadsheet->createSheet();
        $ref->setTitle('Referensi');
        $ref->fromArray([
            ['id', 'jenis_lomba', 'nomor_lomba', 'nama', 'kategori', 'grup', 'min_umur', 'label'],
            [$acaraId1, '25M Gaya Bebas', '1', '25M Gaya Bebas', 'Pria', 'A', '2015', '1 - KU A - 25M Gaya Bebas - Pria'],
            [$acaraId2, '25M Gaya Dada',  '2', '25M Gaya Dada',  'Pria', 'A', '2015', '2 - KU A - 25M Gaya Dada - Pria'],
        ]);

        $atlet = $spreadsheet->createSheet();
        $atlet->setTitle('Input Atlet');
        $excelDate = XlsxDate::PHPToExcel(mktime(0, 0, 0, 5, 10, 2015));
        $atlet->fromArray([
            ['No', 'Nama Lengkap', 'Tanggal Lahir', 'Tahun Lahir', 'Jenis Kelamin',
             'Nomor Lomba 1', 'Nomor Lomba 2', 'Nomor Lomba 3', 'Nomor Lomba 4',
             'Nomor Lomba 5', 'Nomor Lomba 6', 'Nomor Lomba 7', 'Nama Dokumen', 'Catatan'],
            [1, 'Ahmad Fauzi', $excelDate, 2015, 'Pria',
             '1 - KU A - 25M Gaya Bebas - Pria', '2 - KU A - 25M Gaya Dada - Pria',
             '', '', '', '', '', 'ahmadfauzi.pdf', ''],
        ]);

        (new Xlsx($spreadsheet))->save($path);
    }
}
```

- [ ] **Step 2: Write the first failing test (user creation)**

Add inside the class:

```php
    public function test_creates_new_user_from_info_klub_sheet(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsx($acara1->id, $acara2->id, $path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertTrue($result['user_created']);
        $this->assertNotNull($result['user_password']);
        $this->assertDatabaseHas('users', [
            'email' => 'testclub@example.com',
            'club'  => 'Test Club SC',
            'name'  => 'Budi Santoso',
        ]);
    }
```

- [ ] **Step 3: Run test to confirm it fails**

```
cd D:\Project\swimming-competition-web
php artisan test tests/Feature/AtletImportTest.php --filter test_creates_new_user_from_info_klub_sheet
```

Expected: `Error: Class "App\Services\AtletImportService" not found`

- [ ] **Step 4: Create `app/Services/AtletImportService.php` with `parseInfoKlub`**

```php
<?php

namespace App\Services;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Pembayaran;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsxDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AtletImportService
{
    private array $errors = [];

    public function import(string $filePath, int $kompetisiId): array
    {
        $spreadsheet = IOFactory::load($filePath);

        $userInfo  = $this->parseInfoKlub($spreadsheet->getSheetByName('Info Klub'));
        $acaraMap  = $this->parseReferensi($spreadsheet->getSheetByName('Referensi'), $kompetisiId);
        $stats     = $this->parseInputAtlet($spreadsheet->getSheetByName('Input Atlet'), $userInfo['user'], $acaraMap);

        return array_merge($userInfo, $stats, ['errors' => $this->errors]);
    }

    private function parseInfoKlub(Worksheet $sheet): array
    {
        // row 0 = headers, row 1+ = data; use first non-empty row
        $rows = $sheet->toArray(null, true, false, false);
        $dataRow = null;
        for ($i = 1; $i < count($rows); $i++) {
            if (!empty(trim((string)($rows[$i][0] ?? '')))) {
                $dataRow = $rows[$i];
                break;
            }
        }
        if (!$dataRow) {
            throw new \RuntimeException('Sheet "Info Klub" tidak memiliki data klub.');
        }

        $clubName = trim((string)($dataRow[0] ?? ''));
        $picName  = trim((string)($dataRow[1] ?? ''));
        $phone    = trim((string)($dataRow[2] ?? ''));
        $email    = strtolower(trim((string)($dataRow[3] ?? '')));

        $plainPassword = null;
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) {
            $plainPassword = Str::random(12);
            $user = User::create([
                'name'     => $picName,
                'email'    => $email,
                'club'     => $clubName,
                'phone'    => $phone,
                'password' => Hash::make($plainPassword),
            ]);
        }

        return [
            'user'          => $user,
            'user_created'  => $plainPassword !== null,
            'user_email'    => $email,
            'user_password' => $plainPassword,
        ];
    }

    private function parseReferensi(Worksheet $sheet, int $kompetisiId): array
    {
        return [];  // stub — filled in Task 2
    }

    private function parseInputAtlet(Worksheet $sheet, User $user, array $acaraMap): array
    {
        return [    // stub — filled in Task 3
            'athletes_new'     => 0,
            'athletes_reused'  => 0,
            'registrations'    => 0,
            'pembayaran_total' => 0,
        ];
    }
}
```

- [ ] **Step 5: Run test to confirm it passes**

```
php artisan test tests/Feature/AtletImportTest.php --filter test_creates_new_user_from_info_klub_sheet
```

Expected: `PASS`

- [ ] **Step 6: Write test for reusing existing user**

Add inside the class:

```php
    public function test_reuses_existing_user_when_email_matches(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 0,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 0,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        User::factory()->create(['email' => 'testclub@example.com']);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsx($acara1->id, $acara2->id, $path);
        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertFalse($result['user_created']);
        $this->assertNull($result['user_password']);
        $this->assertDatabaseCount('users', 1);
    }
```

- [ ] **Step 7: Run and confirm passes**

```
php artisan test tests/Feature/AtletImportTest.php --filter test_reuses_existing_user_when_email_matches
```

Expected: `PASS`

- [ ] **Step 8: Commit**

```
git add app/Services/AtletImportService.php tests/Feature/AtletImportTest.php
git commit -m "feat: add AtletImportService with Info Klub parsing"
```

---

## Task 2: AtletImportService — Referensi sheet

**Files:**
- Modify: `app/Services/AtletImportService.php` (replace `parseReferensi` stub)
- Modify: `tests/Feature/AtletImportTest.php`

- [ ] **Step 1: Write failing test for Referensi parsing**

Add inside `AtletImportTest`:

```php
    public function test_referensi_builds_label_to_acara_map(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 75000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 75000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsx($acara1->id, $acara2->id, $path);
        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        // With parseReferensi working, 2 registrations should be created
        $this->assertEquals(2, $result['registrations']);
        $this->assertEquals(150000, $result['pembayaran_total']);
    }
```

- [ ] **Step 2: Run test to confirm it fails (registrations = 0 not 2)**

```
php artisan test tests/Feature/AtletImportTest.php --filter test_referensi_builds_label_to_acara_map
```

Expected: `FAIL` — `0` does not equal `2`

- [ ] **Step 3: Replace `parseReferensi` stub with full implementation**

In `app/Services/AtletImportService.php`, replace the `parseReferensi` method:

```php
    private function parseReferensi(Worksheet $sheet, int $kompetisiId): array
    {
        // Header: id, jenis_lomba, nomor_lomba, nama, kategori, grup, min_umur, [max_umur,] label
        // 'label' column is always the last column in the header row
        $rows = $sheet->toArray(null, true, false, false);
        if (empty($rows)) {
            $this->errors[] = 'Sheet "Referensi" kosong.';
            return [];
        }

        $headers  = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
        $labelIdx = array_search('label', $headers);
        if ($labelIdx === false) {
            $this->errors[] = 'Sheet "Referensi" tidak memiliki kolom "label".';
            return [];
        }

        $map = [];
        for ($i = 1; $i < count($rows); $i++) {
            $row     = $rows[$i];
            $rawId   = $row[0] ?? null;
            if (empty($rawId)) continue;

            $acaraId = (int) $rawId;
            $label   = trim((string)($row[$labelIdx] ?? ''));
            if (empty($label)) continue;

            $belongs = Acara::where('id', $acaraId)
                ->where('kompetisi_id', $kompetisiId)
                ->exists();

            if (!$belongs) {
                $this->errors[] = "Referensi baris " . ($i + 1) . ": acara ID {$acaraId} tidak ada di kompetisi {$kompetisiId}.";
                continue;
            }

            $map[$label] = $acaraId;
        }

        return $map;
    }
```

This still won't pass because `parseInputAtlet` is a stub. Move to next step.

- [ ] **Step 4: Replace `parseInputAtlet` stub with full implementation**

In `app/Services/AtletImportService.php`, replace the `parseInputAtlet` method:

```php
    private function parseInputAtlet(Worksheet $sheet, User $user, array $acaraMap): array
    {
        // Columns (0-indexed): No(0), Nama Lengkap(1), Tanggal Lahir(2), Tahun Lahir(3),
        //   Jenis Kelamin(4), Nomor Lomba 1-7 (5-11), Nama Dokumen(12), Catatan(13)
        $rows = $sheet->toArray(null, true, false, false);

        $athletesNew    = 0;
        $athletesReused = 0;
        $registrations  = 0;
        $totalHarga     = 0;
        $pesertaIds     = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row  = $rows[$i];
            $name = trim((string)($row[1] ?? ''));
            if (empty($name)) continue;

            // Parse birth date: numeric = Excel serial, else parse as string
            $rawDate = $row[2] ?? null;
            if (is_numeric($rawDate) && $rawDate > 0) {
                $birthDate = Carbon::instance(XlsxDate::excelToDateTimeObject((float) $rawDate));
            } else {
                try {
                    $birthDate = Carbon::parse($rawDate);
                } catch (\Exception) {
                    $this->errors[] = "Baris " . ($i + 1) . ": tanggal lahir '{$rawDate}' tidak valid, dilewati.";
                    continue;
                }
            }

            $jenisKelamin = trim((string)($row[4] ?? ''));
            if (!in_array($jenisKelamin, ['Pria', 'Wanita'])) {
                $this->errors[] = "Baris " . ($i + 1) . ": jenis kelamin '{$jenisKelamin}' tidak valid, dilewati.";
                continue;
            }

            // Find or create Atlet by (user_id, name)
            $atlet = Atlet::where('user_id', $user->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->first();

            if (!$atlet) {
                $atlet = Atlet::create([
                    'name'          => $name,
                    'umur'          => $birthDate->format('Y-m-d'),
                    'jenis_kelamin' => $jenisKelamin,
                    'user_id'       => $user->id,
                    'is_verified'   => 'verified',
                ]);
                $athletesNew++;
            } else {
                $athletesReused++;
            }

            // Register Nomor Lomba 1–7 (columns 5–11)
            for ($col = 5; $col <= 11; $col++) {
                $label = trim((string)($row[$col] ?? ''));
                if (empty($label)) continue;

                $acaraId = $acaraMap[$label] ?? null;
                if (!$acaraId) {
                    $this->errors[] = "Baris " . ($i + 1) . " ({$name}): label '{$label}' tidak ditemukan di referensi.";
                    continue;
                }

                if (Peserta::where('atlet_id', $atlet->id)->where('acara_id', $acaraId)->exists()) {
                    continue; // already registered
                }

                $acara = Acara::find($acaraId);
                $peserta = Peserta::create([
                    'acara_id'          => $acaraId,
                    'atlet_id'          => $atlet->id,
                    'peserta_user_id'   => $user->id,
                    'status_pembayaran' => 'Selesai',
                    'waktu_pembayaran'  => now()->toDateString(),
                ]);
                $pesertaIds[]  = $peserta->id;
                $totalHarga   += $acara->harga ?? 0;
                $registrations++;
            }
        }

        if (!empty($pesertaIds)) {
            $pembayaran = Pembayaran::create([
                'user_id'           => $user->id,
                'midtrans_order_id' => 'IMPORT-' . time() . '-' . $user->id,
                'metode_pembayaran' => 'IMPORT',
                'total_harga'       => $totalHarga,
                'status'            => 'Berhasil',
            ]);
            Peserta::whereIn('id', $pesertaIds)->update(['pembayaran_id' => $pembayaran->id]);
        }

        return [
            'athletes_new'     => $athletesNew,
            'athletes_reused'  => $athletesReused,
            'registrations'    => $registrations,
            'pembayaran_total' => $totalHarga,
        ];
    }
```

- [ ] **Step 5: Run all tests to confirm they pass**

```
php artisan test tests/Feature/AtletImportTest.php
```

Expected: `3 passed`

- [ ] **Step 6: Write edge-case test — skip duplicate registrations**

Add inside `AtletImportTest`:

```php
    public function test_skips_already_registered_athlete_event(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsx($acara1->id, $acara2->id, $path);

        // Import twice
        (new AtletImportService())->import($path, $kompetisi->id);
        $result2 = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        // Second import: athlete reused, 0 new registrations (already registered)
        $this->assertEquals(0, $result2['athletes_new']);
        $this->assertEquals(1, $result2['athletes_reused']);
        $this->assertEquals(0, $result2['registrations']);
        $this->assertDatabaseCount('acara_atlet', 2); // still only 2, not 4
    }
```

- [ ] **Step 7: Run and confirm passes**

```
php artisan test tests/Feature/AtletImportTest.php
```

Expected: `4 passed`

- [ ] **Step 8: Commit**

```
git add app/Services/AtletImportService.php tests/Feature/AtletImportTest.php
git commit -m "feat: complete AtletImportService with Referensi and Input Atlet parsing"
```

---

## Task 3: Routes and controller methods

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/AdminController.php`

- [ ] **Step 1: Add routes inside the admin middleware group in `routes/web.php`**

Find the admin middleware group (lines around 93–147). Add these two lines inside it alongside the other admin routes:

```php
Route::get('/admin/dashboard/import-atlet', [AdminController::class, 'importAtletForm'])->name('admin.import.atlet.form');
Route::post('/admin/dashboard/import-atlet', [AdminController::class, 'importAtlet'])->name('admin.import.atlet');
```

- [ ] **Step 2: Add the two controller methods to `app/Http/Controllers/AdminController.php`**

First ensure these imports are at the top of the file (add any that are missing):

```php
use App\Services\AtletImportService;
use App\Models\Kompetisi;
use Illuminate\Http\Request;
```

Then add the two methods at the end of the class body (before the closing `}`):

```php
    public function importAtletForm()
    {
        $kompetisis = Kompetisi::orderBy('id', 'desc')->get();
        return view('admin.admin-import-atlet', compact('kompetisis'));
    }

    public function importAtlet(Request $request)
    {
        $request->validate([
            'kompetisi_id' => 'required|exists:kompetisi,id',
            'file'         => 'required|file|mimes:xlsx',
        ]);

        $path = $request->file('file')->store('imports', 'local');
        $fullPath = storage_path('app/' . $path);

        try {
            $result = (new AtletImportService())->import($fullPath, (int) $request->kompetisi_id);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Import gagal: ' . $e->getMessage()]);
        } finally {
            @unlink($fullPath);
        }

        return redirect()
            ->route('admin.import.atlet.form')
            ->with('import_result', $result);
    }
```

- [ ] **Step 3: Write a quick route smoke test**

Add inside `AtletImportTest`:

```php
    public function test_import_form_is_accessible_as_admin(): void
    {
        $admin = User::factory()->create();
        // role is not fillable — update directly
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $admin->id)
            ->update(['role' => 'admin']);
        $admin->refresh();

        $response = $this->actingAs($admin)->get(route('admin.import.atlet.form'));
        $response->assertStatus(200);
    }
```

- [ ] **Step 4: Run to confirm test fails (view missing)**

```
php artisan test tests/Feature/AtletImportTest.php --filter test_import_form_is_accessible_as_admin
```

Expected: FAIL — view not found (skip, will pass after Task 4 creates the view)

- [ ] **Step 5: Commit route + controller**

```
git add routes/web.php app/Http/Controllers/AdminController.php
git commit -m "feat: add admin import-atlet routes and controller methods"
```

---

## Task 4: Admin view

**Files:**
- Create: `resources/views/admin/admin-import-atlet.blade.php`

- [ ] **Step 1: Create the view**

Look at `resources/views/admin/admin-kejuaraan.blade.php` for the layout pattern (it extends `admin.admin-dashboard-layout`). Create `resources/views/admin/admin-import-atlet.blade.php`:

```blade
@extends('admin.admin-dashboard-layout')

@section('title', 'Import Atlet')

@section('content')
<div class="content-wrapper" style="padding: 2rem;">

    <h1>Import Atlet dari XLSX</h1>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:1rem;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Result summary shown after successful import --}}
    @if (session('import_result'))
        @php $r = session('import_result'); @endphp
        <div class="alert alert-success" style="margin-bottom:1.5rem; border:1px solid #c3e6cb; background:#d4edda; padding:1rem; border-radius:6px;">
            <h3 style="margin-top:0;">Import Berhasil</h3>
            <ul>
                <li><strong>Akun Klub:</strong> {{ $r['user_email'] }}
                    @if($r['user_created'])
                        <span style="color:#155724;">(akun baru dibuat)</span>
                        — <strong>Password:</strong>
                        <code style="background:#f8f9fa;padding:2px 6px;border-radius:3px;">{{ $r['user_password'] }}</code>
                        <em style="color:#721c24;">(catat dan bagikan ke klub — tidak akan ditampilkan lagi)</em>
                    @else
                        <span style="color:#0c5460;">(akun sudah ada, digunakan)</span>
                    @endif
                </li>
                <li>Atlet baru: <strong>{{ $r['athletes_new'] }}</strong></li>
                <li>Atlet digunakan kembali: <strong>{{ $r['athletes_reused'] }}</strong></li>
                <li>Pendaftaran dibuat: <strong>{{ $r['registrations'] }}</strong></li>
                <li>Total biaya: <strong>Rp {{ number_format($r['pembayaran_total'], 0, ',', '.') }}</strong></li>
            </ul>
            @if (!empty($r['errors']))
                <hr>
                <h4 style="color:#721c24;">Peringatan / Dilewati:</h4>
                <ul>
                    @foreach($r['errors'] as $err)
                        <li style="color:#721c24;">{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Upload form --}}
    <form action="{{ route('admin.import.atlet') }}" method="POST" enctype="multipart/form-data"
          style="background:#fff;padding:1.5rem;border-radius:8px;border:1px solid #dee2e6;max-width:500px;">
        @csrf

        <div class="form-group" style="margin-bottom:1rem;">
            <label for="kompetisi_id"><strong>Kompetisi</strong></label>
            <select name="kompetisi_id" id="kompetisi_id" class="form-control" required>
                <option value="">-- Pilih Kompetisi --</option>
                @foreach($kompetisis as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label for="file"><strong>File XLSX</strong></label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx" required>
            <small class="form-text text-muted">Format: template resmi dengan sheet Info Klub, Referensi, dan Input Atlet.</small>
        </div>

        <button type="submit" class="btn btn-primary">Import</button>
    </form>

</div>
@endsection
```

- [ ] **Step 2: Run the smoke test that was skipped in Task 3**

```
php artisan test tests/Feature/AtletImportTest.php --filter test_import_form_is_accessible_as_admin
```

Expected: `PASS`

- [ ] **Step 3: Run all tests**

```
php artisan test tests/Feature/AtletImportTest.php
```

Expected: `5 passed`

- [ ] **Step 4: Commit view**

```
git add resources/views/admin/admin-import-atlet.blade.php
git commit -m "feat: add admin-import-atlet view with form and result summary"
```

---

## Task 5: Sidebar navigation link

**Files:**
- Modify: `resources/views/admin/admin-dashboard-sidebar.blade.php`

- [ ] **Step 1: Add Import Atlet link to the Atlet sub-menu**

In `resources/views/admin/admin-dashboard-sidebar.blade.php`, find the `<ul class="sub-menu">` inside the Atlet `<li>` (around line 54). Add a new `<li>` after the existing "Revisi Dokumen" entry:

```blade
                            <li>
                                <a href="{{ route('admin.import.atlet.form') }}">
                                    <span class="text">Import Atlet</span>
                                </a>
                            </li>
```

- [ ] **Step 2: Run all tests to confirm nothing broke**

```
php artisan test tests/Feature/AtletImportTest.php
```

Expected: `5 passed`

- [ ] **Step 3: Commit sidebar**

```
git add resources/views/admin/admin-dashboard-sidebar.blade.php
git commit -m "feat: add Import Atlet link to admin sidebar"
```

---

## Task 6: Manual end-to-end test with the real XLSX

- [ ] **Step 1: Start Laragon and open the browser**

Open `http://swimcomp.test` (or `http://localhost/swimming-competition-web/public`), log in as admin.

- [ ] **Step 2: Navigate to Import Atlet**

Click Atlet in the sidebar → Import Atlet.

- [ ] **Step 3: Upload `22-44.xlsx`**

Select the competition that matches the Referensi sheet (Blue Wave Series 3, ID 29), upload `22-44.xlsx`.

- [ ] **Step 4: Verify result summary**

Confirm you see:
- "Akun baru dibuat" with email `barramundi.sc@gmail.com` and a generated password
- Athlete count and registration count
- Any warnings in the error list

- [ ] **Step 5: Check the database**

```
mysql -u root swimcomp -e "SELECT * FROM users WHERE email='barramundi.sc@gmail.com';"
mysql -u root swimcomp -e "SELECT COUNT(*) FROM atlets WHERE user_id=(SELECT id FROM users WHERE email='barramundi.sc@gmail.com');"
mysql -u root swimcomp -e "SELECT COUNT(*) FROM acara_atlet WHERE peserta_user_id=(SELECT id FROM users WHERE email='barramundi.sc@gmail.com');"
```

- [ ] **Step 6: Re-upload the same file and confirm idempotency**

Upload `22-44.xlsx` again with the same competition selected. The result should show 0 new athletes, 0 new registrations, and the same user reused.

- [ ] **Step 7: Final commit**

```
git add .
git commit -m "feat: complete atlet XLSX import feature"
```
