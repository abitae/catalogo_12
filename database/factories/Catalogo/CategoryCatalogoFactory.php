<?php

namespace Database\Factories\Catalogo;

use App\Models\Catalogo\CategoryCatalogo;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryCatalogoFactory extends Factory
{
    protected $model = CategoryCatalogo::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'logo' => $this->faker->imageUrl(200, 200, 'business'),
            'fondo' => $this->faker->imageUrl(800, 600, 'business'),
            'archivo' => $this->faker->imageUrl(800, 600, 'business'),
            'isActive' => $this->faker->boolean(80), // 80% de probabilidad de estar activo
        ];
    }
}
