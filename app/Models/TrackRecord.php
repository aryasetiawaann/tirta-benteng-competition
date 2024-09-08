<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Atlet;


class TrackRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'atlet_id',
        'nomor_lomba',
        'kompetisi',
        'time'
    ];

    public function atlet(){
        
        return $this->belongsTo(Atlet::class);
    }

}
