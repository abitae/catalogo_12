<?php

namespace Database\Factories\Crm;

use App\Models\Crm\ActivityCrm;
use App\Models\Crm\LeadCrm;
use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\DealCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityCrmFactory extends Factory
{
    protected $model = ActivityCrm::class;

    public function definition()
    {
        return [
            'tipo' => $this->faker->randomElement(['llamada', 'reunion', 'email', 'tarea']),
            'asunto' => $this->faker->sentence(),
            'descripcion' => $this->faker->paragraph(),
            'fecha_vencimiento' => $this->faker->dateTimeBetween('now', '+1 week'),
            'estado' => $this->faker->randomElement(['pendiente', 'completada', 'cancelada']),
            'prioridad' => $this->faker->randomElement(['baja', 'normal', 'alta', 'urgente']),
            'lead_id' => LeadCrm::inRandomOrder()->first()->id,
            'opportunity_id' => OpportunityCrm::inRandomOrder()->first()->id,
            'deal_id' => DealCrm::inRandomOrder()->first()->id,
            'asignado_a' => $this->faker->numberBetween(1, 10),
            'fecha_completado' => $this->faker->optional()->dateTimeThisMonth()
        ];
    }
}
