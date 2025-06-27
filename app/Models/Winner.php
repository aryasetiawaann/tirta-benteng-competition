<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Acara;
use App\Models\Certificate;
use App\Models\Letter;

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
        'certificate_id',
        'letter_id',
    ];


    public function acara()
    {
        return $this->belongsTo(Acara::class);
    }

    public function certificate()
    {
        return $this->belongsTo(certificate::class);
    }

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
