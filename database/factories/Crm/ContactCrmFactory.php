<?php

namespace Database\Factories\Crm;

use App\Models\Crm\ContactCrm;
use App\Models\Crm\LeadCrm;
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
            'lead_id' => LeadCrm::inRandomOrder()->first()->id,
            'notas' => $this->faker->paragraph(),
            'ultima_fecha_contacto' => $this->faker->dateTimeThisMonth(),
            'es_principal' => $this->faker->boolean()
        ];
    }
}
