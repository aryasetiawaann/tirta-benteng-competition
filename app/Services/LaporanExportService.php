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

    public function exportActive(): string
    {
        $active = $this->reports->activeCompetitions();
        if ($active->isEmpty()) {
            throw new \RuntimeException('No active competitions to export.');
        }

        return $this->buildZip($active->all(), combined: true);
    }

    /**
     * @param  array<int, Kompetisi>  $competitions
     */
    private function buildZip(array $competitions, bool $combined): string
    {
        // Time (HHMMSS) is used only in the folder name; the files inside use date only.
        $now = now();
        $date = $now->format('d-m-Y');
        $folder = 'laporan ' . $now->format('d-m-Y_His'); // e.g. "laporan 30-06-2026_210910"
        $ids = array_map(fn ($k) => $k->id, $competitions);

        $relDir = 'laporan-tmp/' . Str::uuid();
        $tmpDir = storage_path('app/' . $relDir);
        File::ensureDirectoryExists($tmpDir);

        // [zip entry name => absolute source path]
        $files = [];

        try {
            if ($combined) {
                $files["{$folder}/list_club_payment {$date}.xlsx"] = $this->writeSheet(
                    $relDir, "list_club_payment {$date}.xlsx",
                    LaporanReportService::CLUB_PAYMENT_HEADINGS,
                    $this->reports->clubPaymentRows($ids), self::CLUB_LEFT,
                );
                $files["{$folder}/list_daftar {$date}.xlsx"] = $this->writeSheet(
                    $relDir, "list_daftar {$date}.xlsx",
                    LaporanReportService::DAFTAR_HEADINGS,
                    $this->reports->daftarRows($ids), self::DAFTAR_LEFT,
                );
            }

            $safeNames = array_map(fn ($k) => str_replace(['/', '\\'], '-', $k->nama), $competitions);
            $dupes = array_keys(array_filter(array_count_values($safeNames), fn ($c) => $c > 1));

            foreach ($competitions as $k) {
                $safe = str_replace(['/', '\\'], '-', $k->nama);
                $suffix = in_array($safe, $dupes, true) ? " ({$k->id})" : '';
                $files["{$folder}/list_club_payment {$date} {$safe}{$suffix}.xlsx"] = $this->writeSheet(
                    $relDir, "list_club_payment {$date} {$safe}{$suffix}.xlsx",
                    LaporanReportService::CLUB_PAYMENT_HEADINGS,
                    $this->reports->clubPaymentRows([$k->id]), self::CLUB_LEFT,
                );
                $files["{$folder}/list_daftar {$date} {$safe}{$suffix}.xlsx"] = $this->writeSheet(
                    $relDir, "list_daftar {$date} {$safe}{$suffix}.xlsx",
                    LaporanReportService::DAFTAR_HEADINGS,
                    $this->reports->daftarRows([$k->id]), self::DAFTAR_LEFT,
                );
            }

            $zipDir = storage_path('app/laporan-zip/' . Str::uuid());
            File::ensureDirectoryExists($zipDir);
            $zipPath = $zipDir . '/' . $folder . '.zip';

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException("Could not create zip archive at {$zipPath}");
            }
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
