<?php

namespace App\Http\Controllers;

use App\Models\HargaKompetisi;
use App\Http\Requests\StoreHargaKompetisiRequest;
use App\Http\Requests\UpdateHargaKompetisiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HargaKompetisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $data = ["judul" => $request->judul,
        "harga" => $request->harga,
        "deskripsi" => $request->deskripsiHarga,
        "kompetisi_id" => $request->kompetisi];

        $validation = Validator::make($data, [
            "judul" => "required",
            "harga" => "required|numeric|min:0"
        ], [
            "judul.required" => "Judul wajib diisi",
            "harga.required" => "Harga wajib diisi",
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh kurang dari 0.',
        ]);

        if ($validation->fails()) {
            return redirect()->back()
                ->withErrors($validation)
                ->withInput();
        }


        HargaKompetisi::create($data);

        return redirect()->route('dashboard.admin.kompetisi')->with('success', 'Detail harga berhasil dimasukkan');
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHargaKompetisiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(HargaKompetisi $hargaKompetisi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HargaKompetisi $hargaKompetisi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHargaKompetisiRequest $request, HargaKompetisi $hargaKompetisi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        HargaKompetisi::find($id)->delete();

        return redirect()->route('admin.dashboard')->with('success','Detail harga berhasil dihapus');
    }
}
