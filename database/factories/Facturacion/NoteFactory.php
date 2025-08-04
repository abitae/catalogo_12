<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\Note>
 */
class NoteFactory extends Factory
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
        $tipoDoc = $this->faker->randomElement(['07', '08']); // 07: Nota de Crédito, 08: Nota de Débito

        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'sucursal_id' => Sucursal::inRandomOrder()->first()?->id ?? Sucursal::factory(),
            'client_id' => Client::inRandomOrder()->first()?->id ?? Client::factory(),
            'tipoDoc' => $tipoDoc,
            'tipoOperacion' => $this->faker->randomElement(['0101', '0102', '0103']),
            'serie' => $tipoDoc === '07' ? 'NC' . $this->faker->regexify('[0-9]{3}') : 'ND' . $this->faker->regexify('[0-9]{3}'),
            'correlativo' => $this->faker->numberBetween(1, 999999),
            'fechaEmision' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'formaPago_moneda' => 'PEN',
            'formaPago_tipo' => $this->faker->randomElement(['01', '02', '03']),
            'tipoMoneda' => 'PEN',

            // Documento que modifica
            'tipoDocModifica' => $this->faker->randomElement(['01', '03']), // Factura o Boleta
            'serieModifica' => $this->faker->randomElement(['F', 'B']) . $this->faker->regexify('[0-9]{3}'),
            'correlativoModifica' => $this->faker->numberBetween(1, 999999),
            'fechaEmisionModifica' => $this->faker->dateTimeBetween('-2 years', '-1 day')->format('Y-m-d'),
            'tipoMonedaModifica' => 'PEN',

            // Motivo de la nota
            'codMotivo' => $tipoDoc === '07'
                ? $this->faker->randomElement(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13']) // Catálogo 09 para NC
                : $this->faker->randomElement(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99', '100']), // Catálogo 10 para ND
            'desMotivo' => $this->faker->sentence(10),

            // Totales
            'mtoOperGravadas' => $valorVenta,
            'mtoIGV' => $mtoIGV,
            'totalImpuestos' => $mtoIGV,
            'valorVenta' => $valorVenta,
            'subTotal' => $subTotal,
            'mtoImpVenta' => $subTotal,
            'monto_letras' => $this->faker->words(3, true),

            // Campos adicionales
            'observacion' => $this->faker->optional()->sentence(),
            'legends' => null,
            'note_reference' => $this->faker->optional()->bothify('REF-####-????'),

            // Archivos y respuestas SUNAT
            'xml_path' => null,
            'xml_hash' => null,
            'cdr_description' => null,
            'cdr_code' => null,
            'cdr_note' => null,
            'cdr_path' => null,
            'errorCode' => null,
            'errorMessage' => null,
        ];
    }
}
