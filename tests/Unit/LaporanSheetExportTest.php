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
