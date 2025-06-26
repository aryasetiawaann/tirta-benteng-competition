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
        Schema::create('winners', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('club');
            $table->string('nik', 20);
            $table->integer('rank');
            $table->string('kelompok_umur', 5);
            $table->string('nomor_lomba');
            $table->text('kode');
            $table->unsignedBigInteger('kompetisi_id');
            $table->foreign('kompetisi_id')->references('id')->on('kompetisi')->onDelete('cascade');
            $table->unsignedBigInteger('acara_id');
            $table->foreign('acara_id')->references('id')->on('acara')->onDelete('cascade');
            $table->unsignedBigInteger('certificate_id')->nullable();
            $table->foreign('certificate_id')->references('id')->on('certificates')->onDelete('cascade');
            $table->unsignedBigInteger('letter_id')->nullable();
            $table->foreign('letter_id')->references('id')->on('letters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('winners');
    }
};
