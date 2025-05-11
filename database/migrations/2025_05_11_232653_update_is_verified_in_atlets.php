<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update enum dengan menambahkan 'need revision'
        DB::statement("ALTER TABLE atlets MODIFY is_verified ENUM('not verified', 'verified', 'need revision') DEFAULT 'not verified'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pastikan tidak ada data 'need revision' sebelum rollback
        DB::table('atlets')
            ->where('is_verified', 'need revision')
            ->update(['is_verified' => 'not verified']);

        // Rollback enum ke versi awal
        DB::statement("ALTER TABLE atlets MODIFY is_verified ENUM('not verified', 'verified') DEFAULT 'not verified'");
    }
};
