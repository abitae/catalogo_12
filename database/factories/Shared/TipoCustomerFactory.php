<?php

namespace Database\Factories\Shared;

use App\Models\Shared\TipoCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shared\TipoCustomer>
 */
class TipoCustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement(['Cliente Regular', 'Cliente VIP', 'Cliente Corporativo', 'Cliente Minorista', 'Cliente Mayorista']),
            'descripcion' => $this->faker->optional()->sentence(),
        ];
    }
}
