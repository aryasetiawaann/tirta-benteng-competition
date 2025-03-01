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
        'peserta_user_id',
        'pembayaran_id',
        'status_pembayaran',
        'waktu_pembayaran',
        'snap_token'
    ];

    // Ini buat di bagian tagihan
    public function getAtlet(){
        return $this->belongsTo(Atlet::class, 'atlet_id');
    }

    public function getAcara(){
        return $this->belongsTo(Acara::class, 'acara_id');
    }

    public function getPembayaran(){
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }

    // Ini General
    public function atlet(){
        return $this->belongsToMany(Atlet::class);
    }

    public function acara(){
        return $this->belongsToMany(Acara::class);
    }
}
