<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDOException;

class CreateDatabase extends Command
{
    protected $signature = 'app:create-database {name?}';
    protected $description = 'Membuat database MySQL jika belum ada';

    public function handle()
    {
        // Ambil nama database dari argumen atau default .env
        $databaseName = $this->argument('name') ?? Config::get('database.connections.mysql.database');

        // Sementara set database ke null agar bisa connect ke MySQL tanpa DB
        Config::set('database.connections.mysql.database', null);

        $query = "CREATE DATABASE IF NOT EXISTS `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

        try {
            DB::statement($query);
            $this->info("✅ Database `$databaseName` berhasil dibuat atau sudah ada.");
        } catch (PDOException $e) {
            $this->error("❌ Gagal membuat database: " . $e->getMessage());
        }

        // Kembalikan config database ke semula
        Config::set('database.connections.mysql.database', $databaseName);
    }
}
