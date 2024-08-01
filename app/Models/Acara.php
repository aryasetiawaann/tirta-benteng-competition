<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kompetisi;
use App\Models\Atlet;


class Acara extends Model
{
    use HasFactory;

    protected $table = 'acara';

    protected $fillable = [
        'kompetisi_id',
        'nomor_lomba',
        'nama',
        'kategori',
        'harga',
        'kuota',
        'grup',
        'min_umur',
        'max_umur',
    ];

    public function kompetisi(){

        return $this->belongsTo(Kompetisi::class, 'kompetisi_id');
    }

    public function peserta(){
        return $this->belongsToMany(Atlet::class);
    }
}
