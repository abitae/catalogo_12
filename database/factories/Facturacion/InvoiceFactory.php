<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $valorVenta = $this->faker->randomFloat(2, 100, 10000);
        $mtoIGV = $valorVenta * 0.18;
        $subTotal = $valorVenta + $mtoIGV;

        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'sucursal_id' => Sucursal::inRandomOrder()->first()?->id ?? Sucursal::factory(),
            'client_id' => Client::inRandomOrder()->first()?->id ?? Client::factory(),
            'tipoDoc' => $this->faker->randomElement(['01', '03', '07', '08']), // Factura, Boleta, Nota de Crédito, Nota de Débito
            'tipoOperacion' => $this->faker->randomElement(['0101', '0102', '0103']), // Venta interna, Venta al por menor, Venta al por mayor
            'serie' => $this->faker->regexify('[A-Z]{1,4}[0-9]{1,3}'),
            'correlativo' => $this->faker->numberBetween(1, 999999),
            'fechaEmision' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'formaPago_moneda' => 'PEN',
            'formaPago_tipo' => $this->faker->randomElement(['01', '02', '03']), // Efectivo, Tarjeta, Transferencia
            'tipoMoneda' => 'PEN',
            'mtoOperGravadas' => $valorVenta,
            'mtoIGV' => $mtoIGV,
            'totalImpuestos' => $mtoIGV,
            'valorVenta' => $valorVenta,
            'subTotal' => $subTotal,
            'mtoImpVenta' => $subTotal,
            'monto_letras' => $this->faker->words(3, true),
            'codBienDetraccion' => $this->faker->optional()->randomElement(['001', '002', '003']),
            'codMedioPago' => $this->faker->optional()->randomElement(['001', '002', '003']),
            'ctaBanco' => $this->faker->optional()->numerify('##########'),
            'setPercent' => $this->faker->optional()->randomFloat(2, 0, 10),
            'setMount' => $this->faker->optional()->randomFloat(2, 0, 1000),
            'observacion' => $this->faker->optional()->sentence(),
            'legends' => json_encode([
                ['code' => '1000', 'value' => 'CIENTO VEINTE Y 00/100 SOLES']
            ]),
            'note_reference' => $this->faker->optional()->sentence(),
            'xml_path' => $this->faker->optional()->filePath(),
            'xml_hash' => $this->faker->optional()->sha1(),
            'cdr_description' => $this->faker->optional()->sentence(),
            'cdr_code' => $this->faker->optional()->randomElement(['0', '98', '99']),
            'cdr_note' => $this->faker->optional()->text(),
            'cdr_path' => $this->faker->optional()->filePath(),
            'errorCode' => $this->faker->optional()->randomElement(['0000', '0001', '0002']),
            'errorMessage' => $this->faker->optional()->sentence(),
        ];
    }
}
