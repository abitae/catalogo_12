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

class InvoiceCreateIndex extends Component
{
    use SearchDocument;
    // Datos de la factura
    public $company_id;
    public $sucursal_id;
    public $client_id;
    public $tipoDoc = '01';
    public $tipoOperacion = '0101';
    public $serie;
    public $correlativo;
    public $fechaEmision;
    public $formaPago_moneda = 'PEN';
    public $formaPago_tipo = '01';
    public $tipoMoneda = 'PEN';
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

    // Percepción
    public $perception_mtoBase = 0;
    public $perception_mto = 0;
    public $perception_mtoTotal = 0;

    // Descuentos Globales
    public $descuentos_mtoBase = 0;
    public $descuentos_mto = 0;

    // Cargos
    public $cargos_mtoBase = 0;
    public $cargos_mto = 0;

    // Anticipos
    public $anticipos_mtoBase = 0;
    public $anticipos_mto = 0;

    // Guías de Remisión
    public $guias = [];

    // Documentos Relacionados
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

        // Generar serie y correlativo inicial
        $this->generarSerieYCorrelativo();
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
    }
    public function updatedCompanyId()
    {
        $this->sucursal_id = null;
        $this->serie = null;
        $this->correlativo = null;
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
                    $this->generarSerieYCorrelativo();
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
                $this->generarSerieYCorrelativo();
            }
        } else {
            $this->series_suffix = null;
            $this->serie = null;
            $this->correlativo = null;
        }
    }

    public function updatedTipoDoc()
    {
        $this->generarSerieYCorrelativo();
        $this->cargarTiposOperacion();
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

    private function generarSerieYCorrelativo()
    {
        if (!$this->sucursal_id || !$this->tipoDoc) {
            return;
        }

        $sucursal = Sucursal::find($this->sucursal_id);
        if (!$sucursal || !$sucursal->series_suffix) {
            return;
        }

        // Generar serie de 4 dígitos: prefijo + series_suffix (ej: F001, B001)
        $prefijo = $this->tipoDoc == '01' ? 'F' : 'B';
        $this->serie = $prefijo . str_pad($sucursal->series_suffix, 3, '0', STR_PAD_LEFT);

        // Buscar el último correlativo para esta serie
        $ultimoCorrelativo = Invoice::where('serie', $this->serie)
            ->orderBy('correlativo', 'desc')
            ->value('correlativo');

        if ($ultimoCorrelativo) {
            // Incrementar el último correlativo (solo el número)
            $this->correlativo = (int)$ultimoCorrelativo + 1;
        } else {
            // Primer documento de esta serie
            $this->correlativo = 1;
        }
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

        // Aplicar descuentos globales
        if ($this->descuentos_mto > 0) {
            $this->total -= $this->descuentos_mto;
        }

        // Aplicar cargos
        if ($this->cargos_mto > 0) {
            $this->total += $this->cargos_mto;
        }

        // Aplicar detracción
        if ($this->setMount > 0) {
            $this->total -= $this->setMount;
        }

        // Aplicar percepción
        if ($this->perception_mtoTotal > 0) {
            $this->total += $this->perception_mtoTotal;
        }

        // Actualizar el IGV de cada producto individual
        foreach ($this->productos as $index => $producto) {
            $valor_sin_igv = $producto['valor_venta'] / 1.18;
            $igv_producto = $producto['valor_venta'] - $valor_sin_igv;
            $this->productos[$index]['igv'] = $igv_producto;
        }
    }

    // Métodos para manejar detracción
    public function updatedCodBienDetraccion()
    {
        if ($this->codBienDetraccion && $this->bienesDetraccion) {
            $bien = $this->bienesDetraccion->firstWhere('codigo', $this->codBienDetraccion);
            if ($bien) {
                $this->setPercent = $bien->porcentaje;
                $this->calcularDetraccion();
            }
        }
    }

    public function calcularDetraccion()
    {
        if ($this->setPercent && $this->subtotal > 0) {
            $this->setMount = round($this->subtotal * ($this->setPercent / 100), 2);
        }
        $this->calcularTotales();
    }

    // Métodos para manejar descuentos globales
    public function calcularDescuentos()
    {
        if ($this->descuentos_mtoBase > 0 && $this->descuentos_mto > 0) {
            $this->calcularTotales();
        }
    }

    // Métodos para manejar cargos
    public function calcularCargos()
    {
        if ($this->cargos_mtoBase > 0 && $this->cargos_mto > 0) {
            $this->calcularTotales();
        }
    }

    // Métodos para manejar percepción
    public function calcularPercepcion()
    {
        if ($this->perception_mtoBase > 0 && $this->perception_mto > 0) {
            $this->perception_mtoTotal = $this->perception_mtoBase + $this->perception_mto;
            $this->calcularTotales();
        }
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

    public function crearFactura()
    {
        $rules = [
            'company_id' => 'required|exists:companies,id',
            'sucursal_id' => 'required|exists:sucursals,id',
            'client_id' => 'required|exists:customers,id',
            'tipoDoc' => 'required|in:01,03,07,08',
            'tipoOperacion' => 'required|exists:sunat_51,codigo',
            'serie' => 'required|string|max:10',
            'correlativo' => 'required|string|max:10',
            'fechaEmision' => 'required|date',
            'formaPago_tipo' => 'required|in:01,02,03',
            'producto_id' => 'required|exists:producto_catalogos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
            'unidad' => 'required|string|max:10',
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
            'serie.required' => 'La serie es obligatoria',
            'serie.string' => 'La serie debe ser un texto',
            'serie.max' => 'La serie no puede exceder 10 caracteres',
            'correlativo.required' => 'El correlativo es obligatorio',
            'correlativo.string' => 'El correlativo debe ser un texto',
            'correlativo.max' => 'El correlativo no puede exceder 10 caracteres',
            'fechaEmision.required' => 'La fecha de emisión es obligatoria',
            'fechaEmision.date' => 'La fecha de emisión debe ser una fecha válida',

            // Forma de Pago
            'formaPago_tipo.required' => 'Debe seleccionar la forma de pago',
            'formaPago_tipo.in' => 'La forma de pago seleccionada no es válida',

            // Producto
            'producto_id.required' => 'Debe seleccionar un producto',
            'producto_id.exists' => 'El producto seleccionado no existe',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.numeric' => 'La cantidad debe ser un número',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'precio_unitario.min' => 'El precio unitario debe ser mayor o igual a 0',
            'unidad.required' => 'La unidad es obligatoria',
            'unidad.string' => 'La unidad debe ser un texto',
            'unidad.max' => 'La unidad no puede exceder 10 caracteres',

            // Mensajes adicionales para validaciones específicas
            'observacion.max' => 'Las observaciones no pueden exceder 500 caracteres',
            'note_reference.max' => 'La referencia no puede exceder 100 caracteres',
        ];
        $this->validate($rules, $messages);

        if (empty($this->productos)) {
            $this->addError('productos', 'Debe agregar al menos un producto a la factura');
            return;
        }

        try {
            DB::beginTransaction();

            // Crear la factura
            $invoice = Invoice::create([
                'company_id' => $this->company_id,
                'sucursal_id' => $this->sucursal_id,
                'client_id' => $this->client_id,
                'tipoDoc' => $this->tipoDoc,
                'tipoOperacion' => $this->tipoOperacion,
                'serie' => $this->serie,
                'correlativo' => $this->correlativo,
                'fechaEmision' => $this->fechaEmision,
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
                'perception_mtoBase' => $this->perception_mtoBase,
                'perception_mto' => $this->perception_mto,
                'perception_mtoTotal' => $this->perception_mtoTotal,
                'descuentos_mtoBase' => $this->descuentos_mtoBase,
                'descuentos_mto' => $this->descuentos_mto,
                'cargos_mtoBase' => $this->cargos_mtoBase,
                'cargos_mto' => $this->cargos_mto,
                'anticipos_mtoBase' => $this->anticipos_mtoBase,
                'anticipos_mto' => $this->anticipos_mto,
                'observacion' => $this->observacion,
                'legends' => $this->leyendasSeleccionadas,
                'guias' => $this->guias,
                'relDocs' => $this->relDocs,
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
            return redirect()->route('facturacion.invoices.index');
        } catch (\Exception $e) {
            DB::rollback();
            $this->addError('general', 'Error al crear la factura. Por favor, verifique los datos e intente nuevamente. Detalle: ' . $e->getMessage());
        }
    }

    public function numeroALetras($numero)
    {
        // Función simple para convertir números a letras
        $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];

        $entero = (int) $numero;
        $decimal = round(($numero - $entero) * 100);

        if ($entero == 0) {
            return 'CERO SOLES';
        }

        $texto = '';
        if ($entero >= 1000) {
            $texto .= 'MIL ';
            $entero -= 1000;
        }

        if ($entero >= 100) {
            $centenas = (int) ($entero / 100);
            $texto .= $unidades[$centenas] . 'CIENTOS ';
            $entero %= 100;
        }

        if ($entero >= 10) {
            $decena = (int) ($entero / 10);
            $texto .= $decenas[$decena] . ' ';
            $entero %= 10;
        }

        if ($entero > 0) {
            $texto .= $unidades[$entero] . ' ';
        }

        $texto .= 'SOLES';

        if ($decimal > 0) {
            $texto .= ' CON ' . $decimal . '/100';
        }

        return trim($texto);
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
