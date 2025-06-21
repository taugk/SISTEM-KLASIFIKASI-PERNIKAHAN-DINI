<?php

namespace Database\Factories;

use App\Models\DataPernikahan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HasilKlasifikasi>
 */
class HasilKlasifikasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pernikahan' => DataPernikahan::factory(),
            'kategori_pernikahan' => $this->faker->randomElement(['Pernikahan Dini', 'Bukan Pernikahan Dini']),
            'confidence' => $this->faker->randomFloat(2, 0.7, 1.0),
            'probabilitas' => json_encode([0.3, 0.7]),
            'akurasi' => $this->faker->randomFloat(2, 0.8, 1.0),
            'penyebab' => json_encode(['pendidikan rendah']),
            'dampak' => json_encode(['ekonomi menurun']),
        ];
    }
}
