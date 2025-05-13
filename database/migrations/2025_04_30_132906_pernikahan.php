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
        Schema::create('pernikahan', function (Blueprint $table) {
            $table->id();

            // Suami
            $table->string('nama_suami');
            $table->date('tanggal_lahir_suami');
            $table->integer('usia_suami');
            $table->enum('pendidikan_suami', [
                'TIDAK/BELUM SEKOLAH',
                'TIDAK TAMAT SD/SEDERAJAT',
                'TAMAT SD/SEDERAJAT',
                'SLTP/SEDERAJAT',
                'SLTA/SEDERAJAT',
                'DIPLOMA I/II',
                'AKADEMI/DIPLOMA III/S. MUDA',
                'DIPLOMA IV/STRATA I',
                'STRATA II',
                'STRATA III'
            ]);
            $table->string('pekerjaan_suami');
            $table->enum('status_suami', [
                'BELUM KAWIN',
                'CERAI HIDUP',
                'CERAI MATI'
            ]);

            // Istri
            $table->string('nama_istri');
            $table->date('tanggal_lahir_istri');
            $table->integer('usia_istri');
            $table->enum('pendidikan_istri', [
                'TIDAK/BELUM SEKOLAH',
                'TIDAK TAMAT SD/SEDERAJAT',
                'TAMAT SD/SEDERAJAT',
                'SLTP/SEDERAJAT',
                'SLTA/SEDERAJAT',
                'DIPLOMA I/II',
                'AKADEMI/DIPLOMA III/S. MUDA',
                'DIPLOMA IV/STRATA I',
                'STRATA II',
                'STRATA III'
            ]);
            $table->enum('status_istri', [
                'BELUM KAWIN',
                'CERAI HIDUP',
                'CERAI MATI'
            ]);
            $table->string('pekerjaan_istri');

            // Data pernikahan
            $table->date('tanggal_akad');
            $table->string('nama_kelurahan');

            // Timestamps
            $table->timestamps(); // created_at dan updated_at otomatis
        });
    }

    public function down()
    {
        Schema::dropIfExists('pernikahan');
    }
};
