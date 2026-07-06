<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Kompetisi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

/**
 * Covers `template:manual-form --all`, which generates one form per active
 * competition straight from the database. Runs against the in-memory sqlite test
 * database (RefreshDatabase), seeding a single active competition so the output is
 * deterministic and the real dev database is never touched. Each run drops its
 * forms into a fresh manual/manual-form_<timestamp>/ folder, which is removed in
 * tearDown.
 */
class GenerateManualFormAllTest extends TestCase
{
    use RefreshDatabase;

    private ?Kompetisi $kompetisi = null;
    /** @var string[] manual-form_* dirs that existed before this test ran */
    private array $dirsBefore = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->kompetisi = Kompetisi::create([
            'nama'              => 'PHPUnit Active Comp ' . uniqid(),
            'lokasi'            => 'Test Pool',
            'buka_pendaftaran'  => now()->subDay()->toDateString(),
            'tutup_pendaftaran' => now()->toDateString(),
            'kategori'          => 'Fun',
            'waktu_kompetisi'   => now()->addDay()->toDateString(),
        ]);

        // Insert out of nomor_lomba order to prove the command sorts them.
        foreach ([['nomor' => 102, 'kat' => 'Wanita'], ['nomor' => 101, 'kat' => 'Pria']] as $row) {
            Acara::create([
                'kompetisi_id' => $this->kompetisi->id,
                'jenis_lomba'  => '50m gaya bebas',
                'nomor_lomba'  => $row['nomor'],
                'nama'         => '50M GAYA BEBAS',
                'kategori'     => $row['kat'],
                'harga'        => 100000,
                'kuota'        => 999,
                'grup'         => 'KIDS',
                'min_umur'     => 2021,
                'max_umur'     => 2026,
            ]);
        }

        $this->dirsBefore = $this->manualFormDirs();
    }

    protected function tearDown(): void
    {
        // Remove any manual-form_<timestamp> folder created during this test.
        foreach (array_diff($this->manualFormDirs(), $this->dirsBefore) as $dir) {
            $this->removeDir($dir);
        }
        if ($this->kompetisi) {
            Acara::where('kompetisi_id', $this->kompetisi->id)->delete();
            $this->kompetisi->delete();
        }

        parent::tearDown();
    }

    /** @return string[] */
    private function manualFormDirs(): array
    {
        return array_values(array_filter(glob(base_path('manual/manual-form_*')) ?: [], 'is_dir'));
    }

    private function removeDir(string $dir): void
    {
        foreach (glob($dir . '/*') ?: [] as $entry) {
            is_dir($entry) ? $this->removeDir($entry) : unlink($entry);
        }
        rmdir($dir);
    }

    /**
     * Locate the single form generated for the seeded competition inside the
     * folder this run created.
     */
    private function generatedFile(): string
    {
        $newDirs = array_diff($this->manualFormDirs(), $this->dirsBefore);
        $this->assertCount(1, $newDirs, 'Expected exactly one new manual-form_<timestamp> folder.');

        return reset($newDirs) . "/{$this->kompetisi->nama} manual-form.xlsx";
    }

    public function test_all_generates_a_form_in_a_timestamped_folder(): void
    {
        $this->artisan('template:manual-form', ['--all' => true])
            ->assertExitCode(0);

        $this->assertFileExists($this->generatedFile());
    }

    public function test_referensi_is_populated_from_the_database_and_sorted(): void
    {
        $this->artisan('template:manual-form', ['--all' => true])->assertExitCode(0);

        $sheet = IOFactory::load($this->generatedFile())->getSheetByName('Referensi');
        $rows  = $sheet->toArray(null, true, false, false);

        // header + 2 acara rows
        $this->assertCount(3, $rows);

        // sorted ascending by nomor_lomba (we inserted 102 before 101)
        $this->assertSame(101, (int) $rows[1][2]);
        $this->assertSame(102, (int) $rows[2][2]);

        // label format, with nama title-cased from the DB's uppercase value
        $this->assertSame('101 - KU KIDS - 50m Gaya Bebas - Pria', $rows[1][8]);
    }

    public function test_all_ignores_the_name_argument(): void
    {
        // --all wins even when a name is supplied; no name-based file is written to manual/.
        $this->artisan('template:manual-form', ['name' => 'ShouldBeIgnored', '--all' => true])
            ->assertExitCode(0);

        $this->assertFileDoesNotExist(base_path('manual/ShouldBeIgnored manual-form.xlsx'));
        $this->assertFileExists($this->generatedFile());
    }
}
