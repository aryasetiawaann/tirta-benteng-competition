<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use Illuminate\Http\Request;
use App\Models\Kompetisi;
use App\Models\Atlet;
use Carbon\Carbon;

class MainPageController extends Controller
{
    public function mainpage(){
        return view('mainpage');
    }


    public function userDashboard(){

        $currentDate = Carbon::now();

        $kompetisis = Kompetisi::all()->sortByDesc('created_at');
        $kompetisi_count = Kompetisi::where('waktu_kompetisi', '>', now())->where('buka_pendaftaran', '<=', now())->count();
        $atlet_count = Atlet::all()->where('user_id', auth()->user()->id)->count();
        $atlets = Atlet::whereHas('acara')->with('acara')->where('user_id', auth()->user()->id)->get()->sortByDesc('created_at');

        $acaras = Acara::whereHas('peserta', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })
        ->whereHas('kompetisi', function ($query) use ($currentDate) {
            $query->where('waktu_kompetisi', '>', $currentDate);
        })
        ->get();
    
        $acara_count = $acaras->count();

        $totalTagihan = 0;
        $tagihanSelesai = 0;

        foreach ($atlets as $atlet) {
            foreach($atlet->acara as $acara) {
                
                if($acara->pivot->status_pembayaran == "Selesai"){
                    $tagihanSelesai += 1;
                }
                
                $totalTagihan += 1;

            }
        }

        return view('pages.dashboard')->with(['kompetisi_count'=> $kompetisi_count, 'atlets'=> $atlets, 'acara_count'=> $acara_count, 'atlet_count'=> $atlet_count,
                    'totalTagihan' => $totalTagihan, 'tagihanSelesai' => $tagihanSelesai, 'kompetisis' => $kompetisis]);
    }
}
