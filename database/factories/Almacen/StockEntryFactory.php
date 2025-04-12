<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\ProductAlmacen;
use App\Models\Almacen\StockEntry;
use App\Models\Almacen\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockEntryFactory extends Factory
{
    protected $model = StockEntry::class;

    public function definition(): array
    {
        return [
            'product_id' => ProductAlmacen::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'entry_date' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
