<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\Sucursal>
 */
class SucursalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ruc' => $this->faker->unique()->numerify('20#########'),
            'razonSocial' => $this->faker->company(),
            'nombreComercial' => $this->faker->companySuffix(),
            'email' => $this->faker->unique()->companyEmail(),
            'telephone' => $this->faker->phoneNumber(),
            'address_id' => \App\Models\Facturacion\Address::factory(),
            'company_id' => \App\Models\Facturacion\Company::inRandomOrder()->first()?->id ?? \App\Models\Facturacion\Company::factory(),
        ];
    }
}
