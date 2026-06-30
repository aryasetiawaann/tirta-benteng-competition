<?php // tests/Unit/LaporanReportServiceTest.php
namespace Tests\Unit;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Kompetisi;
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
        foreach ($regs as $r) {
            // Reuse user by email to handle multiple registrations per user
            if (!isset($users[$r['email']])) {
                $users[$r['email']] = User::factory()->create([
                    'name' => $r['user_name'], 'email' => $r['email'],
                    'club' => $r['club'], 'phone' => $r['phone'], 'role' => 'user',
                ]);
            }
            $u = $users[$r['email']];
            $atlet = Atlet::create([
                'user_id' => $u->id, 'name' => $r['atlet'],
                'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria',
            ]);
            $acara = Acara::factory()->create([
                'kompetisi_id' => $k->id, 'nomor_lomba' => $r['nomor'],
            ]);
            $atlet->acara()->attach($acara->id, ['status_pembayaran' => $r['status']]);
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
}
