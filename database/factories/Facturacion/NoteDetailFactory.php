<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Note;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\NoteDetail>
 */
class NoteDetailFactory extends Factory
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
        $igv = $mtoBaseIgv * 0.18;
        $totalImpuestos = $igv;
        $mtoPrecioUnitario = $mtoValorUnitario * 1.18; // Precio con IGV

        return [
            'note_id' => Note::inRandomOrder()->first()?->id ?? Note::factory(),
            'unidad' => $this->faker->randomElement(['NIU', 'KGM', 'LTR', 'MTR', 'UND', 'PAR', 'DOC', 'ROL', 'GLB', 'BAL', 'CJ', 'BLK', 'BLL', 'BOT', 'CAJ', 'CUB', 'CUC', 'CUP', 'CYL', 'DOC', 'FAR', 'GAL', 'GRM', 'HOR', 'JGO', 'KIT', 'LOT', 'MAL', 'MIL', 'MIN', 'MLT', 'MMT', 'MNT', 'MTK', 'MTQ', 'ONZ', 'PAQ', 'PIE', 'PUL', 'SET', 'TAB', 'TAL', 'TON', 'VIA', 'YRD']),
            'cantidad' => $cantidad,
            'codProducto' => $this->faker->regexify('[A-Z]{2}[0-9]{6}'),
            'codProdSunat' => $this->faker->optional()->regexify('[0-9]{8}'),
            'codProdGS1' => $this->faker->optional()->regexify('[0-9]{13}'),
            'descripcion' => $this->faker->sentence(6),
            'tipAfeIgv' => $this->faker->randomElement(['10', '20', '30', '40']), // 10: Gravado, 20: Exonerado, 30: Inafecto, 40: Exportación
            'mtoValorUnitario' => $mtoValorUnitario,
            'mtoValorVenta' => $mtoValorVenta,
            'descuento' => $descuento,
            'mtoBaseIgv' => $mtoBaseIgv,
            'totalImpuestos' => $totalImpuestos,
            'porcentajeIgv' => 18.00,
            'igv' => $igv,
            'mtoPrecioUnitario' => $mtoPrecioUnitario,
        ];
    }
}
