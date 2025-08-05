<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\SunatTipoOperacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\SunatTipoOperacion>
 */
class SunatTipoOperacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->numerify('####'),
            'descripcion' => $this->faker->sentence(3),
            'isActive' => true,
        ];
    }
} 