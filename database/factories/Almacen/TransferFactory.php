<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\ProductAlmacen;
use App\Models\Almacen\Transfer;
use App\Models\Almacen\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        return [
            'product_id' => ProductAlmacen::factory(),
            'from_warehouse_id' => Warehouse::factory(),
            'to_warehouse_id' => Warehouse::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'transfer_date' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
