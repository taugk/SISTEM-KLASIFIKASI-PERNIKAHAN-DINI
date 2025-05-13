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
        Schema::create('resiko__wilayah', function (Blueprint $table) {
            $table->id();
            $table->integer('jumlah_pernikahan');
            $table->integer('jumlah_pernikahan_dini');
            $table->date('periode');
            $table->enum('resiko_wilayah', ['tinggi', 'sedang', 'rendah'])->default('rendah');
            $table->foreignId('id_wilayah')->constrained('data_wilayah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resiko__wilayah');
    }
};
