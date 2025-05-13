<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //membuat data pengguna
        for ($i = 1; $i <= 10; $i++) {
            Pengguna::create([
                'nama' => 'User ' . $i,
                'username' => 'user' . $i . '@example.com',
                'password' => Hash::make('password' . $i),
                'role' => $i == 1 ? 'admin' : ($i == 2 ? 'kepala kua' : 'penyuluh'), // Assign role based on $i
                'alamat' => 'Alamat ' . $i,  // Memberikan alamat Alamat 1, Alamat 2, dst.
                'foto' => 'user' . $i . '.jpg',  // Memberikan nama file foto user1.jpg, user2.jpg, dst.
            ]);
        }
    }
}
