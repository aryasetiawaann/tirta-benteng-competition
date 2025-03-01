<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Peserta;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'user_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'snap_token',
        'metode_pembayaran',
        'total_harga',
        'status',
        'midtrans_response'
    ];

    public function pendaftaran()
    {
        return $this->hasMany(Peserta::class, 'pembayaran_id');
    }
}
