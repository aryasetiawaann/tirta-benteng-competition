<?php

namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Models\Atlet;
use App\Models\Acara;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use ZipArchive;


class KompetisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua kompetisi yang aktif berdasarkan tanggal tutup pendaftaran
        $kompetisi = Kompetisi::all()->sortByDesc("tutup_pendaftaran");

        // Cek apakah user sudah mengisi nomor telepon
        $userHasPhone = auth()->user()->phone != null;

        // Kirim data kompetisi dan status nomor telepon user ke view
        return view('pages.dashboard-kompetisi')->with([
            'kompetisi' => $kompetisi,
            'userHasPhone' => $userHasPhone
        ]);
    }

    public function kelompokUmur($id)
    {
        $acara = Acara::all()->where("kompetisi_id", $id)->sortBy("grup");

        $grupList = $acara->map(function ($item){
            return [
                'grup' => $item->grup,
                'max_umur' => $item->max_umur,
                'min_umur' => $item->min_umur,
            ];
        })->unique('grup')->sortBy('grup')->values();
        
        $nama_kompetisi = Kompetisi::find($id)->nama;
        $id_kompetisi = Kompetisi::find($id)->id;

        return view('pages.dashboard-kompetisi-kelompokumur', compact('grupList', 'nama_kompetisi', 'id_kompetisi'));
    }

    public function kompetisiSaya(){
        $acara_ids = Atlet::where('user_id', auth()->user()->id) // or auth()->user()->id
        ->with('acara') // eager load acara
        ->get()
        ->flatMap(function ($atlet) {
            return $atlet->acara->pluck('id');
        })
        ->unique();

        $kompetisis = Kompetisi::whereHas('acara', function ($query) use ($acara_ids) {
            $query->whereIn('id', $acara_ids);
        })->get();

        return view('pages.dashboard-kompetisi-saya')->with(['kompetisis' => $kompetisis]);
    }

    public function adminIndex(){

        $kompetisis = Kompetisi::all();

        return view('admin.admin-tambahkompetisi', compact('kompetisis'));
    }

    public function showKompetisiAdmin(){
        $kompetisi = Kompetisi::all()->sortByDesc("waktu_kompetisi");

        return view('admin.admin-tambahacara', compact('kompetisi'));
    }

    public function tambahKompetisi(Request $request)
    {
        $data = [ "nama"=> $request->nama,
        "lokasi"=> $request->lokasi,
        "deskripsi"=> $request->deskripsi,
        "buka_pendaftaran"=> $request->openreg,
        "tutup_pendaftaran"=> $request->closereg,
        "kategori"=> $request->kategori,
        "waktu_techmeeting"=> $request->techmeet,
        "waktu_kompetisi"=> $request->datekompe,
        ];

        $validation = Validator::make($data, [
            "nama" => "required",
            "lokasi" => "required",
            "buka_pendaftaran" => "required|date",
            "tutup_pendaftaran" => "required|date|after:buka_pendaftaran",
            "kategori" => "required",
            "waktu_techmeeting" => "required|date|after:tutup_pendaftaran",
            "waktu_kompetisi" => "required|date|after:waktu_techmeeting",
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'buka_pendaftaran.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tutup_pendaftaran.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tutup_pendaftaran.after' => 'Tanggal tutup pendaftaran harus setelah tanggal buka pendaftaran.',
            'waktu_techmeeting.required' => 'Waktu technical meeting wajib diisi.',
            'waktu_techmeeting.after' => 'Waktu technical meeting harus setelah tanggal tutup pendaftaran.',
            'waktu_kompetisi.required' => 'Waktu kompetisi wajib diisi.',
            'waktu_kompetisi.after' => 'Waktu kompetisi harus setelah waktu technical meeting.',
        ]);

        $validation->after(function($validator) use ($data) {
            if (isset($data['waktu_kompetisi']) && isset($data['waktu_techmeeting']) && isset($data['tutup_pendaftaran'])) {
                $waktuKompetisi = strtotime($data['waktu_kompetisi']);
                $waktuTechMeeting = strtotime($data['waktu_techmeeting']);
                $tutupPendaftaran = strtotime($data['tutup_pendaftaran']);
                
                if ($waktuKompetisi <= $tutupPendaftaran || $waktuKompetisi <= $waktuTechMeeting) {
                    $validator->errors()->add('waktu_kompetisi', 'Waktu kompetisi harus setelah waktu technical meeting dan tanggal tutup pendaftaran.');
                }
            }
        });
        
        if ($validation->fails()) {
            return redirect()->back()
                ->withErrors($validation)
                ->withInput();
        }

        Kompetisi::create($data);

        return redirect()->back()->with('success', 'Data kompetisi berhasil disimpan.');

    }

    public function editKompetisi($id){
        $kompetisi = Kompetisi::find($id);

        return view('admin.admin-editkompetisi', compact('kompetisi'));
    }
    
    public function uploadHasilKompetisi(Request $request)
    {

        $kompetisi = Kompetisi::find($request->kompetisi);

        if ($request->hasFile('file')) {

            if ($kompetisi->file_hasil && File::exists(public_path($kompetisi->file_hasil))) {
                File::delete(public_path($kompetisi->file_hasil));
            }

            $fileName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('assets/file_hasil'), $fileName);
            $kompetisi->file_hasil = 'assets/file_hasil/' . $fileName;
        }

        $kompetisi->save();

        return redirect()->back()->with('success','Upload berhasil');
        
    }

    public function deleteHasilKompetisi($id)
    {
        $kompetisi = Kompetisi::find($id);

        if ($kompetisi->file_hasil && File::exists(public_path($kompetisi->file_hasil))) {
            File::delete(public_path($kompetisi->file_hasil));
            $kompetisi->file_hasil = null;
            $kompetisi->save();
        }

        return redirect()->back()->with('success','File berhasil dihapus');
    }

    public function editHasilKompetisi($id)
    {
        $kompetisi = Kompetisi::find($id);

        return view('admin.admin-editfile', compact('kompetisi'));
    }

    public function updateHasilKompetisi(Request $request)
    {
        $kompetisi = Kompetisi::find($request->id);

        if ($request->hasFile('file')) {

            if ($kompetisi->file_hasil && File::exists(public_path($kompetisi->file_hasil))) {
                File::delete(public_path($kompetisi->file_hasil));
            }

            $fileName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('assets/file_hasil'), $fileName);
            $kompetisi->file_hasil = 'assets/file_hasil/' . $fileName;
        }

        $kompetisi->save();

        return redirect()->route('admin.dashboard')->with('success','File berhasil diperbaharui');
    }

    public function downloadHasilKompetisi($id)
    {
        $kompetisi = Kompetisi::find($id);

        $path = public_path($kompetisi->file_hasil);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        $response->header("Content-Disposition", 'attachment; filename='.$kompetisi->nama.".pdf");

        return $response;

    }

    public function update(Request $request)
    {
        $data = [ "nama"=> $request->nama,
        "lokasi"=> $request->lokasi,
        "deskripsi"=> $request->deskripsi,
        "buka_pendaftaran"=> $request->openreg,
        "tutup_pendaftaran"=> $request->closereg,
        "kategori"=> $request->kategori,
        "waktu_techmeeting"=> $request->techmeet,
        "waktu_kompetisi"=> $request->datekompe,
        ];

        $validation = Validator::make($data, [
            "nama" => "required",
            "lokasi" => "required",
            "buka_pendaftaran" => "required|date",
            "tutup_pendaftaran" => "required|date|after:buka_pendaftaran",
            "kategori" => "required",
            "waktu_techmeeting" => "required|date|after:tutup_pendaftaran",
            "waktu_kompetisi" => "required|date|after:waktu_techmeeting",
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'buka_pendaftaran.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tutup_pendaftaran.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tutup_pendaftaran.after' => 'Tanggal tutup pendaftaran harus setelah tanggal buka pendaftaran.',
            'waktu_techmeeting.required' => 'Waktu technical meeting wajib diisi.',
            'waktu_techmeeting.after' => 'Waktu technical meeting harus setelah tanggal tutup pendaftaran.',
            'waktu_kompetisi.required' => 'Waktu kompetisi wajib diisi.',
            'waktu_kompetisi.after' => 'Waktu kompetisi harus setelah waktu technical meeting.',
        ]);

        $validation->after(function($validator) use ($data) {
            if (isset($data['waktu_kompetisi']) && isset($data['waktu_techmeeting']) && isset($data['tutup_pendaftaran'])) {
                $waktuKompetisi = strtotime($data['waktu_kompetisi']);
                $waktuTechMeeting = strtotime($data['waktu_techmeeting']);
                $tutupPendaftaran = strtotime($data['tutup_pendaftaran']);
                
                if ($waktuKompetisi <= $tutupPendaftaran || $waktuKompetisi <= $waktuTechMeeting) {
                    $validator->errors()->add('waktu_kompetisi', 'Waktu kompetisi harus setelah waktu technical meeting dan tanggal tutup pendaftaran.');
                }
            }
        });
        
        if ($validation->fails()) {
            return redirect()->back()
                ->withErrors($validation)
                ->withInput();
        }

        $kompetisi = Kompetisi::find($request->id);
        $kompetisi->update($data);

        return redirect()->route('dashboard.admin.acara')->with('success', 'Data kompetisi berhasil diperbaharui.');

    }

    public function downloadDokumen($id)
    {
        $kompetisi = Kompetisi::find($id);

        if (!$kompetisi) {
            return redirect()->back()->with('error', 'Kompetisi tidak ditemukan.');
        }

        $acaras = $kompetisi->acara;

        $participantsExist = false;

        foreach ($acaras as $acara) {
            if ($acara->pesertaSelesai->isNotEmpty()) {
                $participantsExist = true;
                break;
            }
        }
    

        if (!$participantsExist) {
            return redirect()->back()->with('error', 'Tidak ada peserta dalam kompetisi ini.');
        }


        $zipFileName = $kompetisi->nama . '.zip';
        $zipFilePath = public_path($zipFileName);


        $zip = new ZipArchive;

        // Buka zip file untuk ditulis
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            
            $hasFiles = false; 

            foreach ($acaras as $acara) {
                $participants = $acara->peserta;

                foreach ($participants as $participant) {


                    if ($participant->dokumen != null && $participant->pivot->status_pembayaran == "Selesai") {
                        $filePath = public_path($participant->dokumen);

                        
                        if (File::exists($filePath)) {
                            // Tambahkan file ke dalam zip dengan nama yang unik
                            $zip->addFile($filePath, $participant->name . '_' . basename($filePath));
                            $hasFiles = true;
                        }
                    }
                }
            }

            $zip->close();
        }

        if (!$hasFiles) {
            File::delete($zipFilePath); // Hapus file zip kosong
            return redirect()->back()->with('error', 'Tidak ada dokumen yang tersedia untuk diunduh.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Kompetisi::find($id)->delete();
        return redirect()->route('dashboard.admin.acara')->with('success','Kompetisi berhasil dihapus');
    }
}
