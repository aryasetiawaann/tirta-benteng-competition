<?php

namespace Database\Factories;
use App\Models\Kompetisi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Acara>
 */
class AcaraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kompetisi_id' => Kompetisi::factory(), 
            'nama' => $this->faker->sentence(3), 
            'status' => $this->faker->randomElement(['Buka', 'Tutup']),
            'harga' => $this->faker->numberBetween(10000, 500000), 
            'kuota' => $this->faker->numberBetween(10, 100), 
            'grup' => $this->faker->word,
            'min_umur' => $this->faker->numberBetween(10, 18), 
            'max_umur' => $this->faker->numberBetween(18, 60), 
        ];
    }
}
