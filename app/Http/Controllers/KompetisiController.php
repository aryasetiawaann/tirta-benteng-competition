<?php

namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Models\Atlet;
use App\Http\Requests\StoreKompetisiRequest;
use App\Http\Requests\UpdateKompetisiRequest;

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
    public function update(UpdateKompetisiRequest $request, Kompetisi $kompetisi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kompetisi $kompetisi)
    {
        //
    }
}
