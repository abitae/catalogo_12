<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\ProductAlmacen;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAlmacenFactory extends Factory
{
    protected $model = ProductAlmacen::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'unique_entry_code' => $this->faker->unique()->uuid
        ];
    }
}
