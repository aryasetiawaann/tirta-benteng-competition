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
        $kompetisi_current = [3,10]; // Define multiple kompetisi_id

        // Fetch kompetisi data
        $kompetisis = Kompetisi::where('waktu_kompetisi', '>', now())
            ->orderBy('waktu_kompetisi', 'desc')
            ->take(3)
            ->get();

        // Count kompetisi
        $kompetisi_count = Kompetisi::where('waktu_kompetisi', '>', now())
            ->where('buka_pendaftaran', '<=', now())
            ->count();

        // Count atlets for the authenticated user
        $atlet_count = Atlet::where('user_id', auth()->user()->id)->count();

        // Fetch atlets with their acara for the authenticated user
        $atlets = Atlet::whereHas('acara', function ($query) use ($kompetisi_current) {
                $query->whereIn('kompetisi_id', $kompetisi_current); // Use `whereIn` for multiple IDs
            })
            ->with(['acara' => function ($query) use ($kompetisi_current) {
                $query->whereIn('kompetisi_id', $kompetisi_current); // Filter acara by multiple kompetisi_id
            }])
            ->where('user_id', auth()->user()->id)
            ->get()
            ->sortByDesc(function ($atlet) {
                return $atlet->acara->max('kompetisi_id');
            });

        // Fetch acaras for the authenticated user that belong to the specified kompetisi_ids
        $acaras = Acara::whereHas('peserta', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->whereHas('kompetisi', function ($query) use ($currentDate, $kompetisi_current) {
                $query->where('waktu_kompetisi', '>', $currentDate)
                    ->whereIn('id', $kompetisi_current); // Use `whereIn` for multiple IDs
            })
            ->get();

        // Count acaras
        $acara_count = $acaras->count();

        // Calculate totalTagihan and tagihanSelesai for multiple kompetisi_id
        $totalTagihan = 0;
        $tagihanSelesai = 0;

        foreach ($atlets as $atlet) {
            foreach ($atlet->acara as $acara) {
                if (in_array($acara->kompetisi_id, $kompetisi_current)) { // Check if acara belongs to kompetisi_current
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
