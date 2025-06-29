<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\Kompetisi;
use App\Models\Acara;
use App\Models\Winner;


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
            $kejuaraan = Kompetisi::all()->sortByDesc('created_at');

            $tahunKompetisi = Kompetisi::selectRaw('YEAR(waktu_kompetisi) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

            return view('riwayat.riwayat', compact('kejuaraan', 'tahunKompetisi'));

        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@index: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function show($id)
    {
        try {
            $kejuaraan = Kompetisi::find($id);

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
            $kejuaraan = Kompetisi::find($id);
            
            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            $nomorAcara = Acara::where('kompetisi_id', $id)
            ->orderBy('nomor_lomba')
            ->get();
            
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
            $kejuaraan = Kompetisi::find($id);

            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.hasil-perlombaan', compact('kejuaraan'));
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@hasilPerlombaan: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function peraihSertifikat($eventId, $nomorAcara)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = Acara::find($nomorAcara);
            
            $kejuaraan = Kompetisi::find($eventId);
            
            $pemenang = Winner::where('kompetisi_id', $kejuaraan->id)
            ->where('acara_id', $nomorAcara->id)->get();

            
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
                // Alih-alih menampilkan error 404, tampilkan halaman dengan pesan file belum diupload
                return view('riwayat.file-not-available', [
                    'title' => 'Keterangan Juara Belum Tersedia',
                    'message' => 'Mohon maaf, file keterangan juara belum diupload oleh penyelenggara.',
                    'backUrl' => route('riwayat.peraih-sertifikat', ['eventId' => $eventId, 'nomorAcara' => $nomorAcara])
                ]);
            }

            return response()->file($path);
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@viewCertificate: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }
    
    public function detailSertifikat($kode)
    {
        try {
            $kode = urlencode($kode);
            
            $pemenang = Winner::where('kode', $kode)->first();

            // Cek dulu apakah data ditemukan
            if (!$pemenang) {
                return view('riwayat.file-not-available', [
                    'title' => 'Keterangan Juara Belum Tersedia',
                    'message' => 'Mohon maaf, data keterangan juara belum tersedia atau tidak ditemukan.',
                    'backUrl' => route('riwayat.index')
                ]);
            }

            $kejuaraan = Kompetisi::find($pemenang->kompetisi_id);
            $nomorAcara = Acara::find($pemenang->acara_id);

            if (!$kejuaraan) {
                abort(404, 'Kejuaraan tidak ditemukan');
            }

            return view('riwayat.detail-sertifikat', compact('kejuaraan', 'nomorAcara', 'pemenang'));

        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@detailSertifikat: ' . $e->getMessage());
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
                // Alih-alih menampilkan error 404, tampilkan halaman dengan pesan file belum diupload
                return view('riwayat.file-not-available', [
                    'title' => 'Surat Keterangan Belum Tersedia',
                    'message' => 'Mohon maaf, file surat keterangan belum diupload oleh penyelenggara.',
                    'backUrl' => route('riwayat.peraih-sertifikat', ['eventId' => $eventId, 'nomorAcara' => $nomorAcara])
                ]);
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
                // Alih-alih menampilkan error 404, tampilkan halaman dengan pesan file belum diupload
                return view('riwayat.file-not-available', [
                    'title' => 'Hasil Perlombaan Belum Tersedia',
                    'message' => 'Mohon maaf, file hasil perlombaan belum diupload oleh penyelenggara.',
                    'backUrl' => route('riwayat.hasil-perlombaan', ['id' => $eventId])
                ]);
            }

            return response()->file($path);
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@viewHasilPerlombaan: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function downloadCertificate($eventId, $nomorAcara, $pesertaId)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = urldecode($nomorAcara);
            
            // Dapatkan data pemenang untuk nama file yang lebih spesifik
            $pemenangList = $this->getPemenangData($eventId, $nomorAcara);
            $pemenang = collect($pemenangList)->firstWhere('id', (int)$pesertaId);
            
            if (!$pemenang) {
                abort(404, 'Data peserta tidak ditemukan');
            }
            
            // Path ke file PDF sertifikat
            $filename = "sertifikat_event_{$eventId}_" . str_replace(' ', '_', $nomorAcara) . ".pdf";
            $path = storage_path("app/public/certificates/{$filename}");

            if (!file_exists($path)) {
                // Alih-alih menampilkan error 404, tampilkan halaman dengan pesan file belum diupload
                return view('riwayat.file-not-available', [
                    'title' => 'Keterangan Juara Belum Tersedia',
                    'message' => 'Mohon maaf, file keterangan juara belum diupload oleh penyelenggara.',
                    'backUrl' => route('riwayat.detail-sertifikat', ['kode' => $pemenang->kode])
                ]);
            }

            // Nama file yang akan diunduh oleh pengguna
            $downloadName = "Sertifikat_{$pemenang['nama']}_{$nomorAcara}.pdf";
            
            return response()->download($path, $downloadName);
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@downloadCertificate: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function downloadSK($eventId, $nomorAcara, $pesertaId)
    {
        try {
            // Decode URL-encoded nomor acara
            $nomorAcara = urldecode($nomorAcara);
            
            // Dapatkan data pemenang untuk nama file yang lebih spesifik
            $pemenangList = $this->getPemenangData($eventId, $nomorAcara);
            $pemenang = collect($pemenangList)->firstWhere('id', (int)$pesertaId);
            
            if (!$pemenang) {
                abort(404, 'Data peserta tidak ditemukan');
            }
            
            // Path ke file PDF surat keterangan
            $filename = "surat_keterangan_event_{$eventId}_" . str_replace(' ', '_', $nomorAcara) . ".pdf";
            $path = storage_path("app/public/surat-keterangan/{$filename}");

            if (!file_exists($path)) {
                // Alih-alih menampilkan error 404, tampilkan halaman dengan pesan file belum diupload
                return view('riwayat.file-not-available', [
                    'title' => 'Surat Keterangan Belum Tersedia',
                    'message' => 'Mohon maaf, file surat keterangan belum diupload oleh penyelenggara.',
                    'backUrl' => route('riwayat.detail-sertifikat', ['kode' => $pemenang->kode])
                ]);
            }

            // Nama file yang akan diunduh oleh pengguna
            $downloadName = "Surat_Keterangan_{$pemenang['nama']}_{$nomorAcara}.pdf";
            
            return response()->download($path, $downloadName);
        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@downloadSK: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem');
        }
    }
}
