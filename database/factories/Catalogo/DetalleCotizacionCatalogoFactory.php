<?php

namespace Database\Factories\Catalogo;

use App\Models\Catalogo\CotizacionCatalogo;
use App\Models\Catalogo\DetalleCotizacionCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalogo\DetalleCotizacionCatalogo>
 */
class DetalleCotizacionCatalogoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DetalleCotizacionCatalogo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cotizacion = CotizacionCatalogo::inRandomOrder()->first() ?? CotizacionCatalogo::factory()->create();
        $producto = ProductoCatalogo::inRandomOrder()->first() ?? ProductoCatalogo::factory()->create();
        $cantidad = $this->faker->numberBetween(1, 10);
        $precio_unitario = $this->faker->randomFloat(2, 10, 1000);

        return [
            'cotizacion_id' => $cotizacion->id,
            'producto_id' => $producto->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $cantidad * $precio_unitario,
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }
}
