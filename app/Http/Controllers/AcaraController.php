<?php

namespace App\Http\Controllers;

use App\Models\Atlet;
use App\Http\Requests\StoreAcaraRequest;
use App\Http\Requests\UpdateAcaraRequest;
use App\Models\Acara;
use App\Models\Kompetisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class AcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($kelompok, $id)
    {
        $acara = Acara::all()->where("kompetisi_id", $id)->where("grup", $kelompok);
        
        $nama_kompetisi = Kompetisi::find($id)->nama;
        $id_kompetisi = Kompetisi::find($id)->id;
        return view('pages.kompetisi-daftar')->with(['acara'=> $acara, 'nama_kompetisi'=> $nama_kompetisi, 'id_kompetisi' => $id_kompetisi, 'kelompok' => $kelompok]);
    }

    public function kompetisiSaya($id){

        $acara_ids = Atlet::where('user_id', auth()->user()->id)
        ->with(['acara' => function ($query) use ($id) {
            $query->where('kompetisi_id', $id);
        }])
        ->get()
        ->flatMap(function ($atlet) {
            return $atlet->acara->pluck('id');
        })
        ->unique();

        $acaras = Acara::whereIn('id', $acara_ids)->get();
        $nama_kompetisi = Kompetisi::find($id)->nama;
        $id_kompetisi = Kompetisi::find($id)->id;

        return view('pages.dashboard-kompetisi-saya-acara')->with(['acaras' => $acaras, 'nama_kompetisi'=> $nama_kompetisi, 'id_kompetisi' => $id_kompetisi]);
    }

    public function kompetisiSayaDetail($id){
        $acara = Acara::find($id);
        $atlets = $acara->peserta()->where('user_id', auth()->user()->id)->get()->sortBy('name');        

        return view('pages.dashboard-kompetisi-saya-acara2')->with(['acara' => $acara,'atlets'=> $atlets]);
    }

    public function showPesertaUser($kelompok, $id){
        $acara = Acara::find($id);
        $acaraId = $acara->id;

        // Ambil tahun dari min_umur dan max_umur
        $minYear = $acara->min_umur;
        $maxYear = $acara->max_umur;

        // Ambil atlit yang belum terdaftar di acara ini
            $atlets = Atlet::where('user_id', auth()->user()->id)
            ->whereDoesntHave('acara', function ($query) use ($acaraId) {
                $query->where('acara_id', $acaraId);
            })
            ->with('acara')
            ->orderBy('name', 'asc')
            ->get()
            ->filter(function ($atlet) use ($minYear, $maxYear) {
                // Mengambil tahun lahir atlet
                $birthYear = Carbon::parse($atlet->umur)->year;

                // Memfilter atlit berdasarkan tahun lahir
                if ($maxYear !== null) {
                    // Jika max_umur ada, atlit harus memiliki tahun lahir antara min_umur dan max_umur
                    return $birthYear >= $minYear && $birthYear <= $maxYear;
                } else {
                    // Jika max_umur null, atlit hanya boleh memiliki tahun lahir sama dengan min_umur
                    return $birthYear == $minYear;
                }
            });

        // Menyaring atlit berdasarkan kategori (Pria/Wanita)
        if ($acara->kategori == 'Pria') {
            $atlets = $atlets->where('jenis_kelamin', 'Pria');
        } elseif ($acara->kategori == 'Wanita') {
            $atlets = $atlets->where('jenis_kelamin', 'Wanita');
        }

        // Mengambil daftar peserta yang sudah terdaftar untuk acara ini
        $atletList = $acara->peserta()->where('user_id', auth()->user()->id)->get()->sortBy('name');

        return view('pages.kompetisi-daftar2')->with([
            'acara' => $acara, 
            'atlets' => $atlets, 
            'kelompok' => $kelompok, 
            'atletsList' => $atletList
        ]);
    }

    public function indexAdmin($id){
        $acara = Acara::all()->where("kompetisi_id", $id)->sortBy("nomor_lomba");
        
        $nama_kompetisi = Kompetisi::find($id)->nama;
        $id_kompetisi = Kompetisi::find($id)->id;
        return view('admin.admin-tambahacara-list', compact('acara', 'nama_kompetisi', 'id_kompetisi'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $data = ["kompetisi_id" => $request->kompe_id,
        "nomor_lomba" => $request->nomor,
        "nama" => $request->nama,
        "kategori" => $request->kategori,
        "jenis_lomba" => $request->jenis_lomba,
        "harga" => $request->harga,
        "kuota" => $request->kuota,
        "grup" => $request->grup,
        "min_umur" => $request->minumur,
        "max_umur" => $request->maxumur];


        $validation = Validator::make($data, [
            "kompetisi_id" => "required|exists:kompetisi,id",
            "nomor_lomba" => "required",
            "nama" => "required",
            "kategori" => "required",
            "jenis_lomba" => "required",
            "harga" => "required|numeric|min:0",
            "kuota" => "required|integer|min:1",
            "grup" => "required",
            "min_umur" => "required|integer|min:0",
            "max_umur" => "nullable|integer|min:0",
        ], [
            'kompetisi_id.required' => 'Kompetisi ID wajib diisi.',
            'kompetisi_id.exists' => 'Kompetisi tidak ditemukan.',
            'nomor_lomba.required' => 'Nomor lomba wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'jenis_lomba.required' => 'Jenis Lomba wajib diisi.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh kurang dari 0.',
            'kuota.required' => 'Kuota wajib diisi.',
            'kuota.integer' => 'Kuota harus berupa angka.',
            'kuota.min' => 'Kuota harus lebih besar dari 0.',
            'grup.required' => 'Kelompok umur wajib diisi.',
            'max_umur.integer' => 'Maksimal tahun harus berupa angka.',
            'max_umur.min' => 'Maksimal tahun tidak boleh kurang dari 0.',
            'min_umur.required' => 'Minimal tahun wajib diisi.',
            'min_umur.integer' => 'Minimal tahun harus berupa angka.',
            'min_umur.min' => 'Minimal tahun tidak boleh kurang dari 0.',
        ]);

        $validation->after(function($validator) use ($data) {
            if (isset($data['max_umur']) && isset($data['min_umur'])) {
                if ($data['max_umur'] < $data['min_umur']) {
                    $validator->errors()->add('max_umur', 'Maksimal tahun harus lebih besar dari atau sama dengan minimal tahun.');
                }
            }
        });
        
        if ($validation->fails()) {
            return redirect()->back()
                ->withErrors($validation)
                ->withInput();
        }
        
        Acara::create($data);
        
        return redirect()->back()->with('success', 'Data acara berhasil disimpan.');

    }

    public function editAcara($id)
    {
        $acara = Acara::find($id);

        return view('admin.admin-editacara', compact('acara'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcaraRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Acara $acara)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Acara $acara)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = ["nomor_lomba" => $request->nomor,
        "nama" => $request->nama,
        "kategori" => $request->kategori,
        "jenis_lomba" => $request->jenis_lomba,
        "harga" => $request->harga,
        "kuota" => $request->kuota,
        "grup" => $request->grup,
        "min_umur" => $request->minumur,
        "max_umur" => $request->maxumur];


        $validation = Validator::make($data, [
            "nomor_lomba" => "required",
            "nama" => "required",
            "kategori" => "required",
            "jenis_lomba" => "required",
            "harga" => "required|numeric|min:0",
            "kuota" => "required|integer|min:1",
            "grup" => "required",
            "min_umur" => "required|integer|min:0",
            "max_umur" => "nullable|integer|min:0",
        ], [
            'nomor_lomba.required' => 'Nomor acara wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'jenis_lomba.required' => 'Jenis lomba wajib diisi.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh kurang dari 0.',
            'kuota.required' => 'Kuota wajib diisi.',
            'kuota.integer' => 'Kuota harus berupa angka.',
            'kuota.min' => 'Kuota harus lebih besar dari 0.',
            'grup.required' => 'Kelompok Umur wajib diisi.',
            'max_umur.integer' => 'Maksimal tahun harus berupa angka.',
            'max_umur.min' => 'Maksimal tahun tidak boleh kurang dari 0.',
            'min_umur.required' => 'Minimal tahun wajib diisi.',
            'min_umur.integer' => 'Minimal tahun harus berupa angka.',
            'min_umur.min' => 'Minimal tahun tidak boleh kurang dari 0.',
        ]);

        $validation->after(function($validator) use ($data) {
            if (isset($data['max_umur']) && isset($data['min_umur'])) {
                if ($data['max_umur'] < $data['min_umur']) {
                    $validator->errors()->add('max_umur', 'Maksimal tahun harus lebih besar dari atau sama dengan minimal tahun.');
                }
            }
        });
        
        if ($validation->fails()) {
            return redirect()->back()
                ->withErrors($validation)
                ->withInput();
        }
        
        $acara = Acara::find($request->id);
        $acara->update($data);
        
        return redirect()->route('dashboard.admin.listacara', $request->kompe_id)->with('success', 'Data acara berhasil diubah.');
   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $acara = Acara::find($id);
        $id_kompetisi = $acara->kompetisi->id;

        $acara->delete();

        return redirect()->route('dashboard.admin.listacara', $id_kompetisi)->with('success','Acara berhasil dihapus');
    }
}
