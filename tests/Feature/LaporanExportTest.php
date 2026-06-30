<?php // tests/Feature/LaporanExportTest.php
namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Kompetisi;
use App\Models\User;
use App\Services\LaporanExportService;
use App\Services\LaporanReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanExportTest extends TestCase
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
            $acara = Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => $r['nomor']]);
            $atlet->acara()->attach($acara->id, ['status_pembayaran' => $r['status']]);
        }
        return $k;
    }

    private function zipEntries(string $path): array
    {
        $zip = new \ZipArchive();
        $zip->open($path);
        $names = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $names[] = $zip->getNameIndex($i);
        }
        $zip->close();
        sort($names);
        return $names;
    }

    public function test_export_single_competition_zip_contains_two_split_files(): void
    {
        $k = $this->seedKompetisi(
            ['nama' => 'Lomba A', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai']]
        );

        $service = new LaporanExportService(new LaporanReportService());
        $path = $service->exportCompetition($k);

        $this->assertFileExists($path);
        $entries = $this->zipEntries($path);
        $this->assertCount(2, $entries);
        foreach ($entries as $e) {
            $this->assertStringStartsWith('laporan ', $e);
            $this->assertStringContainsString('Lomba A', $e);
            $this->assertStringEndsWith('.xlsx', $e);
        }
        $this->assertTrue((bool) preg_grep('/list_club_payment .* Lomba A\.xlsx$/', $entries));
        $this->assertTrue((bool) preg_grep('/list_daftar .* Lomba A\.xlsx$/', $entries));

        @unlink($path);
    }
}
