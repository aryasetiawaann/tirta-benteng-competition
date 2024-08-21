<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Acara;

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
}
