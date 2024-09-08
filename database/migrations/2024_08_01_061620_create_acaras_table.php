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
        Schema::create('acara', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kompetisi_id');
            $table->foreign('kompetisi_id')->references('id')->on('kompetisi')->onDelete('cascade');
            $table->string('jenis_lomba');
            $table->integer('nomor_lomba');
            $table->string('nama');
            $table->enum('kategori', ['Pria','Wanita', 'Campuran'])->default('Campuran');
            $table->integer('harga');
            $table->integer('kuota');
            $table->string('grup');
            $table->integer('max_umur');
            $table->integer('min_umur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acara');
    }
};
