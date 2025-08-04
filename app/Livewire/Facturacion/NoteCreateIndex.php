<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use App\Models\Facturacion\Note;
use App\Models\Facturacion\NoteDetail;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Configuration\SunatUnidadMedida;
use Illuminate\Support\Facades\DB;
use App\Traits\SearchDocument;

class NoteCreateIndex extends Component
{
    use SearchDocument;

    // Datos de la nota
    public $company_id;
    public $sucursal_id;
    public $client_id;
    public $tipoDoc = '07'; // 07: Nota de Crédito, 08: Nota de Débito
    public $tipoOperacion = '0101';
    public $serie;
    public $correlativo;
    public $fechaEmision;
    public $formaPago_moneda = 'PEN';
    public $formaPago_tipo = '01';
    public $tipoMoneda = 'PEN';
    public $observacion;
    public $note_reference;

    // Documento que modifica
    public $tipoDocModifica = '01'; // 01: Factura, 03: Boleta
    public $serieModifica;
    public $correlativoModifica;
    public $fechaEmisionModifica;
    public $tipoMonedaModifica = 'PEN';

    // Motivo de la nota
    public $codMotivo;
    public $desMotivo;

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

    public function mount()
    {
        $this->fechaEmision = date('Y-m-d');
        $this->fechaEmisionModifica = date('Y-m-d', strtotime('-1 day'));

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

        // Cargar motivos según el tipo de documento
        $this->cargarMotivos();
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

    public function updatedTipoDoc()
    {
        $this->generarSerieYCorrelativo();
        $this->cargarMotivos();
    }

    private function generarSerieYCorrelativo()
    {
        if ($this->sucursal_id && $this->tipoDoc) {
            $sucursal = Sucursal::find($this->sucursal_id);
            if ($sucursal && $sucursal->series_suffix) {
                // Generar serie según el tipo de documento
                $prefix = $this->tipoDoc === '07' ? 'NC' : 'ND';
                $this->serie = $prefix . str_pad($sucursal->series_suffix, 3, '0', STR_PAD_LEFT);

                // Generar correlativo secuencial
                $ultimaNota = Note::where('serie', $this->serie)->orderBy('correlativo', 'desc')->first();
                $this->correlativo = $ultimaNota ? (int)$ultimaNota->correlativo + 1 : 1;
            }
        }
    }

    private function cargarMotivos()
    {
        // Cargar motivos según el tipo de documento
        if ($this->tipoDoc === '07') {
            // Motivos para Nota de Crédito (Catálogo 09)
            $this->codMotivo = '01';
            $this->desMotivo = 'Anulación de la operación';
        } else {
            // Motivos para Nota de Débito (Catálogo 10)
            $this->codMotivo = '01';
            $this->desMotivo = 'Intereses por mora';
        }
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
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.numeric' => 'La cantidad debe ser un número',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'precio_unitario.min' => 'El precio unitario debe ser mayor o igual a 0',
            'unidad.required' => 'La unidad es obligatoria',
        ]);

        $producto = ProductoCatalogo::find($this->producto_id);

        if (!$producto) {
            $this->addError('producto_id', 'El producto seleccionado no existe en la base de datos');
            return;
        }

        // Calcular montos - Los precios ya incluyen IGV
        $valor_venta = $this->cantidad * $this->precio_unitario;
        $total_producto = $valor_venta;
        $igv_producto = 0; // Se recalculará en calcularTotales()

        $productoData = [
            'producto_id' => $this->producto_id,
            'codigo' => $producto->code,
            'descripcion' => $this->descripcion_producto ?: $producto->description,
            'unidad' => $this->unidad,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'valor_venta' => $valor_venta,
            'igv' => $igv_producto,
            'total' => $total_producto,
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
                session()->flash('message', 'Producto agregado exitosamente a la nota');
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
        // Limpiar campos
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
            $valor_con_igv = $producto['valor_venta'];
            return $valor_con_igv / 1.18; // Dividimos por 1.18 para obtener el valor sin IGV
        });

        // El IGV es la diferencia entre el total con IGV y el subtotal sin IGV
        $total_con_igv = collect($this->productos)->sum('valor_venta');
        $this->igv = $total_con_igv - $this->subtotal;

        // El total es la suma de todos los valores con IGV
        $this->total = $total_con_igv;

        // Actualizar el IGV de cada producto individual
        foreach ($this->productos as $index => $producto) {
            $valor_sin_igv = $producto['valor_venta'] / 1.18;
            $igv_producto = $producto['valor_venta'] - $valor_sin_igv;
            $this->productos[$index]['igv'] = $igv_producto;
        }
    }

    public function crearNota()
    {
        $rules = [
            'company_id' => 'required|exists:companies,id',
            'sucursal_id' => 'required|exists:sucursals,id',
            'client_id' => 'required|exists:clients,id',
            'tipoDoc' => 'required|in:07,08',
            'tipoOperacion' => 'required|in:0101,0102,0103',
            'serie' => 'required|string|max:10',
            'correlativo' => 'required|string|max:10',
            'fechaEmision' => 'required|date',
            'formaPago_tipo' => 'required|in:01,02,03',
            'tipoDocModifica' => 'required|in:01,03',
            'serieModifica' => 'required|string|max:10',
            'correlativoModifica' => 'required|string|max:10',
            'fechaEmisionModifica' => 'required|date',
            'codMotivo' => 'required|string|max:10',
            'desMotivo' => 'required|string|max:500',
        ];

        $messages = [
            'company_id.required' => 'Debe seleccionar una empresa',
            'sucursal_id.required' => 'Debe seleccionar una sucursal',
            'client_id.required' => 'Debe seleccionar un cliente',
            'tipoDoc.required' => 'Debe seleccionar el tipo de documento',
            'tipoDoc.in' => 'El tipo de documento debe ser 07 (Nota de Crédito) o 08 (Nota de Débito)',
            'serie.required' => 'La serie es obligatoria',
            'correlativo.required' => 'El correlativo es obligatorio',
            'fechaEmision.required' => 'La fecha de emisión es obligatoria',
            'formaPago_tipo.required' => 'Debe seleccionar la forma de pago',
            'tipoDocModifica.required' => 'Debe seleccionar el tipo de documento que modifica',
            'serieModifica.required' => 'La serie del documento que modifica es obligatoria',
            'correlativoModifica.required' => 'El correlativo del documento que modifica es obligatorio',
            'fechaEmisionModifica.required' => 'La fecha de emisión del documento que modifica es obligatoria',
            'codMotivo.required' => 'Debe seleccionar el motivo de la nota',
            'desMotivo.required' => 'Debe ingresar la descripción del motivo',
        ];

        $this->validate($rules, $messages);

        if (empty($this->productos)) {
            $this->addError('productos', 'Debe agregar al menos un producto a la nota');
            return;
        }

        try {
            DB::beginTransaction();

            // Crear la nota
            $note = Note::create([
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
                'tipoDocModifica' => $this->tipoDocModifica,
                'serieModifica' => $this->serieModifica,
                'correlativoModifica' => $this->correlativoModifica,
                'fechaEmisionModifica' => $this->fechaEmisionModifica,
                'tipoMonedaModifica' => $this->tipoMonedaModifica,
                'codMotivo' => $this->codMotivo,
                'desMotivo' => $this->desMotivo,
                'mtoOperGravadas' => $this->subtotal,
                'mtoIGV' => $this->igv,
                'totalImpuestos' => $this->igv,
                'valorVenta' => $this->subtotal,
                'subTotal' => $this->total,
                'mtoImpVenta' => $this->total,
                'monto_letras' => $this->numeroALetras($this->total),
                'observacion' => $this->observacion,
                'note_reference' => $this->note_reference,
            ]);

            // Crear los detalles de la nota
            foreach ($this->productos as $producto) {
                // Calcular valores sin IGV para el detalle
                $valor_sin_igv = $producto['valor_venta'] / 1.18;
                $igv_producto = $producto['valor_venta'] - $valor_sin_igv;

                NoteDetail::create([
                    'note_id' => $note->id,
                    'unidad' => $producto['unidad'],
                    'cantidad' => $producto['cantidad'],
                    'codProducto' => $producto['codigo'],
                    'codProdSunat' => null,
                    'codProdGS1' => null,
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

            session()->flash('success', 'Nota creada exitosamente');
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollback();
            $this->addError('general', 'Error al crear la nota. Por favor, verifique los datos e intente nuevamente. Detalle: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset([
            'client_id', 'tipoDocModifica', 'serieModifica', 'correlativoModifica',
            'fechaEmisionModifica', 'codMotivo', 'desMotivo', 'numeroDocumentoCliente',
            'typeCodeCliente', 'nameCliente', 'addressCliente', 'ubigeoCliente',
            'textoUbigeoCliente', 'phoneCliente', 'emailCliente', 'productos',
            'observacion', 'note_reference'
        ]);
        $this->generarSerieYCorrelativo();
        $this->cargarMotivos();
    }

    private function numeroALetras($numero)
    {
        // Implementar conversión de número a letras
        return 'CERO CON 00/100 SOLES';
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

        return view('livewire.facturacion.note-create-index', compact('companies', 'sucursales', 'clients', 'productos', 'unidades'));
    }
}
