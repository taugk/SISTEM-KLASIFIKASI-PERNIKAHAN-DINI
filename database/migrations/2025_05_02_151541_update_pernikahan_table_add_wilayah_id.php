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
        Schema::table('pernikahan', function (Blueprint $table) {

            

            $table->unsignedBigInteger('wilayah_id');
            $table->foreign('wilayah_id')->references('id')->on('data_wilayah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pernikahan', function (Blueprint $table) {

            // Menghapus kolom wilayah_id dan foreign key
            $table->dropForeign(['wilayah_id']);
            $table->dropColumn('wilayah_id');
        });
    }
};
