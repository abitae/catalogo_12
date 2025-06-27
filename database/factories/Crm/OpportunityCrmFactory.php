<?php

namespace Database\Factories\Crm;

use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\MarcaCrm;
use App\Models\Crm\TipoNegocioCrm;
use App\Models\Shared\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpportunityCrmFactory extends Factory
{
    protected $model = OpportunityCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'estado' => $this->faker->randomElement(['nueva', 'en_proceso', 'ganada', 'perdida']),
            'valor' => $this->faker->randomFloat(2, 1000, 100000),
            'etapa' => $this->faker->randomElement(['inicial', 'negociacion', 'propuesta', 'cierre']),
            'probabilidad' => $this->faker->numberBetween(0, 100),
            'fecha_cierre_esperada' => $this->faker->dateTimeBetween('now', '+3 months'),
            'descripcion' => $this->faker->paragraph(),
            'tipo_negocio_id' => TipoNegocioCrm::activos()->inRandomOrder()->first()?->id ?? TipoNegocioCrm::factory(),
            'marca_id' => MarcaCrm::activas()->inRandomOrder()->first()?->id ?? MarcaCrm::factory(),
            'customer_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    public function nueva()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'nueva',
                'etapa' => 'inicial',
                'probabilidad' => $this->faker->numberBetween(10, 30),
            ];
        });
    }

    public function enProceso()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'en_proceso',
                'etapa' => 'negociacion',
                'probabilidad' => $this->faker->numberBetween(40, 70),
            ];
        });
    }

    public function ganada()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'ganada',
                'etapa' => 'cierre',
                'probabilidad' => 100,
            ];
        });
    }

    public function perdida()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'perdida',
                'etapa' => 'cierre',
                'probabilidad' => 0,
            ];
        });
    }
}
