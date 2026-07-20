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

    // Resmi
    public $officialMaxLanes = 5;
    // Fun
    public $funMaxLanes = 3;
    public $funGroupCount = 3;
    public $funGroups = ['A', 'B', 'C'];

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
        })->orderBy('waktu_kompetisi', 'desc')->get();

        $allCompetitions = Kompetisi::all()->sortByDesc('waktu_kompetisi');

        return view('pages.dashboard-bukuacara', compact('competitions', 'allCompetitions'));
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
                $heats = $this->divideIntoHeats($participants->toArray(), $this->funGroupCount, $this->funMaxLanes);
    
                // Menambahkan data heat ke dalam acara
                $acara->heats = $heats;
            }

            $groups = ['A', 'B', 'C'];
    
            $pdf = Pdf::loadView('layouts.print-layout-bukuacara' , compact('acaras', 'kompetisi', 'time', 'groups'))->setPaper('a4', 'potrait');

        }else{
            foreach ($acaras as $acara) {
                $acara = $this->generateResmi($acara, $this->officialMaxLanes);
            }

            $pdf = Pdf::loadView('layouts.print-layout-bukuacara-resmi', compact('acaras', 'kompetisi', 'time'))->setPaper('a4', 'potrait');

        }

        return $pdf->stream('Buku Acara ' . $kompetisi->nama . '.pdf');
    }


    private function divideIntoHeats($participants, $totalGroups, $participantsPerGroup) // jumlah grup per seri nya
    {
        $totalParticipantsCount = count($participants);
        if ($totalParticipantsCount === 0) {
            return [];
        }

        // Hitung jumlah peserta maksimal per heat (total grup * peserta per grup)
        $maxLanes = $totalGroups * $participantsPerGroup;

        // Hitung jumlah heats yang dibutuhkan
        $numHeats = (int) ceil($totalParticipantsCount / $maxLanes);

        // Hitung sisa peserta untuk Seri 1 (Heat pertama)
        $remainder = $totalParticipantsCount % $maxLanes;
        if ($remainder === 0) {
            $remainder = $maxLanes;
        }

        // Tentukan kapasitas tiap Heat
        // Seri 1 (Heat index 0) akan memiliki kapasitas $remainder
        // Seri lainnya (Heat index 1..$numHeats-1) akan memiliki kapasitas $maxLanes
        $heatCapacities = [];
        $heatCapacities[0] = $remainder;
        for ($i = 1; $i < $numHeats; $i++) {
            $heatCapacities[$i] = $maxLanes;
        }

        // Tentukan target kapasitas dari setiap Group di dalam seluruh Heats
        $groupTargets = [];
        for ($h = 0; $h < $numHeats; $h++) {
            $heatCap = $heatCapacities[$h];
            $base = (int) floor($heatCap / $totalGroups);
            $rem = $heatCap % $totalGroups;

            for ($g = 0; $g < $totalGroups; $g++) {
                // Sebarkan sisa pembagian ke grup-grup pertama di heat tersebut
                $groupTargets[$h][$g] = $g < $rem ? $base + 1 : $base;
            }
        }

        // Step 1: Pisahkan peserta berdasarkan club
        $participantsByClub = [];
        foreach ($participants as $participant) {
            $club = $participant['club'] ?? '-';
            $participantsByClub[$club][] = $participant;
        }

        // Step 2: Acak peserta di dalam masing-masing club
        foreach ($participantsByClub as $club => &$clubParticipants) {
            shuffle($clubParticipants);
        }
        unset($clubParticipants);

        // Step 3: Urutkan klub berdasarkan jumlah peserta terbanyak (descending)
        uasort($participantsByClub, function ($a, $b) {
            return count($b) <=> count($a);
        });

        // Step 4: Inisialisasi struktur heats dan groups yang kosong
        $heats = [];
        for ($h = 0; $h < $numHeats; $h++) {
            $heats[$h] = [];
            for ($g = 0; $g < $totalGroups; $g++) {
                $heats[$h][$g] = [];
            }
        }

        // Step 5: Sebarkan peserta klub-demi-klub, peserta-demi-peserta
        foreach ($participantsByClub as $club => $clubParticipants) {
            foreach ($clubParticipants as $participant) {
                $candidateGroups = [];

                for ($h = 0; $h < $numHeats; $h++) {
                    for ($g = 0; $g < $totalGroups; $g++) {
                        $currentGroupSize = count($heats[$h][$g]);
                        $targetSize = $groupTargets[$h][$g];

                        // Grup hanya boleh dipilih jika belum mencapai target kapasitasnya
                        if ($currentGroupSize < $targetSize) {
                            // Hitung berapa peserta dari club yang sama yang sudah ada di grup ini
                            $sameClubCount = 0;
                            foreach ($heats[$h][$g] as $p) {
                                if (isset($p['club']) && $p['club'] === $club) {
                                    $sameClubCount++;
                                }
                            }

                            $candidateGroups[] = [
                                'heatIndex' => $h,
                                'groupIndex' => $g,
                                'sameClubCount' => $sameClubCount,
                                'groupSize' => $currentGroupSize,
                            ];
                        }
                    }
                }

                // Dari kandidat yang ada, prioritaskan sameClubCount terkecil, lalu groupSize terkecil.
                usort($candidateGroups, function ($a, $b) {
                    if ($a['sameClubCount'] !== $b['sameClubCount']) {
                        return $a['sameClubCount'] <=> $b['sameClubCount'];
                    }
                    return $a['groupSize'] <=> $b['groupSize'];
                });

                if (!empty($candidateGroups)) {
                    $bestCandidate = $candidateGroups[0];
                    // Temukan semua kandidat yang setara untuk diacak (tie-breaking)
                    $tiedCandidates = [];
                    foreach ($candidateGroups as $cand) {
                        if ($cand['sameClubCount'] === $bestCandidate['sameClubCount'] && 
                            $cand['groupSize'] === $bestCandidate['groupSize']) {
                            $tiedCandidates[] = $cand;
                        } else {
                            break;
                        }
                    }

                    // Pilih satu dari kandidat yang setara secara acak
                    $chosen = $tiedCandidates[array_rand($tiedCandidates)];
                    $heats[$chosen['heatIndex']][$chosen['groupIndex']][] = $participant;
                }
            }
        }

        // Step 6: Pad setiap grup dengan null agar ukurannya tepat $participantsPerGroup
        for ($h = 0; $h < $numHeats; $h++) {
            for ($g = 0; $g < $totalGroups; $g++) {
                $currentGroup = $heats[$h][$g];
                $paddingNeeded = $participantsPerGroup - count($currentGroup);
                if ($paddingNeeded > 0) {
                    $heats[$h][$g] = array_merge($currentGroup, array_fill(0, $paddingNeeded, null));
                }
            }
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
                $heats = $this->divideIntoHeats($participants->toArray(), $this->funGroupCount, $this->funMaxLanes);
    
                // Menambahkan data heat ke dalam acara
                $acara->heats = $heats;
            }
    
            return Excel::download(new KompetisiExport($acaras, $this->funGroupCount, $this->funMaxLanes, $this->funGroups, $kompetisi), $kompetisi->nama . '.xlsx');

        }else{

            foreach ($acaras as $acara) {
                $acara = $this->generateResmi($acara, $this->officialMaxLanes);

            }


            return Excel::download(new KompetisiResmi($acaras, $this->officialMaxLanes, $kompetisi), $kompetisi->nama . '.xlsx');
        }

    }

    // ----------------- Untuk kompetisi resmi -----------------
    public function generateResmi($acara, $maxLanes) 
    {
        $participants = $acara->pesertaSelesai;
        
        // Setel club untuk setiap peserta
        foreach ($participants as $participant) {
            $participant->club = $participant->user->club ? $participant->user->club : '-';

            $trackRecord = $participant->trackRecords->firstWhere('nomor_lomba', $acara->jenis_lomba);

            if ($trackRecord && $trackRecord->time > 5.00) {
                $participant->track_record = $trackRecord->time;
            } else {
                $participant->track_record = 999;
            }
        }
            
        // Membagi peserta yang telah diurutkan ke dalam heat
        $participantsArray = $participants->toArray();

        usort($participantsArray, function($a, $b) {
            $cmp = $a['track_record'] <=> $b['track_record'];
            if ($cmp === 0) {
                $dobA = isset($a['umur']) ? $a['umur'] : '9999-12-31';
                $dobB = isset($b['umur']) ? $b['umur'] : '9999-12-31';
                return $dobA <=> $dobB;
            }
            return $cmp;
        });

        // Membagi peserta yang telah diurutkan ke dalam heat
        $heats = array_chunk($participantsArray, $maxLanes);

        $heats = array_values(array_reverse($heats));

        
        // Menambahkan baris kosong di setiap heat
        foreach ($heats as $index => &$heat) {
            $count = count($heat);
            
            // Jika hanya ada 1 peserta, cari 3 peserta terlama dari heat sebelumnya
            if ($count == 1 && $index == 0) {
                
                if (isset($heats[$index + 1]) && is_array($heats[$index + 1])) {
                    $nextHeat =& $heats[$index + 1];
    
                    // Urutkan nextHeat berdasarkan track_record (terbesar ke terkecil = paling lambat dulu)
                    usort($nextHeat, function ($a, $b) {
                        return $b['track_record'] <=> $a['track_record'];
                    });
    
                    // Ambil 3 peserta paling lambat
                    $slowestParticipants = array_slice($nextHeat, 0, 3);
    
                    // Tambahkan ke heat ini
                    $heat = array_merge($heat, $slowestParticipants);
    
                    // Replace peserta terlama di nextHeat jadi null
                    foreach ($slowestParticipants as $sp) {
                        foreach ($nextHeat as $key => $participant) {
                            if (isset($participant['id']) && $participant['id'] === $sp['id']) {
                                $nextHeat[$key] = null;
                            }
                        }
                    }
    
                    $count += 3;
                }
            }

            if ($count < $maxLanes) {
                // Menambahkan elemen kosong jika peserta kurang dari maxLanes
                $heat = array_merge($heat, array_fill($count, $maxLanes - $count, null));
            }
        }
         
        // Mengurutkan peserta di setiap heat dengan logika sortMiddle
        foreach ($heats as &$heat) {
            $heat = $this->sortMiddle($heat, $maxLanes);
        }
            
        // Menambahkan data heat ke dalam acara
        $acara->heats = $heats;

        return $acara;
    }

    // ----------- Untuk sorting peserta pada kompetisi resmi -----------
    private function sortMiddle($participants, $maxLanes)
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

        // Sort participants by 'track_record' in ascending order, with 'umur' as tie-breaker (older first)
        usort($participants, function($a, $b) {
            $trackRecordA = $a['track_record'];
            $trackRecordB = $b['track_record'];
            $cmp = $trackRecordA <=> $trackRecordB;
            if ($cmp === 0) {
                $dobA = isset($a['umur']) ? $a['umur'] : '9999-12-31';
                $dobB = isset($b['umur']) ? $b['umur'] : '9999-12-31';
                return $dobA <=> $dobB;
            }
            return $cmp;
        });
    
        $result = array_fill(0, $maxLanes, null);
        
        if ($maxLanes % 2 == 0) {
            $leftIndex = intdiv($maxLanes, 2) - 1;
        } else {
            $leftIndex = intdiv($maxLanes, 2);
        }
        $rightIndex = $leftIndex + 1;
        
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

}
