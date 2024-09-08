<?php

namespace App\Http\Controllers;

use App\Models\TrackRecord;
use App\Models\Atlet;
use App\Models\JenisLomba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $records = TrackRecord::where('atlet_id', $id)->get();
        $atlet = Atlet::find($id);

      return view('pages.dashboard-trackrecord', compact('records', 'atlet'));
    }

    public function create(Request $request)
    {
        // Menggabungkan track record
        $time = ($request->record_minute * 60) + $request->record_second + ($request->record_millisecond / 100);

        $data = [
            "atlet_id" => $request->atlet_id,
            "nomor_lomba" => $request->kategori,
            "kompetisi" => $request->kompetisi,
            'time' => $time
        ];

        $validation = Validator::make($data, [
            "kompetisi" => "required",
            "nomor_lomba" => "required",
            "time" => "required",
        ], [
            'kompetisi.required' => 'Kompetisi wajib diisi.',
            'nomor_lomba.required' => 'Nomor lomba wajib diisi.',
            'time.required' => 'Durasi renang wajib diisi.',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        TrackRecord::create($data);

        return redirect()->back()->with('success', 'Track record berhasil dibuat');   
    }


    public function edit($id)
    {
        $record = TrackRecord::find($id);


        return view('pages.edit-trackrecord', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {

        $record = TrackRecord::find($id);

        $time = ($request->record_minute * 60) + $request->record_second + ($request->record_millisecond / 100);

        $data = [
            "atlet_id" => $request->atlet_id,
            "nomor_lomba" => $request->kategori,
            "kompetisi" => $request->kompetisi,
            'time' => $time
        ];

        $validation = Validator::make($data, [
            "kompetisi" => "required",
            "nomor_lomba" => "required",
            "time" => "required",
        ], [
            'kompetisi.required' => 'Kompetisi wajib diisi.',
            'nomor_lomba.required' => 'Nomor lomba wajib diisi.',
            'time.required' => 'Durasi renang wajib diisi.',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $record->update($data);

        return redirect()->route('dashboard.track-record.index', $request->atlet_id)->with('success', 'Track record berhasil diperbaharui');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $record = TrackRecord::find($id);

        $record->delete();

        return redirect()->back()->with('success','Track Record berhasil dihapus');
    }
}
