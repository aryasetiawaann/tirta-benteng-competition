<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Acara;
use App\Models\LogoKompetisi;
use App\Models\HargaKompetisi;

class Kompetisi extends Model
{
    use HasFactory;


    protected $table = 'kompetisi';

    protected $fillable = [
        'nama',
        'lokasi',
        'deskripsi',
        'buka_pendaftaran',
        'tutup_pendaftaran',
        'kategori',
        'waktu_techmeeting',
        'waktu_kompetisi',
        'file_hasil',
    ];

    public function acara(){
        return $this->hasMany(Acara::class, 'id');
    }

    public function logo(){
        return $this->hasMany(LogoKompetisi::class, 'kompetisi_id');
    }

    public function harga(){
        return $this->hasMany(HargaKompetisi::class, 'kompetisi_id');
    }
}
