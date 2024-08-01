<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Acara;
use App\Models\User;

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


    public function acara(){
        return $this->belongsToMany(Acara::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
