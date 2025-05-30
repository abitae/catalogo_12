<?php

namespace Database\Factories\Crm;

use App\Models\Crm\MarcaCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarcaCrmFactory extends Factory
{
    protected $model = MarcaCrm::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->company(),
            'descripcion' => $this->faker->sentence(),
            'logo' => $this->faker->imageUrl(200, 200, 'business'),
            'estado' => $this->faker->randomElement(['activo', 'inactivo'])
        ];
    }
}
