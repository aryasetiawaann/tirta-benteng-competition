<?php

namespace App\Http\Controllers;

use App\Models\Atlet;
use App\Http\Requests\StoreAtletRequest;
use App\Http\Requests\UpdateAtletRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class AtletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $atlets = Atlet::where('user_id', request()->user()->id)
        ->orderBy('name', 'asc')
        ->get();

        $atlet_count = $atlets->count();

        return view('pages.dashboard-atlet')->with(['atlets'=>$atlets, 'atlets_count'=>$atlet_count]);
    }

    public function deleteDocument($id)
    {
        $atlet = Atlet::find($id);

        if ($atlet->dokumen && File::exists(public_path($atlet->dokumen))) {
            File::delete(public_path($atlet->dokumen));
            $atlet->dokumen = null;
            $atlet->save();
        }

        return redirect()->back()->with('success','Dokumen berhasil dihapus');
    }

    public function downloadDocument($id)
    {
        $atlet = Atlet::find($id);

        $path = public_path($atlet->dokumen);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        $response->header("Content-Disposition", 'attachment; filename=Dokumen'.$atlet->name.".pdf");

        return $response;

    }

    public function viewDocument($id)
    {
        $atlet = Atlet::find($id);

        $path = public_path($atlet->dokumen);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        $response->header("Content-Disposition", 'inline; filename=Dokumen_' . $atlet->name . ".pdf");

        return $response;
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
        $dokumen = NULL;
        if ($request->hasFile('dokumen')) {
            $fileName = time() . '.' . $request->dokumen->extension();
            $request->dokumen->move(public_path('assets/dokumen'), $fileName);
            $dokumen = 'assets/dokumen/' . $fileName;
        }

        // Menggabungkan track record
        // $track_record = ($request->record_minute * 60) + $request->record_second + ($request->record_millisecond / 100);

        $data = [
            "name" => $request->nama,
            "umur" => $request->umur,
            "jenis_kelamin" => $request->jenisKelamin,
            "user_id" => auth()->user()->id,
            'dokumen' => $dokumen
        ];

        $validation = Validator::make($data, [
            "name" => "required",
            "umur" => "required|date|before:today|unique:atlets,umur,NULL,id,name," . $data['name'],
            "jenis_kelamin" => "required",
        ], [
            'name.required' => 'Nama atlet wajib diisi.',
            'umur.required' => 'Tanggal lahir atlet wajib diisi.',
            'umur.date' => 'Tanggal lahir atlet harus berupa tanggal yang valid.',
            'umur.before' => 'Tanggal lahir tidak boleh sama atau lebih dari hari ini.',
            'umur.unique' => 'Atlet sudah terdaftar dengan nama yang sama.',
            'jenis_kelamin.required' => 'Jenis kelamin atlet wajib diisi.', 
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        Atlet::create($data);

        return redirect()->back()->with('success', 'Atlet berhasil dibuat');
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
        $atlet = Atlet::find($request->atlet_id);

        $dokumen = $atlet->dokumen;
        
        if ($request->hasFile('dokumen')) {
            if ($atlet->dokumen && File::exists(public_path($atlet->dokumen))) {
                File::delete(public_path($atlet->dokumen));
            }

            $fileName = time() . '.' . $request->dokumen->extension();
            $request->dokumen->move(public_path('assets/dokumen'), $fileName);
            $dokumen = 'assets/dokumen/' . $fileName;
        }

        // Menggabungkan track record
        // $track_record = ($request->record_minute * 60) + $request->record_second + ($request->record_millisecond / 100);

        $data = [
            "name" => $request->nama,
            "umur" => $request->umur ? $request->umur : $atlet->umur,
            "jenis_kelamin" => $request->jenisKelamin ? $request->jenisKelamin : $atlet->jenis_kelamin,
            "user_id" => $atlet->user_id,
            "dokumen" => $dokumen
        ];

        $validation = Validator::make($data, [
            "name" => "required",
            "umur" => "nullable|date|before:today",
            "jenis_kelamin" => "nullable",
        ], [
            'name.required' => 'Nama atlet wajib diisi.',
            'umur.date' => 'Tanggal lahir atlet harus berupa tanggal yang valid.',
            'umur.before' => 'Tanggal lahir tidak boleh sama atau lebih dari hari ini.',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $atlet->update($data);

        return redirect('/dashboard/atlet-saya')->with('success', 'Atlet berhasil diperbaharui');
    }

    public function acceptAtletDoc($id){
        
        $atlet = Atlet::find($id);

        $atlet->is_verified = 'verified';

        $atlet->save();

        return redirect('/admin/dashboard')->with('success', 'Atlet berhasil diperbaharui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $atlet = Atlet::find($id);

        if ($atlet->dokumeun && File::exists(public_path($atlet->dokumeun))) {
            File::delete(public_path($atlet->dokumeun));
        }

        $atlet->delete();

        return redirect()->back()->with('success','Atlet berhasil dihapus');
    }
}
