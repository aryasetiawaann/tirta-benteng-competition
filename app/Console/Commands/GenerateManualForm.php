<?php

namespace App\Console\Commands;

use App\Models\Acara;
use App\Services\LaporanReportService;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class GenerateManualForm extends Command
{
    protected $signature = 'template:manual-form
        {name? : Prefix for the output filename (e.g. "ABC" → "ABC manual-form.xlsx"). Ignored with --all.}
        {--all : Generate one form per active competition, reading acara straight from the database instead of manual/acara.csv.}';
    protected $description = 'Generate manual-form.xlsx files. With a name, populate Referensi from manual/acara.csv. With --all, generate one per active competition from the database.';

    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->handleAll();
        }

        $name = $this->argument('name');
        if (!$name) {
            $this->error('Provide a name argument, or pass --all to generate for every active competition.');
            return 1;
        }

        return $this->handleCsv($name);
    }

    private function handleCsv(string $name): int
    {
        $csvPath = base_path('manual/acara.csv');

        if (!file_exists($csvPath)) {
            $this->error("File not found: {$csvPath}");
            return 1;
        }

        $rows = $this->parseCsv($csvPath);
        if (empty($rows)) {
            $this->error('acara.csv has no valid data rows.');
            return 1;
        }

        $outputPath = base_path("manual/{$name} manual-form.xlsx");
        if (!$this->buildForm($rows, $outputPath)) {
            return 1;
        }

        $this->info("Saved: {$outputPath}");
        return 0;
    }

    private function handleAll(): int
    {
        $competitions = app(LaporanReportService::class)->activeCompetitions();

        if ($competitions->isEmpty()) {
            $this->warn('No active competitions found (buka_pendaftaran <= now <= waktu_kompetisi).');
            return 0;
        }

        // Collect this run's forms in their own timestamped folder so they don't
        // overwrite each other across runs or mix in with the source files.
        $dir = base_path('manual/manual-form_' . now()->format('Ymd_His'));
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            $this->error("Could not create output directory: {$dir}");
            return 1;
        }

        $generated = 0;
        foreach ($competitions as $kompetisi) {
            $rows = $this->rowsFromDatabase($kompetisi->id);
            if (empty($rows)) {
                $this->warn("Skipping '{$kompetisi->nama}' (id {$kompetisi->id}): no acara.");
                continue;
            }

            $filename   = $this->sanitizeFilename($kompetisi->nama);
            $outputPath = "{$dir}/{$filename} manual-form.xlsx";
            if (!$this->buildForm($rows, $outputPath)) {
                return 1;
            }

            $this->info(sprintf('Saved: %s (%d nomor lomba)', $outputPath, count($rows)));
            $generated++;
        }

        $this->info(sprintf('Done. Generated %d manual form(s) in %s', $generated, $dir));
        return 0;
    }

    /**
     * Load acara for a competition and shape each row to match acara.csv's column
     * order, so populateReferensi() can consume DB rows and CSV rows identically.
     */
    private function rowsFromDatabase(int $kompetisiId): array
    {
        $acara = Acara::where('kompetisi_id', $kompetisiId)
            ->orderBy('nomor_lomba')
            ->get();

        $rows = [];
        foreach ($acara as $a) {
            $nomorLomba = trim((string) $a->nomor_lomba);
            $grup       = trim((string) $a->grup);
            $kategori   = trim((string) $a->kategori);
            if ($nomorLomba === '' || $grup === '' || $kategori === '') {
                $this->warn("Skipping acara id {$a->id} with empty nomor_lomba/grup/kategori.");
                continue;
            }

            // Index positions mirror the acara.csv header:
            // id,kompetisi_id,jenis_lomba,nomor_lomba,nama,kategori,harga,kuota,grup,max_umur,min_umur
            $rows[] = [
                $a->id,
                $a->kompetisi_id,
                $a->jenis_lomba,
                $a->nomor_lomba,
                $a->nama,
                $a->kategori,
                $a->harga,
                $a->kuota,
                $a->grup,
                $a->max_umur,
                $a->min_umur,
            ];
        }

        return $rows;
    }

    private function sanitizeFilename(string $name): string
    {
        // Strip characters Windows forbids in filenames, then collapse whitespace.
        $name = preg_replace('/[\/\\\\:*?"<>|]+/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private function buildForm(array $rows, string $outputPath): bool
    {
        $templatePath = base_path('manual/manual-form.xlsx');
        if (!file_exists($templatePath)) {
            $this->error("Template not found: {$templatePath}");
            return false;
        }

        $spreadsheet = IOFactory::load($templatePath);

        $this->populateReferensi($spreadsheet, $rows);
        $this->rebuildValidation($spreadsheet, count($rows));
        $this->applyTables($spreadsheet);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        return true;
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
