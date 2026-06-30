<?php // app/Http/Controllers/LaporanController.php
namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Services\LaporanExportService;
use App\Services\LaporanReportService;

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
        $path = $this->exporter->exportCompetition($k);

        app()->terminating(function () use ($path) {
            \Illuminate\Support\Facades\File::deleteDirectory(dirname($path));
        });
        return response()->download($path, basename($path));
    }

    public function exportAllActive()
    {
        if ($this->reports->activeCompetitions()->isEmpty()) {
            return back()->with('error', 'Tidak ada kompetisi aktif untuk diekspor.');
        }

        $path = $this->exporter->exportActive();

        app()->terminating(function () use ($path) {
            \Illuminate\Support\Facades\File::deleteDirectory(dirname($path));
        });
        return response()->download($path, basename($path));
    }
}
