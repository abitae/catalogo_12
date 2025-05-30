<?php

namespace Database\Factories\Crm;

use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\TipeNegocioCrm;
use App\Models\Crm\MarcaCrm;
use App\Models\Crm\LeadCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpportunityCrmFactory extends Factory
{
    protected $model = OpportunityCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'estado' => $this->faker->randomElement(['nueva', 'en_proceso', 'ganada', 'perdida']),
            'tipo_negocio_id' => TipeNegocioCrm::inRandomOrder()->first()->id,
            'marca_id' => MarcaCrm::inRandomOrder()->first()->id,
            'lead_id' => LeadCrm::inRandomOrder()->first()->id,
            'valor' => $this->faker->randomFloat(2, 1000, 100000),
            'etapa' => $this->faker->randomElement(['inicial', 'negociacion', 'propuesta', 'cierre']),
            'probabilidad' => $this->faker->numberBetween(0, 100),
            'fecha_cierre_esperada' => $this->faker->dateTimeBetween('now', '+3 months'),
            'descripcion' => $this->faker->paragraph(),
            'asignado_a' => $this->faker->numberBetween(1, 10),
            'ultima_fecha_actividad' => $this->faker->dateTimeThisMonth()
        ];
    }
}
