<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kompetisi;
use App\Models\Atlet;

class UnduhanController extends Controller
{
    public function userBukuAcara(){

        $acara_ids = Atlet::where('user_id', auth()->user()->id) // or auth()->user()->id
        ->with('acara') // eager load acara
        ->get()
        ->flatMap(function ($atlet) {
            return $atlet->acara->pluck('id');
        })
        ->unique();

        $competitions = Kompetisi::whereHas('acara', function ($query) use ($acara_ids) {
            $query->whereIn('id', $acara_ids);
        })->get();

        return view('pages.dashboard-bukuacara', compact('competitions'));
    }
}
