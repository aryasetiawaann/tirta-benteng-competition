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

        // A single payment can cover registrations across multiple competitions
        // (the tagihan flow lets users pay across competitions at once), so its
        // real total_harga must be split between them, not counted once per
        // competition. We split each payment's total_harga proportionally to each
        // competition's share of the registration prices (acara.harga).
        $paymentIds = DB::table('acara_atlet as aa')
            ->join('acara as ac', 'aa.acara_id', '=', 'ac.id')
            ->whereIn('ac.kompetisi_id', $kompetisiIds)
            ->whereNotNull('aa.pembayaran_id')
            ->distinct()
            ->pluck('aa.pembayaran_id')
            ->all();

        if (empty($paymentIds)) {
            return [];
        }

        // Load every registration of those payments across ALL competitions they
        // touch (not just the in-scope ones) so the price-share denominator is
        // complete and an in-scope competition only gets its fair slice.
        $regs = DB::table('acara_atlet as aa')
            ->join('acara as ac', 'aa.acara_id', '=', 'ac.id')
            ->join('pembayaran as p', 'aa.pembayaran_id', '=', 'p.id')
            ->whereIn('aa.pembayaran_id', $paymentIds)
            ->select('aa.pembayaran_id as pembayaran_id', 'p.total_harga as total_harga', 'p.status as status', 'ac.kompetisi_id as kompetisi_id', 'ac.harga as harga')
            ->get();

        $out = [];
        foreach ($regs->groupBy('pembayaran_id') as $rows) {
            $first = $rows->first();
            $bucket = match ($first->status) {
                'Berhasil' => 'terkumpul',
                'Menunggu' => 'tertunda',
                default => null, // Gagal / Kedaluarsa collect nothing.
            };
            if ($bucket === null) {
                continue;
            }

            $weights = $rows->groupBy('kompetisi_id')->map(fn ($g) => (int) $g->sum('harga'))->all();
            foreach ($this->splitAmount((int) $first->total_harga, $weights) as $compId => $amount) {
                $out[$compId] ??= ['terkumpul' => 0, 'tertunda' => 0];
                $out[$compId][$bucket] += $amount;
            }
        }

        return $out;
    }

    /**
     * Split an integer $total across weighted keys so the shares always sum back
     * to exactly $total (largest-remainder method). Zero total weight splits by
     * count instead of dividing by zero.
     *
     * @param  array<int|string, int>  $weights
     * @return array<int|string, int>
     */
    private function splitAmount(int $total, array $weights): array
    {
        $totalWeight = array_sum($weights);
        if ($totalWeight <= 0) {
            $weights = array_fill_keys(array_keys($weights), 1);
            $totalWeight = count($weights);
        }

        $shares = [];
        $remainders = [];
        $allocated = 0;
        foreach ($weights as $key => $w) {
            $exact = $total * $w / $totalWeight;
            $shares[$key] = (int) floor($exact);
            $remainders[$key] = $exact - $shares[$key];
            $allocated += $shares[$key];
        }

        // Hand the leftover units to the largest fractional remainders, tie-broken
        // by larger weight then key so the result is deterministic.
        $leftover = $total - $allocated;
        uksort($remainders, fn ($a, $b) => [$remainders[$b], $weights[$b], $b] <=> [$remainders[$a], $weights[$a], $a]);
        foreach (array_keys($remainders) as $key) {
            if ($leftover <= 0) {
                break;
            }
            $shares[$key]++;
            $leftover--;
        }

        return $shares;
    }

    public function summaries(array $kompetisiIds): array
    {
        $base = $this->baseRows($kompetisiIds);
        $byComp = $base->groupBy('kompetisi_id');
        $revenue = $this->revenueByComp($kompetisiIds);
        $out = [];

        foreach ($this->orderedCompetitionIds($base) as $compId) {
            $compRows = $byComp[$compId];

            $athletes = $compRows->groupBy('atlet_id')->map(fn ($g) => $g->first());
            $nomor = $compRows->count();
            $selesai = $compRows->where('status_pembayaran', 'Selesai')->count();
            $peserta = $compRows->pluck('atlet_name')->unique()->count();

            // Top club by unique athletes; ties broken alphabetically.
            $pairs = $athletes->groupBy('club')
                ->map(fn ($g, $club) => ['club' => (string) $club, 'count' => $g->count()])
                ->values()->all();
            usort($pairs, fn ($a, $b) => [$b['count'], $a['club']] <=> [$a['count'], $b['club']]);

            $topClub = $pairs[0]['club'] ?? null;
            $topClubPeserta = $pairs[0]['count'] ?? 0;
            $topClubNomor = $topClub !== null ? $compRows->where('club', $topClub)->count() : 0;

            $rev = $revenue[$compId] ?? ['terkumpul' => 0, 'tertunda' => 0];

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
                'nomor_per_atlet' => $peserta > 0 ? round($nomor / $peserta, 1) : 0.0,
                'club_terbanyak' => $topClub,
                'club_terbanyak_peserta' => $topClubPeserta,
                'club_terbanyak_nomor' => $topClubNomor,
                'pendapatan_terkumpul' => $rev['terkumpul'],
                'pendapatan_tertunda' => $rev['tertunda'],
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
