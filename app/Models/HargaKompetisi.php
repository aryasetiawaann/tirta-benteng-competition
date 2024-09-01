<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaKompetisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kompetisi_id',
        'judul',
        'harga',
        'deskripsi'
    ];
}
