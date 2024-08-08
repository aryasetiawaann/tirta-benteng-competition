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
        Schema::create('acara_atlet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acara_id');
            $table->unsignedBigInteger('atlet_id');
            $table->foreign('acara_id')->references('id')->on('acara')->onDelete('cascade');
            $table->foreign('atlet_id')->references('id')->on('atlets')->onDelete('cascade');
            $table->enum('status_pembayaran', ['Menunggu','Selesai'])->default('Menunggu');
            $table->date('waktu_pembayaran')->nullable();
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acara_atlet');
    }
};
