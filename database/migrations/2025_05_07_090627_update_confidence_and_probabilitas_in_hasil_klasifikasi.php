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
        Schema::table('hasil_klasifikasi', function (Blueprint $table) {
            // Ubah confidence jadi decimal (misalnya maksimal 100.00)
            $table->decimal('confidence', 5, 2)->change();

            // Ubah probabilitas jadi tipe TEXT untuk simpan JSON
            $table->text('probabilitas')->change();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_klasifikasi', function (Blueprint $table) {
            $table->string('confidence')->change();
            $table->string('probabilitas')->change();
        });
    }
};
