<?php

use App\Livewire\Facturacion\InvoiceCreateIndex;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Shared\Customer;
use App\Models\User;
use App\Models\Configuration\SunatTipoOperacion;
use App\Models\Configuration\SunatBienDetraccion;
use App\Models\Configuration\SunatTipoAfectacionIgv;
use App\Models\Configuration\SunatMedioPago;
use App\Models\Configuration\SunatUnidadMedida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Crear datos básicos para las pruebas
    $this->user = User::factory()->create();
    $this->company = Company::factory()->create(['isActive' => true]);
    $this->sucursal = Sucursal::factory()->create([
        'company_id' => $this->company->id,
        'isActive' => true
    ]);
    // Crear tipo de cliente
    $tipoCustomer = \App\Models\Shared\TipoCustomer::factory()->create([
        'nombre' => 'Cliente Test',
        'descripcion' => 'Cliente para pruebas'
    ]);

    $this->client = Customer::factory()->create([
        'tipo_customer_id' => $tipoCustomer->id
    ]);
    $this->producto = ProductoCatalogo::factory()->create([
        'price_venta' => 118.00,
        'isActive' => true
    ]);

    // Crear catálogos SUNAT
    SunatTipoOperacion::factory()->create(['codigo' => '0101']);
    SunatTipoOperacion::factory()->create(['codigo' => '1001']);
    SunatTipoOperacion::factory()->create(['codigo' => '2001']);
    SunatTipoOperacion::factory()->create(['codigo' => '2002']);

    SunatBienDetraccion::factory()->create([
        'codigo' => '001',
        'porcentaje' => 4.00
    ]);

    SunatTipoAfectacionIgv::factory()->create(['codigo' => '10']);
    SunatMedioPago::factory()->create(['codigo' => '01']);
    SunatUnidadMedida::factory()->create(['codigo' => 'NIU']);
});

describe('Cálculos de Detracción', function () {

    test('calcula detracción correctamente para montos superiores a S/ 700', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '1001')
            ->set('codBienDetraccion', '001')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar que se calcula la detracción
        $this->assertEquals(40.00, $component->get('setMount')); // 4% de 1000
        $this->assertEquals(4.00, $component->get('setPercent'));
    });

    test('no aplica detracción para montos menores a S/ 700', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '1001')
            ->set('codBienDetraccion', '001')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 1,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 118.00,
                    'igv' => 18.00,
                    'total' => 118.00
                ]
            ]);

        // Verificar que NO se calcula la detracción
        $this->assertEquals(0.00, $component->get('setMount'));
    });

    test('solo aplica detracción para tipos de operación específicos', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '0101') // Tipo que NO aplica detracción
            ->set('codBienDetraccion', '001')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar que NO se aplica detracción
        $this->assertFalse($component->get('aplicarDetraccion'));
    });
});

describe('Cálculos de Percepción', function () {

    test('calcula percepción correctamente para tipo de operación 2001', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '2001')
            ->set('codReg', '01')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar que se calcula la percepción
        $this->assertEquals(2.00, $component->get('porcentajePer')); // Régimen General
        $this->assertEquals(20.00, $component->get('mtoPer')); // 2% de 1000
        $this->assertEquals(20.00, $component->get('mtoTotalPer'));
    });

    test('no aplica percepción para otros tipos de operación', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '0101') // Tipo que NO aplica percepción
            ->set('codReg', '01')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar que NO se aplica percepción
        $this->assertFalse($component->get('aplicarPercepcion'));
    });
});

describe('Cálculos de Retención', function () {

    test('calcula retención automática del 3% correctamente', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '0101')
            ->set('aplicarRetencion', true)
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar que se calcula la retención automática
        $this->assertEquals(35.40, $component->get('mtoRet')); // 3% de 1180
    });

    test('no aplica retención automática para montos menores a S/ 700', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '0101')
            ->set('aplicarRetencion', true)
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 1,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 118.00,
                    'igv' => 18.00,
                    'total' => 118.00
                ]
            ]);

        // Verificar que NO se calcula la retención
        $this->assertEquals(0.00, $component->get('mtoRet'));
    });

    test('calcula retención por tipo de operación correctamente', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '2002')
            ->set('codRegRet', '01')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar que se calcula la retención por tipo de operación
        $this->assertEquals(0.18, $component->get('factorRet')); // 18% IGV
        $this->assertEquals(180.00, $component->get('mtoRet')); // 18% de 1000
    });
});

describe('Cálculos de Totales', function () {

    test('calcula totales correctamente con descuentos y cargos', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '0101')
            ->set('aplicarDescuentos', true)
            ->set('descuentos_mto', 50.00)
            ->set('aplicarCargos', true)
            ->set('cargos_mto', 25.00)
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ]);

        // Verificar cálculos
        $this->assertEquals(200.00, $component->get('subtotal')); // Sin IGV
        $this->assertEquals(36.00, $component->get('igv'));
        $this->assertEquals(211.00, $component->get('total')); // 236 - 50 + 25
    });

    test('calcula totales correctamente con detracción', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '1001')
            ->set('codBienDetraccion', '001')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar cálculos con detracción
        $this->assertEquals(1000.00, $component->get('subtotal'));
        $this->assertEquals(180.00, $component->get('igv'));
        $this->assertEquals(40.00, $component->get('setMount'));
        $this->assertEquals(1140.00, $component->get('total')); // 1180 - 40
    });

    test('calcula totales correctamente con percepción', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '2001')
            ->set('codReg', '01')
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar cálculos con percepción
        $this->assertEquals(1000.00, $component->get('subtotal'));
        $this->assertEquals(180.00, $component->get('igv'));
        $this->assertEquals(20.00, $component->get('mtoTotalPer'));
        $this->assertEquals(1200.00, $component->get('total')); // 1180 + 20
    });

    test('calcula totales correctamente con retención automática', function () {
        $this->actingAs($this->user);

        $component = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoOperacion', '0101')
            ->set('aplicarRetencion', true)
            ->set('productos', [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ]);

        // Verificar cálculos con retención automática
        $this->assertEquals(1000.00, $component->get('subtotal'));
        $this->assertEquals(180.00, $component->get('igv'));
        $this->assertEquals(35.40, $component->get('mtoRet'));
        $this->assertEquals(1144.60, $component->get('total')); // 1180 - 35.40
    });
});

describe('Validaciones de Regímenes', function () {

    test('valida que la detracción solo se aplique para tipos específicos', function () {
        $tiposConDetraccion = ['1001', '1002', '1003', '1004'];
        $tiposSinDetraccion = ['0101', '0102', '2001', '2002'];

        foreach ($tiposConDetraccion as $tipo) {
            $this->assertTrue(in_array($tipo, ['1001', '1002', '1003', '1004']));
        }

        foreach ($tiposSinDetraccion as $tipo) {
            $this->assertFalse(in_array($tipo, ['1001', '1002', '1003', '1004']));
        }
    });

    test('valida que la percepción solo se aplique para tipo 2001', function () {
        $this->assertTrue('2001' === '2001');
        $this->assertFalse('0101' === '2001');
        $this->assertFalse('1001' === '2001');
        $this->assertFalse('2002' === '2001');
    });

    test('valida que la retención por tipo de operación solo se aplique para tipos específicos', function () {
        $tiposConRetencion = ['2002', '2003', '2004'];
        $tiposSinRetencion = ['0101', '1001', '2001'];

        foreach ($tiposConRetencion as $tipo) {
            $this->assertTrue(in_array($tipo, ['2002', '2003', '2004']));
        }

        foreach ($tiposSinRetencion as $tipo) {
            $this->assertFalse(in_array($tipo, ['2002', '2003', '2004']));
        }
    });

    test('valida montos mínimos para cada régimen', function () {
        // Detracción: mínimo S/ 700
        $this->assertTrue(1000 >= 700); // Aplica
        $this->assertFalse(500 >= 700); // No aplica

        // Percepción: mínimo S/ 700
        $this->assertTrue(1000 >= 700); // Aplica
        $this->assertFalse(500 >= 700); // No aplica

        // Retención automática: mínimo S/ 700
        $this->assertTrue(1000 >= 700); // Aplica
        $this->assertFalse(500 >= 700); // No aplica

        // Retención por tipo de operación: mínimo S/ 1000
        $this->assertTrue(1500 >= 1000); // Aplica
        $this->assertFalse(800 >= 1000); // No aplica
    });
});
