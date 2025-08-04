<?php

namespace Database\Factories\Facturacion;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facturacion\Despatch>
 */
class DespatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaEmision = $this->faker->dateTimeBetween('-1 year', 'now');
        $fechaInicioTraslado = $this->faker->dateTimeBetween($fechaEmision, '+1 week');
        $fechaFinTraslado = $this->faker->dateTimeBetween($fechaInicioTraslado, '+1 month');

        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'sucursal_id' => Sucursal::inRandomOrder()->first()?->id ?? Sucursal::factory(),
            'client_id' => Client::inRandomOrder()->first()?->id ?? Client::factory(),
            'tipoDoc' => '09', // Guía de Remisión Remitente
            'serie' => 'T' . $this->faker->regexify('[0-9]{3}'),
            'correlativo' => $this->faker->numberBetween(1, 999999),
            'fechaEmision' => $fechaEmision->format('Y-m-d'),
            'tipoMoneda' => 'PEN',

            // Destinatario
            'tipoDocDestinatario' => $this->faker->randomElement(['DNI', 'RUC', 'CE', 'PAS']),
            'numDocDestinatario' => $this->faker->regexify('[0-9]{8,11}'),
            'rznSocialDestinatario' => $this->faker->company(),
            'direccionDestinatario' => $this->faker->address(),
            'ubigeoDestinatario' => $this->faker->optional()->regexify('[0-9]{6}'),

            // Transportista
            'tipoDocTransportista' => $this->faker->optional()->randomElement(['DNI', 'RUC', 'CE', 'PAS']),
            'numDocTransportista' => $this->faker->optional()->regexify('[0-9]{8,11}'),
            'rznSocialTransportista' => $this->faker->optional()->company(),
            'placaVehiculo' => $this->faker->optional()->regexify('[A-Z]{3}[0-9]{3}'),
            'codEstabDestino' => $this->faker->optional()->regexify('[0-9]{4}'),

            // Dirección de partida
            'direccionPartida' => $this->faker->address(),
            'ubigeoPartida' => $this->faker->optional()->regexify('[0-9]{6}'),

            // Dirección de llegada
            'direccionLlegada' => $this->faker->address(),
            'ubigeoLlegada' => $this->faker->optional()->regexify('[0-9]{6}'),

            // Fechas de traslado
            'fechaInicioTraslado' => $fechaInicioTraslado->format('Y-m-d'),
            'fechaFinTraslado' => $fechaFinTraslado->format('Y-m-d'),

            // Motivo de traslado
            'codMotivoTraslado' => $this->faker->randomElement(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']), // Catálogo 20
            'desMotivoTraslado' => $this->faker->sentence(8),

            // Indicadores
            'indicadorTransbordo' => $this->faker->boolean(20), // 20% de probabilidad
            'pesoBrutoTotal' => $this->faker->optional()->randomFloat(2, 1, 1000),
            'numeroBultos' => $this->faker->optional()->numberBetween(1, 100),
            'modalidadTraslado' => $this->faker->randomElement(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']), // Catálogo 18

            // Documentos relacionados
            'documentosRelacionados' => $this->faker->optional()->randomElements([
                ['tipoDoc' => '01', 'serie' => 'F001', 'correlativo' => $this->faker->numberBetween(1, 999999)],
                ['tipoDoc' => '03', 'serie' => 'B001', 'correlativo' => $this->faker->numberBetween(1, 999999)],
            ], $this->faker->numberBetween(0, 2)),

            // Campos adicionales
            'observacion' => $this->faker->optional()->sentence(),
            'legends' => null,

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
