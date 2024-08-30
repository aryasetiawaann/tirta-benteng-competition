<?php

namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Models\Atlet;
use App\Http\Requests\StoreKompetisiRequest;
use App\Http\Requests\UpdateKompetisiRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KompetisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kompetisi = Kompetisi::all()->sortByDesc("tutup_pendaftaran");

        return view('pages.dashboard-kompetisi')->with(['kompetisi'=>$kompetisi]);
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

        return view('admin.admin-tambahkompetisi');
    }

    public function showKompetisiAdmin(){
        $kompetisi = Kompetisi::all()->sortByDesc("tutup_pendaftaran");

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
                ->withInput()
                ->with('error', 'Validasi gagal, silakan periksa kembali input Anda.');
        }

        Kompetisi::create($data);

        return redirect()->back()->with('success', 'Data berhasil disimpan.');

    }

    public function editKompetisi($id){
        $kompetisi = Kompetisi::find($id);

        return view('admin.admin-editkompetisi', compact('kompetisi'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKompetisiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Kompetisi $kompetisi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kompetisi $kompetisi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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
                ->withInput()
                ->with('error', 'Validasi gagal, silakan periksa kembali input Anda.');
        }

        $kompetisi = Kompetisi::find($request->id);
        $kompetisi->update($data);

        return redirect()->route('dashboard.admin.acara')->with('success', 'Data berhasil diperbaharui.');

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
