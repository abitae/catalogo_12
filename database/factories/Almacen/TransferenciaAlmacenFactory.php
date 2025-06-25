<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\TransferenciaAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferenciaAlmacenFactory extends Factory
{
    protected $model = TransferenciaAlmacen::class;

    public function definition()
    {
        $almacenOrigen = WarehouseAlmacen::inRandomOrder()->first();
        $almacenDestino = WarehouseAlmacen::inRandomOrder()->first();
        $productos = ProductoAlmacen::inRandomOrder()->limit(3)->get();

        return [
            'code' => 'TRF-' . $this->faker->unique()->numberBetween(1000, 9999),
            'almacen_origen_id' => $almacenOrigen->id,
            'almacen_destino_id' => $almacenDestino->id,
            'productos' => $productos->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'code' => $producto->code,
                    'nombre' => $producto->nombre,
                    'cantidad' => $this->faker->numberBetween(1, 10),
                    'unidad_medida' => $producto->unidad_medida
                ];
            })->toArray(),
            'fecha_transferencia' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'estado' => $this->faker->randomElement(['pendiente', 'completada', 'cancelada']),
            'observaciones' => $this->faker->optional()->sentence(),
            'usuario_id' => User::factory(),
            'fecha_confirmacion' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'motivo_transferencia' => $this->faker->sentence
        ];
    }
}
