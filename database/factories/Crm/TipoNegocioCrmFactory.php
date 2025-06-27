<?php

namespace Database\Factories\Crm;

use App\Models\Crm\TipoNegocioCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoNegocioCrmFactory extends Factory
{
    protected $model = TipoNegocioCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->company(),
            'codigo' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{2}'),
            'descripcion' => $this->faker->sentence(),
            'activo' => $this->faker->boolean(80) // 80% probabilidad de estar activo
        ];
    }

    public function activo()
    {
        return $this->state(function (array $attributes) {
            return [
                'activo' => true,
            ];
        });
    }

    public function inactivo()
    {
        return $this->state(function (array $attributes) {
            return [
                'activo' => false,
            ];
        });
    }
}
