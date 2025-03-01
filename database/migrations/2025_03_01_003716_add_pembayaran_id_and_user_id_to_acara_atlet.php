<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('acara_atlet', function (Blueprint $table) {
            $table->unsignedBigInteger('peserta_user_id')->nullable()->after('id');
            $table->foreign('peserta_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('pembayaran_id')->nullable()->after('atlet_id');
            $table->foreign('pembayaran_id')->references('id')->on('pembayaran')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acara_atlet', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_id']);
            $table->dropForeign(['user_id']); 

            // Hapus kolom pembayaran_id dan user_id
            $table->dropColumn(['pembayaran_id', 'user_id']);
        });
    }
};
