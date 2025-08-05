<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\InvoiceDetail;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Shared\Customer;
use App\Models\Configuration\SunatUnidadMedida;
use App\Models\Configuration\SunatTipoOperacion;
use App\Models\Configuration\SunatTipoAfectacionIgv;
use App\Models\Configuration\SunatBienDetraccion;
use App\Models\Configuration\SunatMedioPago;
use App\Models\Configuration\SunatLeyenda;
use Illuminate\Support\Facades\DB;
use App\Traits\SearchDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Luecano\NumeroALetras\NumeroALetras;

class InvoiceCreateIndex extends Component
{
    use SearchDocument;
    // Datos de la factura
    public $company_id;
    public $sucursal_id;
    public $client_id;
    public $tipoDoc = '01';
    public $tipoOperacion = '0101';
    public $fechaEmision;
    public $fechaVencimiento;
    public $formaPago_moneda = 'PEN';
    public $formaPago_tipo = '01';
    public $tipoMoneda = 'PEN';
    public $tipoVenta = 'contado';
    public $cuotas = [];
    public $observacion;
    public $note_reference;

    // Búsqueda de cliente
    public $numeroDocumentoCliente;

    // Información de la empresa
    public $razonSocial;
    public $nombreComercial;
    public $ruc;
    public $series_suffix;

    // Información de cliente
    public $typeCodeCliente = 'DNI';
    public $nameCliente;
    public $addressCliente;
    public $ubigeoCliente;
    public $textoUbigeoCliente;
    public $phoneCliente;
    public $emailCliente;

    // Producto a agregar
    public $producto_id;
    public $cantidad = 1;
    public $precio_unitario = 0;
    public $descripcion_producto;
    public $unidad = 'NIU';

    // Búsqueda de productos
    public $busquedaProducto;
    public $productosFiltrados = [];

    // Productos en memoria
    public $productos = [];

    // Modal de productos
    public $modal_productos = false;
    public $editando_producto = false;
    public $indice_producto_editar = null;

    // Modal de selección de productos del catálogo
    public $escojeProducto = false;
    public $producto_seleccionado = null;

    // Totales calculados
    public $subtotal = 0;
    public $igv = 0;
    public $total = 0;

    // Tipos de operación
    public $tiposOperacion;

    // Campos adicionales para GREENTER
    public $tipAfeIgv = '10'; // Tipo de afectación IGV (10-Gravado por defecto)
    public $tiposAfectacionIgv;
    public $bienesDetraccion;
    public $mediosPago;
    public $leyendas;
    public $leyendasSeleccionadas = [];

    // Detracción
    public $codBienDetraccion = null;
    public $codMedioPago = null;
    public $ctaBanco = null;
    public $setPercent = null;
    public $setMount = null;
    public $aplicarDetraccion = false;

    // Percepción
    public $codReg = null;
    public $porcentajePer = null;
    public $mtoBasePer = 0;
    public $mtoPer = 0;
    public $mtoTotalPer = 0;
    public $aplicarPercepcion = false;

    // Retención
    public $codRegRet = null;
    public $mtoBaseRet = 0;
    public $factorRet = null;
    public $mtoRet = 0;
    public $aplicarRetencion = false;

    // Descuentos Globales
    public $aplicarDescuentos = false;
    public $descuentos_mto = 0;

    // Cargos
    public $aplicarCargos = false;
    public $cargos_mto = 0;

    // Anticipos
    public $anticipos_mto = 0;

    // Guías de Remisión
    public $aplicarGuias = false;
    public $guias = [];

    // Documentos Relacionados
    public $aplicarDocumentos = false;
    public $relDocs = [];

    // Anticipos Array
    public $anticiposArray = [];

    // Descuentos Array
    public $descuentosArray = [];

    // Cargos Array
    public $cargosArray = [];

    // Tributos Array
    public $tributosArray = [];

    // Propiedad computada para el monto en letras
    public function getMontoEnLetrasProperty()
    {
        return $this->numeroALetras($this->total);
    }

    public function mount()
    {
        $this->fechaEmision = date('Y-m-d');
        $this->fechaVencimiento = date('Y-m-d', strtotime('+30 days'));

        // Inicializar propiedades
        $this->tiposOperacion = collect();
        $this->tiposAfectacionIgv = collect();
        $this->bienesDetraccion = collect();
        $this->mediosPago = collect();
        $this->leyendas = collect();

        // Inicializar tipo de documento del cliente
        $this->typeCodeCliente = 'DNI';

        // Cargar catálogos SUNAT
        $this->cargarTiposOperacion();
        $this->cargarCatalogosSunat();

        // Inicializar con la primera empresa disponible
        $primeraEmpresa = Company::where('isActive', true)
            ->whereHas('sucursales', function ($query) {
                $query->where('isActive', true);
            })
            ->first();

        if ($primeraEmpresa) {
            $this->company_id = $primeraEmpresa->id;
            $this->razonSocial = $primeraEmpresa->razonSocial;
            $this->nombreComercial = $primeraEmpresa->nombreComercial;
            $this->ruc = $primeraEmpresa->ruc;

            // Inicializar con la primera sucursal de la empresa
            $primeraSucursal = Sucursal::where('company_id', $primeraEmpresa->id)
                ->where('isActive', true)
                ->first();

            if ($primeraSucursal) {
                $this->sucursal_id = $primeraSucursal->id;
                $this->series_suffix = $primeraSucursal->series_suffix;
            }
        }

        // Tipo de documento inicial: Factura
        $this->tipoDoc = '01';

        // Asignar leyendas automáticamente
        $this->asignarLeyendasAutomaticamente();
    }
    // buscar Cliente
    public function searchClient()
    {
        $this->validate([
            'typeCodeCliente' => 'required|string|max:255',
            'numeroDocumentoCliente' => 'required|string|max:11',
        ], [
            'typeCodeCliente.required' => 'Debe seleccionar el tipo de documento del cliente',
            'typeCodeCliente.string' => 'El tipo de documento debe ser un texto',
            'typeCodeCliente.max' => 'El tipo de documento no puede exceder 255 caracteres',
            'numeroDocumentoCliente.required' => 'Debe ingresar el número de documento del cliente',
            'numeroDocumentoCliente.string' => 'El número de documento debe ser un texto',
            'numeroDocumentoCliente.max' => 'El número de documento no puede exceder 11 caracteres',
        ]);
        $customer = Customer::where('tipoDoc', $this->typeCodeCliente)
            ->where('numDoc', $this->numeroDocumentoCliente)
            ->first();

        if (!$customer) {
            $response = $this->searchComplete($this->typeCodeCliente, $this->numeroDocumentoCliente);
            if ($response['encontrado']) {
                if ($this->typeCodeCliente == 'DNI') {
                    $this->nameCliente = $response['data']->nombre ?? '';
                    $this->addressCliente  = $response['data']->direccion ?? '';
                    $this->ubigeoCliente = $response['data']->codigo_ubigeo ?? '';
                    $this->textoUbigeoCliente = $response['texto_ubigeo'] ?? '';
                    $this->phoneCliente = $response['data']->telefono ?? '';
                    $this->emailCliente = $response['data']->email ?? '';
                } else {
                    $this->nameCliente = $response['data']->nombre_comercial ?? '';
                    $this->addressCliente  = $response['data']->direccion ?? '';
                    $this->ubigeoCliente = $response['data']->codigo_ubigeo ?? '';
                    $this->textoUbigeoCliente = $response['texto_ubigeo'] ?? '';
                    $this->phoneCliente = $response['data']->telefono ?? '';
                    $this->emailCliente = $response['data']->email ?? '';
                }
                $customer = Customer::create([
                    'tipoDoc' => $this->typeCodeCliente,
                    'numDoc' => $this->numeroDocumentoCliente,
                    'rznSocial' => $this->nameCliente,
                    'nombreComercial' => $this->nameCliente,
                    'email' => $this->emailCliente,
                    'telefono' => $this->phoneCliente,
                    'direccion' => $this->addressCliente,
                    'codigoPostal' => $this->ubigeoCliente,
                    'image' => null,
                    'archivo' => null,
                    'notas' => null,
                    'tipo_customer_id' => 1,
                ]);
                $this->client_id = $customer->id;
            } else {
                $this->addError('codeCliente', 'No se encontró el cliente con el documento proporcionado. Verifique el número de documento e intente nuevamente.');
            }
        } else {
            $this->client_id = $customer->id;
            $this->nameCliente = $customer->rznSocial;
            $this->addressCliente = $customer->direccion;
            $this->ubigeoCliente = $customer->ubigeo;
            $this->textoUbigeoCliente = $customer->texto_ubigeo;
            $this->phoneCliente = $customer->telefono;
            $this->emailCliente = $customer->email;
        }
        //dd($customer);
    }
    public function updatedCompanyId()
    {
        $this->sucursal_id = null;
        $this->series_suffix = null;

        if ($this->company_id) {
            $company = Company::find($this->company_id);
            if ($company) {
                $this->razonSocial = $company->razonSocial;
                $this->nombreComercial = $company->nombreComercial;
                $this->ruc = $company->ruc;

                // Buscar la primera sucursal de la nueva empresa
                $primeraSucursal = Sucursal::where('company_id', $this->company_id)
                    ->where('isActive', true)
                    ->first();

                if ($primeraSucursal) {
                    $this->sucursal_id = $primeraSucursal->id;
                    $this->series_suffix = $primeraSucursal->series_suffix;
                }
            }
        } else {
            $this->razonSocial = null;
            $this->nombreComercial = null;
            $this->ruc = null;
        }
    }

    public function updatedSucursalId()
    {
        if ($this->sucursal_id) {
            $sucursal = Sucursal::find($this->sucursal_id);
            if ($sucursal) {
                $this->series_suffix = $sucursal->series_suffix;
            }
        } else {
            $this->series_suffix = null;
        }
    }

    public function updatedTipoDoc()
    {
        $this->cargarTiposOperacion();
        $this->asignarLeyendasAutomaticamente();
    }



    public function updatedFechaEmision()
    {
        // Si la fecha de vencimiento es anterior a la fecha de emisión, actualizarla
        if ($this->fechaVencimiento && $this->fechaEmision && $this->fechaVencimiento <= $this->fechaEmision) {
            $this->fechaVencimiento = date('Y-m-d', strtotime($this->fechaEmision . ' +30 days'));
        }
    }

    public function updatedFechaVencimiento()
    {
        // Validar que la fecha de vencimiento sea posterior a la fecha de emisión
        if ($this->fechaVencimiento && $this->fechaEmision && $this->fechaVencimiento <= $this->fechaEmision) {
            $this->addError('fechaVencimiento', 'La fecha de vencimiento debe ser posterior a la fecha de emisión');
        } else {
            $this->resetErrorBag('fechaVencimiento');
        }
    }

    public function updatedTipoVenta()
    {
        // Si cambia a contado, limpiar cuotas
        if ($this->tipoVenta === 'contado') {
            $this->cuotas = [];
        }
    }

    public function agregarCuota()
    {
        $this->cuotas[] = [
            'monto' => 0,
            'fecha_pago' => date('Y-m-d', strtotime('+30 days')),
        ];
    }

    public function eliminarCuota($index)
    {
        unset($this->cuotas[$index]);
        $this->cuotas = array_values($this->cuotas);
    }

    public function updatedAplicarGuias()
    {
        if (!$this->aplicarGuias) {
            $this->guias = [];
        }
    }

    public function updatedAplicarDocumentos()
    {
        if (!$this->aplicarDocumentos) {
            $this->relDocs = [];
        }
    }

    public function updatedBusquedaProducto()
    {
        if ($this->busquedaProducto && strlen($this->busquedaProducto) >= 2) {
            $this->productosFiltrados = ProductoCatalogo::where('isActive', true)
                ->where(function ($query) {
                    $query->where('code', 'like', '%' . $this->busquedaProducto . '%')
                        ->orWhere('description', 'like', '%' . $this->busquedaProducto . '%')
                        ->orWhere('code_fabrica', 'like', '%' . $this->busquedaProducto . '%')
                        ->orWhere('code_peru', 'like', '%' . $this->busquedaProducto . '%');
                })
                ->with(['category', 'brand'])
                ->limit(10)
                ->get();
        } else {
            $this->productosFiltrados = [];
        }
    }

    public function updatedNumeroDocumentoCliente()
    {
        if ($this->numeroDocumentoCliente) {
            $cliente = Client::where('numDoc', $this->numeroDocumentoCliente)->first();
            if ($cliente) {
                $this->client_id = $cliente->id;
            } else {
                $this->client_id = null;
            }
        } else {
            $this->client_id = null;
        }
    }

    private function cargarTiposOperacion()
    {
        $this->tiposOperacion = SunatTipoOperacion::getByTipoComprobante($this->tipoDoc);

        // Verificar que la colección no esté vacía y que el tipo de operación actual no esté en la lista
        if ($this->tiposOperacion && $this->tiposOperacion->count() > 0) {
            $tipoOperacionExiste = $this->tiposOperacion->contains('codigo', $this->tipoOperacion);
            if (!$tipoOperacionExiste) {
                $primerTipo = $this->tiposOperacion->first();
                if ($primerTipo) {
                    $this->tipoOperacion = $primerTipo->codigo;
                }
            }
        }
    }

    private function cargarCatalogosSunat()
    {
        // Cargar tipos de afectación IGV
        $this->tiposAfectacionIgv = SunatTipoAfectacionIgv::getAll();

        // Cargar bienes de detracción
        $this->bienesDetraccion = SunatBienDetraccion::getAll();

        // Cargar medios de pago
        $this->mediosPago = SunatMedioPago::getAll();

        // Cargar leyendas
        $this->leyendas = SunatLeyenda::getAll();
    }

    private function generarSerieYCorrelativo($tipoDoc, $sucursal_id)
    {
        if (!$sucursal_id || !$tipoDoc) {
            return ['serie' => null, 'correlativo' => null];
        }

        $sucursal = Sucursal::find($sucursal_id);
        if (!$sucursal || !$sucursal->series_suffix) {
            return ['serie' => null, 'correlativo' => null];
        }

        // Generar serie de 4 dígitos: prefijo + series_suffix (ej: F001, B001)
        $prefijo = $tipoDoc == '01' ? 'F' : 'B';
        $serie = $prefijo . str_pad($sucursal->series_suffix, 3, '0', STR_PAD_LEFT);

        // Buscar el último correlativo para esta serie
        $ultimoCorrelativo = Invoice::where('serie', $serie)
            ->orderBy('correlativo', 'desc')
            ->value('correlativo');

        if ($ultimoCorrelativo) {
            // Incrementar el último correlativo (solo el número)
            $correlativo = (int)$ultimoCorrelativo + 1;
        } else {
            // Primer documento de esta serie
            $correlativo = 1;
        }

        return [
            'serie' => $serie,
            'correlativo' => (string)$correlativo
        ];
    }

    public function updatedProductoId()
    {
        if ($this->producto_id) {
            $producto = ProductoCatalogo::find($this->producto_id);
            if ($producto) {
                $this->precio_unitario = $producto->price_venta ?? 0;
            }
        }
    }

    public function updatedCantidad()
    {
        $this->validateOnly('cantidad', [
            'cantidad' => 'required|numeric|min:0.01|max:999999.99',
        ], [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.numeric' => 'La cantidad debe ser un número',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'cantidad.max' => 'La cantidad no puede exceder 999,999.99',
        ]);
    }

    public function updatedPrecioUnitario()
    {
        $this->validateOnly('precio_unitario', [
            'precio_unitario' => 'required|numeric|min:0|max:999999.99',
        ], [
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'precio_unitario.min' => 'El precio unitario debe ser mayor o igual a 0',
            'precio_unitario.max' => 'El precio unitario no puede exceder 999,999.99',
        ]);
    }



    public function seleccionarProducto($productoId)
    {
        $this->producto_id = $productoId;
        $this->busquedaProducto = null; // Limpiar búsqueda
        $this->productosFiltrados = []; // Limpiar lista filtrada

        // Auto-completar precio y descripción
        $producto = ProductoCatalogo::find($productoId);
        if ($producto) {
            $this->precio_unitario = $producto->price_venta ?? 0;
            $this->descripcion_producto = $producto->description ?? '';
        }
    }

    public function agregarProducto()
    {
        $this->validate([
            'producto_id' => 'required|exists:producto_catalogos,id',
            'cantidad' => 'required|numeric|min:0.01|max:999999.99',
            'precio_unitario' => 'required|numeric|min:0|max:999999.99',
            'unidad' => 'required|string|max:10',
            'descripcion_producto' => 'nullable|string|max:500',
        ], [
            'producto_id.required' => 'Debe seleccionar un producto',
            'producto_id.exists' => 'El producto seleccionado no existe',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.numeric' => 'La cantidad debe ser un número',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'cantidad.max' => 'La cantidad no puede exceder 999,999.99',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'precio_unitario.min' => 'El precio unitario debe ser mayor o igual a 0',
            'precio_unitario.max' => 'El precio unitario no puede exceder 999,999.99',
            'unidad.required' => 'La unidad es obligatoria',
            'unidad.string' => 'La unidad debe ser un texto',
            'unidad.max' => 'La unidad no puede exceder 10 caracteres',
            'descripcion_producto.string' => 'La descripción debe ser un texto',
            'descripcion_producto.max' => 'La descripción no puede exceder 500 caracteres',
        ]);

        $producto = ProductoCatalogo::find($this->producto_id);

        if (!$producto) {
            $this->addError('producto_id', 'El producto seleccionado no existe en la base de datos');
            return;
        }



        // Calcular montos - Los precios ya incluyen IGV
        $valor_venta = $this->cantidad * $this->precio_unitario;
        // Como el precio ya incluye IGV, el valor_venta ya incluye IGV
        $total_producto = $valor_venta;
        // El IGV se calculará en calcularTotales() basado en la diferencia
        $igv_producto = 0; // Se recalculará en calcularTotales()

        $productoData = [
            'producto_id' => $this->producto_id,
            'codigo' => $producto->code,
            'descripcion' => $this->descripcion_producto ?: $producto->description,
            'unidad' => $this->unidad,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'valor_venta' => $valor_venta,
            'igv' => $igv_producto, // Se recalculará en calcularTotales()
            'total' => $total_producto, // Ya incluye IGV
        ];

        if ($this->editando_producto && $this->indice_producto_editar !== null) {
            // Editar producto existente
            $this->productos[$this->indice_producto_editar] = $productoData;
        } else {
            // Verificar si el producto ya está en la lista
            $indiceExistente = collect($this->productos)->search(function ($item) {
                return $item['producto_id'] == $this->producto_id;
            });

            if ($indiceExistente !== false) {
                // Producto duplicado: actualizar cantidad y precio con los últimos valores
                $this->productos[$indiceExistente] = $productoData;
                session()->flash('message', 'Producto actualizado exitosamente con los nuevos valores');
            } else {
                // Agregar nuevo producto
                $this->productos[] = $productoData;
                session()->flash('message', 'Producto agregado exitosamente a la factura');
            }
        }

        // Limpiar campos del producto
        $this->producto_id = null;
        $this->cantidad = 1;
        $this->precio_unitario = 0;
        $this->descripcion_producto = null;
        $this->unidad = 'NIU';
        $this->busquedaProducto = null;
        $this->productosFiltrados = [];

        $this->calcularTotales();

        // Cerrar el modal después de agregar/editar el producto
        $this->modal_productos = false;
        $this->editando_producto = false;
        $this->indice_producto_editar = null;
    }

    public function abrirModalProductos()
    {
        $this->modal_productos = true;
        $this->editando_producto = false;
        $this->indice_producto_editar = null;
        // Limpiar campos
        $this->producto_id = null;
        $this->cantidad = 1;
        $this->precio_unitario = 0;
        $this->descripcion_producto = null;
        $this->unidad = 'NIU';
    }

    public function editarProducto($index)
    {
        if (isset($this->productos[$index])) {
            $producto = $this->productos[$index];
            $this->editando_producto = true;
            $this->indice_producto_editar = $index;
            $this->producto_id = $producto['producto_id'];
            $this->cantidad = $producto['cantidad'];
            $this->precio_unitario = $producto['precio_unitario'];
            $this->descripcion_producto = $producto['descripcion'];
            $this->unidad = $producto['unidad'];
            $this->modal_productos = true;
        }
    }

    public function cerrarModalProductos()
    {
        $this->modal_productos = false;
        $this->editando_producto = false;
        $this->indice_producto_editar = null;
        $this->producto_id = null;
        $this->cantidad = 1;
        $this->precio_unitario = 0;
        $this->descripcion_producto = null;
        $this->unidad = 'NIU';
        $this->busquedaProducto = null;
        $this->productosFiltrados = [];
    }

    public function eliminarProducto($index)
    {
        unset($this->productos[$index]);
        $this->productos = array_values($this->productos);
        $this->calcularTotales();
    }



    public function actualizarProductoEnTabla($index, $campo, $valor)
    {
        if (!isset($this->productos[$index])) {
            return;
        }

        // Validar el campo específico antes de actualizar
        switch ($campo) {
            case 'cantidad':
                if (!is_numeric($valor) || $valor <= 0 || $valor > 999999.99) {
                    $this->addError("productos.{$index}.cantidad", 'La cantidad debe ser un número mayor a 0 y menor a 999,999.99');
                    return;
                }
                break;
            case 'precio_unitario':
                if (!is_numeric($valor) || $valor < 0 || $valor > 999999.99) {
                    $this->addError("productos.{$index}.precio_unitario", 'El precio unitario debe ser un número mayor o igual a 0 y menor a 999,999.99');
                    return;
                }
                break;
            case 'unidad':
                if (empty($valor) || strlen($valor) > 10) {
                    $this->addError("productos.{$index}.unidad", 'La unidad es obligatoria y no puede exceder 10 caracteres');
                    return;
                }
                break;
            case 'descripcion':
                if (empty($valor) || strlen($valor) > 500) {
                    $this->addError("productos.{$index}.descripcion", 'La descripción es obligatoria y no puede exceder 500 caracteres');
                    return;
                }
                break;
        }

        // Actualizar el valor
        $this->productos[$index][$campo] = $valor;

        // Recalcular valores del producto
        if (in_array($campo, ['cantidad', 'precio_unitario'])) {
            $this->productos[$index]['valor_venta'] = $this->productos[$index]['cantidad'] * $this->productos[$index]['precio_unitario'];
            $this->calcularTotales();
        }

        // Limpiar errores si la validación pasó
        $this->resetErrorBag("productos.{$index}.{$campo}");
    }

    public function calcularTotales()
    {
        // Los precios ya incluyen IGV, por lo que necesitamos calcular el subtotal sin IGV
        $this->subtotal = collect($this->productos)->sum(function ($producto) {
            // Si el precio ya incluye IGV, calculamos el valor sin IGV
            $valor_con_igv = $producto['valor_venta'];
            return $valor_con_igv / 1.18; // Dividimos por 1.18 para obtener el valor sin IGV
        });

        // El IGV es la diferencia entre el total con IGV y el subtotal sin IGV
        $total_con_igv = collect($this->productos)->sum('valor_venta');
        $this->igv = $total_con_igv - $this->subtotal;

        // El total es la suma de todos los valores con IGV
        $this->total = $total_con_igv;

        // Aplicar descuentos globales (solo si están habilitados)
        if ($this->aplicarDescuentos && $this->descuentos_mto > 0) {
            $this->total -= $this->descuentos_mto;
        }

        // Aplicar cargos (solo si están habilitados)
        if ($this->aplicarCargos && $this->cargos_mto > 0) {
            $this->total += $this->cargos_mto;
        }

        // Aplicar detracción
        if ($this->setMount > 0) {
            $this->total -= $this->setMount;
        }

        // Aplicar percepción
        if ($this->mtoTotalPer > 0) {
            $this->total += $this->mtoTotalPer;
        }

        // Calcular retención automática si está habilitada
        if ($this->aplicarRetencion) {
            $this->calcularRetencionAutomatica();
        }

        // Aplicar retención (como descuento global)
        if ($this->aplicarRetencion && $this->mtoRet > 0) {
            $this->total -= $this->mtoRet;
        }

        // Actualizar el IGV de cada producto individual
        foreach ($this->productos as $index => $producto) {
            $valor_sin_igv = $producto['valor_venta'] / 1.18;
            $igv_producto = $producto['valor_venta'] - $valor_sin_igv;
            $this->productos[$index]['igv'] = $igv_producto;
        }
    }

    // ========================================
    // MÉTODOS PARA DETRACCIONES (SPOT)
    // ========================================

    /**
     * Verifica si la operación está sujeta a detracción según DL 940
     */
    public function verificarDetraccion()
    {
        // Solo aplicar detracción para tipos de operación específicos
        if (!in_array($this->tipoOperacion, ['1001', '1002', '1003', '1004'])) {
            $this->aplicarDetraccion = false;
            $this->resetearDetraccion();
            return;
        }

        // Verificar si el monto excede el mínimo afecto (S/ 700)
        if ($this->subtotal < 700) {
            $this->aplicarDetraccion = false;
            $this->resetearDetraccion();
            return;
        }

        // Si hay bien de detracción seleccionado, aplicar
        if ($this->codBienDetraccion) {
            $this->aplicarDetraccion = true;
            $this->calcularDetraccion();
        }
    }

    public function updatedCodBienDetraccion()
    {
        if ($this->codBienDetraccion && $this->bienesDetraccion) {
            $bien = $this->bienesDetraccion->firstWhere('codigo', $this->codBienDetraccion);
            if ($bien) {
                $this->setPercent = $bien->porcentaje;
                $this->verificarDetraccion();
            }
        }
    }

    public function updatedTipoOperacion()
    {
        $this->asignarLeyendasAutomaticamente();
        $this->verificarDetraccion();
        $this->verificarPercepcion();
        $this->verificarRetencion();
    }

    public function calcularDetraccion()
    {
        if ($this->aplicarDetraccion && $this->setPercent && $this->subtotal > 0) {
            // La detracción se calcula sobre el valor de venta (sin IGV)
            $this->setMount = round($this->subtotal * ($this->setPercent / 100), 2);
        } else {
            $this->setMount = 0;
        }
        $this->calcularTotales();
    }

    public function resetearDetraccion()
    {
        $this->codBienDetraccion = null;
        $this->codMedioPago = null;
        $this->ctaBanco = null;
        $this->setPercent = null;
        $this->setMount = 0;
        $this->calcularTotales();
    }

    // ========================================
    // MÉTODOS PARA PERCEPCIONES
    // ========================================

    /**
     * Verifica si la operación está sujeta a percepción
     */
    public function verificarPercepcion()
    {
        // Solo aplicar percepción para tipo de operación 2001
        if ($this->tipoOperacion !== '2001') {
            $this->aplicarPercepcion = false;
            $this->resetearPercepcion();
            return;
        }

        // Verificar si el monto excede el mínimo afecto
        if ($this->subtotal < 700) {
            $this->aplicarPercepcion = false;
            $this->resetearPercepcion();
            return;
        }

        // Si hay régimen de percepción seleccionado, aplicar
        if ($this->codReg) {
            $this->aplicarPercepcion = true;
            $this->calcularPercepcion();
        }
    }

    public function updatedCodReg()
    {
        if ($this->codReg) {
            // Aquí se cargarían los porcentajes según el régimen seleccionado
            // Por ahora usamos valores estándar
            switch ($this->codReg) {
                case '01': // Régimen General
                    $this->porcentajePer = 2.00;
                    break;
                case '02': // Régimen Especial
                    $this->porcentajePer = 1.00;
                    break;
                case '03': // Régimen MYPE
                    $this->porcentajePer = 0.50;
                    break;
                default:
                    $this->porcentajePer = 0;
            }
            $this->verificarPercepcion();
        }
    }

    public function calcularPercepcion()
    {
        if ($this->aplicarPercepcion && $this->porcentajePer && $this->subtotal > 0) {
            // La percepción se calcula sobre el valor de venta (sin IGV)
            $this->mtoBasePer = $this->subtotal;
            $this->mtoPer = round($this->subtotal * ($this->porcentajePer / 100), 2);
            $this->mtoTotalPer = $this->mtoPer; // La percepción es solo el monto calculado
        } else {
            $this->mtoBasePer = 0;
            $this->mtoPer = 0;
            $this->mtoTotalPer = 0;
        }
        $this->calcularTotales();
    }

    public function resetearPercepcion()
    {
        $this->codReg = null;
        $this->porcentajePer = null;
        $this->mtoBasePer = 0;
        $this->mtoPer = 0;
        $this->mtoTotalPer = 0;
        $this->calcularTotales();
    }

    // ========================================
    // MÉTODOS PARA RETENCIONES
    // ========================================

    /**
     * Verifica si la operación está sujeta a retención
     */
    public function verificarRetencion()
    {
        // Solo aplicar retención para tipos de operación específicos
        if (!in_array($this->tipoOperacion, ['2002', '2003', '2004'])) {
            $this->aplicarRetencion = false;
            $this->resetearRetencion();
            return;
        }

        // Verificar si el monto excede el mínimo afecto
        if ($this->subtotal < 1000) {
            $this->aplicarRetencion = false;
            $this->resetearRetencion();
            return;
        }

        // Si hay régimen de retención seleccionado, aplicar
        if ($this->codRegRet) {
            $this->aplicarRetencion = true;
            $this->calcularRetencion();
        }
    }

    public function updatedCodRegRet()
    {
        if ($this->codRegRet) {
            // Aquí se cargarían los factores según el régimen seleccionado
            // Por ahora usamos valores estándar
            switch ($this->codRegRet) {
                case '01': // Retención IGV
                    $this->factorRet = 0.18; // 18% IGV
                    break;
                case '02': // Retención Renta
                    $this->factorRet = 0.03; // 3% Renta
                    break;
                case '03': // Retención IGV + Renta
                    $this->factorRet = 0.21; // 18% + 3%
                    break;
                default:
                    $this->factorRet = 0;
            }
            $this->verificarRetencion();
        }
    }

    public function calcularRetencion()
    {
        if ($this->aplicarRetencion && $this->factorRet && $this->subtotal > 0) {
            // La retención se calcula sobre el valor de venta (sin IGV)
            $this->mtoBaseRet = $this->subtotal;
            $this->mtoRet = round($this->subtotal * $this->factorRet, 2);
        } else {
            $this->mtoBaseRet = 0;
            $this->mtoRet = 0;
        }
        $this->calcularTotales();
    }

    public function resetearRetencion()
    {
        $this->codRegRet = null;
        $this->mtoBaseRet = 0;
        $this->factorRet = null;
        $this->mtoRet = 0;
        $this->calcularTotales();
    }

    // Métodos para manejar descuentos globales
    public function calcularDescuentos()
    {
        if ($this->aplicarDescuentos && $this->descuentos_mto > 0) {
            $this->calcularTotales();
        }
        $this->asignarLeyendasAutomaticamente();
    }

    // Métodos para manejar cargos
    public function calcularCargos()
    {
        if ($this->aplicarCargos && $this->cargos_mto > 0) {
            $this->calcularTotales();
        }
        $this->asignarLeyendasAutomaticamente();
    }



    // Métodos para manejar guías de remisión
    public function agregarGuia()
    {
        $this->guias[] = [
            'tipoDoc' => '09',
            'serie' => '',
            'correlativo' => '',
        ];
    }

    public function eliminarGuia($index)
    {
        unset($this->guias[$index]);
        $this->guias = array_values($this->guias);
    }

    // Métodos para manejar documentos relacionados
    public function agregarDocumentoRelacionado()
    {
        $this->relDocs[] = [
            'tipoDoc' => '01',
            'serie' => '',
            'correlativo' => '',
            'fechaEmision' => '',
        ];
    }

    public function eliminarDocumentoRelacionado($index)
    {
        unset($this->relDocs[$index]);
        $this->relDocs = array_values($this->relDocs);
    }

    // Métodos para manejar anticipos
    public function agregarAnticipo()
    {
        $this->anticiposArray[] = [
            'tipoDoc' => '02',
            'serie' => '',
            'correlativo' => '',
            'fechaEmision' => '',
            'monto' => 0,
        ];
    }

    public function eliminarAnticipo($index)
    {
        unset($this->anticiposArray[$index]);
        $this->anticiposArray = array_values($this->anticiposArray);
    }

    // Métodos para manejar descuentos array
    public function agregarDescuento()
    {
        $this->descuentosArray[] = [
            'codigo' => '00',
            'descripcion' => '',
            'monto' => 0,
        ];
    }

    public function eliminarDescuento($index)
    {
        unset($this->descuentosArray[$index]);
        $this->descuentosArray = array_values($this->descuentosArray);
    }

    // Métodos para manejar cargos array
    public function agregarCargo()
    {
        $this->cargosArray[] = [
            'codigo' => '00',
            'descripcion' => '',
            'monto' => 0,
        ];
    }

    public function eliminarCargo($index)
    {
        unset($this->cargosArray[$index]);
        $this->cargosArray = array_values($this->cargosArray);
    }

    // Métodos para manejar checkboxes de descuentos y cargos
    public function updatedAplicarDescuentos()
    {
        if (!$this->aplicarDescuentos) {
            $this->descuentos_mto = 0;
        }
        $this->calcularTotales();
        $this->asignarLeyendasAutomaticamente();
    }

    public function updatedAplicarCargos()
    {
        if (!$this->aplicarCargos) {
            $this->cargos_mto = 0;
        }
        $this->calcularTotales();
        $this->asignarLeyendasAutomaticamente();
    }

    public function updatedAplicarRetencion()
    {
        if (!$this->aplicarRetencion) {
            $this->mtoRet = 0;
        } else {
            // Calcular retención automática del 3% si el monto es superior a S/ 700
            $this->calcularRetencionAutomatica();
        }
        $this->calcularTotales();
        $this->asignarLeyendasAutomaticamente();
    }

    /**
     * Calcula la retención automática del 3% sobre el monto total
     */
    public function calcularRetencionAutomatica()
    {
        if ($this->aplicarRetencion && $this->total > 700) {
            $this->mtoRet = round($this->total * 0.03, 2); // 3% del total
        } else {
            $this->mtoRet = 0;
        }
    }

    // Método para asignar leyendas automáticamente
    private function asignarLeyendasAutomaticamente()
    {
        $this->leyendasSeleccionadas = [];

        // Leyendas según tipo de documento
        if ($this->tipoDoc == '01') {
            // Factura
            $this->leyendasSeleccionadas[] = '1000'; // MONTO EN LETRAS
        } elseif ($this->tipoDoc == '03') {
            // Boleta
            $this->leyendasSeleccionadas[] = '1000'; // MONTO EN LETRAS
            $this->leyendasSeleccionadas[] = '1002'; // TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE
        }

        // Leyendas según tipo de operación
        if (in_array($this->tipoOperacion, ['1001', '1002', '1003', '1004']) && $this->aplicarDetraccion) {
            // Detracción
            $this->leyendasSeleccionadas[] = '2006'; // DETRACCIÓN
        }

        if ($this->tipoOperacion == '2001' && $this->aplicarPercepcion) {
            // Percepción
            $this->leyendasSeleccionadas[] = '2000'; // COMPROBANTE DE PERCEPCIÓN
        }

        if ((in_array($this->tipoOperacion, ['2002', '2003', '2004']) && $this->aplicarRetencion) ||
            ($this->aplicarRetencion && $this->mtoRet > 0)) {
            // Retención
            $this->leyendasSeleccionadas[] = '2001'; // COMPROBANTE DE RETENCIÓN
        }

        // Leyendas según descuentos y cargos
        if ($this->aplicarDescuentos && $this->descuentos_mto > 0) {
            $this->leyendasSeleccionadas[] = '1001'; // VALOR VENTA APROXIMADO
        }

        if ($this->aplicarCargos && $this->cargos_mto > 0) {
            $this->leyendasSeleccionadas[] = '1001'; // VALOR VENTA APROXIMADO
        }

        // Eliminar duplicados
        $this->leyendasSeleccionadas = array_unique($this->leyendasSeleccionadas);
    }

    public function crearFactura()
    {
        //dd($this->correlativo);
        $rules = [
            'company_id' => 'required|exists:companies,id',
            'sucursal_id' => 'required|exists:sucursals,id',
            'client_id' => 'required|exists:customers,id',
            'tipoDoc' => 'required|in:01,03,07,08',
            'tipoOperacion' => 'required|exists:sunat_51,codigo',
            'fechaEmision' => 'required|date',
            'fechaVencimiento' => 'required|date|after:fechaEmision',
            'tipoVenta' => 'required|in:contado,credito',
            'cuotas' => 'nullable|array',
            'formaPago_tipo' => 'required',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:producto_catalogos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01|max:999999.99',
            'productos.*.precio_unitario' => 'required|numeric|min:0|max:999999.99',
            'productos.*.unidad' => 'required|string|max:10',
            'productos.*.descripcion' => 'required|string|max:500',
            'observacion' => 'nullable|string|max:500',
            'note_reference' => 'nullable|string|max:100',
        ];
        $messages = [
            // Empresa y Sucursal
            'company_id.required' => 'Debe seleccionar una empresa',
            'company_id.exists' => 'La empresa seleccionada no existe',
            'sucursal_id.required' => 'Debe seleccionar una sucursal',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe',

            // Cliente
            'client_id.required' => 'Debe seleccionar un cliente',
            'client_id.exists' => 'El cliente seleccionado no existe',

            // Documento
            'tipoDoc.required' => 'Debe seleccionar el tipo de documento',
            'tipoDoc.in' => 'El tipo de documento seleccionado no es válido',
            'tipoOperacion.required' => 'Debe seleccionar el tipo de operación',
            'tipoOperacion.exists' => 'El tipo de operación seleccionado no es válido',
            'fechaEmision.required' => 'La fecha de emisión es obligatoria',
            'fechaEmision.date' => 'La fecha de emisión debe ser una fecha válida',
            'fechaVencimiento.required' => 'La fecha de vencimiento es obligatoria',
            'fechaVencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida',
            'fechaVencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de emisión',
            'tipoVenta.required' => 'Debe seleccionar el tipo de venta',
            'tipoVenta.in' => 'El tipo de venta seleccionado no es válido',
            'cuotas.integer' => 'El número de cuotas debe ser un número entero',
            'cuotas.min' => 'El número de cuotas debe ser al menos 1',
            'cuotas.max' => 'El número de cuotas no puede exceder 60',

            // Forma de Pago
            'formaPago_tipo.required' => 'Debe seleccionar la forma de pago',

            // Productos
            'productos.required' => 'Debe agregar al menos un producto a la factura',
            'productos.array' => 'Los productos deben estar en formato de lista',
            'productos.min' => 'Debe agregar al menos un producto a la factura',
            'productos.*.producto_id.required' => 'Debe seleccionar un producto',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria',
            'productos.*.cantidad.numeric' => 'La cantidad debe ser un número',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'productos.*.cantidad.max' => 'La cantidad no puede exceder 999,999.99',
            'productos.*.precio_unitario.required' => 'El precio unitario es obligatorio',
            'productos.*.precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'productos.*.precio_unitario.min' => 'El precio unitario debe ser mayor o igual a 0',
            'productos.*.precio_unitario.max' => 'El precio unitario no puede exceder 999,999.99',
            'productos.*.unidad.required' => 'La unidad es obligatoria',
            'productos.*.unidad.string' => 'La unidad debe ser un texto',
            'productos.*.unidad.max' => 'La unidad no puede exceder 10 caracteres',
            'productos.*.descripcion.required' => 'La descripción del producto es obligatoria',
            'productos.*.descripcion.string' => 'La descripción debe ser un texto',
            'productos.*.descripcion.max' => 'La descripción no puede exceder 500 caracteres',

            // Mensajes adicionales para validaciones específicas
            'observacion.max' => 'Las observaciones no pueden exceder 500 caracteres',
            'note_reference.max' => 'La referencia no puede exceder 100 caracteres',
        ];
        $this->validate($rules, $messages);

        // Validación adicional para cuotas cuando es a crédito
        if ($this->tipoVenta === 'credito' && empty($this->cuotas)) {
            $this->addError('cuotas', 'Debe agregar al menos una cuota para ventas a crédito');
            return;
        }

        try {
            DB::beginTransaction();

            // Generar serie y correlativo automáticamente
            $serieYCorrelativo = $this->generarSerieYCorrelativo($this->tipoDoc, $this->sucursal_id);

            if (!$serieYCorrelativo['serie'] || !$serieYCorrelativo['correlativo']) {
                $this->addError('general', 'No se pudo generar la serie y correlativo. Verifique la sucursal seleccionada.');
                return;
            }

            // Crear la factura
            $invoice = Invoice::create([
                'user_id' => Auth::user()->id,
                'company_id' => $this->company_id,
                'sucursal_id' => $this->sucursal_id,
                'client_id' => $this->client_id,
                'tipoDoc' => $this->tipoDoc,
                'tipoOperacion' => $this->tipoOperacion,
                'serie' => $serieYCorrelativo['serie'],
                'correlativo' => $serieYCorrelativo['correlativo'],
                'fechaEmision' => $this->fechaEmision,
                'fechaVencimiento' => $this->fechaVencimiento,
                'formaPago_moneda' => $this->formaPago_moneda,
                'formaPago_tipo' => $this->formaPago_tipo,
                'tipoMoneda' => $this->tipoMoneda,
                'mtoOperGravadas' => $this->subtotal, // Valor sin IGV
                'mtoOperInafectas' => 0, // Operaciones inafectas
                'mtoOperExoneradas' => 0, // Operaciones exoneradas
                'mtoOperGratuitas' => 0, // Operaciones gratuitas
                'mtoIGV' => $this->igv,
                'mtoIGVGratuitas' => 0, // IGV de operaciones gratuitas
                'totalImpuestos' => $this->igv,
                'valorVenta' => $this->subtotal, // Valor sin IGV
                'subTotal' => $this->total, // Total con IGV
                'mtoImpVenta' => $this->total, // Total con IGV
                'monto_letras' => $this->numeroALetras($this->total),
                'codBienDetraccion' => $this->codBienDetraccion,
                'codMedioPago' => $this->codMedioPago,
                'ctaBanco' => $this->ctaBanco,
                'setPercent' => $this->setPercent,
                'setMount' => $this->setMount,
                'codReg' => $this->codReg,
                'porcentajePer' => $this->porcentajePer,
                'mtoBasePer' => $this->mtoBasePer,
                'mtoPer' => $this->mtoPer,
                'mtoTotalPer' => $this->mtoTotalPer,
                'codRegRet' => $this->codRegRet,
                'mtoBaseRet' => $this->mtoBaseRet,
                'factorRet' => $this->factorRet,
                'mtoRet' => $this->mtoRet,
                'tipoVenta' => $this->tipoVenta,
                'cuotas' => $this->tipoVenta === 'credito' ? $this->cuotas : null,
                'descuentos_mto' => $this->descuentos_mto,
                'cargos_mto' => $this->cargos_mto,
                'anticipos_mto' => $this->anticipos_mto,
                'observacion' => $this->observacion,
                'legends' => $this->leyendasSeleccionadas,
                'guias' => $this->aplicarGuias ? $this->guias : [],
                'relDocs' => $this->aplicarDocumentos ? $this->relDocs : [],
                'anticipos' => $this->anticiposArray,
                'descuentos' => $this->descuentosArray,
                'cargos' => $this->cargosArray,
                'tributos' => $this->tributosArray,
                'note_reference' => $this->note_reference,
            ]);

            // Crear los detalles de la factura
            foreach ($this->productos as $producto) {
                // Calcular valores sin IGV para el detalle
                $valor_sin_igv = $producto['valor_venta'] / 1.18;
                $igv_producto = $producto['valor_venta'] - $valor_sin_igv;

                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'unidad' => $producto['unidad'],
                    'cantidad' => $producto['cantidad'],
                    'codProducto' => $producto['codigo'],
                    'descripcion' => $producto['descripcion'],
                    'tipAfeIgv' => '10', // Gravado
                    'mtoValorUnitario' => $producto['precio_unitario'] / 1.18, // Precio sin IGV
                    'mtoValorVenta' => $producto['valor_venta'] / 1.18, // Valor venta sin IGV
                    'descuento' => 0, // Sin descuento
                    'mtoBaseIgv' => $valor_sin_igv, // Base imponible sin IGV
                    'totalImpuestos' => $igv_producto,
                    'porcentajeIgv' => 18.00,
                    'igv' => $igv_producto,
                    'mtoPrecioUnitario' => $producto['precio_unitario'] / 1.18, // Precio unitario sin IGV
                ]);
            }

            DB::commit();

            session()->flash('message', 'Factura creada exitosamente');
            return redirect()->route('facturacion.facturas.create');
        } catch (\Exception $e) {
            DB::rollback();
            $this->addError('general', 'Error al crear la factura. Por favor, verifique los datos e intente nuevamente. Detalle: ' . $e->getMessage());
        }
    }

    public function numeroALetras($numero)
    {
        $formatter = new NumeroALetras();
        return $formatter->toMoney($numero, 2, 'SOLES', 'CENTIMOS');
    }

    // Métodos para el modal de selección de productos del catálogo
    public function abrirModalEscogerProducto()
    {
        $this->escojeProducto = true;
    }

    public function cerrarModalEscogerProducto()
    {
        $this->escojeProducto = false;
    }

    public function seleccionarProductoDelCatalogo($productoId)
    {
        $producto = ProductoCatalogo::find($productoId);
        if ($producto) {
            $this->producto_seleccionado = $producto;
            $this->producto_id = $producto->id;
            $this->precio_unitario = $producto->price_venta;
            $this->unidad = $producto->unidadMedida->codigo ?? 'NIU';
            $this->descripcion_producto = $producto->description;
            $this->cerrarModalEscogerProducto();
        }
    }

    public function limpiarProductoSeleccionado()
    {
        $this->producto_seleccionado = null;
        $this->producto_id = null;
        $this->precio_unitario = null;
        $this->unidad = null;
        $this->descripcion_producto = null;
    }

    public function render()
    {
        $companies = Company::where('isActive', true)
            ->whereHas('sucursales', function ($query) {
                $query->where('isActive', true);
            })
            ->get();
        $sucursales = $this->company_id ? Sucursal::where('company_id', $this->company_id)->get() : collect();
        $clients = Client::all();
        $productos = ProductoCatalogo::where('isActive', true)->with(['category', 'brand'])->get();

        // Obtener unidades de medida desde la tabla sunat_03
        $unidades = SunatUnidadMedida::getUnidadesForSelect();

        return view('livewire.facturacion.invoice-create-index', compact(
            'companies',
            'sucursales',
            'clients',
            'productos',
            'unidades'
        ));
    }
}
