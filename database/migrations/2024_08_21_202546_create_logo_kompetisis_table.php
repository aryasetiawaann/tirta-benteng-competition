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
        Schema::create('logo_kompetisis', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('kompetisi_id');
            $table->foreign('kompetisi_id')->references('id')->on('kompetisis')->onDelete('cascade');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logo_kompetisis');
    }
};
