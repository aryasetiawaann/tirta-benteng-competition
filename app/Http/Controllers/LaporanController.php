<?php // app/Http/Controllers/LaporanController.php
namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Services\LaporanExportService;
use App\Services\LaporanReportService;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

        return $this->downloadAndCleanup($this->exporter->exportCompetition($k));
    }

    public function exportAllActive()
    {
        if ($this->reports->activeCompetitions()->isEmpty()) {
            return back()->with('error', 'Tidak ada kompetisi aktif untuk diekspor.');
        }

        return $this->downloadAndCleanup($this->exporter->exportActive());
    }

    /** Stream the zip and schedule its temp directory for cleanup after send. */
    private function downloadAndCleanup(string $path): BinaryFileResponse
    {
        app()->terminating(fn () => File::deleteDirectory(dirname($path)));

        return response()->download($path, basename($path));
    }
}
