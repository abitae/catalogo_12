<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facturacion\Despatch;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;

class DespatchExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando ejemplos de Guías de Remisión...');

        // Verificar que existan las relaciones necesarias
        $company = Company::first();
        $sucursal = Sucursal::first();
        $client = Client::first();

        if (!$company || !$sucursal || !$client) {
            $this->command->warn('No se pueden crear ejemplos sin company, sucursal o client. Ejecute primero esos seeders.');
            return;
        }

        // Ejemplo 1: Guía de Remisión por venta
        Despatch::create([
            'company_id' => $company->id,
            'sucursal_id' => $sucursal->id,
            'client_id' => $client->id,
            'tipoDoc' => '09', // Guía de Remisión Remitente
            'serie' => 'T001',
            'correlativo' => '1',
            'fechaEmision' => date('Y-m-d'),
            'tipoMoneda' => 'PEN',

            // Destinatario
            'tipoDocDestinatario' => 'RUC',
            'numDocDestinatario' => '20123456789',
            'rznSocialDestinatario' => 'EMPRESA DESTINATARIA S.A.C.',
            'direccionDestinatario' => 'Av. Principal 123, Lima',
            'ubigeoDestinatario' => '150101',

            // Transportista
            'tipoDocTransportista' => 'RUC',
            'numDocTransportista' => '20123456788',
            'rznSocialTransportista' => 'TRANSPORTES RÁPIDOS S.A.C.',
            'placaVehiculo' => 'ABC123',
            'codEstabDestino' => '0001',

            // Dirección de partida
            'direccionPartida' => 'Av. Industrial 456, Lima',
            'ubigeoPartida' => '150101',

            // Dirección de llegada
            'direccionLlegada' => 'Av. Principal 123, Lima',
            'ubigeoLlegada' => '150101',

            // Fechas de traslado
            'fechaInicioTraslado' => date('Y-m-d'),
            'fechaFinTraslado' => date('Y-m-d', strtotime('+1 day')),

            // Motivo de traslado
            'codMotivoTraslado' => '01', // Venta
            'desMotivoTraslado' => 'Venta de mercadería',

            // Indicadores
            'indicadorTransbordo' => false,
            'pesoBrutoTotal' => 150.50,
            'numeroBultos' => 5,
            'modalidadTraslado' => '01', // Transporte público

            // Documentos relacionados
            'documentosRelacionados' => [
                [
                    'tipoDoc' => '01',
                    'serie' => 'F001',
                    'correlativo' => '1'
                ]
            ],

            'observacion' => 'Mercadería en perfecto estado',
        ]);

        // Ejemplo 2: Guía de Remisión por traslado entre establecimientos
        Despatch::create([
            'company_id' => $company->id,
            'sucursal_id' => $sucursal->id,
            'client_id' => $client->id,
            'tipoDoc' => '09', // Guía de Remisión Remitente
            'serie' => 'T001',
            'correlativo' => '2',
            'fechaEmision' => date('Y-m-d'),
            'tipoMoneda' => 'PEN',

            // Destinatario
            'tipoDocDestinatario' => 'RUC',
            'numDocDestinatario' => '20123456789',
            'rznSocialDestinatario' => 'EMPRESA DESTINATARIA S.A.C.',
            'direccionDestinatario' => 'Av. Comercial 789, Arequipa',
            'ubigeoDestinatario' => '040101',

            // Transportista
            'tipoDocTransportista' => 'RUC',
            'numDocTransportista' => '20123456787',
            'rznSocialTransportista' => 'LOGÍSTICA NACIONAL S.A.C.',
            'placaVehiculo' => 'XYZ789',
            'codEstabDestino' => '0002',

            // Dirección de partida
            'direccionPartida' => 'Av. Industrial 456, Lima',
            'ubigeoPartida' => '150101',

            // Dirección de llegada
            'direccionLlegada' => 'Av. Comercial 789, Arequipa',
            'ubigeoLlegada' => '040101',

            // Fechas de traslado
            'fechaInicioTraslado' => date('Y-m-d'),
            'fechaFinTraslado' => date('Y-m-d', strtotime('+3 days')),

            // Motivo de traslado
            'codMotivoTraslado' => '04', // Traslado entre establecimientos
            'desMotivoTraslado' => 'Traslado de mercadería entre sucursales',

            // Indicadores
            'indicadorTransbordo' => true,
            'pesoBrutoTotal' => 250.75,
            'numeroBultos' => 8,
            'modalidadTraslado' => '02', // Transporte privado

            // Documentos relacionados
            'documentosRelacionados' => [
                [
                    'tipoDoc' => '01',
                    'serie' => 'F001',
                    'correlativo' => '2'
                ]
            ],

            'observacion' => 'Mercadería con control de temperatura',
        ]);

        $this->command->info('Se crearon 2 ejemplos de guías de remisión exitosamente.');
    }
}
