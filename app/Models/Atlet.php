<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Acara;
use App\Models\User;
use App\Models\TrackRecord;

class Atlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'umur',
        'jenis_kelamin',
        'user_id',
        'dokumen',
        'is_verified',
    ];


    public function acara(){
        return $this->belongsToMany(Acara::class)->withPivot(['status_pembayaran', 'snap_token', 'updated_at', 'id', 'waktu_pembayaran']);
    }

    public function acaraBayarMenunggu(){
        return $this->belongsToMany(Acara::class)
        ->withPivot(['status_pembayaran', 'snap_token', 'updated_at', 'id', 'waktu_pembayaran'])
        ->wherePivot('status_pembayaran', 'Menunggu');
    }

    public function acaraBayarSelesai(){
        return $this->belongsToMany(Acara::class)
        ->withPivot(['status_pembayaran', 'snap_token', 'updated_at', 'id', 'waktu_pembayaran'])
        ->wherePivot('status_pembayaran', 'Selesai');
    }    

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trackRecords()
    {
        return $this->hasMany(TrackRecord::class);
    }
}
