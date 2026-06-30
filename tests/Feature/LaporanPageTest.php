<?php // tests/Feature/LaporanPageTest.php
namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Kompetisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPageTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_laporan_page(): void
    {
        $this->get(route('admin.laporan'))->assertRedirect();
    }

    public function test_non_admin_redirected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('admin.laporan'))
            ->assertRedirect();
    }

    public function test_admin_sees_active_competition_summary(): void
    {
        $this->withoutVite();

        $k = Kompetisi::factory()->create([
            'nama' => 'Lomba Aktif',
            'buka_pendaftaran' => now()->subDay(),
            'waktu_kompetisi' => now()->addDay(),
        ]);
        $u = User::factory()->create(['club' => 'Alpha', 'phone' => '0811', 'role' => 'user']);
        $atlet = Atlet::create(['user_id' => $u->id, 'name' => 'Andi', 'umur' => '2010-01-01', 'jenis_kelamin' => 'Pria']);
        $acara = Acara::factory()->create(['kompetisi_id' => $k->id, 'nomor_lomba' => 1]);
        $atlet->acara()->attach($acara->id, ['status_pembayaran' => 'Selesai']);

        $this->actingAs($this->admin())
            ->get(route('admin.laporan'))
            ->assertOk()
            ->assertSee('Lomba Aktif');
    }

    public function test_admin_can_download_single_export_zip(): void
    {
        $k = Kompetisi::factory()->create([
            'nama' => 'Lomba Aktif',
            'buka_pendaftaran' => now()->subDay(),
            'waktu_kompetisi' => now()->addDay(),
        ]);

        $res = $this->actingAs($this->admin())->get(route('admin.laporan.export', $k->id));
        $res->assertOk();
        $this->assertStringContainsString('.zip', $res->headers->get('content-disposition'));
    }

    public function test_export_all_with_no_active_redirects_with_error(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.laporan.export-all'))
            ->assertRedirect();
    }
}
