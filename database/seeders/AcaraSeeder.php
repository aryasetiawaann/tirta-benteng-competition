<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acara')->insert([
            [
                'kompetisi_id' => 1,  // Id dari Kompetisi Coding Nasional
                'nomor_lomba' => 1,
                'nama' => 'Pemrograman Web',
                'kategori' => 'Campuran',
                'harga' => 50000,
                'kuota' => 100,
                'grup' => 'A',
                'max_umur' => 35,
                'min_umur' => 18,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kompetisi_id' => 1,  // Id dari Kompetisi Coding Nasional
                'nomor_lomba' => 2,
                'nama' => 'Pemrograman Mobile',
                'kategori' => 'Pria',
                'harga' => 50000,
                'kuota' => 50,
                'grup' => 'B',
                'max_umur' => 30,
                'min_umur' => 18,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kompetisi_id' => 2,  // Id dari Fun Coding Challenge
                'nomor_lomba' => 1,
                'nama' => 'Pemrograman Python',
                'kategori' => 'Wanita',
                'harga' => 30000,
                'kuota' => 80,
                'grup' => 'C',
                'max_umur' => 40,
                'min_umur' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kompetisi_id' => 3,  // Id dari AI Hackathon 2024
                'nomor_lomba' => 1,
                'nama' => 'AI Challenge',
                'kategori' => 'Campuran',
                'harga' => 100000,
                'kuota' => 150,
                'grup' => 'D',
                'max_umur' => 40,
                'min_umur' => 18,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
