<?php // app/Http/Controllers/LaporanController.php
namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Services\LaporanExportService;
use App\Services\LaporanReportService;
use Illuminate\Http\Request;
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

    public function exportOne(Request $request, $id)
    {
        $k = Kompetisi::findOrFail($id);
        $path = $this->exporter->exportCompetition($k);

        return $this->downloadAndCleanup($path, $request);
    }

    public function exportAllActive(Request $request)
    {
        if ($this->reports->activeCompetitions()->isEmpty()) {
            return back()->with('error', 'Tidak ada kompetisi aktif untuk diekspor.');
        }

        $path = $this->exporter->exportActive();

        return $this->downloadAndCleanup($path, $request);
    }

    /**
     * Stream the zip, schedule temp cleanup, and echo back the client's
     * download_token as a readable cookie so the page can detect that the
     * download has started and re-enable its export button.
     */
    private function downloadAndCleanup(string $path, Request $request): BinaryFileResponse
    {
        app()->terminating(function () use ($path) {
            File::deleteDirectory(dirname($path));
        });

        $response = response()->download($path, basename($path));

        $token = (string) $request->query('download_token', '');
        if ($token !== '' && preg_match('/^[A-Za-z0-9]{1,64}$/', $token)) {
            // httpOnly = false so the page's JS can read it to detect the download started.
            $response->headers->setCookie(cookie('download_token', $token, 1, '/', null, null, false));
        }

        return $response;
    }
}
