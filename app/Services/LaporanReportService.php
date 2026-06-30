<?php // app/Services/LaporanReportService.php
namespace App\Services;

use App\Models\Kompetisi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanReportService
{
    public const CLUB_PAYMENT_HEADINGS = [
        'No', 'Club', 'Email', 'Nomor Telepon',
        'Total Peserta per Club', 'Total Nomor per Club',
        'Total Selesai per Club', 'Total Menunggu per Club', 'Nama Kompetisi',
    ];

    public const DAFTAR_HEADINGS = [
        'No', 'Nama Atlet', 'Nomor Lomba', 'Club',
        'Nomor Telepon', 'Status Pembayaran', 'Nama Kompetisi',
    ];

    public function activeCompetitions(): Collection
    {
        $now = now();
        return Kompetisi::where('buka_pendaftaran', '<=', $now)
            ->where('waktu_kompetisi', '>=', $now)
            ->orderBy('waktu_kompetisi')
            ->get();
    }

    private function baseRows(array $kompetisiIds): Collection
    {
        if (empty($kompetisiIds)) {
            return collect();
        }

        return DB::table('acara_atlet as aa')
            ->join('atlets as at', 'aa.atlet_id', '=', 'at.id')
            ->join('users as u', 'at.user_id', '=', 'u.id')
            ->join('acara as ac', 'aa.acara_id', '=', 'ac.id')
            ->join('kompetisi as k', 'ac.kompetisi_id', '=', 'k.id')
            ->whereIn('ac.kompetisi_id', $kompetisiIds)
            ->select([
                'ac.kompetisi_id as kompetisi_id',
                'k.nama as kompetisi_nama',
                'u.id as user_id',
                'u.club as club',
                'u.email as email',
                'u.phone as phone',
                'u.name as user_name',
                'at.name as atlet_name',
                'ac.nomor_lomba as nomor_lomba',
                'aa.status_pembayaran as status_pembayaran',
            ])
            ->get();
    }
}
