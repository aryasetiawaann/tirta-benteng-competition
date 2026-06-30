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
                'at.id as atlet_id',
                'at.jenis_kelamin as jenis_kelamin',
                'at.umur as umur',
                'ac.nomor_lomba as nomor_lomba',
                'aa.status_pembayaran as status_pembayaran',
            ])
            ->get();
    }

    /** @return array<int, int|string> ordered competition ids by name asc */
    private function orderedCompetitionIds(Collection $base): array
    {
        $names = $base->groupBy('kompetisi_id')->map(fn ($g) => $g->first()->kompetisi_nama);
        return $names->sort()->keys()->all();
    }

    public function clubPaymentRows(array $kompetisiIds): array
    {
        $base = $this->baseRows($kompetisiIds);
        $byComp = $base->groupBy('kompetisi_id');
        $rows = [];

        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];
            $compNama = $compRows->first()->kompetisi_nama;

            $users = [];
            foreach ($compRows->groupBy('user_id') as $urows) {
                $first = $urows->first();
                $users[] = [
                    'club' => $first->club,
                    'email' => $first->email,
                    'phone' => $first->phone,
                    'peserta' => $urows->pluck('atlet_name')->unique()->count(),
                    'nomor' => $urows->count(),
                    'selesai' => $urows->where('status_pembayaran', 'Selesai')->count(),
                    'menunggu' => $urows->where('status_pembayaran', 'Menunggu')->count(),
                ];
            }
            usort($users, fn ($a, $b) => [$a['club'], $a['email']] <=> [$b['club'], $b['email']]);

            $no = 1;
            foreach ($users as $u) {
                $rows[] = [
                    'No' => $no++,
                    'Club' => $u['club'],
                    'Email' => $u['email'],
                    'Nomor Telepon' => "'" . $u['phone'],
                    'Total Peserta per Club' => $u['peserta'],
                    'Total Nomor per Club' => $u['nomor'],
                    'Total Selesai per Club' => $u['selesai'],
                    'Total Menunggu per Club' => $u['menunggu'],
                    'Nama Kompetisi' => $compNama,
                ];
            }

            $rows[] = [
                'No' => null,
                'Club' => 'Total Semua',
                'Email' => '',
                'Nomor Telepon' => '',
                'Total Peserta per Club' => $compRows->pluck('atlet_name')->unique()->count(),
                'Total Nomor per Club' => $compRows->count(),
                'Total Selesai per Club' => $compRows->where('status_pembayaran', 'Selesai')->count(),
                'Total Menunggu per Club' => $compRows->where('status_pembayaran', 'Menunggu')->count(),
                'Nama Kompetisi' => $compNama,
            ];
        }

        return $rows;
    }

    /** @return array<int|string, array{terkumpul:int, tertunda:int}> */
    private function revenueByComp(array $kompetisiIds): array
    {
        if (empty($kompetisiIds)) {
            return [];
        }

        $payments = DB::table('acara_atlet as aa')
            ->join('acara as ac', 'aa.acara_id', '=', 'ac.id')
            ->join('pembayaran as p', 'aa.pembayaran_id', '=', 'p.id')
            ->whereIn('ac.kompetisi_id', $kompetisiIds)
            ->select('ac.kompetisi_id as kompetisi_id', 'p.id as pembayaran_id', 'p.total_harga', 'p.status')
            ->distinct()
            ->get();

        $out = [];
        foreach ($payments->groupBy('kompetisi_id') as $compId => $rows) {
            $out[$compId] = [
                'terkumpul' => (int) $rows->where('status', 'Berhasil')->sum('total_harga'),
                'tertunda' => (int) $rows->where('status', 'Menunggu')->sum('total_harga'),
            ];
        }
        return $out;
    }

    /** @return array<int|string, int> total kuota per competition */
    private function quotaByComp(array $kompetisiIds): array
    {
        if (empty($kompetisiIds)) {
            return [];
        }

        return DB::table('acara')
            ->whereIn('kompetisi_id', $kompetisiIds)
            ->groupBy('kompetisi_id')
            ->selectRaw('kompetisi_id, SUM(kuota) as total_kuota')
            ->pluck('total_kuota', 'kompetisi_id')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    public function summaries(array $kompetisiIds): array
    {
        $base = $this->baseRows($kompetisiIds);
        $byComp = $base->groupBy('kompetisi_id');
        $revenue = $this->revenueByComp($kompetisiIds);
        $quota = $this->quotaByComp($kompetisiIds);
        $out = [];

        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];

            $athletes = $compRows->groupBy('atlet_id')->map(fn ($g) => $g->first());
            $nomor = $compRows->count();
            $selesai = $compRows->where('status_pembayaran', 'Selesai')->count();
            $peserta = $compRows->pluck('atlet_name')->unique()->count();

            $ages = $athletes->map(fn ($a) => \Carbon\Carbon::parse($a->umur)->age);

            // Top club by unique athletes; ties broken alphabetically.
            $pairs = $athletes->groupBy('club')
                ->map(fn ($g, $club) => ['club' => (string) $club, 'count' => $g->count()])
                ->values()->all();
            usort($pairs, fn ($a, $b) => [$b['count'], $a['club']] <=> [$a['count'], $b['club']]);

            $rev = $revenue[$compId] ?? ['terkumpul' => 0, 'tertunda' => 0];
            $totalKuota = $quota[$compId] ?? 0;

            $out[] = [
                'kompetisi_id' => $compId,
                'nama' => $compRows->first()->kompetisi_nama,
                'peserta' => $peserta,
                'nomor' => $nomor,
                'club' => $compRows->pluck('user_id')->unique()->count(),
                'selesai' => $selesai,
                'menunggu' => $compRows->where('status_pembayaran', 'Menunggu')->count(),
                'tingkat_pelunasan' => $nomor > 0 ? round($selesai / $nomor * 100, 1) : 0.0,
                'nomor_lomba_count' => $compRows->pluck('nomor_lomba')->unique()->count(),
                'gender_l' => $athletes->where('jenis_kelamin', 'Pria')->count(),
                'gender_p' => $athletes->where('jenis_kelamin', 'Wanita')->count(),
                'umur_rata' => $ages->isEmpty() ? 0.0 : round($ages->avg(), 1),
                'nomor_per_atlet' => $peserta > 0 ? round($nomor / $peserta, 1) : 0.0,
                'club_terbanyak' => $pairs[0]['club'] ?? null,
                'pendapatan_terkumpul' => $rev['terkumpul'],
                'pendapatan_tertunda' => $rev['tertunda'],
                'keterisian_kuota' => $totalKuota > 0 ? round($nomor / $totalKuota * 100, 1) : null,
            ];
        }

        return $out;
    }

    public function daftarRows(array $kompetisiIds): array
    {
        $base = $this->baseRows($kompetisiIds);
        $byComp = $base->groupBy('kompetisi_id');
        $rows = [];

        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];
            $compNama = $compRows->first()->kompetisi_nama;

            $list = $compRows->map(fn ($r) => [
                'name' => $r->atlet_name,
                'nomor' => $r->nomor_lomba,
                'club' => $r->club,
                'phone' => $r->phone,
                'status' => $r->status_pembayaran,
            ])->values()->all();
            usort($list, fn ($a, $b) => [$a['club'], $a['name'], $a['nomor']] <=> [$b['club'], $b['name'], $b['nomor']]);

            $no = 1;
            foreach ($list as $r) {
                $rows[] = [
                    'No' => $no++,
                    'Nama Atlet' => $r['name'],
                    'Nomor Lomba' => $r['nomor'],
                    'Club' => $r['club'],
                    'Nomor Telepon' => "'" . $r['phone'],
                    'Status Pembayaran' => $r['status'],
                    'Nama Kompetisi' => $compNama,
                ];
            }

            $rows[] = [
                'No' => null, 'Nama Atlet' => 'Total Atlet', 'Nomor Lomba' => '', 'Club' => '',
                'Nomor Telepon' => '', 'Status Pembayaran' => $compRows->pluck('atlet_name')->unique()->count(),
                'Nama Kompetisi' => $compNama,
            ];
            $rows[] = [
                'No' => null, 'Nama Atlet' => 'Total Club', 'Nomor Lomba' => '', 'Club' => '',
                'Nomor Telepon' => '', 'Status Pembayaran' => $compRows->pluck('user_name')->unique()->count(),
                'Nama Kompetisi' => $compNama,
            ];
        }

        return $rows;
    }
}
