<?php

namespace App\Http\Controllers;

use App\Models\Winner;
use App\Models\Kompetisi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KejuaraanImport;

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

    /**
     * Display the specified resource.
     */
    public function show(Winner $winner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Winner $winner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWinnerRequest $request, Winner $winner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Winner $winner)
    {
        //
    }
}
