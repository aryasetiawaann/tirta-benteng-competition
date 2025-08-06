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
        Schema::table('kompetisi', function (Blueprint $table) {
            $table->boolean('has_pricing')->default(false);
            $table->integer('max_participation')->nullable();
            $table->integer('additional_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kompetisi', function (Blueprint $table) {
            $table->dropColumn('has_pricing');
            $table->integer('max_participation');
            $table->integer('additional_price')->nullable();

        });
    }
};
