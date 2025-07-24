<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inicioSus = Carbon::instance($this->faker->dateTimeBetween('-2 years', 'now'));
        $finSus = (clone $inicioSus)->addMonths($this->faker->numberBetween(1, 24));
        $inicioProd = Carbon::instance($this->faker->dateTimeBetween($inicioSus, $finSus));
        $finProd = (clone $inicioProd)->addMonths($this->faker->numberBetween(1, 12));
        return [
            'ruc' => $this->faker->unique()->numerify('20#########'),
            'razonSocial' => $this->faker->company(),
            'nombreComercial' => $this->faker->companySuffix(),
            'email' => $this->faker->unique()->companyEmail(),
            'telephone' => $this->faker->phoneNumber(),
            'address_id' => \App\Models\Facturacion\Address::factory(),
            'ctaBanco' => $this->faker->bankAccountNumber(),
            'nroMtc' => $this->faker->bothify('MTC-####'),
            'logo_path' => $this->faker->imageUrl(200, 200, 'business', true, 'logo'),
            'sol_user' => $this->faker->userName(),
            'sol_pass' => $this->faker->password(8, 16),
            'cert_path' => $this->faker->filePath(),
            'client_id' => $this->faker->uuid(),
            'client_secret' => $this->faker->sha256(),
            'isProduction' => $this->faker->boolean(),
            'isActive' => $this->faker->boolean(90),
            'inicio_suscripcion' => $inicioSus,
            'fin_suscripcion' => $finSus,
            'inicio_produccion' => $inicioProd,
            'fin_produccion' => $finProd,
        ];
    }
}
