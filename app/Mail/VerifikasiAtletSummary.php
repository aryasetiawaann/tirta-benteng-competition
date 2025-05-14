<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifikasiAtletSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $jumlah;

    public function __construct($jumlah)
    {
        $this->jumlah = $jumlah;
    }

    public function build()
    {
        return $this->subject('Laporan Verifikasi Atlet Harian')
            ->view('layouts.email-layout-atlet-verification');
    }
}
