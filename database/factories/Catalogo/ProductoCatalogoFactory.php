<?php

namespace Database\Factories\Catalogo;

use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoCatalogoFactory extends Factory
{
    protected $model = ProductoCatalogo::class;

    public function definition(): array
    {
        return [
            'brand_id' => BrandCatalogo::inRandomOrder()->first()?->id ?? BrandCatalogo::factory(),
            'category_id' => CategoryCatalogo::inRandomOrder()->first()?->id ?? CategoryCatalogo::factory(),
            'line_id' => LineCatalogo::inRandomOrder()->first()?->id ?? LineCatalogo::factory(),
            'code' => $this->faker->unique()->bothify('PROD-####'),
            'code_fabrica' => $this->faker->unique()->bothify('FAB-####'),
            'code_peru' => $this->faker->unique()->bothify('PER-####'),
            'price_compra' => $this->faker->randomFloat(2, 10, 1000),
            'price_venta' => $this->faker->randomFloat(2, 20, 2000),
            'stock' => $this->faker->numberBetween(0, 100),
            'dias_entrega' => $this->faker->numberBetween(1, 30),
            'description' => $this->faker->paragraph(),
            'garantia' => $this->faker->randomElement(['6 meses', '1 año', '2 años', 'Sin garantía']),
            'observaciones' => $this->faker->optional()->paragraph(),
            'image' => $this->faker->imageUrl(400, 400, 'product'),
            'archivo' => $this->faker->imageUrl(800, 600, 'product'),
            'archivo2' => $this->faker->optional()->imageUrl(800, 600, 'product'),
            'caracteristicas' => json_encode([
                'color' => $this->faker->colorName(),
                'material' => $this->faker->word(),
                'dimensiones' => $this->faker->randomElement(['10x20x30', '15x25x35', '20x30x40']),
                'peso' => $this->faker->randomFloat(2, 0.1, 10) . ' kg'
            ]),
            'isActive' => $this->faker->boolean(80), // 80% de probabilidad de estar activo
        ];
    }
}
