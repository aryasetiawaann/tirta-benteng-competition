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

        $kompetisi = Kompetisi::find($id);

        // if($kompetisi->kategori == "Fun"){

        //     foreach($acaras as $acara) {
        //         $participants = $acara->pesertaSelesai;
    
    
        //         foreach ($participants as $participant) {
        //             $participant->club =  $participant->user->club ? $participant->user->club : '-';
        //         }
    
        //         // Membagi peserta ke dalam heat
        //         $heats = $this->divideIntoHeats($participants->toArray());
    
        //         // Menambahkan data heat ke dalam acara
        //         $acara->heats = $heats;
        //     }
    
        //     $pdf = Pdf::loadView('layouts.print-layout-bukuacara' , compact('acaras'))->setPaper('a4', 'potrait');

        // }else{

        //     foreach ($acaras as $acara) {
        //         $participants = $acara->pesertaSelesai;
    
        //         foreach ($participants as $participant) {
        //             $participant->club = $participant->user->club ? $participant->user->club : '-';
        //         }
    
        //         // Mengurutkan peserta dengan logika sortMiddle
        //         $sortedParticipants = $this->sortMiddle($participants->toArray());
    
        //         // Membagi peserta yang telah diurutkan ke dalam heat tanpa membagi lagi menjadi grup kecil
        //         $heats = $this->divideIntoHeatsWithoutGroups($sortedParticipants);
    
        //         // Menambahkan data heat ke dalam acara
        //         $acara->heats = $heats;
        //     }
    
        //     $pdf = Pdf::loadView('layouts.print-layout-bukuacara-resmi', compact('acaras'))->setPaper('a4', 'potrait');

        // }

        foreach($acaras as $acara) {
                    $participants = $acara->pesertaSelesai;
        
        
                    foreach ($participants as $participant) {
                        $participant->club =  $participant->user->club ? $participant->user->club : '-';
                    }
        
                    // Membagi peserta ke dalam heat
                    $heats = $this->divideIntoHeats($participants->toArray());
        
                    // Menambahkan data heat ke dalam acara
                    $acara->heats = $heats;
                }
        
                $pdf = Pdf::loadView('layouts.print-layout-bukuacara' , compact('acaras'))->setPaper('a4', 'potrait');

        return $pdf->stream('BUKU_ACARA.pdf');
    }

    private function sortMiddle($participants)
    {
        // Konversi peserta menjadi array dari nilai yang ingin diurutkan, misalnya berdasarkan skor
        $arr = array_column($participants, 'track_record'); // Anggap 'nilai' adalah atribut yang ingin diurutkan

        // Langkah 1: Urutkan array secara menurun
        rsort($arr);

        // Langkah 2 & 3: Buat array baru untuk hasil
        $result = [];
        $left = 0;
        $right = count($arr) - 1;

        foreach ($arr as $i => $value) {
            if ($i % 2 == 0) {
                array_push($result, $value); // Masukkan ke tengah dari kiri
            } else {
                array_unshift($result, $value); // Masukkan ke tengah dari kanan
            }
        }

        // Kembalikan array yang telah diurutkan dengan logika di tengah
        return $result;
    }

    private function divideIntoHeatsWithoutGroups($participants, $maxLanes = 8)
    {
        // Membagi peserta ke dalam heat tanpa membaginya lagi menjadi grup
        return array_chunk($participants, $maxLanes);
    }


    private function divideIntoHeats($participants, $maxLanes = 8)
    {
        // Step 1: Pisahkan peserta berdasarkan club
        $participantsByClub = [];
        foreach ($participants as $participant) {
            $club = $participant['club'];
            $participantsByClub[$club][] = $participant;
        }

        // Step 2: Cek apakah semua peserta berasal dari club yang sama
        if (count($participantsByClub) === 1) {
            // Semua peserta berasal dari satu club, cukup acak dan bagi mereka
            shuffle($participants); // Acak peserta
            $heats = array_chunk($participants, $maxLanes);

            foreach ($heats as &$heat) {
                $heat = array_chunk($heat, 4); // Membagi setiap heat menjadi 2 grup (4 peserta per grup)
            }

            return $heats;
        }

        // Step 3: Ambil peserta secara acak dari tiap club jika lebih dari satu club
        $shuffledParticipants = [];
        while (!empty($participantsByClub)) {
            foreach ($participantsByClub as $club => $participantsInClub) {
                if (!empty($participantsInClub)) {
                    // Ambil peserta secara acak dari tiap club
                    $shuffledParticipants[] = array_shift($participantsByClub[$club]);
                }

                // Hapus club jika semua pesertanya sudah habis
                if (empty($participantsByClub[$club])) {
                    unset($participantsByClub[$club]);
                }
            }
        }

        // Step 4: Membagi peserta yang telah diacak ke dalam heat, maksimal 8 peserta per heat
        $heats = array_chunk($shuffledParticipants, $maxLanes);

        // Step 5: Membagi setiap heat menjadi 2 grup (4 peserta per grup)
        foreach ($heats as &$heat) {
            $heat = array_chunk($heat, 4);
        }

        return $heats;
    }

}
