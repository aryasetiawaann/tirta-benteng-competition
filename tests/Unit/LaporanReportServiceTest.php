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
        foreach ($regs as $r) {
            $u = User::factory()->create([
                'name' => $r['user_name'], 'email' => $r['email'],
                'club' => $r['club'], 'phone' => $r['phone'], 'role' => 'user',
            ]);
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
}
