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
}
