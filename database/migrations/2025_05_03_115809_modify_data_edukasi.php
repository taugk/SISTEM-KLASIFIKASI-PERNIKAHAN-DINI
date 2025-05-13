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
            $table->string('kd_edukasi')->primary();  // Kode edukasi
            $table->string('judul');  // Judul edukasi
            $table->text('deskripsi');  // Deskripsi edukasi
            $table->string('gambar')->nullable();  // Gambar edukasi
            $table->string('kategori')->nullable();  // Kategori edukasi, disimpan sebagai string
            $table->unsignedBigInteger('pengguna_id');  // ID pengguna yang menambahkan
            $table->timestamps();  // Created_at dan updated_at

            // Membuat foreign key untuk pengguna_id (mengacu pada tabel pengguna)
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
