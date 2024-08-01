<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function create(Request $request){

        $data = [ "acara_id"=> $request->acara,
            "atlet_id"=> $request->atlet,
        ];

        Peserta::create($data);

        return redirect()->back()->with('success','Atlet berhasil ditambahkan');
    }
}
