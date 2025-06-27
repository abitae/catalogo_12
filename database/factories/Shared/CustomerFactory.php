<?php

namespace Database\Factories\Shared;

use App\Models\Shared\Customer;
use App\Models\Shared\TipoCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shared\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipoDoc = $this->faker->randomElement(['DNI', 'RUC', 'CE', 'PAS']);

        // Generar número de documento según el tipo
        $numDoc = match($tipoDoc) {
            'DNI' => $this->faker->numerify('########'),
            'RUC' => $this->faker->numerify('###########'),
            'CE' => $this->faker->numerify('########'),
            'PAS' => $this->faker->bothify('??########'),
            default => $this->faker->numerify('##########'),
        };

        return [
            'tipoDoc' => $tipoDoc,
            'numDoc' => $this->faker->unique()->numerify($numDoc),
            'rznSocial' => $this->faker->company(),
            'nombreComercial' => $this->faker->optional(0.7)->company(),
            'email' => $this->faker->optional(0.8)->safeEmail(),
            'telefono' => $this->faker->optional(0.8)->phoneNumber(),
            'direccion' => $this->faker->optional(0.6)->address(),
            'codigoPostal' => $this->faker->optional(0.5)->postcode(),
            'image' => null, // No generar imágenes en factory
            'archivo' => null, // No generar archivos en factory
            'notas' => $this->faker->optional(0.3)->paragraph(),
            'tipo_customer_id' => TipoCustomer::inRandomOrder()->first()?->id,
        ];
    }

    /**
     * Indica que el cliente es corporativo
     */
    public function corporativo(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_customer_id' => TipoCustomer::where('nombre', 'Cliente Corporativo')->first()?->id,
            'rznSocial' => $this->faker->company() . ' S.A.',
        ]);
    }

    /**
     * Indica que el cliente es VIP
     */
    public function vip(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_customer_id' => TipoCustomer::where('nombre', 'Cliente VIP')->first()?->id,
        ]);
    }

    /**
     * Indica que el cliente es minorista
     */
    public function minorista(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_customer_id' => TipoCustomer::where('nombre', 'Cliente Minorista')->first()?->id,
        ]);
    }

    /**
     * Indica que el cliente es mayorista
     */
    public function mayorista(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_customer_id' => TipoCustomer::where('nombre', 'Cliente Mayorista')->first()?->id,
        ]);
    }
}
