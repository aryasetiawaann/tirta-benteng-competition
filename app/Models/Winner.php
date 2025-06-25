<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Acara;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'club',
        'nik',
        'rank',
        'kelompok_umur',
        'nomor_lomba',
        'kode',
        'kompetisi_id',
        'acara_id',
    ];


    public function acara()
    {
        return $this->belongsTo(Acara::class);
    }
}
