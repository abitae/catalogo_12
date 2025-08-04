<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use App\Models\Facturacion\Despatch;
use App\Models\Facturacion\DespatchDetail;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Configuration\SunatUnidadMedida;
use Illuminate\Support\Facades\DB;
use App\Traits\SearchDocument;

class DespatcheCreateIndex extends Component
{
    use SearchDocument;

    // Datos de la guía de remisión
    public $company_id;
    public $sucursal_id;
    public $client_id;
    public $tipoDoc = '09'; // Guía de Remisión Remitente
    public $serie;
    public $correlativo;
    public $fechaEmision;
    public $tipoMoneda = 'PEN';

    // Destinatario
    public $tipoDocDestinatario = 'RUC';
    public $numDocDestinatario;
    public $rznSocialDestinatario;
    public $direccionDestinatario;
    public $ubigeoDestinatario;

    // Transportista
    public $tipoDocTransportista = 'RUC';
    public $numDocTransportista;
    public $rznSocialTransportista;
    public $placaVehiculo;
    public $codEstabDestino;

    // Dirección de partida
    public $direccionPartida;
    public $ubigeoPartida;

    // Dirección de llegada
    public $direccionLlegada;
    public $ubigeoLlegada;

    // Fechas de traslado
    public $fechaInicioTraslado;
    public $fechaFinTraslado;

    // Motivo de traslado
    public $codMotivoTraslado = '01';
    public $desMotivoTraslado;

    // Indicadores
    public $indicadorTransbordo = false;
    public $pesoBrutoTotal;
    public $numeroBultos;
    public $modalidadTraslado = '01';

    // Documentos relacionados
    public $documentosRelacionados = [];

    // Campos adicionales
    public $observacion;

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
    public $descripcion_producto;
    public $unidad = 'NIU';
    public $pesoBruto;
    public $pesoNeto;
    public $codLote;
    public $fechaVencimiento;

    // Búsqueda de productos
    public $busquedaProducto;
    public $productosFiltrados = [];

    // Productos en memoria
    public $productos = [];

    // Modal de productos
    public $modal_productos = false;
    public $editando_producto = false;
    public $indice_producto_editar = null;

    public function mount()
    {
        $this->fechaEmision = date('Y-m-d');
        $this->fechaInicioTraslado = date('Y-m-d');
        $this->fechaFinTraslado = date('Y-m-d', strtotime('+1 day'));

        // Inicializar con la primera empresa disponible
        $firstCompany = Company::whereHas('sucursales', function($query) {
            $query->where('isActive', true);
        })->first();

        if ($firstCompany) {
            $this->company_id = $firstCompany->id;
            $this->razonSocial = $firstCompany->razonSocial;
            $this->nombreComercial = $firstCompany->nombreComercial;
            $this->ruc = $firstCompany->ruc;

            // Seleccionar la primera sucursal activa
            $firstSucursal = $firstCompany->sucursales()->where('isActive', true)->first();
            if ($firstSucursal) {
                $this->sucursal_id = $firstSucursal->id;
                $this->series_suffix = $firstSucursal->series_suffix;
                $this->generarSerieYCorrelativo();
            }
        }

        // Cargar motivos de traslado
        $this->cargarMotivosTraslado();
    }

    public function updatedCompanyId()
    {
        $this->sucursal_id = null;
        $this->serie = null;
        $this->correlativo = null;

        if ($this->company_id) {
            $company = Company::find($this->company_id);
            if ($company) {
                $this->razonSocial = $company->razonSocial;
                $this->nombreComercial = $company->nombreComercial;
                $this->ruc = $company->ruc;

                // Seleccionar automáticamente la primera sucursal activa
                $firstSucursal = $company->sucursales()->where('isActive', true)->first();
                if ($firstSucursal) {
                    $this->sucursal_id = $firstSucursal->id;
                    $this->series_suffix = $firstSucursal->series_suffix;
                    $this->generarSerieYCorrelativo();
                }
            }
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
        }
    }

    private function generarSerieYCorrelativo()
    {
        if ($this->sucursal_id) {
            $sucursal = Sucursal::find($this->sucursal_id);
            if ($sucursal && $sucursal->series_suffix) {
                // Generar serie para guía de remisión
                $this->serie = 'T' . str_pad($sucursal->series_suffix, 3, '0', STR_PAD_LEFT);

                // Generar correlativo secuencial
                $ultimaGuia = Despatch::where('serie', $this->serie)->orderBy('correlativo', 'desc')->first();
                $this->correlativo = $ultimaGuia ? (int)$ultimaGuia->correlativo + 1 : 1;
            }
        }
    }

    private function cargarMotivosTraslado()
    {
        // Cargar motivos de traslado según el código
        switch ($this->codMotivoTraslado) {
            case '01':
                $this->desMotivoTraslado = 'Venta';
                break;
            case '02':
                $this->desMotivoTraslado = 'Compra';
                break;
            case '03':
                $this->desMotivoTraslado = 'Consignación';
                break;
            case '04':
                $this->desMotivoTraslado = 'Traslado entre establecimientos';
                break;
            case '05':
                $this->desMotivoTraslado = 'Exportación';
                break;
            case '06':
                $this->desMotivoTraslado = 'Importación';
                break;
            default:
                $this->desMotivoTraslado = 'Venta';
                break;
        }
    }

    public function updatedCodMotivoTraslado()
    {
        $this->cargarMotivosTraslado();
    }

    public function searchClient()
    {
        $this->validate([
            'typeCodeCliente' => 'required|string|max:255',
            'numeroDocumentoCliente' => 'required|string|max:11',
        ], [
            'typeCodeCliente.required' => 'Debe seleccionar el tipo de documento del cliente',
            'numeroDocumentoCliente.required' => 'Debe ingresar el número de documento del cliente',
        ]);

        $client = Client::where('tipoDoc', $this->typeCodeCliente)
            ->where('numDoc', $this->numeroDocumentoCliente)
            ->first();

        if (!$client) {
            $response = $this->searchComplete($this->typeCodeCliente, $this->numeroDocumentoCliente);
            if ($response && isset($response['success']) && $response['success']) {
                $this->nameCliente = $response['data']->rznSocial ?? '';
                $this->addressCliente = $response['data']->direccion ?? '';
                $this->ubigeoCliente = $response['data']->ubigeo ?? '';
                $this->textoUbigeoCliente = $response['data']->texto_ubigeo ?? '';
                $this->phoneCliente = $response['data']->telefono ?? '';
                $this->emailCliente = $response['data']->email ?? '';
            } else {
                $this->addError('codeCliente', 'No se encontró el cliente con el documento proporcionado.');
            }
        } else {
            $this->client_id = $client->id;
            $this->nameCliente = $client->rznSocial;
            $this->addressCliente = $client->direccion;
            $this->ubigeoCliente = $client->ubigeo;
            $this->textoUbigeoCliente = $client->texto_ubigeo;
            $this->phoneCliente = $client->telefono;
            $this->emailCliente = $client->email;
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

    public function seleccionarProducto($productoId)
    {
        $this->producto_id = $productoId;
        $this->busquedaProducto = null;
        $this->productosFiltrados = [];

        // Auto-completar descripción
        $producto = ProductoCatalogo::find($productoId);
        if ($producto) {
            $this->descripcion_producto = $producto->description ?? '';
        }
    }

    public function agregarProducto()
    {
        $this->validate([
            'producto_id' => 'required|exists:producto_catalogos,id',
            'cantidad' => 'required|numeric|min:0.01|max:999999.99',
            'unidad' => 'required|string|max:10',
            'pesoBruto' => 'nullable|numeric|min:0',
            'pesoNeto' => 'nullable|numeric|min:0',
        ], [
            'producto_id.required' => 'Debe seleccionar un producto',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.numeric' => 'La cantidad debe ser un número',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'unidad.required' => 'La unidad es obligatoria',
            'pesoBruto.numeric' => 'El peso bruto debe ser un número',
            'pesoBruto.min' => 'El peso bruto debe ser mayor o igual a 0',
            'pesoNeto.numeric' => 'El peso neto debe ser un número',
            'pesoNeto.min' => 'El peso neto debe ser mayor o igual a 0',
        ]);

        $producto = ProductoCatalogo::find($this->producto_id);

        if (!$producto) {
            $this->addError('producto_id', 'El producto seleccionado no existe en la base de datos');
            return;
        }

        $productoData = [
            'producto_id' => $this->producto_id,
            'codigo' => $producto->code,
            'descripcion' => $this->descripcion_producto ?: $producto->description,
            'unidad' => $this->unidad,
            'cantidad' => $this->cantidad,
            'pesoBruto' => $this->pesoBruto,
            'pesoNeto' => $this->pesoNeto,
            'codLote' => $this->codLote,
            'fechaVencimiento' => $this->fechaVencimiento,
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
                // Producto duplicado: actualizar cantidad y otros valores
                $this->productos[$indiceExistente] = $productoData;
                session()->flash('message', 'Producto actualizado exitosamente con los nuevos valores');
            } else {
                // Agregar nuevo producto
                $this->productos[] = $productoData;
                session()->flash('message', 'Producto agregado exitosamente a la guía de remisión');
            }
        }

        // Limpiar campos del producto
        $this->producto_id = null;
        $this->cantidad = 1;
        $this->descripcion_producto = null;
        $this->unidad = 'NIU';
        $this->pesoBruto = null;
        $this->pesoNeto = null;
        $this->codLote = null;
        $this->fechaVencimiento = null;
        $this->busquedaProducto = null;
        $this->productosFiltrados = [];

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
        $this->descripcion_producto = null;
        $this->unidad = 'NIU';
        $this->pesoBruto = null;
        $this->pesoNeto = null;
        $this->codLote = null;
        $this->fechaVencimiento = null;
    }

    public function editarProducto($index)
    {
        if (isset($this->productos[$index])) {
            $producto = $this->productos[$index];
            $this->editando_producto = true;
            $this->indice_producto_editar = $index;
            $this->producto_id = $producto['producto_id'];
            $this->cantidad = $producto['cantidad'];
            $this->descripcion_producto = $producto['descripcion'];
            $this->unidad = $producto['unidad'];
            $this->pesoBruto = $producto['pesoBruto'];
            $this->pesoNeto = $producto['pesoNeto'];
            $this->codLote = $producto['codLote'];
            $this->fechaVencimiento = $producto['fechaVencimiento'];
            $this->modal_productos = true;
        }
    }

    public function cerrarModalProductos()
    {
        $this->modal_productos = false;
        $this->editando_producto = false;
        $this->indice_producto_editar = null;
        // Limpiar campos
        $this->producto_id = null;
        $this->cantidad = 1;
        $this->descripcion_producto = null;
        $this->unidad = 'NIU';
        $this->pesoBruto = null;
        $this->pesoNeto = null;
        $this->codLote = null;
        $this->fechaVencimiento = null;
        $this->busquedaProducto = null;
        $this->productosFiltrados = [];
    }

    public function eliminarProducto($index)
    {
        unset($this->productos[$index]);
        $this->productos = array_values($this->productos);
    }

    public function agregarDocumentoRelacionado()
    {
        $this->documentosRelacionados[] = [
            'tipoDoc' => '01',
            'serie' => '',
            'correlativo' => '',
        ];
    }

    public function eliminarDocumentoRelacionado($index)
    {
        unset($this->documentosRelacionados[$index]);
        $this->documentosRelacionados = array_values($this->documentosRelacionados);
    }

    public function crearGuiaRemision()
    {
        $rules = [
            'company_id' => 'required|exists:companies,id',
            'sucursal_id' => 'required|exists:sucursals,id',
            'client_id' => 'required|exists:clients,id',
            'tipoDoc' => 'required|in:09',
            'serie' => 'required|string|max:10',
            'correlativo' => 'required|string|max:10',
            'fechaEmision' => 'required|date',
            'tipoDocDestinatario' => 'required|string|max:10',
            'numDocDestinatario' => 'required|string|max:20',
            'rznSocialDestinatario' => 'required|string|max:200',
            'direccionDestinatario' => 'required|string|max:200',
            'direccionPartida' => 'required|string|max:200',
            'direccionLlegada' => 'required|string|max:200',
            'fechaInicioTraslado' => 'required|date',
            'codMotivoTraslado' => 'required|string|max:10',
            'desMotivoTraslado' => 'required|string|max:500',
            'modalidadTraslado' => 'required|string|max:10',
        ];

        $messages = [
            'company_id.required' => 'Debe seleccionar una empresa',
            'sucursal_id.required' => 'Debe seleccionar una sucursal',
            'client_id.required' => 'Debe seleccionar un cliente',
            'tipoDoc.required' => 'Debe seleccionar el tipo de documento',
            'serie.required' => 'La serie es obligatoria',
            'correlativo.required' => 'El correlativo es obligatorio',
            'fechaEmision.required' => 'La fecha de emisión es obligatoria',
            'tipoDocDestinatario.required' => 'Debe seleccionar el tipo de documento del destinatario',
            'numDocDestinatario.required' => 'Debe ingresar el número de documento del destinatario',
            'rznSocialDestinatario.required' => 'Debe ingresar la razón social del destinatario',
            'direccionDestinatario.required' => 'Debe ingresar la dirección del destinatario',
            'direccionPartida.required' => 'Debe ingresar la dirección de partida',
            'direccionLlegada.required' => 'Debe ingresar la dirección de llegada',
            'fechaInicioTraslado.required' => 'Debe ingresar la fecha de inicio de traslado',
            'codMotivoTraslado.required' => 'Debe seleccionar el motivo de traslado',
            'desMotivoTraslado.required' => 'Debe ingresar la descripción del motivo de traslado',
            'modalidadTraslado.required' => 'Debe seleccionar la modalidad de traslado',
        ];

        $this->validate($rules, $messages);

        if (empty($this->productos)) {
            $this->addError('productos', 'Debe agregar al menos un producto a la guía de remisión');
            return;
        }

        try {
            DB::beginTransaction();

            // Crear la guía de remisión
            $despatch = Despatch::create([
                'company_id' => $this->company_id,
                'sucursal_id' => $this->sucursal_id,
                'client_id' => $this->client_id,
                'tipoDoc' => $this->tipoDoc,
                'serie' => $this->serie,
                'correlativo' => $this->correlativo,
                'fechaEmision' => $this->fechaEmision,
                'tipoMoneda' => $this->tipoMoneda,
                'tipoDocDestinatario' => $this->tipoDocDestinatario,
                'numDocDestinatario' => $this->numDocDestinatario,
                'rznSocialDestinatario' => $this->rznSocialDestinatario,
                'direccionDestinatario' => $this->direccionDestinatario,
                'ubigeoDestinatario' => $this->ubigeoDestinatario,
                'tipoDocTransportista' => $this->tipoDocTransportista,
                'numDocTransportista' => $this->numDocTransportista,
                'rznSocialTransportista' => $this->rznSocialTransportista,
                'placaVehiculo' => $this->placaVehiculo,
                'codEstabDestino' => $this->codEstabDestino,
                'direccionPartida' => $this->direccionPartida,
                'ubigeoPartida' => $this->ubigeoPartida,
                'direccionLlegada' => $this->direccionLlegada,
                'ubigeoLlegada' => $this->ubigeoLlegada,
                'fechaInicioTraslado' => $this->fechaInicioTraslado,
                'fechaFinTraslado' => $this->fechaFinTraslado,
                'codMotivoTraslado' => $this->codMotivoTraslado,
                'desMotivoTraslado' => $this->desMotivoTraslado,
                'indicadorTransbordo' => $this->indicadorTransbordo,
                'pesoBrutoTotal' => $this->pesoBrutoTotal,
                'numeroBultos' => $this->numeroBultos,
                'modalidadTraslado' => $this->modalidadTraslado,
                'documentosRelacionados' => $this->documentosRelacionados,
                'observacion' => $this->observacion,
            ]);

            // Crear los detalles de la guía de remisión
            foreach ($this->productos as $producto) {
                DespatchDetail::create([
                    'despatch_id' => $despatch->id,
                    'unidad' => $producto['unidad'],
                    'cantidad' => $producto['cantidad'],
                    'codProducto' => $producto['codigo'],
                    'codProdSunat' => null,
                    'codProdGS1' => null,
                    'descripcion' => $producto['descripcion'],
                    'pesoBruto' => $producto['pesoBruto'],
                    'pesoNeto' => $producto['pesoNeto'],
                    'codLote' => $producto['codLote'],
                    'fechaVencimiento' => $producto['fechaVencimiento'],
                    'codigoUnidadMedida' => null,
                    'codigoProductoSUNAT' => null,
                    'codigoProductoGS1' => null,
                ]);
            }

            DB::commit();

            session()->flash('success', 'Guía de remisión creada exitosamente');
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollback();
            $this->addError('general', 'Error al crear la guía de remisión. Por favor, verifique los datos e intente nuevamente. Detalle: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset([
            'client_id', 'tipoDocDestinatario', 'numDocDestinatario', 'rznSocialDestinatario',
            'direccionDestinatario', 'ubigeoDestinatario', 'tipoDocTransportista', 'numDocTransportista',
            'rznSocialTransportista', 'placaVehiculo', 'codEstabDestino', 'direccionPartida',
            'ubigeoPartida', 'direccionLlegada', 'ubigeoLlegada', 'fechaFinTraslado', 'codMotivoTraslado',
            'desMotivoTraslado', 'indicadorTransbordo', 'pesoBrutoTotal', 'numeroBultos',
            'modalidadTraslado', 'documentosRelacionados', 'observacion', 'numeroDocumentoCliente',
            'typeCodeCliente', 'nameCliente', 'addressCliente', 'ubigeoCliente', 'textoUbigeoCliente',
            'phoneCliente', 'emailCliente', 'productos'
        ]);
        $this->generarSerieYCorrelativo();
        $this->cargarMotivosTraslado();
    }

    public function render()
    {
        $companies = Company::whereHas('sucursales', function($query) {
            $query->where('isActive', true);
        })->get();

        $sucursales = collect();
        if ($this->company_id) {
            $sucursales = Sucursal::where('company_id', $this->company_id)
                ->where('isActive', true)
                ->get();
        }

        $clients = Client::all();
        $productos = ProductoCatalogo::where('isActive', true)->with(['category', 'brand'])->get();
        $unidades = SunatUnidadMedida::getUnidadesForSelect();

        return view('livewire.facturacion.despatche-create-index', compact('companies', 'sucursales', 'clients', 'productos', 'unidades'));
    }
}
