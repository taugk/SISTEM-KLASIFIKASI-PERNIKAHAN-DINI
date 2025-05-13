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
        Schema::table('data_edukasi', function (Blueprint $table) {
            $table->string('kd_kategori')->after('pengguna_id');

            $table->foreign('kd_kategori')
                  ->references('kd_kategori')->on('kategori_edukasi')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_edukasi', function (Blueprint $table) {
            $table->dropForeign(['kd_kategori']);
            $table->dropColumn('kd_kategori');
        });
    }
};
