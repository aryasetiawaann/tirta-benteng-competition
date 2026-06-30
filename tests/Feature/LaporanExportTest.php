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

    public function test_export_active_includes_combined_plus_split_and_excludes_inactive(): void
    {
        $a = $this->seedKompetisi(
            ['nama' => 'CompA', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDay()],
            [['club' => 'Alpha', 'email' => 'a@x.com', 'phone' => '0811', 'user_name' => 'UA', 'atlet' => 'Andi', 'nomor' => 1, 'status' => 'Selesai']]
        );
        $b = $this->seedKompetisi(
            ['nama' => 'CompB', 'buka_pendaftaran' => now()->subDay(), 'waktu_kompetisi' => now()->addDays(2)],
            [['club' => 'Beta', 'email' => 'b@x.com', 'phone' => '0822', 'user_name' => 'UB', 'atlet' => 'Budi', 'nomor' => 1, 'status' => 'Menunggu']]
        );
        // Inactive (already held) — must not appear.
        $this->seedKompetisi(
            ['nama' => 'CompPast', 'buka_pendaftaran' => now()->subDays(10), 'waktu_kompetisi' => now()->subDay()],
            [['club' => 'Gamma', 'email' => 'g@x.com', 'phone' => '0833', 'user_name' => 'UG', 'atlet' => 'Gita', 'nomor' => 1, 'status' => 'Selesai']]
        );

        $service = new LaporanExportService(new LaporanReportService());
        $path = $service->exportActive();
        $entries = $this->zipEntries($path);

        // 2 combined + 2 per active competition (2 actives) = 6
        $this->assertCount(6, $entries);
        $this->assertTrue((bool) preg_grep('/list_club_payment [0-9 :-]+\.xlsx$/', $entries)); // combined (no name suffix)
        $this->assertTrue((bool) preg_grep('/CompA\.xlsx$/', $entries));
        $this->assertTrue((bool) preg_grep('/CompB\.xlsx$/', $entries));
        $this->assertEmpty(preg_grep('/CompPast/', $entries));

        @unlink($path);
    }

    public function test_export_active_throws_when_none_active(): void
    {
        $this->expectException(\RuntimeException::class);
        (new LaporanExportService(new LaporanReportService()))->exportActive();
    }
}
