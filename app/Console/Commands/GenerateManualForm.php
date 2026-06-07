<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class GenerateManualForm extends Command
{
    protected $signature = 'template:manual-form {name : Prefix for the output filename (e.g. "ABC" → "ABC manual-form.xlsx")}';
    protected $description = 'Generate a manual-form.xlsx populated with Referensi from manual/acara.csv';

    public function handle(): int
    {
        $csvPath      = base_path('manual/acara.csv');
        $templatePath = base_path('manual/manual-form.xlsx');
        $name         = $this->argument('name');
        $outputPath   = base_path("manual/{$name} manual-form.xlsx");

        if (!file_exists($csvPath)) {
            $this->error("File not found: {$csvPath}");
            return 1;
        }
        if (!file_exists($templatePath)) {
            $this->error("Template not found: {$templatePath}");
            return 1;
        }

        $rows = $this->parseCsv($csvPath);
        if (empty($rows)) {
            $this->error('acara.csv has no valid data rows.');
            return 1;
        }

        $spreadsheet = IOFactory::load($templatePath);

        $this->populateReferensi($spreadsheet, $rows);
        $this->rebuildValidation($spreadsheet, count($rows));
        $this->applyTables($spreadsheet);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        $this->info("Saved: {$outputPath}");
        return 0;
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        fgetcsv($handle); // skip header
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $nomorLomba = trim((string) ($row[3] ?? ''));
            $grup       = trim((string) ($row[8] ?? ''));
            $kategori   = trim((string) ($row[5] ?? ''));
            if ($nomorLomba === '' || $grup === '' || $kategori === '') {
                $this->warn('Skipping row with empty nomor_lomba/grup/kategori: ' . implode(',', $row));
                continue;
            }
            $rows[] = $row;
        }
        fclose($handle);

        usort($rows, fn($a, $b) => (int) $a[3] <=> (int) $b[3]);

        return $rows;
    }

    private function populateReferensi(Spreadsheet $spreadsheet, array $rows): void
    {
        $sheet = $spreadsheet->getSheetByName('Referensi');

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->removeRow(2, $highestRow - 1);
        }

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;
            $nama   = ucwords(strtolower((string) ($row[4] ?? '')));
            $label  = "{$row[3]} - KU {$row[8]} - {$nama} - {$row[5]}";

            $sheet->setCellValue("A{$rowNum}", $row[0]);   // id
            $sheet->setCellValue("B{$rowNum}", $row[2]);   // jenis_lomba
            $sheet->setCellValue("C{$rowNum}", $row[3]);   // nomor_lomba
            $sheet->setCellValue("D{$rowNum}", $nama);     // nama (titlecase)
            $sheet->setCellValue("E{$rowNum}", $row[5]);   // kategori
            $sheet->setCellValue("F{$rowNum}", $row[8]);   // grup
            $sheet->setCellValue("G{$rowNum}", $row[10]);  // min_umur
            $sheet->setCellValue("H{$rowNum}", $row[9]);   // max_umur
            $sheet->setCellValue("I{$rowNum}", $label);    // label
        }
    }

    private function rebuildValidation(Spreadsheet $spreadsheet, int $rowCount): void
    {
        $lastRow = $rowCount + 1; // +1 for header row

        $spreadsheet->removeNamedRange('LabelReferensi');
        $spreadsheet->addNamedRange(new NamedRange(
            'LabelReferensi',
            $spreadsheet->getSheetByName('Referensi'),
            "\$I\$2:\$I\${$lastRow}"
        ));

        $sheet = $spreadsheet->getSheetByName('Input Atlet');
        foreach (['F', 'G', 'H', 'I', 'J', 'K', 'L'] as $col) {
            for ($row = 2; $row <= 500; $row++) {
                $v = $sheet->getCell("{$col}{$row}")->getDataValidation();
                $v->setType(DataValidation::TYPE_LIST);
                $v->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $v->setAllowBlank(true);
                $v->setShowDropDown(true);
                $v->setShowErrorMessage(true);
                $v->setErrorTitle('Label tidak valid');
                $v->setError('Pilih nomor lomba dari daftar referensi.');
                $v->setFormula1('LabelReferensi');
            }
        }
    }

    private function applyTables(Spreadsheet $spreadsheet): void
    {
        $infoKlub = $spreadsheet->getSheetByName('Info Klub');
        $infoKlub->removeTableCollection();
        $infoKlub->addTable(new Table('A1:E2', 'TableInfoKlub'));

        $inputAtlet = $spreadsheet->getSheetByName('Input Atlet');
        $inputAtlet->removeTableCollection();
        $inputAtlet->addTable(new Table('A1:N500', 'TableInputAtlet'));
    }
}
