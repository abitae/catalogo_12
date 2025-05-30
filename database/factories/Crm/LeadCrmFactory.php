<?php

namespace Database\Factories\Crm;

use App\Models\Crm\LeadCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadCrmFactory extends Factory
{
    protected $model = LeadCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->name(),
            'correo' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'empresa' => $this->faker->company(),
            'estado' => $this->faker->randomElement(['nuevo', 'en_proceso', 'calificado', 'perdido']),
            'origen' => $this->faker->randomElement(['web', 'referido', 'evento', 'redes_sociales']),
            'notas' => $this->faker->paragraph(),
            'asignado_a' => $this->faker->numberBetween(1, 10),
            'ultima_fecha_contacto' => $this->faker->dateTimeThisMonth()
        ];
    }
}
