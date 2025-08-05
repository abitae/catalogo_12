<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\SunatBienDetraccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\SunatBienDetraccion>
 */
class SunatBienDetraccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->numerify('###'),
            'descripcion' => $this->faker->sentence(4),
            'porcentaje' => $this->faker->randomFloat(2, 1, 10),
            'isActive' => true,
        ];
    }
} 