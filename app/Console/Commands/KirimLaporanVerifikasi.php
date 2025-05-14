<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifikasiAtletSummary;
use App\Models\Atlet;

class KirimLaporanVerifikasi extends Command
{
    protected $signature = 'laporan:verifikasi';
    protected $description = 'Kirim laporan jumlah atlet yang menunggu verifikasi ke admin setiap hari';

    public function handle()
    {
        $email = 'tirtabenteng05@gmail.com';

        $jumlah = Atlet::where('is_verified', 'not verified')->whereNotNull('dokumen')->count();

        if($jumlah > 0){

            Mail::to($email)->send(new VerifikasiAtletSummary($jumlah));
    
            $this->info("Email laporan verifikasi terkirim. Jumlah: $jumlah");
        }else {
            
            $this->info('Email tidak terkirim. Tidak ada atlet yang perlu diverifikasi');
        }


        return Command::SUCCESS;
    }
}
