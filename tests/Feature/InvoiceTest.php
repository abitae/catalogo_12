<?php

use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\InvoiceDetail;
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
    // Crear usuario
    $this->user = User::factory()->create();

    // Crear empresa
    $this->company = Company::factory()->create([
        'razonSocial' => 'EMPRESA TEST S.A.C.',
        'nombreComercial' => 'EMPRESA TEST',
        'ruc' => '20123456789',
        'isActive' => true
    ]);

    // Crear sucursal
    $this->sucursal = Sucursal::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Sucursal Principal',
        'series_suffix' => '001',
        'isActive' => true
    ]);

    // Crear tipo de cliente
    $tipoCustomer = \App\Models\Shared\TipoCustomer::factory()->create([
        'nombre' => 'Cliente Test',
        'descripcion' => 'Cliente para pruebas'
    ]);

    // Crear cliente
    $this->client = Customer::factory()->create([
        'tipoDoc' => 'RUC',
        'numDoc' => '20123456789',
        'rznSocial' => 'CLIENTE TEST S.A.C.',
        'nombreComercial' => 'CLIENTE TEST',
        'direccion' => 'Av. Test 123',
        'codigoPostal' => '15001',
        'tipo_customer_id' => $tipoCustomer->id
    ]);

    // Crear productos
    $this->producto1 = ProductoCatalogo::factory()->create([
        'code' => 'PROD001',
        'description' => 'Producto Test 1',
        'price_venta' => 118.00, // Precio con IGV
        'isActive' => true
    ]);

    $this->producto2 = ProductoCatalogo::factory()->create([
        'code' => 'PROD002',
        'description' => 'Producto Test 2',
        'price_venta' => 236.00, // Precio con IGV
        'isActive' => true
    ]);

    // Crear catálogos SUNAT
    $this->tipoOperacion = SunatTipoOperacion::factory()->create([
        'codigo' => '0101',
        'descripcion' => 'Venta interna'
    ]);

    $this->bienDetraccion = SunatBienDetraccion::factory()->create([
        'codigo' => '001',
        'descripcion' => 'Azúcar y melaza de caña',
        'porcentaje' => 4.00
    ]);

    $this->tipoAfectacionIgv = SunatTipoAfectacionIgv::factory()->create([
        'codigo' => '10',
        'descripcion' => 'Gravado - Operación Onerosa'
    ]);

    $this->medioPago = SunatMedioPago::factory()->create([
        'codigo' => '01',
        'descripcion' => 'Efectivo'
    ]);

    $this->unidadMedida = SunatUnidadMedida::factory()->create([
        'codigo' => 'NIU',
        'descripcion' => 'Unidad (Bienes)'
    ]);
});

describe('Creación de Facturas', function () {

    test('puede crear factura de venta interna básica', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'tipoDoc' => '01',
            'tipoOperacion' => '0101',
            'tipoVenta' => 'contado',
            'mtoOperGravadas' => 200.00, // Sin IGV
            'mtoIGV' => 36.00,
            'subTotal' => 236.00
        ]);

        $this->assertDatabaseHas('invoice_details', [
            'unidad' => 'NIU',
            'cantidad' => 2,
            'mtoValorVenta' => 200.00, // Sin IGV
            'igv' => 36.00
        ]);
    });

    test('puede crear factura con detracción SPOT', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '1001') // Tipo de operación con detracción
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('codBienDetraccion', '001') // Bien de detracción
            ->set('setPercent', 4.00)
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 10, // Cantidad para superar S/ 700
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '1001',
            'codBienDetraccion' => '001',
            'setPercent' => 4.00,
            'setMount' => 40.00, // 4% de 1000 (sin IGV)
            'mtoOperGravadas' => 1000.00,
            'mtoIGV' => 180.00,
            'subTotal' => 1140.00 // 1180 - 40 de detracción
        ]);
    });

    test('puede crear factura con percepción', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '2001') // Tipo de operación con percepción
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('codReg', '01') // Régimen de percepción
            ->set('porcentajePer', 2.00)
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 10, // Cantidad para superar S/ 700
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '2001',
            'codReg' => '01',
            'porcentajePer' => 2.00,
            'mtoBasePer' => 1000.00,
            'mtoPer' => 20.00, // 2% de 1000
            'mtoTotalPer' => 20.00,
            'mtoOperGravadas' => 1000.00,
            'mtoIGV' => 180.00,
            'subTotal' => 1200.00 // 1180 + 20 de percepción
        ]);
    });

    test('puede crear factura con retención automática del 3%', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101') // Venta interna
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('aplicarRetencion', true) // Activar retención manual
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 10, // Cantidad para superar S/ 700
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '0101',
            'mtoRet' => 35.40, // 3% de 1180
            'mtoOperGravadas' => 1000.00,
            'mtoIGV' => 180.00,
            'subTotal' => 1144.60 // 1180 - 35.40 de retención
        ]);
    });

    test('puede crear factura con retención por tipo de operación', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '2002') // Tipo de operación con retención
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('codRegRet', '01') // Retención IGV
            ->set('factorRet', 0.18)
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 10, // Cantidad para superar S/ 1000
                    'precio_unitario' => 118.00,
                    'valor_venta' => 1180.00,
                    'igv' => 180.00,
                    'total' => 1180.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '2002',
            'codRegRet' => '01',
            'factorRet' => 0.18,
            'mtoBaseRet' => 1000.00,
            'mtoRet' => 180.00, // 18% de 1000
            'mtoOperGravadas' => 1000.00,
            'mtoIGV' => 180.00,
            'subTotal' => 1000.00 // 1180 - 180 de retención
        ]);
    });

    test('puede crear factura con descuentos y cargos globales', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('aplicarDescuentos', true)
            ->set('descuentos_mto', 50.00)
            ->set('aplicarCargos', true)
            ->set('cargos_mto', 25.00)
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '0101',
            'descuentos_mto' => 50.00,
            'cargos_mto' => 25.00,
            'mtoOperGravadas' => 200.00,
            'mtoIGV' => 36.00,
            'subTotal' => 211.00 // 236 - 50 + 25
        ]);
    });

    test('puede crear factura a crédito con cuotas', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'credito')
            ->set('formaPago_tipo', '01')
            ->set('cuotas', [
                [
                    'monto' => 118.00,
                    'fecha_pago' => '2024-02-15'
                ],
                [
                    'monto' => 118.00,
                    'fecha_pago' => '2024-03-15'
                ]
            ])
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoVenta' => 'credito',
            'cuotas' => json_encode([
                ['monto' => 118.00, 'fecha_pago' => '2024-02-15'],
                ['monto' => 118.00, 'fecha_pago' => '2024-03-15']
            ]),
            'mtoOperGravadas' => 200.00,
            'mtoIGV' => 36.00,
            'subTotal' => 236.00
        ]);
    });

    test('valida que la detracción solo se aplique para montos superiores a S/ 700', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '1001')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('codBienDetraccion', '001')
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 1, // Cantidad para NO superar S/ 700
                    'precio_unitario' => 118.00,
                    'valor_venta' => 118.00,
                    'igv' => 18.00,
                    'total' => 118.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '1001',
            'codBienDetraccion' => '001',
            'setMount' => 0.00, // No debe aplicar detracción
            'mtoOperGravadas' => 100.00,
            'mtoIGV' => 18.00,
            'subTotal' => 118.00
        ]);
    });

    test('valida que la retención automática solo se aplique para montos superiores a S/ 700', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('aplicarRetencion', true)
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 1, // Cantidad para NO superar S/ 700
                    'precio_unitario' => 118.00,
                    'valor_venta' => 118.00,
                    'igv' => 18.00,
                    'total' => 118.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '0101',
            'mtoRet' => 0.00, // No debe aplicar retención
            'mtoOperGravadas' => 100.00,
            'mtoIGV' => 18.00,
            'subTotal' => 118.00
        ]);
    });

    test('puede crear factura con guías de remisión', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('aplicarGuias', true)
            ->set('guias', [
                [
                    'serie' => 'T001',
                    'correlativo' => '00000001'
                ]
            ])
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '0101',
            'guias' => json_encode([
                ['serie' => 'T001', 'correlativo' => '00000001']
            ]),
            'mtoOperGravadas' => 200.00,
            'mtoIGV' => 36.00,
            'subTotal' => 236.00
        ]);
    });

    test('puede crear factura con documentos relacionados', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('aplicarDocumentos', true)
            ->set('relDocs', [
                [
                    'tipoDoc' => '01',
                    'serie' => 'F001',
                    'correlativo' => '00000001',
                    'fechaEmision' => '2024-01-10'
                ]
            ])
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ])
            ->call('crearFactura');

        $this->assertDatabaseHas('invoices', [
            'tipoOperacion' => '0101',
            'relDocs' => json_encode([
                ['tipoDoc' => '01', 'serie' => 'F001', 'correlativo' => '00000001', 'fechaEmision' => '2024-01-10']
            ]),
            'mtoOperGravadas' => 200.00,
            'mtoIGV' => 36.00,
            'subTotal' => 236.00
        ]);
    });

    test('valida que se requieran campos obligatorios', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->call('crearFactura')
            ->assertHasErrors([
                'company_id' => 'required',
                'sucursal_id' => 'required',
                'client_id' => 'required',
                'tipoDoc' => 'required',
                'tipoOperacion' => 'required',
                'fechaEmision' => 'required',
                'fechaVencimiento' => 'required'
            ]);
    });

    test('valida que la fecha de vencimiento sea posterior a la fecha de emisión', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-01-10') // Fecha anterior
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ])
            ->call('crearFactura')
            ->assertHasErrors(['fechaVencimiento' => 'after']);
    });

    test('valida que se requieran productos para crear la factura', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'contado')
            ->set('formaPago_tipo', '01')
            ->set('productos', []) // Sin productos
            ->call('crearFactura')
            ->assertHasErrors(['productos' => 'required']);
    });

    test('valida que se requieran cuotas para ventas a crédito', function () {
        $this->actingAs($this->user);

        $response = Livewire::test('facturacion.invoice-create-index')
            ->set('company_id', $this->company->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->set('client_id', $this->client->id)
            ->set('tipoDoc', '01')
            ->set('tipoOperacion', '0101')
            ->set('fechaEmision', '2024-01-15')
            ->set('fechaVencimiento', '2024-02-15')
            ->set('tipoVenta', 'credito')
            ->set('formaPago_tipo', '01')
            ->set('cuotas', []) // Sin cuotas
            ->set('productos', [
                [
                    'producto_id' => $this->producto1->id,
                    'codigo' => $this->producto1->code,
                    'descripcion' => $this->producto1->description,
                    'unidad' => 'NIU',
                    'cantidad' => 2,
                    'precio_unitario' => 118.00,
                    'valor_venta' => 236.00,
                    'igv' => 36.00,
                    'total' => 236.00
                ]
            ])
            ->call('crearFactura')
            ->assertHasErrors(['cuotas' => 'required']);
    });
});
