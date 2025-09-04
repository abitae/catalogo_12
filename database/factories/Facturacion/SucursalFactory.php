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
            'name' => $this->faker->company(),
            'ruc' => $this->faker->unique()->numerify('20#########'),
            'razonSocial' => $this->faker->company(),
            'nombreComercial' => $this->faker->companySuffix(),
            'email' => $this->faker->unique()->companyEmail(),
            'telephone' => $this->faker->phoneNumber(),
            'address_id' => \App\Models\Facturacion\Address::inRandomOrder()->first()?->id ?? \App\Models\Facturacion\Address::factory(),
            'company_id' => \App\Models\Facturacion\Company::inRandomOrder()->first()?->id ?? \App\Models\Facturacion\Company::factory(),
            'isActive' => $this->faker->boolean(80), // 80% probabilidad de estar activo
            'logo_path' => $this->faker->optional()->imageUrl(200, 200, 'business'),
            'series_suffix' => $this->faker->optional()->numerify('##'), // 2 dígitos del 01 al 99
            'codigoSunat' => $this->faker->optional()->numerify('####'), // 4 dígitos del 0000 al 9999
        ];
    }
}
