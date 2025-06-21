<?php

namespace Database\Factories;

use App\Models\DataWilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataPernikahan>
 */
class DataPernikahanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_suami' => $this->faker->name('male'),
            'tanggal_lahir_suami' => $this->faker->date('Y-m-d', '-20 years'),
            'usia_suami' => $this->faker->numberBetween(19, 35),
            'pendidikan_suami' => $this->faker->numberBetween(1, 5),
            'pekerjaan_suami' => $this->faker->numberBetween(1, 5),
            'status_suami' => $this->faker->randomElement(['BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI']),

            'nama_istri' => $this->faker->name('female'),
            'tanggal_lahir_istri' => $this->faker->date('Y-m-d', '-18 years'),
            'usia_istri' => $this->faker->numberBetween(17, 30),
            'pendidikan_istri' => $this->faker->numberBetween(1, 5),
            'pekerjaan_istri' => $this->faker->numberBetween(1, 5),
            'status_istri' => $this->faker->randomElement(['BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI']),

            'tanggal_akad' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'wilayah_id' => DataWilayah::factory(), // relasi langsung dengan factory wilayah
        ];
    }
}
