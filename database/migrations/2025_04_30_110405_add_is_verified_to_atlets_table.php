<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('atlets', function (Blueprint $table) {
        $table->enum('is_verified', ['not verified', 'verified'])->default('not verified')->after('dokumen');
    });
}

public function down()
{
    Schema::table('atlets', function (Blueprint $table) {
        $table->dropColumn('is_verified');
    });
}
};
