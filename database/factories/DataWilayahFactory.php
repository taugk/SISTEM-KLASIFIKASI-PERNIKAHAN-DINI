<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataWilayah>
 */
class DataWilayahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provinsi' => $this->faker->state(),
            'kabupaten' => $this->faker->city(),
            'kecamatan' => $this->faker->streetName(),
            'desa' => $this->faker->streetAddress(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
