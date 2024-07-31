<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'umur',
        'jenis_kelamin',
        'track_record',
        'user_id',
    ];

}
