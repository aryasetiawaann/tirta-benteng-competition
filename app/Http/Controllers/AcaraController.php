<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Atlet;
use App\Http\Requests\StoreAcaraRequest;
use App\Http\Requests\UpdateAcaraRequest;
use App\Models\Kompetisi;
class AcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $acara = Acara::all()->where("kompetisi_id", $id)->sortBy("nomor_lomba");
        
        $nama_kompetisi = Kompetisi::find($id)->nama;
        return view('pages.kompetisi-daftar')->with(['acara'=> $acara, 'nama_kompetisi'=> $nama_kompetisi]);
    }

    public function showPesertaUser($id){
        $acara = Acara::find($id);
        $atlets = Atlet::all()->where('user_id', request()->user()->id)
        ->where('umur', '>=', $acara->min_umur)
        ->where('umur', '<=', $acara->max_umur);

        if ($acara->kategori == 'Pria') {
            $atlets = $atlets->where('jenis_kelamin', 'Pria');
        } elseif ($acara->kategori == 'Wanita') {
            $atlets = $atlets->where('jenis_kelamin', 'Wanita');
        }

        return view('pages.kompetisi-daftar2')->with(['acara'=> $acara, 'atlets'=> $atlets]);
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
    public function update(UpdateAcaraRequest $request, Acara $acara)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Acara $acara)
    {
        //
    }
}
