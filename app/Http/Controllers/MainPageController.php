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

        $kompetisis = Kompetisi::where('waktu_kompetisi', ">=", now())->where('buka_pendaftaran', '<=', now())->orderBy('waktu_kompetisi', 'asc')->get();

        return view('mainpage', compact('kompetisis'));
    }


    public function userDashboard()
    {
        $currentDate = Carbon::now();
        $user = auth()->user()->id;

        // Fetch kompetisi data
        $kompetisis = Kompetisi::where('waktu_kompetisi', '>', $currentDate)
            ->orderBy('waktu_kompetisi', 'desc')
            ->take(3)
            ->get();

        // Count kompetisi
        $kompetisi_count = $kompetisis->count();

        $kompetisi_ids = $kompetisis->pluck('id');


        // Count atlets for the authenticated user
        $atlet_count = Atlet::where('user_id', $user)->count();

        // Fetch atlets with their acara for the authenticated user
        $atlets = Atlet::where('user_id', $user)
                ->whereHas('acara.kompetisi', function ($query) use ($kompetisi_ids) {
                    $query->whereIn('id', $kompetisi_ids);
                })
                ->with('acara.kompetisi')
                ->get();

        // $atlets = Atlet::whereHas('acara', function ($query) use ($kompetisi_current) {
        //         $query->whereIn('kompetisi_id', $kompetisi_current); // Use `whereIn` for multiple IDs
        //     })
        //     ->with(['acara' => function ($query) use ($kompetisi_current) {
        //         $query->whereIn('kompetisi_id', $kompetisi_current); // Filter acara by multiple kompetisi_id
        //     }])
        //     ->where('user_id', auth()->user()->id)
        //     ->get()
        //     ->sortByDesc(function ($atlet) {
        //         return $atlet->acara->max('kompetisi_id');
        //     });

        // Count acaras
        $acara_count = $atlets->flatMap(function ($atlet) {
                    return $atlet->acara; // asumsikan relasi acara sudah di-load dengan with('acara.kompetisi')
                })
                ->unique('id') // filter acara yang duplikat berdasarkan id
                ->count();


        // Calculate totalTagihan and tagihanSelesai for multiple kompetisi_id
        $totalTagihan = 0;
        $tagihanSelesai = 0;

        foreach ($atlets as $atlet) {
            foreach ($atlet->acara as $acara) {
                if (in_array($acara->kompetisi_id, $kompetisi_ids->toArray())) { // Check if acara belongs to kompetisi_current
                    if ($acara->pivot->status_pembayaran == "Selesai") {
                        $tagihanSelesai += 1;
                    }
                    $totalTagihan += 1;
                }
            }
        }

        return view('pages.dashboard')->with([
            'kompetisi_count' => $kompetisi_count,
            'atlets' => $atlets,
            'acara_count' => $acara_count,
            'atlet_count' => $atlet_count,
            'totalTagihan' => $totalTagihan,
            'tagihanSelesai' => $tagihanSelesai,
            'kompetisis' => $kompetisis,
        ]);
    }
}
