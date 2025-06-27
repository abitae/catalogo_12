<?php

namespace Database\Factories\Crm;

use App\Models\Crm\ContactCrm;
use App\Models\Shared\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactCrmFactory extends Factory
{
    protected $model = ContactCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'correo' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'cargo' => $this->faker->jobTitle(),
            'empresa' => $this->faker->company(),
            'ultima_fecha_contacto' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'notas' => $this->faker->optional()->paragraph(),
            'es_principal' => $this->faker->boolean(30), // 30% probabilidad de ser principal
            'customer_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
        ];
    }

    public function principal()
    {
        return $this->state(function (array $attributes) {
            return [
                'es_principal' => true,
            ];
        });
    }

    public function secundario()
    {
        return $this->state(function (array $attributes) {
            return [
                'es_principal' => false,
            ];
        });
    }

    public function conNotas()
    {
        return $this->state(function (array $attributes) {
            return [
                'notas' => $this->faker->paragraph(),
            ];
        });
    }

    public function reciente()
    {
        return $this->state(function (array $attributes) {
            return [
                'ultima_fecha_contacto' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }
}
