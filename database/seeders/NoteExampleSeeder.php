<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facturacion\Note;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use Carbon\Carbon;

class NoteExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando ejemplos de Notas de Crédito y Débito...');

        // Verificar que existan las relaciones necesarias
        $company = Company::first();
        $sucursal = Sucursal::first();
        $client = Client::first();

        if (!$company || !$sucursal || !$client) {
            $this->command->warn('No se pueden crear ejemplos sin company, sucursal o client. Ejecute primero esos seeders.');
            return;
        }

        // Ejemplo 1: Nota de Crédito por anulación de factura
        Note::create([
            'company_id' => $company->id,
            'sucursal_id' => $sucursal->id,
            'client_id' => $client->id,
            'tipoDoc' => '07', // Nota de Crédito
            'tipoOperacion' => '0101',
            'serie' => 'NC001',
            'correlativo' => '1',
            'fechaEmision' => date('Y-m-d'),
            'formaPago_moneda' => 'PEN',
            'formaPago_tipo' => '01',
            'tipoMoneda' => 'PEN',

            // Documento que modifica
            'tipoDocModifica' => '01', // Factura
            'serieModifica' => 'F001',
            'correlativoModifica' => '1',
            'fechaEmisionModifica' => date('Y-m-d', strtotime('-5 days')),
            'tipoMonedaModifica' => 'PEN',

            // Motivo de la nota
            'codMotivo' => '01', // Anulación de la operación
            'desMotivo' => 'Anulación de factura por solicitud del cliente',

            // Totales
            'mtoOperGravadas' => 1000.00,
            'mtoIGV' => 180.00,
            'totalImpuestos' => 180.00,
            'valorVenta' => 1000.00,
            'subTotal' => 1180.00,
            'mtoImpVenta' => 1180.00,
            'monto_letras' => 'MIL CIENTO OCHENTA Y 00/100 SOLES',

            'observacion' => 'Nota de crédito por anulación de factura',
        ]);

        // Ejemplo 2: Nota de Débito por intereses moratorios
        Note::create([
            'company_id' => $company->id,
            'sucursal_id' => $sucursal->id,
            'client_id' => $client->id,
            'tipoDoc' => '08', // Nota de Débito
            'tipoOperacion' => '0101',
            'serie' => 'ND001',
            'correlativo' => '1',
            'fechaEmision' => date('Y-m-d'),
            'formaPago_moneda' => 'PEN',
            'formaPago_tipo' => '01',
            'tipoMoneda' => 'PEN',

            // Documento que modifica
            'tipoDocModifica' => '01', // Factura
            'serieModifica' => 'F001',
            'correlativoModifica' => '2',
            'fechaEmisionModifica' => date('Y-m-d', strtotime('-30 days')),
            'tipoMonedaModifica' => 'PEN',

            // Motivo de la nota
            'codMotivo' => '01', // Intereses por mora
            'desMotivo' => 'Intereses moratorios por pago fuera de fecha',

            // Totales
            'mtoOperGravadas' => 50.00,
            'mtoIGV' => 9.00,
            'totalImpuestos' => 9.00,
            'valorVenta' => 50.00,
            'subTotal' => 59.00,
            'mtoImpVenta' => 59.00,
            'monto_letras' => 'CINCUENTA Y NUEVE Y 00/100 SOLES',

            'observacion' => 'Intereses por pago fuera de fecha',
        ]);

        $this->command->info('Se crearon 2 ejemplos de notas exitosamente.');
    }
}
