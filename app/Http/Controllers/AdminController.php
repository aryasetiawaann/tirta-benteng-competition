<?php

namespace App\Http\Controllers;

use App\Models\Kompetisi;
use App\Models\Atlet;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(){

      
      $kompetisis = Kompetisi::whereNull('file_hasil')->get();
      $kompetisi_file = Kompetisi::whereNotNull('file_hasil')->orderByDesc('waktu_kompetisi')->get();
      $kompetisi = Kompetisi::all()->sortByDesc('waktu_kompetisi');


      return view('admin.admin-dashboard', compact('kompetisis', 'kompetisi_file', 'kompetisi'));
    }

    public function verification(){
      $notVerAtlets = Atlet::with('user')->whereNotNull('dokumen')->where('is_verified', 'not verified')->get()->sortBy('updated_at');
      $flagAtlets = Atlet::with('user')->whereNotNull('dokumen')->where('is_verified', 'need revision')->get()->sortBy('updated_at');
      return view('admin.admin-verifikasi-atlet', compact('notVerAtlets', 'flagAtlets'));
    }
}
