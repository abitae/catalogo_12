<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\SunatMedioPago;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\SunatMedioPago>
 */
class SunatMedioPagoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->numerify('##'),
            'descripcion' => $this->faker->sentence(2),
            'isActive' => true,
        ];
    }
} 