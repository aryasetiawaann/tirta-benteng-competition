<?php

namespace App\Models;

use App\Models\Kompetisi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_amount',
        'harga',
        'kompetisi_id',
    ];

    public function kompetisi(){
        return $this->belongsTo(Kompetisi::class);
    }

}
