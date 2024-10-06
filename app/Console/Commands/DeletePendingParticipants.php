<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kompetisi; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeletePendingParticipants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'participants:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus peserta yang status_pembayaran masih Menunggu dua hari setelah tanggal_pendaftaran kompetisi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Mendapatkan tanggal dua hari yang lalu
            $targetDate = Carbon::today()->subDays(2);

            // Mendapatkan semua acara yang tanggal_pendaftarannya dua hari yang lalu
            $kompetisis = Kompetisi::whereDate('tutup_pendaftaran', $targetDate)->get();

            $deletedCount = 0;

            foreach ($kompetisis as $kompetisi) {

                $acaras = $kompetisi->acara;

                foreach ($acaras as $acara) {
                    $pesertas = $acara->peserta()->wherePivot('status_pembayaran', 'Menunggu')->get();
    
                    foreach ($pesertas as $peserta) {
                        
                        $acara->peserta()->detach($peserta->id);

                        $deletedCount++;
                    }
                }
            }

            $this->info("Berhasil menghapus {$deletedCount} peserta dengan status_pembayaran Menunggu.");

            Log::info("participants:cleanup - Berhasil menghapus {$deletedCount} peserta dengan status_pembayaran Menunggu pada tanggal_pendaftaran {$targetDate->toDateString()}.");

            return 0; // Indikasi keberhasilan
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            Log::error("participants:cleanup - Terjadi kesalahan: " . $e->getMessage());
            return 1; // Indikasi kegagalan
        }
    }
}
