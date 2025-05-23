<?php

namespace Database\Factories\Catalogo;

use App\Models\Catalogo\BrandCatalogo;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandCatalogoFactory extends Factory
{
    protected $model = BrandCatalogo::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'logo' => $this->faker->imageUrl(200, 200, 'business'),
            'archivo' => $this->faker->imageUrl(800, 600, 'business'),
            'isActive' => $this->faker->boolean(80), // 80% de probabilidad de estar activo
        ];
    }
}
