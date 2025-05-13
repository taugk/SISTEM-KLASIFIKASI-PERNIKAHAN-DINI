<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PernikahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pernikahan')->insert([
            [
                // Suami
                'nama_suami' => 'John Doe',
                'tanggal_lahir_suami' => '1990-01-01',
                'usia_suami' => 35,
                'pendidikan_suami' => 'STRATA I', // Ensure this matches exactly
                'pekerjaan_suami' => 'Engineer',
                'status_suami' => 'BELUM KAWIN',

                // Istri
                'nama_istri' => 'Jane Smith',
                'tanggal_lahir_istri' => '1992-02-15',
                'usia_istri' => 33,
                'pendidikan_istri' => 'DIPLOMA IV/STRATA I', // Ensure this matches exactly
                'status_istri' => 'BELUM KAWIN',
                'pekerjaan_istri' => 'Teacher',

                // Data pernikahan
                'tanggal_akad' => Carbon::now()->toDateString(),
                'nama_kelurahan' => 'Kelurahan A',

                // Timestamps
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // Suami
                'nama_suami' => 'Muhammad Rizki',
                'tanggal_lahir_suami' => '1988-04-21',
                'usia_suami' => 36,
                'pendidikan_suami' => 'TAMAT SD/SEDERAJAT', // Ensure this matches exactly
                'pekerjaan_suami' => 'Farmer',
                'status_suami' => 'CERAI HIDUP',

                // Istri
                'nama_istri' => 'Siti Zainab',
                'tanggal_lahir_istri' => '1990-06-30',
                'usia_istri' => 34,
                'pendidikan_istri' => 'SLTA/SEDERAJAT', // Ensure this matches exactly
                'status_istri' => 'CERAI HIDUP',
                'pekerjaan_istri' => 'Housewife',

                // Data pernikahan
                'tanggal_akad' => Carbon::now()->toDateString(),
                'nama_kelurahan' => 'Kelurahan B',

                // Timestamps
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
