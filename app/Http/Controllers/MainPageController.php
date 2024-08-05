<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use Illuminate\Http\Request;
use App\Models\Kompetisi;
use App\Models\Atlet;

class MainPageController extends Controller
{
    public function mainpage(){
        return view('mainpage');
    }


    public function userDashboard(){

        $kompetisi_count = Kompetisi::where('tutup_pendaftaran', '>=', now())->count();
        $atlet_count = Atlet::all()->where('user_id', auth()->user()->id)->count();
        $atlets = Atlet::whereHas('acara')->with('acara')->where('user_id', auth()->user()->id)->get()->sortByDesc('created_at');

        $acara_ids = $atlets->flatMap(function($atlet) {
            return $atlet->acara->pluck('id');
        })->unique();
        
        $acara_count = $acara_ids->count();

        return view('pages.dashboard')->with(['kompetisi_count'=> $kompetisi_count, 'atlets'=> $atlets, 'acara_count'=> $acara_count, 'atlet_count'=> $atlet_count]);
    }
}
