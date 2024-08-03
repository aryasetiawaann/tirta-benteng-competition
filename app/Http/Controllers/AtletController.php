<?php

namespace App\Http\Controllers;

use App\Models\Atlet;
use App\Http\Requests\StoreAtletRequest;
use App\Http\Requests\UpdateAtletRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AtletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $atlets = Atlet::all()->where('user_id', request()->user()->id);

        $atlet_count = $atlets->count();

        return view('pages.dashboard-atlet')->with(['atlets'=>$atlets, 'atlets_count'=>$atlet_count]);
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
    public function store(Request $request)
    {
        $data = [ "name"=> $request->nama,
        "umur"=> $request->umur,
        "jenis_kelamin"=> $request->jenisKelamin,
        "track_record"=> $request->record,
        "user_id"=> auth()->user()->id,
        ];

        $validation = Validator::make($data, [
            "name"=> "required",
            "umur"=> "required",
            "jenis_kelamin"=> "required",
            "track_record" => "numeric|regex:/^\d+\.\d{2}$/"
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        Atlet::create($data);

        return redirect()->back()->with('success','Atlet berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Atlet $atlet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
       $atlet = Atlet::find($id);

       return view('pages.edit-atlet')->with(['atlet'=>$atlet]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = [ "name"=> $request->nama,
        "umur"=> $request->umur,
        "jenis_kelamin"=> $request->jenisKelamin,
        "track_record"=> $request->record,
        "user_id"=> auth()->user()->id,
        ];

        $validation = Validator::make($data, [
            "name"=> "required",
            "umur"=> "required",
            "jenis_kelamin"=> "required",
            "track_record" => "numeric|regex:/^\d+\.\d{2}$/"
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $atlet = Atlet::find($request->atlet_id);
        $atlet->update($data);

        return redirect('/dashboard/atlet-saya')->with('success','Atlet berhasil dibuat');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Atlet::find($id)->delete();
        return redirect()->back()->with('success','Atlet berhasil dihapus');
    }
}