<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\MovimientoAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovimientoAlmacenFactory extends Factory
{
    protected $model = MovimientoAlmacen::class;

    public function definition()
    {
        $cantidad = $this->faker->randomFloat(2, 1, 50);
        $valorUnitario = $this->faker->randomFloat(2, 10, 1000);
        $valorTotal = $cantidad * $valorUnitario;

        return [
            'code' => 'MOV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'tipo_movimiento' => $this->faker->randomElement(['entrada', 'salida']),
            'almacen_id' => WarehouseAlmacen::inRandomOrder()->first()->id,
            'producto_id' => ProductoAlmacen::inRandomOrder()->first()->id,
            'cantidad' => $cantidad,
            'fecha_movimiento' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'motivo' => $this->faker->sentence,
            'documento_referencia' => $this->faker->optional()->bothify('DOC-####-???'),
            'estado' => $this->faker->randomElement(['pendiente', 'completado', 'cancelado']),
            'observaciones' => $this->faker->sentence,
            'usuario_id' => User::factory(),
            'valor_unitario' => $valorUnitario,
            'valor_total' => $valorTotal
        ];
    }
}
