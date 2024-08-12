<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KompetisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kompetisi')->insert([
            [
                'nama' => 'Kompetisi Coding Nasional',
                'lokasi' => 'Jakarta Convention Center, Jakarta',
                'deskripsi' => 'Kompetisi coding terbesar di Indonesia dengan peserta dari seluruh nusantara.',
                'buka_pendaftaran' => Carbon::create('2024', '08', '01'),
                'tutup_pendaftaran' => Carbon::create('2024', '09', '15'),
                'kategori' => 'Resmi',
                'waktu_techmeeting' => Carbon::create('2024', '09', '25'),
                'waktu_kompetisi' => Carbon::create('2024', '10', '10'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'Fun Coding Challenge',
                'lokasi' => 'Universitas XYZ, Bandung',
                'deskripsi' => 'Kompetisi coding untuk bersenang-senang dengan hadiah menarik.',
                'buka_pendaftaran' => Carbon::create('2024', '07', '01'),
                'tutup_pendaftaran' => Carbon::create('2024', '08', '01'),
                'kategori' => 'Fun',
                'waktu_techmeeting' => null,
                'waktu_kompetisi' => Carbon::create('2024', '08', '15'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'AI Hackathon 2024',
                'lokasi' => 'Online',
                'deskripsi' => 'Hackathon untuk mengembangkan aplikasi berbasis AI dalam waktu 24 jam.',
                'buka_pendaftaran' => Carbon::create('2024', '05', '10'),
                'tutup_pendaftaran' => Carbon::create('2024', '06', '10'),
                'kategori' => 'Resmi',
                'waktu_techmeeting' => Carbon::create('2024', '06', '15'),
                'waktu_kompetisi' => Carbon::create('2024', '06', '20'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
