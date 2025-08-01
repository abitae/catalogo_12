<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Invoice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\InvoiceDetail>
 */
class InvoiceDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cantidad = $this->faker->randomFloat(2, 1, 100);
        $mtoValorUnitario = $this->faker->randomFloat(2, 10, 1000);
        $mtoValorVenta = $cantidad * $mtoValorUnitario;
        $descuento = $this->faker->randomFloat(2, 0, $mtoValorVenta * 0.1); // Máximo 10% de descuento
        $mtoBaseIgv = $mtoValorVenta - $descuento;
        $porcentajeIgv = 18.00;
        $igv = $mtoBaseIgv * ($porcentajeIgv / 100);
        $totalImpuestos = $igv;
        $mtoPrecioUnitario = $mtoValorUnitario + ($igv / $cantidad);

        return [
            'invoice_id' => Invoice::inRandomOrder()->first()?->id ?? Invoice::factory(),
            'unidad' => $this->faker->randomElement(['NIU', 'ZZ', 'KGM', 'LTR', 'MTR']), // Unidades de medida SUNAT
            'cantidad' => $cantidad,
            'codProducto' => $this->faker->optional()->numerify('PROD-#####'),
            'codProdSunat' => $this->faker->optional()->numerify('SUNAT-#####'),
            'codProdGS1' => $this->faker->optional()->numerify('##########'),
            'descripcion' => $this->faker->sentence(3),
            'tipAfeIgv' => $this->faker->randomElement(['10', '20', '30', '40']), // Gravado, Exonerado, Inafecto, Exportación
            'mtoValorUnitario' => $mtoValorUnitario,
            'mtoValorVenta' => $mtoValorVenta,
            'descuento' => $descuento,
            'mtoBaseIgv' => $mtoBaseIgv,
            'totalImpuestos' => $totalImpuestos,
            'porcentajeIgv' => $porcentajeIgv,
            'igv' => $igv,
            'mtoPrecioUnitario' => $mtoPrecioUnitario,
        ];
    }
}
