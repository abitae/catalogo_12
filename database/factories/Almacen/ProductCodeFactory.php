<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\ProductAlmacen;
use App\Models\Almacen\ProductCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCodeFactory extends Factory
{
    protected $model = ProductCode::class;

    public function definition(): array
    {
        return [
            'product_id' => ProductAlmacen::factory(),
            'output_code' => $this->faker->unique()->uuid
        ];
    }
}
