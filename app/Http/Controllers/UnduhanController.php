<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kompetisi;
use App\Models\Atlet;
use App\Models\Acara;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KompetisiExport;
use App\Exports\KompetisiResmi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;


class UnduhanController extends Controller
{
    public function userBukuAcara(){

        $acara_ids = Atlet::where('user_id', auth()->user()->id) 
        ->with('acara') 
        ->get()
        ->flatMap(function ($atlet) {
            return $atlet->acara->pluck('id');
        })
        ->unique();

        $competitions = Kompetisi::whereHas('acara', function ($query) use ($acara_ids) {
            $query->whereIn('id', $acara_ids);
        })->orderBy('created_at', 'desc')->get();

        return view('pages.dashboard-bukuacara', compact('competitions'));
    }

    public function showBukuAcara($id){


        $currentTime = Carbon::now(); // Mendapatkan waktu saat ini
        $time = $currentTime->format('g:i A d/m/Y');   

        $acaras = Acara::where('kompetisi_id', $id)->orderBy('nomor_lomba', 'asc')->get();

        $kompetisi = Kompetisi::find($id);

        if($kompetisi->kategori == "Fun"){

            foreach($acaras as $acara) {
                $participants = $acara->pesertaSelesai;
    
    
                foreach ($participants as $participant) {
                    $participant->club =  $participant->user->club ? $participant->user->club : '-';

                    $trackRecord = $participant->trackRecords->firstWhere('nomor_lomba', $acara->jenis_lomba);

                    if ($trackRecord) {
                        $participant->track_record = $trackRecord->time;
                    } else {
                        $participant->track_record = 999;
                    }
                }
    
                // Membagi peserta ke dalam heat
                $heats = $this->divideIntoHeats($participants->toArray());
    
                // Menambahkan data heat ke dalam acara
                $acara->heats = $heats;
            }
    
            $pdf = Pdf::loadView('layouts.print-layout-bukuacara' , compact('acaras', 'kompetisi', 'time'))->setPaper('a4', 'potrait');

        }else{

            foreach ($acaras as $acara) {
                $participants = $acara->pesertaSelesai;
            
                // Setel club untuk setiap peserta
                foreach ($participants as $participant) {
                    $participant->club = $participant->user->club ? $participant->user->club : '-';

                    $trackRecord = $participant->trackRecords->firstWhere('nomor_lomba', $acara->jenis_lomba);

                    if ($trackRecord) {
                        $participant->track_record = $trackRecord->time;
                    } else {
                        $participant->track_record = 999;
                    }
                }
            
                // Membagi peserta yang telah diurutkan ke dalam heat
                $participantsArray = $participants->toArray();

                // Membagi peserta yang telah diurutkan ke dalam heat
                $heats = $this->divideIntoHeatsWithoutGroups($participantsArray);

                // Mengurutkan peserta di setiap heat dengan logika sortMiddle
                foreach ($heats as &$heat) {
                    $heat = $this->sortMiddle($heat);
                }
            
                // Menambahkan data heat ke dalam acara
                $acara->heats = $heats;
            }

            $pdf = Pdf::loadView('layouts.print-layout-bukuacara-resmi', compact('acaras', 'kompetisi', 'time'))->setPaper('a4', 'potrait');

        }

        return $pdf->stream('Buku Acara ' . $kompetisi->nama . '.pdf');
    }

    private function sortMiddle($participants)
    {
        // Ensure all participants have a track record, set default if not set
        $participants = array_filter($participants, function($participant) {
            return $participant !== null; // Filter out null participants
        });
    
        // Ensure all remaining participants have a valid track record
        $participants = array_map(function($participant) {
            if (!isset($participant['track_record']) || $participant['track_record'] == 0) {
                $participant['track_record'] = 999; // Default value
            }
            return $participant;
        }, $participants);

        // Sort participants by 'track_record' in ascending order
        usort($participants, function($a, $b) {
            $trackRecordA = $a['track_record'];
            $trackRecordB = $b['track_record'];
            return $trackRecordA <=> $trackRecordB;
        });
    
        $result = array_fill(0, 8, null); // Initialize array with 8 null elements
        
        $middleIndex = intdiv(8, 2); // Middle position in an 8-element array
        $leftIndex = $middleIndex - 1;
        $rightIndex = $middleIndex;
        
        foreach ($participants as $i => $participant) {
            if ($i % 2 == 0) {
                // Place participant in the middle and downwards
                $result[$leftIndex--] = $participant;
            } else {
                // Place participant in the middle and upwards
                $result[$rightIndex++] = $participant;
            }
        }

        return $result; // Return the sorted array
    }

    


    private function divideIntoHeatsWithoutGroups($participants, $maxLanes = 8)
    {
        // Membagi peserta ke dalam heat
        $heats = array_chunk($participants, $maxLanes);

        // Menambahkan baris kosong di setiap heat sehingga memiliki 8 baris
        foreach ($heats as &$heat) {
            $count = count($heat);
            if ($count < $maxLanes) {
                // Menambahkan elemen kosong jika peserta kurang dari 8
                $heat = array_merge($heat, array_fill($count, $maxLanes - $count, null));
            }
        }

        return $heats;
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
                // Isi dengan null jika kurang dari $maxLanes peserta
                $heat = array_merge($heat, array_fill(0, $maxLanes - count($heat), null));
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

        // Step 5: Isi setiap heat dengan null jika kurang dari $maxLanes peserta
        foreach ($heats as &$heat) {
            $heat = array_merge($heat, array_fill(0, $maxLanes - count($heat), null));
            $heat = array_chunk($heat, 4); // Membagi setiap heat menjadi 2 grup (4 peserta per grup)
        }

        return $heats;
    }


    public function showBukuHasil(){

        $acara_ids = Atlet::where('user_id', auth()->user()->id) 
        ->with('acara') 
        ->get()
        ->flatMap(function ($atlet) {
            return $atlet->acara->pluck('id');
        })
        ->unique();

        $competitions = Kompetisi::whereHas('acara', function ($query) use ($acara_ids) {
            $query->whereIn('id', $acara_ids);
        })->orderBy('created_at', 'desc')->get();

        return view('pages.dashboard-bukuhasil', compact('competitions'));
    }


    public function downloadBukuHasil($id)
    {
        $kompetisi = Kompetisi::find($id);

        $path = public_path($kompetisi->file_hasil);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        $response->header("Content-Disposition", 'attachment; filename='.$kompetisi->nama.".pdf");

        return $response;
    }

    public function downloadExcel($id)
    {
        $acaras = Acara::where('kompetisi_id', $id)->orderBy('nomor_lomba', 'asc')->get();

        $kompetisi = Kompetisi::find($id);

        if($kompetisi->kategori == "Fun"){

            foreach($acaras as $acara) {
                $participants = $acara->pesertaSelesai;
    
    
                foreach ($participants as $participant) {
                    $participant->club =  $participant->user->club ? $participant->user->club : '-';

                    $trackRecord = $participant->trackRecords->firstWhere('nomor_lomba', $acara->jenis_lomba);

                    if ($trackRecord) {
                        $participant->track_record = $trackRecord->time;
                    } else {
                        $participant->track_record = 999;
                    }
                }
    
                // Membagi peserta ke dalam heat
                $heats = $this->divideIntoHeats($participants->toArray());
    
                // Menambahkan data heat ke dalam acara
                $acara->heats = $heats;
            }
    
            return Excel::download(new KompetisiExport($acaras), $kompetisi->nama . '.xlsx');

        }else{

            foreach ($acaras as $acara) {
                $participants = $acara->pesertaSelesai;
            
                // Setel club untuk setiap peserta
                foreach ($participants as $participant) {
                    $participant->club = $participant->user->club ? $participant->user->club : '-';

                    $trackRecord = $participant->trackRecords->firstWhere('nomor_lomba', $acara->jenis_lomba);

                    if ($trackRecord) {
                        $participant->track_record = $trackRecord->time;
                    } else {
                        $participant->track_record = 999;
                    }
                }
            
                // Membagi peserta yang telah diurutkan ke dalam heat
                $participantsArray = $participants->toArray();

                // Membagi peserta yang telah diurutkan ke dalam heat
                $heats = $this->divideIntoHeatsWithoutGroups($participantsArray);

                // Mengurutkan peserta di setiap heat dengan logika sortMiddle
                foreach ($heats as &$heat) {
                    $heat = $this->sortMiddle($heat);
                }
            
                // Menambahkan data heat ke dalam acara
                $acara->heats = $heats;
            }

            return Excel::download(new KompetisiResmi($acaras), $kompetisi->nama . '.xlsx');
        }

    }

}
