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
        Schema::create('harga_kompetisis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kompetisi_id');
            $table->foreign('kompetisi_id')->references('id')->on('kompetisi')->onDelete('cascade');
            $table->string('judul');
            $table->integer('harga');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_kompetisis');
    }
};
