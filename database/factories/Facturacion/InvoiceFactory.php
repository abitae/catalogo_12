<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use App\Models\User;

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
        $tipoVenta = $this->faker->randomElement(['contado', 'credito']);
        $estadoPago = $this->faker->randomElement(['Cancelado', 'Por pagar', 'Parcial']);

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'sucursal_id' => Sucursal::inRandomOrder()->first()?->id ?? Sucursal::factory(),
            'client_id' => Client::inRandomOrder()->first()?->id ?? Client::factory(),
            'tipoDoc' => $this->faker->randomElement(['01', '03', '07', '08']), // Factura, Boleta, Nota de Crédito, Nota de Débito
            'tipoOperacion' => $this->faker->randomElement(['0101', '0102', '0103']), // Venta interna, Venta al por menor, Venta al por mayor
            'serie' => $this->faker->regexify('[A-Z]{1,4}[0-9]{1,3}'),
            'correlativo' => $this->faker->numberBetween(1, 999999),
            'fechaEmision' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'fechaVencimiento' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'formaPago_moneda' => 'PEN',
            'formaPago_tipo' => $this->faker->randomElement(['01', '02', '03']), // Efectivo, Tarjeta, Transferencia
            'tipoMoneda' => 'PEN',
            'estado_pago_invoice' => $estadoPago,
            'mtoOperGravadas' => $valorVenta,
            'mtoOperInafectas' => 0,
            'mtoOperExoneradas' => 0,
            'mtoOperGratuitas' => 0,
            'mtoIGV' => $mtoIGV,
            'mtoIGVGratuitas' => 0,
            'totalImpuestos' => $mtoIGV,
            'valorVenta' => $valorVenta,
            'subTotal' => $subTotal,
            'mtoImpVenta' => $subTotal,
            'monto_letras' => $this->faker->words(3, true),
            'codBienDetraccion' => $this->faker->optional()->randomElement(['001', '002', '003']),
            'codMedioPago' => $this->faker->optional()->randomElement(['01', '02', '03']),
            'ctaBanco' => $this->faker->optional()->numerify('##########'),
            'setPercent' => $this->faker->optional()->randomFloat(2, 0, 10),
            'setMount' => $this->faker->optional()->randomFloat(2, 0, 1000),
            'codReg' => $this->faker->optional()->randomElement(['01', '02', '03']),
            'porcentajePer' => $this->faker->optional()->randomFloat(2, 0, 5),
            'mtoBasePer' => $this->faker->optional()->randomFloat(2, 0, 1000),
            'mtoPer' => $this->faker->optional()->randomFloat(2, 0, 50),
            'mtoTotalPer' => $this->faker->optional()->randomFloat(2, 0, 50),
            'codRegRet' => $this->faker->optional()->randomElement(['01', '02', '03']),
            'mtoBaseRet' => $this->faker->optional()->randomFloat(2, 0, 1000),
            'factorRet' => $this->faker->optional()->randomFloat(2, 0, 1),
            'mtoRet' => $this->faker->optional()->randomFloat(2, 0, 100),
            'tipoVenta' => $tipoVenta,
            'cuotas' => function () use ($tipoVenta) {
                if ($tipoVenta === 'contado') {
                    return null;
                }

                // Generar cuotas realistas para ventas a crédito
                $numCuotas = $this->faker->numberBetween(2, 6);
                $montoTotal = $this->faker->randomFloat(2, 1000, 5000);
                $montoCuota = round($montoTotal / $numCuotas, 2);
                $cuotas = [];

                for ($i = 0; $i < $numCuotas; $i++) {
                    $cuotas[] = [
                        'monto' => $montoCuota,
                        'fecha_pago' => $this->faker->dateTimeBetween('+30 days', '+' . (30 * ($i + 1)) . ' days')->format('Y-m-d'),
                    ];
                }

                return $cuotas;
            },
            'descuentos_mto' => $this->faker->optional()->randomFloat(2, 0, 100),
            'cargos_mto' => $this->faker->optional()->randomFloat(2, 0, 50),
            'anticipos_mto' => $this->faker->optional()->randomFloat(2, 0, 200),
            'observacion' => $this->faker->optional()->sentence(),
            'legends' => json_encode([
                ['code' => '1000', 'value' => 'CIENTO VEINTE Y 00/100 SOLES']
            ]),
            'guias' => $this->faker->optional()->randomElements([
                ['serie' => 'T001', 'correlativo' => '00000001'],
                ['serie' => 'T002', 'correlativo' => '00000002']
            ], $this->faker->numberBetween(0, 2)),
            'relDocs' => $this->faker->optional()->randomElements([
                ['tipoDoc' => '01', 'serie' => 'F001', 'correlativo' => '00000001', 'fechaEmision' => '2024-01-01'],
                ['tipoDoc' => '03', 'serie' => 'B001', 'correlativo' => '00000001', 'fechaEmision' => '2024-01-01']
            ], $this->faker->numberBetween(0, 2)),
            'anticipos' => $this->faker->optional()->randomElements([
                ['tipoDoc' => '02', 'serie' => 'A001', 'correlativo' => '00000001', 'total' => 100.00],
                ['tipoDoc' => '02', 'serie' => 'A002', 'correlativo' => '00000002', 'total' => 200.00]
            ], $this->faker->numberBetween(0, 2)),
            'descuentos' => $this->faker->optional()->randomElements([
                ['codigo' => '00', 'factor' => 0.05, 'monto' => 50.00, 'base' => 1000.00],
                ['codigo' => '01', 'factor' => 0.10, 'monto' => 100.00, 'base' => 1000.00]
            ], $this->faker->numberBetween(0, 2)),
            'cargos' => $this->faker->optional()->randomElements([
                ['codigo' => '00', 'factor' => 0.02, 'monto' => 20.00, 'base' => 1000.00],
                ['codigo' => '01', 'factor' => 0.03, 'monto' => 30.00, 'base' => 1000.00]
            ], $this->faker->numberBetween(0, 2)),
            'tributos' => $this->faker->optional()->randomElements([
                ['codigo' => '1000', 'nombre' => 'IGV', 'codigoInternacional' => 'VAT', 'tasaTributo' => 0.18, 'montoBase' => 1000.00, 'monto' => 180.00],
                ['codigo' => '2000', 'nombre' => 'ISC', 'codigoInternacional' => 'EXC', 'tasaTributo' => 0.05, 'montoBase' => 1000.00, 'monto' => 50.00]
            ], $this->faker->numberBetween(0, 2)),
            'note_reference' => $this->faker->optional()->sentence(),
            'xml_path' => $this->faker->optional()->filePath(),
            'xml_hash' => $this->faker->optional()->sha1(),
            'cdr_description' => $this->faker->optional()->sentence(),
            'cdr_code' => $this->faker->optional()->randomElement(['0', '98', '99']),
            'cdr_note' => $this->faker->optional()->text(),
            'cdr_path' => $this->faker->optional()->filePath(),
            'errorCode' => $this->faker->optional()->randomElement(['0000', '0001', '0002']),
            'errorMessage' => $this->faker->optional()->sentence(),
            'exportacion' => $this->faker->optional()->randomElements([
                ['codigo' => '01', 'descripcion' => 'Exportación definitiva'],
                ['codigo' => '02', 'descripcion' => 'Exportación temporal']
            ], $this->faker->numberBetween(0, 1)),
        ];
    }
}
