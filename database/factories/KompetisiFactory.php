<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kompetisi>
 */
class KompetisiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->word,
            'status' => $this->faker->randomElement(['Registrasi', 'Selesai']),
            'lokasi' => $this->faker->address,
            'deskripsi' => $this->faker->paragraph,
            'buka_pendaftaran' => $this->faker->date(),
            'tutup_pendaftaran' => $this->faker->date(),
        ];
    }
}
