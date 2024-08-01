<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Atlet;
use App\Models\Acara;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'acara_atlet';

    protected $fillable = [
        'acara_id',
        'atlet_id',
        'status_pembayaran',
    ];


    public function atlet(){
        return $this->belongsToMany(Atlet::class);
    }

    public function acara(){
        return $this->belongsToMany(Acara::class);
    }
}
