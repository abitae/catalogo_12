<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\SunatUnidadMedida;
use Illuminate\Database\Eloquent\Factories\Factory;

class SunatUnidadMedidaFactory extends Factory
{
    protected $model = SunatUnidadMedida::class;

    public function definition()
    {
        return [
            'codigo' => $this->faker->unique()->regexify('[A-Z]{2,4}'),
            'descripcion' => $this->faker->sentence(3),
        ];
    }
} 