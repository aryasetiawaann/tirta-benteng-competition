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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('club', 'LIKE', "%{$search}%")
                  ->orWhere('kode', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_lomba', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('filter_dokumen')) {
            $filter = $request->filter_dokumen;
            if ($filter == 'has_sertifikat') {
                $query->whereNotNull('certificate_id');
            } elseif ($filter == 'no_sertifikat') {
                $query->whereNull('certificate_id');
            } elseif ($filter == 'has_sk') {
                $query->whereNotNull('letter_id');
            } elseif ($filter == 'no_sk') {
                $query->whereNull('letter_id');
            } elseif ($filter == 'has_both') {
                $query->whereNotNull('certificate_id')->whereNotNull('letter_id');
            } elseif ($filter == 'no_both') {
                $query->whereNull('certificate_id')->whereNull('letter_id');
            }
        }

        $pemenangList = $query->orderBy('nomor_lomba')->paginate(10)->withQueryString();

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
        $kompetisiId = $request->input('kompetisi_id');

        // Ambil semua pemenang untuk kompetisi ini
        $winners = Winner::where('kompetisi_id', $kompetisiId)->get();

        foreach ($request->file('dokumen') as $file) {
            $originalName = $file->getClientOriginalName();
            
            $newFileName = $originalName;
            
            // Handle jika pemisahnya '%F' atau '%f'
            $newFileName = str_ireplace('%f', '%2F', $newFileName);
            
            // Ubah % menjadi %2F (kecuali jika sudah %2F)
            $newFileName = preg_replace('/%(?!2F)/i', '%2F', $newFileName);
            
            // Ambil nama file tanpa ekstensi untuk pencocokan
            $fileName = pathinfo($newFileName, PATHINFO_FILENAME);
            $decodedFileName = urldecode($fileName);

            // Cari Winner berdasarkan kode yang ada di nama file
            $winner = $winners->first(function ($w) use ($decodedFileName) {
                // Normalisasi kode dari DB agar jika DB menggunakan % atau %F, disamakan dulu
                $kodeNormalized = str_ireplace('%f', '%2F', $w->kode);
                $kodeNormalized = preg_replace('/%(?!2F)/i', '%2F', $kodeNormalized);
                
                $kodeLower = strtolower(urldecode($kodeNormalized));
                $fileNameLower = strtolower($decodedFileName);
                
                // Jika nama file sama persis dengan kode
                if ($fileNameLower === $kodeLower) {
                    return true;
                }
                
                // Cek apakah nama file dimulai dengan kode + pemisah
                if (str_starts_with($fileNameLower, $kodeLower . '-') || 
                    str_starts_with($fileNameLower, $kodeLower . '_') || 
                    str_starts_with($fileNameLower, $kodeLower . ' ')) {
                    return true;
                }

                // Cek apakah nama file berakhiran dengan pemisah + kode
                if (str_ends_with($fileNameLower, '-' . $kodeLower) || 
                    str_ends_with($fileNameLower, '_' . $kodeLower) || 
                    str_ends_with($fileNameLower, ' ' . $kodeLower)) {
                    return true;
                }

                // Cek apakah nama file mengandung pemisah + kode + pemisah di tengah
                if (str_contains($fileNameLower, '-' . $kodeLower . '-') ||
                    str_contains($fileNameLower, '_' . $kodeLower . '_') ||
                    str_contains($fileNameLower, ' ' . $kodeLower . ' ')) {
                    return true;
                }

                return false;
            });

            if (!$winner) {
                continue; // Skip jika tidak ditemukan
            }

            // Simpan file ke storage dengan nama baru
            $path = $file->storeAs('dokumen_kejuaraan', $newFileName, 'public');

            // Simpan ke model sesuai jenis
            if ($jenis === 'sertifikat') {



                $certificate = Certificate::create([
                    'path' => $path,
                    'filename' => $newFileName,
                ]);

                $winner->certificate_id = $certificate->id;
            } elseif ($jenis === 'surat_keterangan') {
                $letter = Letter::create([
                    'path' => $path,
                    'filename' => $newFileName,
                ]);

                $winner->letter_id = $letter->id;
            }

            // Simpan perubahan di Winner
            $winner->save();
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diproses.');
    }

}
