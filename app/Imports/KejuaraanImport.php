<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Winner;
use App\Models\Acara;

class KejuaraanImport implements ToCollection, WithHeadingRow
{
    protected $kompetisiId;

    public function __construct($kompetisiId)
    {
        $this->kompetisiId = $kompetisiId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Cari acara_id berdasarkan nomor_acara
            $acara = Acara::where('nomor_lomba', $row['acara'])
                          ->where('kompetisi_id', $this->kompetisiId)
                          ->first();

            if (!$acara || $row['nama_atlet'] == null) {
                // Lewati jika acara tidak ditemukan di kompetisi ini
                continue;
            }

            $nama = ucwords(strtolower($row['nama_atlet']));
            $club = ucwords(strtolower($row['klub']));
            $kode = $row['kode'];

            $sudahAda = Winner::where('acara_id', $acara->id)
                            ->where('nama', $nama)
                            ->where('kode', $kode)
                            ->exists();

            if ($sudahAda) {
                continue; // Lewati jika duplikat
            }

            Winner::create([
                'nama'          => ucwords(strtolower($row['nama_atlet'])),
                'club'          => ucwords(strtolower($row['klub'])),
                'nik'           => $row['nomor_induk'] ?? '-',
                'rank'          => $row['rank'],
                'kelompok_umur' => $row['kelompok_umur'],
                'nomor_lomba'   => $row['nomor_lomba'],
                'kode'          => $row['kode'],
                'kompetisi_id'  => $this->kompetisiId,
                'acara_id'      => $acara->id,
            ]);
        }
    }
}