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
