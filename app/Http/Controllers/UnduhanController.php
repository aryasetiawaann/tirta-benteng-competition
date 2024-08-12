<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kompetisi;
use App\Models\Atlet;
use App\Models\Acara;
use Barryvdh\DomPDF\Facade\Pdf;


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

    public function showBukuAcara($id){

        $acaras = Acara::where('kompetisi_id', $id)->get();

        $kompetisi = Kompetisi::find("id");

        // if($kompetisi->kategori == "Fun"){

        // }else{

        // }

        foreach($acaras as $acara) {
            $participants = $acara->pesertaSelesai; // Pastikan Anda sudah relasi 'participants' di model Acara


            foreach ($participants as $participant) {
                $participant->club =  $participant->user->club ? $participant->user->club : '-';
            }

            // Membagi peserta ke dalam heat
            $heats = $this->divideIntoHeats($participants->toArray());

            // Menambahkan data heat ke dalam acara
            $acara->heats = $heats;
        }

        $pdf = Pdf::loadView('layouts.print-layout-bukuacara' , compact('acaras'))->setPaper('a4', 'potrait');
        return $pdf->stream('BUKU_ACARA.pdf', ["Attachment" => false]);
    }

    private function divideIntoHeats($participants, $maxLanes = 8)
    {
        // Membagi peserta ke dalam heat, maksimal 8 peserta per heat
        $heats = array_chunk($participants, $maxLanes);

        // Loop untuk membagi setiap heat menjadi 2 grup (4 peserta per grup)
        foreach ($heats as &$heat) {
            $heat = array_chunk($heat, 4);
        }

        return $heats;
    }
}
