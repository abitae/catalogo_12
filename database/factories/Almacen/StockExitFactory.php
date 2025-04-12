<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\ProductCode;
use App\Models\Almacen\StockExit;
use App\Models\Almacen\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockExitFactory extends Factory
{
    protected $model = StockExit::class;

    public function definition(): array
    {
        return [
            'product_code_id' => ProductCode::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'exit_date' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
