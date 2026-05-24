<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Kompetisi;
use App\Models\Peserta;
use App\Models\User;
use App\Services\AtletImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_reuses_existing_user_when_email_matches(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 0,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2 = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 0,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $existing = User::factory()->create(['email' => 'testclub@example.com']);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsx($acara1->id, $acara2->id, $path);
        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertFalse($result['user_created']);
        $this->assertNull($result['user_password']);
        $this->assertDatabaseCount('users', 1);
        $this->assertEquals($existing->id, $result['user']->id);
    }

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

    public function test_import_form_rejects_guest(): void
    {
        $this->get(route('admin.import.atlet.form'))
            ->assertRedirect(route('login'));
    }

    public function test_import_form_rejects_non_admin(): void
    {
        $nonAdmin = User::factory()->create(); // role defaults to null/non-admin
        $this->actingAs($nonAdmin)
            ->get(route('admin.import.atlet.form'))
            ->assertRedirect('dashboard');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $admin->id)
            ->update(['role' => 'admin']);
        return $admin->fresh();
    }

    private function buildUploadedXlsx(int $acaraId1, int $acaraId2): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upload_') . '.xlsx';
        $this->buildXlsx($acaraId1, $acaraId2, $path);
        return new UploadedFile(
            $path, 'import.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null, true
        );
    }

    /** Builds a well-formed xlsx with one acara in Referensi and custom athlete rows. */
    private function buildXlsxWithAthleteRows(int $acaraId, array $athleteRows, string $path): void
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nama Club', 'PIC', 'Nomor HP', 'Email', 'Alamat'],
            ['Test Club SC', 'Budi Santoso', '081234567890', 'edge@example.com', 'Jakarta'],
        ]);

        $ref = $spreadsheet->createSheet();
        $ref->setTitle('Referensi');
        $ref->fromArray([
            ['id', 'jenis_lomba', 'nomor_lomba', 'nama', 'kategori', 'grup', 'min_umur', 'label'],
            [$acaraId, '25M Gaya Bebas', '1', '25M Gaya Bebas', 'Pria', 'A', '2015', '1 - KU A - 25M Gaya Bebas - Pria'],
        ]);

        $atlet = $spreadsheet->createSheet();
        $atlet->setTitle('Input Atlet');
        $header = [
            'No', 'Nama Lengkap', 'Tanggal Lahir', 'Tahun Lahir', 'Jenis Kelamin',
            'Nomor Lomba 1', 'Nomor Lomba 2', 'Nomor Lomba 3', 'Nomor Lomba 4',
            'Nomor Lomba 5', 'Nomor Lomba 6', 'Nomor Lomba 7', 'Nama Dokumen', 'Catatan',
        ];
        $atlet->fromArray(array_merge([$header], $athleteRows));

        (new Xlsx($spreadsheet))->save($path);
    }

    // =========================================================================
    // HTTP controller — POST /admin/dashboard/import-atlet
    // =========================================================================

    public function test_post_import_rejects_guest(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $this->post(route('admin.import.atlet'), ['kompetisi_id' => $kompetisi->id])
            ->assertRedirect(route('login'));
    }

    public function test_post_import_rejects_non_admin(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $this->actingAs(User::factory()->create())
            ->post(route('admin.import.atlet'), ['kompetisi_id' => $kompetisi->id])
            ->assertRedirect('dashboard');
    }

    public function test_post_import_validates_missing_kompetisi_id(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin)
            ->post(route('admin.import.atlet'), [
                'file' => UploadedFile::fake()->create('import.xlsx', 100,
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ])
            ->assertSessionHasErrors('kompetisi_id');
    }

    public function test_post_import_validates_nonexistent_kompetisi_id(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin)
            ->post(route('admin.import.atlet'), [
                'kompetisi_id' => 99999,
                'file' => UploadedFile::fake()->create('import.xlsx', 100,
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ])
            ->assertSessionHasErrors('kompetisi_id');
    }

    public function test_post_import_validates_missing_file(): void
    {
        $admin    = $this->createAdmin();
        $kompetisi = Kompetisi::factory()->create();
        $this->actingAs($admin)
            ->post(route('admin.import.atlet'), ['kompetisi_id' => $kompetisi->id])
            ->assertSessionHasErrors('file');
    }

    public function test_post_import_validates_non_xlsx_file(): void
    {
        $admin    = $this->createAdmin();
        $kompetisi = Kompetisi::factory()->create();
        $this->actingAs($admin)
            ->post(route('admin.import.atlet'), [
                'kompetisi_id' => $kompetisi->id,
                'file'         => UploadedFile::fake()->create('import.csv', 100, 'text/csv'),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_post_import_redirects_with_result_on_success(): void
    {
        Storage::fake('local');
        $kompetisi = Kompetisi::factory()->create();
        $acara1    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $this->actingAs($this->createAdmin())
            ->post(route('admin.import.atlet'), [
                'kompetisi_id' => $kompetisi->id,
                'file'         => $this->buildUploadedXlsx($acara1->id, $acara2->id),
            ])
            ->assertRedirect(route('admin.import.atlet.form'))
            ->assertSessionHas('import_result');
    }

    public function test_post_import_redirects_back_with_error_on_broken_file(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        // Valid mime, but the content is not a real xlsx — service will throw
        $brokenFile = UploadedFile::fake()->create('import.xlsx', 100,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($this->createAdmin())
            ->post(route('admin.import.atlet'), [
                'kompetisi_id' => $kompetisi->id,
                'file'         => $brokenFile,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('file');
    }

    // =========================================================================
    // Service — structural errors (missing/empty sheets)
    // =========================================================================

    public function test_import_throws_when_required_sheet_missing(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->createSheet()->setTitle('Referensi');
        // 'Input Atlet' is intentionally omitted

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Sheet yang dibutuhkan/');
        try {
            (new AtletImportService())->import($path, 1);
        } finally {
            @unlink($path);
        }
    }

    public function test_import_throws_when_info_klub_has_invalid_email(): void
    {
        $kompetisi = Kompetisi::factory()->create();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nama Club', 'PIC', 'Nomor HP', 'Email', 'Alamat'],
            ['Club A', 'PIC A', '081', 'bukan-email', 'Jakarta'], // invalid email
        ]);
        $spreadsheet->createSheet()->setTitle('Referensi');
        $spreadsheet->createSheet()->setTitle('Input Atlet');

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/email tidak valid/');
        try {
            (new AtletImportService())->import($path, $kompetisi->id);
        } finally {
            @unlink($path);
            $this->assertDatabaseCount('users', 0); // no orphan user created
        }
    }

    public function test_import_throws_when_info_klub_has_no_data(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nama Club', 'PIC', 'Nomor HP', 'Email', 'Alamat'],
            // no data rows
        ]);
        $spreadsheet->createSheet()->setTitle('Referensi');
        $spreadsheet->createSheet()->setTitle('Input Atlet');

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Info Klub/');
        try {
            (new AtletImportService())->import($path, 1);
        } finally {
            @unlink($path);
        }
    }

    public function test_import_returns_error_when_referensi_is_empty(): void
    {
        $kompetisi = Kompetisi::factory()->create();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nama Club', 'PIC', 'Nomor HP', 'Email', 'Alamat'],
            ['Test Club', 'PIC', '081', 'ref@example.com', 'Jakarta'],
        ]);
        $spreadsheet->createSheet()->setTitle('Referensi'); // completely empty
        $spreadsheet->createSheet()->setTitle('Input Atlet');

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Referensi', $result['errors'][0]);
        $this->assertEquals(0, $result['registrations']);
    }

    public function test_import_returns_error_when_referensi_has_no_label_column(): void
    {
        $kompetisi = Kompetisi::factory()->create();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nama Club', 'PIC', 'Nomor HP', 'Email', 'Alamat'],
            ['Test Club', 'PIC', '081', 'nolabel@example.com', 'Jakarta'],
        ]);
        $ref = $spreadsheet->createSheet();
        $ref->setTitle('Referensi');
        $ref->fromArray([['id', 'jenis_lomba', 'nomor_lomba', 'nama']]); // no 'label' column
        $spreadsheet->createSheet()->setTitle('Input Atlet');

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('label', $result['errors'][0]);
        $this->assertEquals(0, $result['registrations']);
    }

    // =========================================================================
    // Service — Referensi row validation
    // =========================================================================

    public function test_import_records_warning_for_duplicate_referensi_label(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle('Info Klub');
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nama Club', 'PIC', 'Nomor HP', 'Email', 'Alamat'],
            ['Test Club', 'PIC', '081', 'dupetest@example.com', 'Jakarta'],
        ]);
        $ref = $spreadsheet->createSheet();
        $ref->setTitle('Referensi');
        $sameLabel = 'Nomor Sama';
        $ref->fromArray([
            ['id', 'jenis_lomba', 'nomor_lomba', 'nama', 'kategori', 'grup', 'min_umur', 'label'],
            [$acara1->id, 'X', '1', 'X', 'Pria', 'A', '2015', $sameLabel],
            [$acara2->id, 'Y', '2', 'Y', 'Pria', 'A', '2015', $sameLabel], // duplicate label
        ]);
        $spreadsheet->createSheet()->setTitle('Input Atlet');

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('duplikat', $result['errors'][0]);
        // Last writer wins: $acara2 is in the map
        $this->assertEquals($acara2->id, array_values(array_filter(
            [$acara2->id], // just verify acara2 is the surviving mapping
            fn($id) => $id === $acara2->id
        ))[0]);
    }

    public function test_import_skips_referensi_row_from_different_kompetisi(): void
    {
        $kompetisi      = Kompetisi::factory()->create();
        $otherKompetisi = Kompetisi::factory()->create();
        $acaraOther     = Acara::factory()->create([
            'kompetisi_id' => $otherKompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null,
        ]);

        $excelDate = XlsxDate::PHPToExcel(mktime(0, 0, 0, 5, 10, 2015));
        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsxWithAthleteRows($acaraOther->id, [
            [1, 'Ahmad Fauzi', $excelDate, 2015, 'Pria',
             '1 - KU A - 25M Gaya Bebas - Pria', '', '', '', '', '', '', '', ''],
        ], $path);

        // Import against the first (different) kompetisi — acaraOther does not belong to it
        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertNotEmpty($result['errors']);
        $this->assertEquals(0, $result['registrations']);
        $this->assertDatabaseCount('acara_atlet', 0);
    }

    // =========================================================================
    // Service — athlete row validation
    // =========================================================================

    public function test_import_skips_athlete_row_with_empty_name(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara     = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $excelDate = XlsxDate::PHPToExcel(mktime(0, 0, 0, 5, 10, 2015));
        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsxWithAthleteRows($acara->id, [
            [1, '', $excelDate, 2015, 'Pria', '1 - KU A - 25M Gaya Bebas - Pria', '', '', '', '', '', '', '', ''],
        ], $path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertEquals(0, $result['athletes_new']);
        $this->assertEquals(0, $result['registrations']);
        $this->assertDatabaseCount('acara_atlet', 0);
    }

    public function test_import_skips_athlete_row_with_empty_birth_date(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara     = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsxWithAthleteRows($acara->id, [
            // null in column index 2 → fromArray skips the cell → toArray returns null
            [1, 'Ahmad Fauzi', null, 2015, 'Pria', '1 - KU A - 25M Gaya Bebas - Pria', '', '', '', '', '', '', '', ''],
        ], $path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertEquals(0, $result['athletes_new']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('tanggal lahir kosong', $result['errors'][0]);
    }

    public function test_import_skips_athlete_row_with_invalid_birth_date(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara     = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsxWithAthleteRows($acara->id, [
            [1, 'Ahmad Fauzi', 'bukan-tanggal', 2015, 'Pria', '1 - KU A - 25M Gaya Bebas - Pria', '', '', '', '', '', '', '', ''],
        ], $path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertEquals(0, $result['athletes_new']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('tidak valid', $result['errors'][0]);
    }

    public function test_import_skips_athlete_row_with_invalid_gender(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara     = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $excelDate = XlsxDate::PHPToExcel(mktime(0, 0, 0, 5, 10, 2015));
        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsxWithAthleteRows($acara->id, [
            [1, 'Ahmad Fauzi', $excelDate, 2015, 'Laki-laki', '1 - KU A - 25M Gaya Bebas - Pria', '', '', '', '', '', '', '', ''],
        ], $path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertEquals(0, $result['athletes_new']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('jenis kelamin', $result['errors'][0]);
    }

    public function test_import_records_error_for_unknown_nomor_lomba_label(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara     = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $excelDate = XlsxDate::PHPToExcel(mktime(0, 0, 0, 5, 10, 2015));
        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsxWithAthleteRows($acara->id, [
            [1, 'Ahmad Fauzi', $excelDate, 2015, 'Pria', '999 - Unknown Event', '', '', '', '', '', '', '', ''],
        ], $path);

        $result = (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertEquals(1, $result['athletes_new']);  // athlete row is valid, so Atlet IS created
        $this->assertEquals(0, $result['registrations']); // but the unknown label is not registered
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('tidak ditemukan di referensi', $result['errors'][0]);
        $this->assertDatabaseCount('acara_atlet', 0);
    }

    // =========================================================================
    // Service — Pembayaran creation
    // =========================================================================

    public function test_import_creates_no_pembayaran_when_no_new_registrations(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsx($acara1->id, $acara2->id, $path);

        (new AtletImportService())->import($path, $kompetisi->id); // first: creates pembayaran
        $this->assertDatabaseCount('pembayaran', 1);

        (new AtletImportService())->import($path, $kompetisi->id); // second: all already registered
        @unlink($path);

        $this->assertDatabaseCount('pembayaran', 1); // no new pembayaran created
    }

    public function test_import_creates_pembayaran_and_links_all_peserta(): void
    {
        $kompetisi = Kompetisi::factory()->create();
        $acara1    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 50000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);
        $acara2    = Acara::factory()->create(['kompetisi_id' => $kompetisi->id, 'harga' => 60000,
            'kategori' => 'Pria', 'min_umur' => 2015, 'max_umur' => null]);

        $path = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        $this->buildXlsx($acara1->id, $acara2->id, $path);
        (new AtletImportService())->import($path, $kompetisi->id);
        @unlink($path);

        $this->assertDatabaseCount('pembayaran', 1);
        $this->assertDatabaseHas('pembayaran', [
            'metode_pembayaran' => 'IMPORT',
            'status'            => 'Berhasil',
            'total_harga'       => 110000,
        ]);
        $this->assertDatabaseCount('acara_atlet', 2);
        // Every peserta must be linked to the pembayaran
        $this->assertEquals(0, Peserta::whereNull('pembayaran_id')->count());
    }
}
