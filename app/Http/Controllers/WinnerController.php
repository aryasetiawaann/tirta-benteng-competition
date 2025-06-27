<?php

namespace App\Http\Controllers;

use App\Models\Winner;
use App\Models\Certificate;
use App\Models\Letter;
use App\Models\Kompetisi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KejuaraanImport;
use Illuminate\Support\Facades\Storage;

class WinnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kompetisiList = Kompetisi::all();

        $query = Winner::query()->with('acara');

        if ($request->has('filter_kompetisi') && $request->filter_kompetisi != '') {
            $query->where('kompetisi_id', $request->filter_kompetisi);
        }

        $pemenangList = $query->orderBy('nomor_lomba')->paginate(10);

        return view('admin.admin-kejuaraan', compact('kompetisiList', 'pemenangList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $kompetisiId = $request->input('kompetisi_id'); 

        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
            'kompetisi_id' => 'required|exists:kompetisi,id',
        ]);

        Excel::import(new KejuaraanImport($kompetisiId), $request->file('file'));

        return back()->with('success', 'Data kejuaraan berhasil diimpor!');
    }



    public function inputDokumen(Request $request)
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:sertifikat,surat_keterangan',
            'dokumen.*' => 'required|mimes:pdf|max:2048',
            'kompetisi_id' => 'required|exists:kompetisi,id',
        ]);

        $jenis = $request->jenis_dokumen;

        // dd($request->file('dokumen'));

        foreach ($request->file('dokumen') as $file) {
            // Ambil nama file tanpa ekstensi
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Ambil bagian kode di antara tanda '-' pertama dan terakhir
            $segments = explode('-', $fileName);
            if (count($segments) < 3) {
                continue; // Skip jika format tidak valid
            }


            // Ambil kode
            $kode = $segments[1] . '-' . $segments[2]; // "33%2FTBSC-TNG%2FVI%2F2025"

            // Cari Winner berdasarkan kode
            $winner = Winner::where('kode', $kode)->where('kompetisi_id', $request->input('kompetisi_id'))->first();


            if (!$winner) {
                continue; // Skip jika tidak ditemukan
            }

            // Simpan file ke storage
            $path = $file->store('dokumen_kejuaraan', 'public');

            // Simpan ke model sesuai jenis
            if ($jenis === 'sertifikat') {



                $certificate = Certificate::create([
                    'path' => $path,
                    'filename' => $file->getClientOriginalName(),
                ]);

                $winner->certificate_id = $certificate->id;
            } elseif ($jenis === 'surat_keterangan') {
                $letter = Letter::create([
                    'path' => $path,
                    'filename' => $file->getClientOriginalName(),
                ]);

                $winner->letter_id = $letter->id;
            }

            // Simpan perubahan di Winner
            $winner->save();
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diproses.');
    }

}
