<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('atlets')->insert([
            [
                'user_id' => 1,  // Id dari user yang sesuai di tabel 'users'
                'name' => 'Ayu Lestari',
                'umur' => Carbon::create('1990', '05', '15'),
                'jenis_kelamin' => 'Wanita',
                'track_record' => 9.58,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 1,  // Id dari user yang sesuai di tabel 'users'
                'name' => 'Budi Santoso',
                'umur' => Carbon::create('1988', '11', '22'),
                'jenis_kelamin' => 'Pria',
                'track_record' => 10.12,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 1,  // Id dari user yang sesuai di tabel 'users'
                'name' => 'Siti Aminah',
                'umur' => Carbon::create('1995', '03', '10'),
                'jenis_kelamin' => 'Wanita',
                'track_record' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 1,  // Id dari user yang sesuai di tabel 'users'
                'name' => 'Joko Prasetyo',
                'umur' => Carbon::create('1992', '08', '30'),
                'jenis_kelamin' => 'Pria',
                'track_record' => 8.90,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
