<?php

namespace App\Http\Controllers;

use App\Models\Kompetisi;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(){

      $kompetisis = Kompetisi::whereNull('file_hasil')->get();
      $kompetisi_file = Kompetisi::whereNotNull('file_hasil')->orderByDesc('created_at')->get();
      $kompetisi = Kompetisi::all()->sortByDesc('created_at');

        return view('admin.admin-dashboard', compact('kompetisis', 'kompetisi_file', 'kompetisi'));
      }
}
