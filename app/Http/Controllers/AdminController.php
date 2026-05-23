<?php

namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Models\Atlet;
use App\Models\Peserta;
use App\Services\AtletImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard(){

      
      $kompetisis = Kompetisi::whereNull('file_hasil')->get();
      $kompetisi_file = Kompetisi::whereNotNull('file_hasil')->orderByDesc('waktu_kompetisi')->get();
      $kompetisi = Kompetisi::all()->sortByDesc('waktu_kompetisi');


      return view('admin.admin-dashboard', compact('kompetisis', 'kompetisi_file', 'kompetisi'));
    }

    public function verification(){

      $notVerAtlets = Atlet::with('user')->whereNotNull('dokumen')->where('is_verified', 'not verified')->get()->sortBy('updated_at');

      return view('admin.admin-verifikasi-atlet', compact('notVerAtlets'));
    }

    public function revision(){
      $flagAtlets = Atlet::with('user')->whereNotNull('dokumen')->where('is_verified', 'need revision')->get()->sortBy('updated_at');

      return view('admin.admin-revisi-atlet', compact('flagAtlets'));
    }

    public function atletList() {

      $atlets = Atlet::orderByDesc('created_at')->get();

      return view('admin.admin-list-atlet', compact('atlets'));
    }

    public function pembayaranList(){

      $pesertas = Peserta::with('getAcara', 'getAtlet')
                ->where('status_pembayaran', 'Menunggu')
                ->get();

      return view('admin.admin-verifikasi-bayar', compact('pesertas'));
    }

    public function updatePembayaran(Request $request){
      
      $peserta_ids = $request->peserta_ids;
      
      if (!$peserta_ids || !is_array($peserta_ids)) {
          return response()->json(['message' => 'Data peserta tidak valid.'], 400);
      }

      $total = count($peserta_ids);

      // Update semua peserta yang termasuk dalam daftar ID
      Peserta::whereIn('id', $peserta_ids)->update([
          'status_pembayaran' => 'Selesai',
      ]);

      return response()->json(['message' => "$total status pembayaran berhasil diperbarui."]);
    }

    public function importAtletForm()
    {
        $kompetisis = Kompetisi::orderBy('id', 'desc')->get();
        return view('admin.admin-import-atlet', compact('kompetisis'));
    }

    public function importAtlet(Request $request)
    {
        $request->validate([
            'kompetisi_id' => 'required|exists:kompetisi,id',
            'file'         => 'required|file|mimes:xlsx',
        ]);

        $path = $request->file('file')->store('imports', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $result = (new AtletImportService())->import($fullPath, (int) $request->kompetisi_id);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Import gagal: ' . $e->getMessage()]);
        } finally {
            @unlink($fullPath);
        }

        return redirect()
            ->route('admin.import.atlet.form')
            ->with('import_result', $result);
    }
}
