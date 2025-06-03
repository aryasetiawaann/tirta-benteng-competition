<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class RiwayatController extends Controller
{
    // Data dummy untuk kejuaraan - nanti bisa diganti dengan database
    private function getKejuaraanData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Area Swimming Championship',
                'year' => '2025',
                'location' => 'Gelanggang Remaja Jakarta Utara',
                'date' => '19 April 2025',
                'type' => 'Fun Competition',
                'image' => 'assets/img/flyer1.jpg'
            ],
            [
                'id' => 2,
                'title' => 'Area Swimming Competition',
                'year' => '2025',
                'location' => 'Taman Alfa Indah',
                'date' => '23 Februari 2025',
                'type' => 'Fun Competition',
                'image' => 'assets/img/flyer1.jpg'
            ],
            [
                'id' => 3,
                'title' => 'AREA SPEED CHALLENGE 2025',
                'year' => '2025',
                'location' => 'Palem Tirta Ganda',
                'date' => '25 Januari 2025',
                'type' => 'Fun Competition',
                'image' => 'assets/img/flyer1.jpg'
            ],
            [
                'id' => 4,
                'title' => 'AREA Fun Swimming Championship',
                'year' => '2024',
                'location' => 'Palem Tirta Ganda',
                'date' => '28 Juli 2024',
                'type' => 'Fun Competition',
                'image' => 'assets/img/flyer1.jpg'
            ],
            [
                'id' => 5,
                'title' => 'Tirta Benteng Fun Swimming Competition',
                'year' => '2024',
                'location' => 'Yonif 203, Arya Kamuning',
                'date' => '01 Juni 2024',
                'type' => 'Fun Competition',
                'image' => 'assets/img/flyer1.jpg'
            ]
        ];
    }

    private function getNomorAcaraData($eventId)
    {
        // Data dummy nomor acara - nanti bisa diganti dengan database
        return [
            'Acara 121 | 25 M GAYA DADA - KU A PUTRA',
            'Acara 122 | 25 M GAYA DADA - KU A PUTRI',
            'Acara 123 | 50 M GAYA BEBAS - KU B PUTRA',
            'Acara 124 | 50 M GAYA BEBAS - KU B PUTRI',
            'Acara 125 | 100 M GAYA PUNGGUNG - KU C PUTRA',
            'Acara 126 | 100 M GAYA PUNGGUNG - KU C PUTRI',
            'Acara 127 | 200 M GAYA KUPU-KUPU - KU D PUTRA',
            'Acara 128 | 200 M GAYA KUPU-KUPU - KU D PUTRI',
            'Acara 129 | 400 M GAYA BEBAS - KU E PUTRA',
            'Acara 130 | 400 M GAYA BEBAS - KU E PUTRI'
        ];
    }
    
    private function getPemenangData($eventId, $nomorAcara)
    {
        // Data dummy pemenang - nanti bisa diganti dengan database
        return [
            [
                'id' => 1,
                'nama' => 'Ahmad Fauzan',
                'klub' => 'Aquatic Club Jakarta',
                'peringkat' => 1
            ],
            [
                'id' => 2,
                'nama' => 'Budi Santoso',
                'klub' => 'Tirta Swimming Club',
                'peringkat' => 2
            ],
            [
                'id' => 3,
                'nama' => 'Charlie Wijaya',
                'klub' => 'Dolphin Swim Team',
                'peringkat' => 3
            ]
        ];
    }

    public function index()
    {
        try {
            $kejuaraan = $this->getKejuaraanData();
            return view('riwayat.riwayat', compact('kejuaraan'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@index: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function show($id)
    {
        try {
            $kejuaraan = collect($this->getKejuaraanData())->firstWhere('id', (int)$id);

            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.show', compact('kejuaraan'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@show: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function sertifikat($id)
    {
        try {
            $kejuaraan = collect($this->getKejuaraanData())->firstWhere('id', (int)$id);
            $nomorAcara = $this->getNomorAcaraData($id);

            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.sertifikat', compact('kejuaraan', 'nomorAcara'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@sertifikat: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function suratKeterangan($id)
    {
        try {
            $kejuaraan = collect($this->getKejuaraanData())->firstWhere('id', (int)$id);
            $nomorAcara = $this->getNomorAcaraData($id);

            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.surat-keterangan', compact('kejuaraan', 'nomorAcara'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@suratKeterangan: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function hasilPerlombaan($id)
    {
        try {
            $kejuaraan = collect($this->getKejuaraanData())->firstWhere('id', (int)$id);
            $nomorAcara = $this->getNomorAcaraData($id);

            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.hasil-perlombaan', compact('kejuaraan', 'nomorAcara'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@hasilPerlombaan: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function peraihSertifikat($eventId, $nomorAcara)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = urldecode($nomorAcara);
            
            $kejuaraan = collect($this->getKejuaraanData())->firstWhere('id', (int)$eventId);
            $pemenang = $this->getPemenangData($eventId, $nomorAcara);
            
            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.peraih-sertifikat', compact('kejuaraan', 'nomorAcara', 'pemenang'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@peraihSertifikat: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function peraihSK($eventId, $nomorAcara)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = urldecode($nomorAcara);
            
            $kejuaraan = collect($this->getKejuaraanData())->firstWhere('id', (int)$eventId);
            $pemenang = $this->getPemenangData($eventId, $nomorAcara);
            
            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.peraih-sk', compact('kejuaraan', 'nomorAcara', 'pemenang'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@peraihSK: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function viewCertificate($eventId, $nomorAcara)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = urldecode($nomorAcara);
            
            // Path ke file PDF sertifikat
            $filename = "sertifikat_event_{$eventId}_" . str_replace(' ', '_', $nomorAcara) . ".pdf";
            $path = storage_path("app/public/certificates/{$filename}");

            if (!file_exists($path)) {
                abort(404, 'File sertifikat tidak ditemukan');
            }

            return response()->file($path);
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@viewCertificate: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function viewSuratKeterangan($eventId, $nomorAcara)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = urldecode($nomorAcara);
            
            // Path ke file PDF surat keterangan
            $filename = "surat_keterangan_event_{$eventId}_" . str_replace(' ', '_', $nomorAcara) . ".pdf";
            $path = storage_path("app/public/surat-keterangan/{$filename}");

            if (!file_exists($path)) {
                abort(404, 'File surat keterangan tidak ditemukan');
            }

            return response()->file($path);
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@viewSuratKeterangan: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function viewHasilPerlombaan($eventId, $nomorAcara)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = urldecode($nomorAcara);
            
            // Path ke file PDF hasil perlombaan
            $filename = "hasil_perlombaan_event_{$eventId}_" . str_replace(' ', '_', $nomorAcara) . ".pdf";
            $path = storage_path("app/public/hasil-perlombaan/{$filename}");

            if (!file_exists($path)) {
                abort(404, 'File hasil perlombaan tidak ditemukan');
            }

            return response()->file($path);
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@viewHasilPerlombaan: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }
}
