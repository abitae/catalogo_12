<?php

namespace Database\Factories\Crm;

use App\Models\Crm\TipeNegocioCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipeNegocioCrmFactory extends Factory
{
    protected $model = TipeNegocioCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->word(),
            'descripcion' => $this->faker->sentence(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo'])
        ];
    }
}
