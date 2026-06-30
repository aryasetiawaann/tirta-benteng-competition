<?php // tests/Unit/LaporanReportServiceTest.php
namespace Tests\Unit;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Kompetisi;
use App\Models\Pembayaran;
use App\Models\User;
use App\Services\LaporanReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function seedKompetisi(array $attrs, array $regs): Kompetisi
    {
        $k = Kompetisi::factory()->create($attrs);
        $users = [];
        $atlets = [];
        foreach ($regs as $r) {
            // Reuse user by email to handle multiple registrations per user
            if (!isset($users[$r['email']])) {
                $users[$r['email']] = User::factory()->create([
                    'name' => $r['user_name'], 'email' => $r['email'],
                    'club' => $r['club'], 'phone' => $r['phone'], 'role' => 'user',
                ]);
            }
            $u = $users[$r['email']];
            $atletKey = $r['email'] . '|' . $r['atlet'];
            if (!isset($atlets[$atletKey])) {
                $atlets[$atletKey] = Atlet::create([
                    'user_id' => $u->id, 'name' => $r['atlet'],
                    'umur' => $r['umur'] ?? '2010-01-01',
                    'jenis_kelamin' => $r['jenis_kelamin'] ?? 'Pria',
                ]);
            }
            $atlet = $atlets[$atletKey];
            $acara = Acara::factory()->create([
                'kompetisi_id' => $k->id, 'nomor_lomba' => $r['nomor'],
                'kuota' => $r['kuota'] ?? 50,
            ]);
            $pivot = ['status_pembayaran' => $r['status']];
            if (isset($r['pembayaran_id'])) {
                $pivot['pembayaran_id'] = $r['pembayaran_id'];
            }
            $atlet->acara()->attach($acara->id, $pivot);
        }
        return $k;
    }

    public function test_active_competitions_filters_by_date_window(): void
    {
        Kompetisi::factory()->create([ // active
            'nama' => 'Active', 'buka_pendaftaran' => now()->subDay(),
            'waktu_kompetisi' => now()->addDays(5),
        ]);
        Kompetisi::factory()->create([ // not open yet
            'nama' => 'Future', 'buka_pendaftaran' => now()->addDays(2),
            'waktu_kompetisi' => now()->addDays(9),
        ]);
        Kompetisi::factory()->create([ // already held
            'nama' => 'Past', 'buka_pendaftaran' => now()->subDays(10),
            'waktu_kompetisi' => now()->subDay(),
        ]);

        $names = (new LaporanReportService())->activeCompetitions()->pluck('nama')->all();

        $this->assertSame(['Active'], $names);
    }

    public function test_club_payment_rows_aggregate_per_user_with_total_semua(): void
    {
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba A', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [
                // Club Alpha user: 2 athletes, 3 entries (2 selesai, 1 menunggu)
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai'],
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 2, 'status' => 'Selesai'],
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Budi', 'nomor' => 1, 'status' => 'Menunggu'],
            ]
        );

        $rows = (new LaporanReportService())->clubPaymentRows([$k->id]);

        // First data row
        $this->assertSame(1, $rows[0]['No']);
        $this->assertSame('Alpha', $rows[0]['Club']);
        $this->assertSame("'0811", $rows[0]['Nomor Telepon']);
        $this->assertSame(2, $rows[0]['Total Peserta per Club']); // Andi, Budi
        $this->assertSame(3, $rows[0]['Total Nomor per Club']);   // 3 entries
        $this->assertSame(2, $rows[0]['Total Selesai per Club']);
        $this->assertSame(1, $rows[0]['Total Menunggu per Club']);

        // Total Semua row
        $total = $rows[1];
        $this->assertNull($total['No']);
        $this->assertSame('Total Semua', $total['Club']);
        $this->assertSame(2, $total['Total Peserta per Club']);
        $this->assertSame(3, $total['Total Nomor per Club']);
        $this->assertSame('Lomba A', $total['Nama Kompetisi']);
    }

    public function test_summaries_counts_clubs_and_participants(): void
    {
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba B', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai'],
                ['club' => 'Beta',  'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Cici', 'nomor' => 1, 'status' => 'Menunggu'],
            ]
        );

        $s = collect((new LaporanReportService())->summaries([$k->id]))->firstWhere('kompetisi_id', $k->id);

        $this->assertSame('Lomba B', $s['nama']);
        $this->assertSame(2, $s['peserta']);
        $this->assertSame(2, $s['nomor']);
        $this->assertSame(2, $s['club']);
        $this->assertSame(1, $s['selesai']);
        $this->assertSame(1, $s['menunggu']);
    }

    public function test_summaries_includes_participation_stats(): void
    {
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba D', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [
                // Alpha: Andi (Pria, born 2010) with 2 entries on nomor 1 & 2
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai', 'jenis_kelamin' => 'Pria', 'umur' => '2010-01-01'],
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 2, 'status' => 'Selesai', 'jenis_kelamin' => 'Pria', 'umur' => '2010-01-01'],
                // Beta: Cici (Wanita, born 2014) with 1 entry on nomor 1
                ['club' => 'Beta', 'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Cici', 'nomor' => 1, 'status' => 'Menunggu', 'jenis_kelamin' => 'Wanita', 'umur' => '2014-01-01'],
            ]
        );

        $s = collect((new LaporanReportService())->summaries([$k->id]))->firstWhere('kompetisi_id', $k->id);

        $this->assertSame(1, $s['gender_l']);                 // Andi
        $this->assertSame(1, $s['gender_p']);                 // Cici
        $this->assertSame(2, $s['nomor_lomba_count']);        // nomor 1 & 2 distinct
        $this->assertSame(1.5, $s['nomor_per_atlet']);        // 3 entries / 2 peserta
        $this->assertEqualsWithDelta(66.7, $s['tingkat_pelunasan'], 0.1); // 2 Selesai / 3
        $expectedAvg = round((\Carbon\Carbon::parse('2010-01-01')->age + \Carbon\Carbon::parse('2014-01-01')->age) / 2, 1);
        $this->assertSame($expectedAvg, $s['umur_rata']);
        $this->assertSame('Alpha', $s['club_terbanyak']);     // Alpha 1 athlete vs Beta 1 -> tie -> alphabetical
    }

    public function test_summaries_sums_distinct_payments_for_revenue(): void
    {
        $k = Kompetisi::factory()->create([
            'nama' => 'Lomba E', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay(),
        ]);
        $u = User::factory()->create(['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'role' => 'user']);
        $atlet = Atlet::create(['user_id' => $u->id, 'name' => 'Andi', 'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria']);

        // One Berhasil payment of 150000 covering TWO entries -> counted once.
        $paid = Pembayaran::create([
            'user_id' => $u->id, 'midtrans_order_id' => 'ORD-1', 'metode_pembayaran' => 'qris',
            'total_harga' => 150000, 'status' => 'Berhasil',
        ]);
        // One Menunggu payment of 50000.
        $pending = Pembayaran::create([
            'user_id' => $u->id, 'midtrans_order_id' => 'ORD-2', 'metode_pembayaran' => 'qris',
            'total_harga' => 50000, 'status' => 'Menunggu',
        ]);
        // One Gagal payment of 99999 -> excluded entirely.
        $failed = Pembayaran::create([
            'user_id' => $u->id, 'midtrans_order_id' => 'ORD-3', 'metode_pembayaran' => 'qris',
            'total_harga' => 99999, 'status' => 'Gagal',
        ]);

        foreach ([[1, $paid], [2, $paid], [3, $pending], [4, $failed]] as [$nomor, $pay]) {
            $acara = Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => $nomor]);
            $atlet->acara()->attach($acara->id, [
                'status_pembayaran' => $pay->status === 'Berhasil' ? 'Selesai' : 'Menunggu',
                'pembayaran_id' => $pay->id,
            ]);
        }

        $s = collect((new LaporanReportService())->summaries([$k->id]))->firstWhere('kompetisi_id', $k->id);

        $this->assertSame(150000, $s['pendapatan_terkumpul']); // distinct Berhasil payment, once
        $this->assertSame(50000, $s['pendapatan_tertunda']);   // Menunggu payment
    }

    public function test_daftar_rows_list_with_total_atlet_and_total_club(): void
    {
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba C', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [
                ['club' => 'Beta',  'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Cici', 'nomor' => 2, 'status' => 'Selesai'],
                ['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Menunggu'],
            ]
        );

        $rows = (new LaporanReportService())->daftarRows([$k->id]);

        // Ordered by club asc -> Alpha first
        $this->assertSame('Andi', $rows[0]['Nama Atlet']);
        $this->assertSame(1, $rows[0]['No']);
        $this->assertSame("'0811", $rows[0]['Nomor Telepon']);
        $this->assertSame('Cici', $rows[1]['Nama Atlet']);

        // Total rows
        $totalAtlet = $rows[2];
        $this->assertSame('Total Atlet', $totalAtlet['Nama Atlet']);
        $this->assertSame(2, $totalAtlet['Status Pembayaran']);
        $totalClub = $rows[3];
        $this->assertSame('Total Club', $totalClub['Nama Atlet']);
        $this->assertSame(2, $totalClub['Status Pembayaran']);
        $this->assertSame('Lomba C', $totalClub['Nama Kompetisi']);
    }
}
