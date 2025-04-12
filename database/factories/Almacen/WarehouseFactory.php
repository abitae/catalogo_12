<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company,
            'location' => $this->faker->address
        ];
    }
}
