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
        Schema::create('data_edukasi', function (Blueprint $table) {
            $table->id('kd_edukasi')->unique();
            $table->string('judul'); // kolom untuk judul edukasi
            $table->text('deskripsi'); // kolom untuk deskripsi edukasi
            $table->string('gambar')->nullable(); // kolom untuk gambar (nullable, bisa kosong)
            $table->unsignedBigInteger('pengguna_id'); // kolom untuk pengguna_id (foreign key)
            $table->timestamps(); // kolom created_at dan updated_at

            // Menambahkan foreign key constraint
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->onDelete('cascade');
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
