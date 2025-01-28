<?php

namespace App\Http\Controllers;

use App\Models\TrackRecord;
use App\Models\Atlet;
use App\Models\Kompetisi;
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
        $competitions = Kompetisi::all();

        return view('pages.dashboard-trackrecord', compact('records', 'atlet', 'competitions'));
    }

    public function create(Request $request)
    {
        // Definisi pesan error dalam Bahasa Indonesia
        $messages = [
            'atlet_id.required' => 'ID atlet wajib diisi.',
            'atlet_id.exists' => 'Atlet yang dipilih tidak valid.',
            'kategori.required' => 'Kategori lomba wajib diisi.',
            'kompetisi.required' => 'Kolom kompetisi wajib diisi.',
            'kompetisi_lainnya.required_if' => 'Nama kompetisi wajib diisi jika memilih "Lainnya".',
            'kompetisi_lainnya.max' => 'Kolom kompetisi lainnya tidak boleh lebih dari 255 karakter.',
        ];

        // Validasi request dengan pesan custom
        $validator = Validator::make($request->all(), [
            'atlet_id' => 'required|exists:atlets,id',
            'kategori' => 'required',
            'kompetisi' => 'required',
            'kompetisi_lainnya' => 'required_if:kompetisi,lainnya|nullable|string|max:255',
        ], $messages);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator) // Kirim kembali error
                            ->withInput(); // Kirim kembali data input
        }

        // Tentukan kompetisi yang akan disimpan
        $kompetisi = $request->kompetisi;
        if ($kompetisi === 'lainnya') {
            $kompetisi = $request->kompetisi_lainnya;
        }

        // Menggabungkan track record (konversi ke detik desimal)
        $time = ($request->record_minute * 60) + $request->record_second + ($request->record_millisecond / 100);

        // Simpan data ke database
        TrackRecord::create([
            'atlet_id' => $request->atlet_id,
            'nomor_lomba' => $request->kategori,
            'kompetisi' => $kompetisi,
            'time' => $time,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Track record berhasil dibuat.');
    }


    public function edit($id)
    {
        $record = TrackRecord::find($id);
        $competitions = Kompetisi::all();
        $selectedCompetition = $record->kompetisi;

        return view('pages.edit-trackrecord', compact('record', "competitions", 'selectedCompetition'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $record = TrackRecord::find($id);

        // Validasi input awal
        $request->validate([
            'atlet_id' => 'required|exists:atlets,id',
            'kategori' => 'required',
            'kompetisi' => 'required',
            'kompetisi_lainnya' => 'nullable|string|max:255',
        ]);

        // Tentukan kompetisi yang akan disimpan
        $kompetisi = $request->kompetisi;
        if ($kompetisi === 'lainnya') {
            $kompetisi = $request->kompetisi_lainnya;
        }

        // Menggabungkan track record (konversi ke detik desimal)
        $time = ($request->record_minute * 60) + $request->record_second + ($request->record_millisecond / 100);

        $record->update([
            'atlet_id' => $request->atlet_id,
            'nomor_lomba' => $request->kategori,
            'kompetisi' => $kompetisi,
            'time' => $time,
        ]);

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
