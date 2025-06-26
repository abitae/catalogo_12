<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\MovimientoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovimientoAlmacenFactory extends Factory
{
    protected $model = MovimientoAlmacen::class;

    public function definition()
    {
        $tipo_movimiento = $this->faker->randomElement(['entrada', 'salida']);
        $tipo_documento = $this->faker->randomElement(['factura', 'boleta', 'nota_credito', 'nota_debito', 'guia_remision', 'nota_de_venta']);
        $tipo_operacion = $this->faker->randomElement(['compra', 'venta', 'ajuste', 'transferencia', 'devolucion']);
        $tipo_pago = $this->faker->randomElement(['efectivo', 'tarjeta', 'transferencia', 'cheque']);
        $forma_pago = $this->faker->randomElement(['contado', 'credito']);
        $tipo_moneda = $this->faker->randomElement(['PEN', 'USD', 'EUR']);
        $estado = $this->faker->randomElement(['pendiente', 'completado', 'cancelado']);

        // Obtener productos reales de la base de datos
        $productos = ProductoAlmacen::inRandomOrder()->limit($this->faker->numberBetween(1, 5))->get();

        // Generar productos de ejemplo con lotes
        $productosArray = [];
        foreach ($productos as $producto) {
            $cantidad = $this->faker->randomFloat(2, 1, 50);
            $precio = $producto->precio_unitario;

            $productosArray[] = [
                'id' => $producto->id,
                'code' => $producto->code,
                'nombre' => $producto->nombre,
                'cantidad' => $cantidad,
                'precio' => $precio,
                'unidad_medida' => $producto->unidad_medida,
                'lote' => $producto->lote ?? 'LOTE-' . $this->faker->unique()->numberBetween(1000, 9999) . '-' . $this->faker->date('Y')
            ];
        }

        // Calcular totales
        $subtotal = collect($productosArray)->sum(function($producto) {
            return $producto['cantidad'] * $producto['precio'];
        });
        $descuento = $this->faker->randomFloat(2, 0, $subtotal * 0.1); // Máximo 10% de descuento
        $impuesto = ($subtotal - $descuento) * 0.18; // IGV 18%
        $total = $subtotal - $descuento + $impuesto;

        // Fecha de emisión
        $fecha_emision = $this->faker->dateTimeBetween('-1 month', 'now');

        // Fecha de vencimiento solo si es crédito
        $fecha_vencimiento = null;
        if ($forma_pago === 'credito') {
            $fecha_vencimiento = $this->faker->dateTimeBetween($fecha_emision, '+3 months');
        }

        // Seleccionar un producto aleatorio para los campos específicos
        $productoSeleccionado = $productos->first();

        return [
            'code' => 'MOV' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT) . '-' . str_pad($this->faker->numberBetween(1, 99), 2, '0', STR_PAD_LEFT),
            'tipo_movimiento' => $tipo_movimiento,
            'almacen_id' => WarehouseAlmacen::inRandomOrder()->first()->id,
            'producto_id' => $productoSeleccionado->id,
            'lote' => $productoSeleccionado->lote ?? 'LOTE-' . $this->faker->unique()->numberBetween(1000, 9999) . '-' . $this->faker->date('Y'),
            'cantidad' => $this->faker->randomFloat(2, 1, 50),
            'fecha_movimiento' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'motivo' => $this->faker->sentence,
            'documento_referencia' => $this->faker->bothify('DOC-####-???'),
            'estado' => $estado,
            'observaciones' => $this->faker->optional(0.8)->sentence,
            'usuario_id' => User::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'tipo_pago' => $tipo_pago,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $this->faker->bothify('DOC-####-???'),
            'tipo_operacion' => $tipo_operacion,
            'forma_pago' => $forma_pago,
            'tipo_moneda' => $tipo_moneda,
            'fecha_emision' => $fecha_emision,
            'fecha_vencimiento' => $fecha_vencimiento,
            'productos' => $productosArray,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'impuesto' => $impuesto,
            'total' => $total
        ];
    }
}
