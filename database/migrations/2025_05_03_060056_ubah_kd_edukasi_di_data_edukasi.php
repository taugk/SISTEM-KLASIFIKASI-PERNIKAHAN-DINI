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
        // Hapus kolom auto increment lama
    Schema::table('data_edukasi', function (Blueprint $table) {
        $table->dropPrimary(); // hilangkan primary key lama
        $table->dropColumn('kd_edukasi');
    });

    Schema::table('data_edukasi', function (Blueprint $table) {
        $table->string('kd_edukasi')->primary();

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_edukasi');
    }
};
