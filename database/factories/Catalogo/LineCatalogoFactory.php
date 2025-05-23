<?php

namespace Database\Factories\Catalogo;

use App\Models\Catalogo\LineCatalogo;
use Illuminate\Database\Eloquent\Factories\Factory;

class LineCatalogoFactory extends Factory
{
    protected $model = LineCatalogo::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('LINE-####'),
            'name' => $this->faker->unique()->words(2, true),
            'logo' => $this->faker->imageUrl(200, 200, 'business'),
            'fondo' => $this->faker->imageUrl(800, 600, 'business'),
            'archivo' => $this->faker->imageUrl(800, 600, 'business'),
            'isActive' => $this->faker->boolean(80), // 80% de probabilidad de estar activo
        ];
    }
}
