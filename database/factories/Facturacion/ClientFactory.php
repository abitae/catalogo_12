<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Address;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $address = Address::factory()->create();
        return [
            'tipoDoc' => $this->faker->randomElement(['RUC', 'DNI']),
            'numDoc' => $this->faker->unique()->numerify('###########'),
            'rznSocial' => $this->faker->company,
            'email' => $this->faker->email,
            'telephone' => $this->faker->phoneNumber,
            'address_id' => $address->id,
        ];
    }
}
