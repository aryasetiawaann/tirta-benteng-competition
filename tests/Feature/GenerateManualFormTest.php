<?php

namespace Tests\Feature;

use Tests\TestCase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class GenerateManualFormTest extends TestCase
{
    private string $outputPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->outputPath = base_path('manual/TestGen manual-form.xlsx');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->outputPath)) {
            unlink($this->outputPath);
        }
        parent::tearDown();
    }

    public function test_command_generates_output_file(): void
    {
        $this->assertFileExists(base_path('manual/acara.csv'));
        $this->artisan('template:manual-form', ['name' => 'TestGen'])
            ->assertExitCode(0);
        $this->assertFileExists($this->outputPath);
    }

    public function test_referensi_sheet_has_correct_headers(): void
    {
        $this->artisan('template:manual-form', ['name' => 'TestGen'])->assertExitCode(0);
        $spreadsheet = IOFactory::load($this->outputPath);
        $sheet = $spreadsheet->getSheetByName('Referensi');
        $this->assertNotNull($sheet);
        $headers = $sheet->rangeToArray('A1:I1')[0];
        $this->assertSame('id',          $headers[0]);
        $this->assertSame('jenis_lomba', $headers[1]);
        $this->assertSame('nomor_lomba', $headers[2]);
        $this->assertSame('nama',        $headers[3]);
        $this->assertSame('kategori',    $headers[4]);
        $this->assertSame('grup',        $headers[5]);
        $this->assertSame('min_umur',    $headers[6]);
        $this->assertSame('max_umur',    $headers[7]);
        $this->assertSame('label',       $headers[8]);
    }

    public function test_referensi_label_format_matches_expected(): void
    {
        $this->artisan('template:manual-form', ['name' => 'TestGen'])->assertExitCode(0);
        $spreadsheet = IOFactory::load($this->outputPath);
        $sheet = $spreadsheet->getSheetByName('Referensi');
        $row = $sheet->rangeToArray('A2:I2')[0];
        // columns: id(0), jenis_lomba(1), nomor_lomba(2), nama(3), kategori(4), grup(5), min_umur(6), max_umur(7), label(8)
        $nomorLomba = $row[2];
        $grup       = $row[5];
        $nama       = $row[3];
        $kategori   = $row[4];
        $this->assertSame("{$nomorLomba} - KU {$grup} - {$nama} - {$kategori}", $row[8]);
    }

    public function test_referensi_rows_sorted_by_nomor_lomba(): void
    {
        $this->artisan('template:manual-form', ['name' => 'TestGen'])->assertExitCode(0);
        $spreadsheet = IOFactory::load($this->outputPath);
        $sheet = $spreadsheet->getSheetByName('Referensi');
        $all = $sheet->toArray(null, true, false, false);
        $nomors = array_map(fn($r) => (int) $r[2], array_slice($all, 1));
        $sorted = $nomors;
        sort($sorted);
        $this->assertSame($sorted, $nomors);
    }

    public function test_named_range_label_referensi_exists(): void
    {
        $this->artisan('template:manual-form', ['name' => 'TestGen'])->assertExitCode(0);
        $spreadsheet = IOFactory::load($this->outputPath);
        $this->assertNotNull($spreadsheet->getNamedRange('LabelReferensi'));
    }

    public function test_data_validation_on_all_nomor_lomba_columns(): void
    {
        $this->artisan('template:manual-form', ['name' => 'TestGen'])->assertExitCode(0);
        $spreadsheet = IOFactory::load($this->outputPath);
        $sheet = $spreadsheet->getSheetByName('Input Atlet');
        foreach (['F', 'G', 'H', 'I', 'J', 'K', 'L'] as $col) {
            $dv = $sheet->getCell("{$col}2")->getDataValidation();
            $this->assertSame(DataValidation::TYPE_LIST, $dv->getType(),     "Col {$col}: expected list type");
            $this->assertSame('LabelReferensi',          $dv->getFormula1(), "Col {$col}: expected LabelReferensi formula");
        }
    }

    public function test_tables_applied_to_info_klub_and_input_atlet(): void
    {
        $this->artisan('template:manual-form', ['name' => 'TestGen'])->assertExitCode(0);
        $spreadsheet = IOFactory::load($this->outputPath);
        $this->assertCount(1, $spreadsheet->getSheetByName('Info Klub')->getTableCollection());
        $this->assertCount(1, $spreadsheet->getSheetByName('Input Atlet')->getTableCollection());
    }

    public function test_command_fails_when_csv_missing(): void
    {
        $csv    = base_path('manual/acara.csv');
        $backup = base_path('manual/acara.csv.bak');
        rename($csv, $backup);

        try {
            $this->artisan('template:manual-form', ['name' => 'TestGen'])
                ->assertExitCode(1);
        } finally {
            rename($backup, $csv);
        }
    }

    public function test_output_filename_uses_provided_name(): void
    {
        $this->artisan('template:manual-form', ['name' => 'Kejuaraan Nasional 2026'])
            ->assertExitCode(0);
        $path = base_path('manual/Kejuaraan Nasional 2026 manual-form.xlsx');
        $this->assertFileExists($path);
        unlink($path);
    }
}
