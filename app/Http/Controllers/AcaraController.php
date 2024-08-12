<?php

namespace App\Http\Controllers;

use App\Models\Atlet;
use App\Http\Requests\StoreAcaraRequest;
use App\Http\Requests\UpdateAcaraRequest;
use App\Models\Acara;
use App\Models\Kompetisi;
use Carbon\Carbon;
class AcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $acara = Acara::all()->where("kompetisi_id", $id)->sortBy("nomor_lomba");
        
        $nama_kompetisi = Kompetisi::find($id)->nama;
        $id_kompetisi = Kompetisi::find($id)->id;
        return view('pages.kompetisi-daftar')->with(['acara'=> $acara, 'nama_kompetisi'=> $nama_kompetisi, 'id_kompetisi' => $id_kompetisi]);
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
        $acara_id = $acara->id;
        $atlets = $acara->peserta()->where('user_id', auth()->user()->id)->get()->sortBy('name');        

        return view('pages.dashboard-kompetisi-saya-acara2')->with(['acaras' => $acara,'atlets'=> $atlets, 'acara_id'=> $acara_id]);
    }

    public function showPesertaUser($id){
        $acara = Acara::find($id);
        $acaraId = $acara->id;
        $currentDate = Carbon::now();

        $atlets = Atlet::where('user_id', auth()->user()->id)
            ->whereDoesntHave('acara', function ($query) use ($acaraId) {
                $query->where('acara_id', $acaraId);
            })
            ->with('acara')
            ->get()
            ->filter(function ($atlet) use ($currentDate, $acara) {
                $age = $currentDate->diffInYears(Carbon::parse($atlet->umur));
                return $age >= $acara->min_umur && $age <= $acara->max_umur;
            });

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
