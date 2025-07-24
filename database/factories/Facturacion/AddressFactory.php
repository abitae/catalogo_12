<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ubigueo' => $this->faker->postcode(),
            'codigoPais' => $this->faker->countryCode(),
            'departamento' => $this->faker->state(),
            'provincia' => $this->faker->citySuffix(),
            'distrito' => $this->faker->city(),
            'urbanizacion' => $this->faker->streetSuffix(),
            'direccion' => $this->faker->streetAddress(),
            'codLocal' => $this->faker->bothify('LOC-####'),
        ];
    }
}
