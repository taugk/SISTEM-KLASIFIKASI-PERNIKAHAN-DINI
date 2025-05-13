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
        Schema::rename('resiko__wilayah', 'resiko_wilayah');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('resiko_wilayah', 'resiko__wilayah');
    }
};
