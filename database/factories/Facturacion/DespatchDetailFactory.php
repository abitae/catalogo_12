<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Despatch;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\DespatchDetail>
 */
class DespatchDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cantidad = $this->faker->randomFloat(2, 1, 100);
        $pesoBruto = $this->faker->randomFloat(2, 0.1, 50);
        $pesoNeto = $pesoBruto * 0.95; // El peso neto suele ser menor al bruto

        return [
            'despatch_id' => Despatch::inRandomOrder()->first()?->id ?? Despatch::factory(),
            'unidad' => $this->faker->randomElement(['NIU', 'KGM', 'LTR', 'MTR', 'UND', 'PAR', 'DOC', 'ROL', 'GLB', 'BAL', 'CJ', 'BLK', 'BLL', 'BOT', 'CAJ', 'CUB', 'CUC', 'CUP', 'CYL', 'DOC', 'FAR', 'GAL', 'GRM', 'HOR', 'JGO', 'KIT', 'LOT', 'MAL', 'MIL', 'MIN', 'MLT', 'MMT', 'MNT', 'MTK', 'MTQ', 'ONZ', 'PAQ', 'PIE', 'PUL', 'SET', 'TAB', 'TAL', 'TON', 'VIA', 'YRD']),
            'cantidad' => $cantidad,
            'codProducto' => $this->faker->regexify('[A-Z]{2}[0-9]{6}'),
            'codProdSunat' => $this->faker->optional()->regexify('[0-9]{8}'),
            'codProdGS1' => $this->faker->optional()->regexify('[0-9]{13}'),
            'descripcion' => $this->faker->sentence(6),
            'pesoBruto' => $pesoBruto,
            'pesoNeto' => $pesoNeto,
            'codLote' => $this->faker->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'fechaVencimiento' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d') : null,
            'codigoUnidadMedida' => $this->faker->optional()->regexify('[A-Z]{3}'),
            'codigoProductoSUNAT' => $this->faker->optional()->regexify('[0-9]{8}'),
            'codigoProductoGS1' => $this->faker->optional()->regexify('[0-9]{13}'),
        ];
    }
}
