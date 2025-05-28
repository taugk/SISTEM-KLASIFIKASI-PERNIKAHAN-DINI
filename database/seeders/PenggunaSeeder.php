<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Pengguna::create([
                'nama' => 'User ' . $i,
                'username' => 'user' . $i,
                'password' => Hash::make('password' . $i), // password1, password2, dst.
                'role' => match ($i) {
                    1 => 'admin',
                    2 => 'kepala kua',
                    default => 'penyuluh',
                },
                'alamat' => 'Alamat ' . $i,
                'foto' => 'user' . $i . '.jpg',
            ]);
        }
    }
}

