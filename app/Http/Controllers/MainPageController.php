<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use Illuminate\Http\Request;
use App\Models\Kompetisi;

class MainPageController extends Controller
{
    public function mainpage(){
        return view('mainpage');
    }


    public function userDashboard(){

        $kompetisi_count = Kompetisi::where('tutup_pendaftaran', '>', now())->count();

        return view('pages.dashboard')->with(['kompetisi_count'=> $kompetisi_count]);
    }
}
