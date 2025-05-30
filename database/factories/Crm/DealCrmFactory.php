<?php

namespace Database\Factories\Crm;

use App\Models\Crm\DealCrm;
use App\Models\Crm\OpportunityCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealCrmFactory extends Factory
{
    protected $model = DealCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'opportunity_id' => OpportunityCrm::inRandomOrder()->first()->id,
            'valor' => $this->faker->randomFloat(2, 1000, 100000),
            'etapa' => $this->faker->randomElement(['inicial', 'negociacion', 'propuesta', 'cierre']),
            'fecha_cierre' => $this->faker->dateTimeBetween('now', '+3 months'),
            'descripcion' => $this->faker->paragraph(),
            'terminos' => $this->faker->paragraph(),
            'asignado_a' => $this->faker->numberBetween(1, 10),
            'estado' => $this->faker->randomElement(['activo', 'cerrado', 'perdido']),
            'probabilidad' => $this->faker->numberBetween(0, 100),
            'ingreso_esperado' => $this->faker->randomFloat(2, 1000, 100000)
        ];
    }
}
