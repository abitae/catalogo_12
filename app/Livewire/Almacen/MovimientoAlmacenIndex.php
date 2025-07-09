<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\MovimientoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Exports\Almacen\MovimientoAlmacenExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mary\Traits\Toast;

class MovimientoAlmacenIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'code'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'almacen_filter' => ['except' => ''],
        'tipo_movimiento_filter' => ['except' => ''],
        'estado_filter' => ['except' => '']
    ];

    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $almacen_filter = '';
    public $tipo_movimiento_filter = '';
    public $estado_filter = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';

    // Modal Form Movimiento
    public $modal_form_movimiento = false;
    public $movimiento_id = null;
    public $movimiento = null;
    public MovimientoAlmacen $movimiento_detalle;
    public $movimientosExportar = null;
    public $modal_detalle_movimiento = false;

    // Variables para el formulario
    public $code = '';
    public $tipo_movimiento = 'entrada';
    public $almacen_id = '';
    public $tipo_pago = 'efectivo';
    public $tipo_documento = 'factura';
    public $numero_documento = '';
    public $tipo_operacion = 'compra';
    public $forma_pago = 'contado';
    public $tipo_moneda = 'PEN';
    public $fecha_emision = '';
    public $fecha_vencimiento = '';
    public $estado = 'pendiente';
    public $observaciones = '';
    public $subtotal = 0;
    public $descuento = 0;
    public $impuesto = 0;
    public $total = 0;

    // Productos seleccionados
    public $productos_seleccionados = [];
    public $cantidades = [];
    public $precios = [];
    public $lotes = [];
    public $productos_disponibles;
    public $producto_seleccionado = null;
    public $cantidad_producto = 1;
    public $precio_producto = 0;
    public $lote_producto = '';

    protected function rules()
    {
        return [
            'code' => 'required|min:3|max:50',
            'tipo_movimiento' => 'required|in:entrada,salida',
            'almacen_id' => 'required|exists:almacenes,id',
            'tipo_pago' => 'required|in:efectivo,tarjeta,transferencia,cheque',
            'tipo_documento' => 'required|in:factura,boleta,nota_credito,nota_debito,guia_remision',
            'numero_documento' => 'required|string|max:50',
            'tipo_operacion' => 'required|in:compra,venta,ajuste,transferencia,devolucion',
            'forma_pago' => 'required|in:contado,credito',
            'tipo_moneda' => 'required|in:PEN,USD,EUR',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'estado' => 'required|in:pendiente,completado,cancelado',
            'observaciones' => 'nullable|string|max:500',
            'productos_seleccionados' => 'required|array|min:1',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|numeric|min:1',
            'precios' => 'required|array',
            'precios.*' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'descuento' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0'
        ];
    }

    protected $messages = [
        'code.required' => 'El código es requerido',
        'code.min' => 'El código debe tener al menos 3 caracteres',
        'code.max' => 'El código no debe exceder los 50 caracteres',
        'tipo_movimiento.required' => 'El tipo de movimiento es requerido',
        'tipo_movimiento.in' => 'El tipo de movimiento debe ser entrada o salida',
        'almacen_id.required' => 'El almacén es requerido',
        'almacen_id.exists' => 'El almacén seleccionado no existe',
        'tipo_pago.required' => 'El tipo de pago es requerido',
        'tipo_pago.in' => 'El tipo de pago debe ser válido',
        'tipo_documento.required' => 'El tipo de documento es requerido',
        'tipo_documento.in' => 'El tipo de documento debe ser válido',
        'numero_documento.required' => 'El número de documento es requerido',
        'numero_documento.max' => 'El número de documento no debe exceder los 50 caracteres',
        'tipo_operacion.required' => 'El tipo de operación es requerido',
        'tipo_operacion.in' => 'El tipo de operación debe ser válido',
        'forma_pago.required' => 'La forma de pago es requerida',
        'forma_pago.in' => 'La forma de pago debe ser válida',
        'tipo_moneda.required' => 'El tipo de moneda es requerido',
        'tipo_moneda.in' => 'El tipo de moneda debe ser válido',
        'fecha_emision.required' => 'La fecha de emisión es requerida',
        'fecha_emision.date' => 'La fecha de emisión debe ser válida',
        'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser válida',
        'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser posterior o igual a la fecha de emisión',
        'estado.required' => 'El estado es requerido',
        'estado.in' => 'El estado debe ser válido',
        'observaciones.max' => 'Las observaciones no deben exceder los 500 caracteres',
        'productos_seleccionados.required' => 'Debe seleccionar al menos un producto',
        'productos_seleccionados.array' => 'Los productos deben ser un array',
        'productos_seleccionados.min' => 'Debe seleccionar al menos un producto',
        'cantidades.required' => 'Las cantidades son requeridas',
        'cantidades.array' => 'Las cantidades deben ser un array',
        'cantidades.*.required' => 'La cantidad es requerida',
        'cantidades.*.numeric' => 'La cantidad debe ser un número',
        'cantidades.*.min' => 'La cantidad debe ser mayor a 0',
        'precios.required' => 'Los precios son requeridos',
        'precios.array' => 'Los precios deben ser un array',
        'precios.*.required' => 'El precio es requerido',
        'precios.*.numeric' => 'El precio debe ser un número',
        'precios.*.min' => 'El precio debe ser mayor o igual a 0',
        'subtotal.required' => 'El subtotal es requerido',
        'subtotal.numeric' => 'El subtotal debe ser un número',
        'subtotal.min' => 'El subtotal debe ser mayor o igual a 0',
        'descuento.required' => 'El descuento es requerido',
        'descuento.numeric' => 'El descuento debe ser un número',
        'descuento.min' => 'El descuento debe ser mayor o igual a 0',
        'impuesto.required' => 'El impuesto es requerido',
        'impuesto.numeric' => 'El impuesto debe ser un número',
        'impuesto.min' => 'El impuesto debe ser mayor o igual a 0',
        'total.required' => 'El total es requerido',
        'total.numeric' => 'El total debe ser un número',
        'total.min' => 'El total debe ser mayor o igual a 0'
    ];

    protected $listeners = ['actualizarProductosDisponibles', 'calcularTotales'];

    public function mount()
    {
        $this->fecha_inicio = now()->subDays(7)->format('Y-m-d');
        $this->fecha_fin = now()->endOfDay()->format('Y-m-d');
        $this->productos_disponibles = collect();
        $this->producto_seleccionado = null;
        $this->cantidad_producto = 1;
        $this->precio_producto = 0;
        $this->productos_seleccionados = [];
        $this->cantidades = [];
        $this->precios = [];
        $this->lotes = [];
        $this->asegurarProductoSeleccionado();
        $this->resetForm();
        $this->actualizarProductosDisponibles();
        $this->generarCodigo();
    }

    public function resetForm()
    {
        $this->reset([
            'code',
            'tipo_movimiento',
            'almacen_id',
            'tipo_pago',
            'tipo_documento',
            'numero_documento',
            'tipo_operacion',
            'forma_pago',
            'tipo_moneda',
            'fecha_emision',
            'fecha_vencimiento',
            'estado',
            'observaciones',
            'subtotal',
            'descuento',
            'impuesto',
            'total',
            'movimiento_id',
            'producto_seleccionado',
            'cantidad_producto',
            'precio_producto',
            'lote_producto'
        ]);
        $this->fecha_emision = now()->format('Y-m-d');
        $this->tipo_movimiento = 'entrada';
        $this->estado = 'pendiente';
        $this->productos_disponibles = collect();
        $this->productos_seleccionados = [];
        $this->cantidades = [];
        $this->precios = [];
        $this->lotes = [];
        $this->asegurarProductoSeleccionado();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'sortField',
            'sortDirection',
            'almacen_filter',
            'tipo_movimiento_filter',
            'estado_filter',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados');
    }

    public function render()
    {
        $query = MovimientoAlmacen::query()
            ->with(['almacen', 'user'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('numero_documento', 'like', '%' . $this->search . '%')
                        ->orWhere('observaciones', 'like', '%' . $this->search . '%')
                        ->orWhereJsonContains('productos', ['code' => $this->search ]);
                });
            })

            ->when($this->almacen_filter, fn($q) => $q->where('almacen_id', $this->almacen_filter))
            ->when($this->tipo_movimiento_filter, fn($q) => $q->where('tipo_movimiento', $this->tipo_movimiento_filter))
            ->when($this->estado_filter, fn($q) => $q->where('estado', $this->estado_filter))
            ->when($this->fecha_inicio, fn($q) => $q->where('fecha_emision', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->where('fecha_emision', '<=', $this->fecha_fin))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->movimientosExportar = $query->get();
        $movimientos = $query->latest()->paginate($this->perPage);

        return view('livewire.almacen.movimiento-almacen-index', [
            'movimientos' => $movimientos,
            'almacenes' => WarehouseAlmacen::where('estado', true)->get(),
            'productos' => ProductoAlmacen::where('estado', true)->get()
        ]);
    }

    public function generarCodigo()
    {
        $usuarioId = Auth::user()->id;
        $numero = 1;
        $codigoGenerado = false;

        while (!$codigoGenerado) {
            $codigo = 'MOV' . str_pad($numero, 3, '0', STR_PAD_LEFT) . '-' . str_pad($usuarioId, 2, '0', STR_PAD_LEFT);

            // Verificar si el código ya existe
            $existeCodigo = MovimientoAlmacen::where('code', $codigo)->exists();

            if (!$existeCodigo) {
                $this->code = $codigo;
                $codigoGenerado = true;
            } else {
                $numero++;
            }
        }
    }

    public function nuevoMovimiento()
    {
        $this->resetForm();
        $this->generarCodigo();
        $this->producto_seleccionado = null;
        $this->cantidad_producto = 1;
        $this->precio_producto = 0;
        $this->modal_form_movimiento = true;
    }

    public function editarMovimiento($id)
    {
        $this->movimiento_id = $id;
        $this->movimiento = MovimientoAlmacen::with(['almacen', 'user'])->find($id);

        // Verificar si el movimiento está pendiente
        if ($this->movimiento->estado !== 'pendiente') {
            $this->error('Solo se pueden editar movimientos en estado pendiente.');
            return;
        }

        // Cargar datos básicos del movimiento
        $this->code = $this->movimiento->code;
        $this->tipo_movimiento = $this->movimiento->tipo_movimiento;
        $this->almacen_id = $this->movimiento->almacen_id;
        $this->tipo_pago = $this->movimiento->tipo_pago;
        $this->tipo_documento = $this->movimiento->tipo_documento;
        $this->numero_documento = $this->movimiento->numero_documento;
        $this->tipo_operacion = $this->movimiento->tipo_operacion;
        $this->forma_pago = $this->movimiento->forma_pago;
        $this->tipo_moneda = $this->movimiento->tipo_moneda;
        $this->fecha_emision = $this->movimiento->fecha_emision;
        $this->fecha_vencimiento = $this->movimiento->fecha_vencimiento ? $this->movimiento->fecha_vencimiento : '';
        $this->estado = $this->movimiento->estado;
        $this->observaciones = $this->movimiento->observaciones;
        $this->subtotal = $this->movimiento->subtotal;
        $this->descuento = $this->movimiento->descuento;
        $this->impuesto = $this->movimiento->impuesto;
        $this->total = $this->movimiento->total;

        // Cargar productos disponibles del almacén
        $productosAlmacen = ProductoAlmacen::where('almacen_id', $this->almacen_id)
            ->where('estado', true)
            ->get();

        // Cargar productos seleccionados con sus cantidades y precios
        $productosMovimiento = collect($this->movimiento->productos);
        $this->productos_seleccionados = $productosMovimiento->pluck('id')->toArray();
        $this->cantidades = $productosMovimiento->pluck('cantidad', 'id')->toArray();
        $this->precios = $productosMovimiento->pluck('precio', 'id')->toArray();

        // Crear productos_disponibles incluyendo todos los productos del movimiento
        $this->productos_disponibles = collect();

        // Agregar productos del almacén actual
        foreach ($productosAlmacen as $producto) {
            $this->productos_disponibles->push($producto);
        }

        // Agregar productos del movimiento que no estén en el almacén actual
        foreach ($productosMovimiento as $productoMov) {
            $existe = $this->productos_disponibles->contains('id', $productoMov['id']);
            if (!$existe) {
                // Crear un objeto temporal del producto para mostrarlo en la lista
                $productoTemp = new \stdClass();
                $productoTemp->id = $productoMov['id'];
                $productoTemp->code = $productoMov['code'];
                $productoTemp->nombre = $productoMov['nombre'];
                $productoTemp->unidad_medida = $productoMov['unidad_medida'];
                $productoTemp->stock_actual = 0; // No disponible en este almacén
                $productoTemp->precio_venta = $productoMov['precio'];
                $productoTemp->estado = false; // Marcado como no disponible

                $this->productos_disponibles->push($productoTemp);
            }
        }

        // Inicializar variables de selección
        $this->producto_seleccionado = null;
        $this->cantidad_producto = 1;
        $this->precio_producto = 0;

        $this->modal_form_movimiento = true;
    }

    public function guardarMovimiento()
    {
        $this->validate();

        // Verificar si es una edición y si el movimiento está pendiente
        if ($this->movimiento_id) {
            $movimiento = MovimientoAlmacen::findOrFail($this->movimiento_id);
            if ($movimiento->estado !== 'pendiente') {
                $this->error('Solo se pueden editar movimientos en estado pendiente.');
                return;
            }
        }

        // Asegurar que el estado sea pendiente al guardar
        $this->estado = 'pendiente';

        $productos = collect($this->productos_seleccionados)
            ->map(function ($productoId) {
                $producto = null;

                // Buscar en productos_disponibles si es una colección válida
                if ($this->productos_disponibles && $this->productos_disponibles->count()) {
                    $producto = $this->productos_disponibles->first(function($p) use ($productoId) {
                        return $p->id == $productoId;
                    });
                }

                // Si no se encuentra en productos_disponibles, buscar directamente en la base de datos
                if (!$producto) {
                    $producto = ProductoAlmacen::find($productoId);
                }

                if (!$producto) {
                    throw new \Exception("Producto con ID {$productoId} no encontrado.");
                }

                return [
                    'id' => $producto->id,
                    'code' => $producto->code,
                    'nombre' => $producto->nombre,
                    'cantidad' => $this->cantidades[$productoId],
                    'precio' => $this->precios[$productoId],
                    'unidad_medida' => $producto->unidad_medida,
                    'lote' => $this->lotes[$productoId] ?? null
                ];
            })
            ->toArray();

        $data = [
            'code' => $this->code,
            'tipo_movimiento' => $this->tipo_movimiento,
            'almacen_id' => $this->almacen_id,
            'user_id' => Auth::user()->id,
            'tipo_pago' => $this->tipo_pago,
            'tipo_documento' => $this->tipo_documento,
            'numero_documento' => $this->numero_documento,
            'tipo_operacion' => $this->tipo_operacion,
            'forma_pago' => $this->forma_pago,
            'tipo_moneda' => $this->tipo_moneda,
            'fecha_emision' => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento ?: null,
            'productos' => $productos,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'subtotal' => $this->subtotal,
            'descuento' => $this->descuento,
            'impuesto' => $this->impuesto,
            'total' => $this->total
        ];

        try {
            DB::beginTransaction();

            if ($this->movimiento_id) {
                $movimiento->update($data);
                $mensaje = 'Movimiento actualizado correctamente.';

                Log::info('Movimiento actualizado', [
                    'user_id' => Auth::id(),
                    'movimiento_id' => $this->movimiento_id,
                    'code' => $this->code,
                    'tipo_movimiento' => $this->tipo_movimiento,
                    'almacen_id' => $this->almacen_id,
                    'total' => $this->total,
                    'productos_count' => count($productos)
                ]);
            } else {
                $movimiento = MovimientoAlmacen::create($data);
                $mensaje = 'Movimiento creado correctamente.';

                Log::info('Movimiento creado', [
                    'user_id' => Auth::id(),
                    'movimiento_id' => $movimiento->id,
                    'code' => $this->code,
                    'tipo_movimiento' => $this->tipo_movimiento,
                    'almacen_id' => $this->almacen_id,
                    'total' => $this->total,
                    'productos_count' => count($productos)
                ]);
            }

            DB::commit();

            $this->modal_form_movimiento = false;
            $this->resetForm();
            $this->success($mensaje);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al guardar movimiento', [
                'user_id' => Auth::id(),
                'movimiento_id' => $this->movimiento_id,
                'code' => $this->code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Error al guardar el movimiento: ' . $e->getMessage());
        }
    }

    public function exportarMovimientos()
    {
        return Excel::download(new MovimientoAlmacenExport($this->movimientosExportar), 'movimientos_' . date('Y-m-d_H-i-s') . '.xlsx');
        $this->reset(['movimientosExportar']);
    }

    public function actualizarProductosDisponibles()
    {
        if (!$this->almacen_id) {
            $this->productos_disponibles = collect();
            return;
        }

        $productos = ProductoAlmacen::query()
            ->where('estado', true)
            ->where('almacen_id', $this->almacen_id);

        // Si es salida, solo mostrar productos con stock disponible
        if ($this->tipo_movimiento === 'salida') {
            $productos->where('stock_actual', '>', 0);
        }

        $this->productos_disponibles = $productos->get() ?: collect();
    }

    /**
     * Actualiza los productos disponibles basándose en el lote seleccionado
     */
    public function actualizarProductosPorLote()
    {
        if (!$this->almacen_id || empty($this->lote_producto)) {
            $this->actualizarProductosDisponibles();
            return;
        }

        $productos = ProductoAlmacen::query()
            ->where('estado', true)
            ->where('almacen_id', $this->almacen_id)
            ->where('lote', $this->lote_producto);

        // Si es salida, solo mostrar productos con stock disponible en el lote
        if ($this->tipo_movimiento === 'salida') {
            $productos->where('stock_actual', '>', 0);
        }

        $this->productos_disponibles = $productos->get() ?: collect();
    }

    /**
     * Obtiene los lotes disponibles en el almacén seleccionado
     */
    public function getLotesDisponibles()
    {
        if (!$this->almacen_id) {
            return collect();
        }

        return ProductoAlmacen::where('almacen_id', $this->almacen_id)
            ->where('estado', true)
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->distinct()
            ->pluck('lote')
            ->filter();
    }

    /**
     * Obtiene productos disponibles en un lote específico
     */
    public function getProductosEnLote($lote)
    {
        if (!$this->almacen_id || empty($lote)) {
            return collect();
        }

        $productos = ProductoAlmacen::where('almacen_id', $this->almacen_id)
            ->where('estado', true)
            ->where('lote', $lote);

        if ($this->tipo_movimiento === 'salida') {
            $productos->where('stock_actual', '>', 0);
        }

        return $productos->get();
    }

    /**
     * Sugiere automáticamente productos cuando se selecciona un lote
     */
    public function updatedLoteProducto()
    {
        if (!empty($this->lote_producto)) {
            $this->actualizarProductosPorLote();

            // Si solo hay un producto en el lote, seleccionarlo automáticamente
            if ($this->productos_disponibles->count() === 1) {
                $this->producto_seleccionado = $this->productos_disponibles->first()->id;
                $this->updatedProductoSeleccionado();
            }
        } else {
            $this->actualizarProductosDisponibles();
        }
    }

    public function agregarProducto()
    {
        // Asegurar que las variables estén definidas
        $this->asegurarProductoSeleccionado();

        // Verificar que productos_disponibles sea una colección válida
        if (!$this->productos_disponibles || !$this->productos_disponibles->count()) {
            $this->error('No hay productos disponibles en el almacén.');
            return;
        }

        // Verificar que producto_seleccionado esté definido
        if (!$this->producto_seleccionado) {
            $this->error('Debe seleccionar un producto.');
            return;
        }

        $this->validate([
            'producto_seleccionado' => 'required|exists:productos_almacen,id',
            'cantidad_producto' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Solo validar stock para salidas
                    if ($this->tipo_movimiento === 'salida') {
                        $productoSeleccionado = $this->producto_seleccionado;
                        $productosDisponibles = $this->productos_disponibles;

                        $producto = $productosDisponibles->first(function($p) use ($productoSeleccionado) {
                            return $p->id == $productoSeleccionado;
                        });

                        if (!$producto) {
                            $fail("El producto seleccionado no está disponible en el almacén.");
                            return;
                        }

                        // Si se especifica un lote, validar stock por lote
                        if (!empty($this->lote_producto)) {
                            $stockLote = ProductoAlmacen::tieneStockSuficienteEnLoteYAlmacen(
                                $this->lote_producto,
                                $this->almacen_id,
                                $value
                            );

                            if (!$stockLote) {
                                $stockDisponible = ProductoAlmacen::getStockTotalPorLoteYAlmacen(
                                    $this->lote_producto,
                                    $this->almacen_id
                                );
                                $fail("Stock insuficiente en lote {$this->lote_producto}. Disponible: {$stockDisponible}, Solicitado: {$value}");
                            }
                        } else {
                            // Validación tradicional sin lote específico
                            if (!$producto->tieneStockSuficiente($value)) {
                                $fail("La cantidad excede el stock disponible ({$producto->stock_actual})");
                            }
                        }
                    }
                }
            ],
            'precio_producto' => 'required|numeric|min:0',
            'lote_producto' => 'nullable|string|max:255'
        ]);

        if (!in_array($this->producto_seleccionado, $this->productos_seleccionados)) {
            $this->productos_seleccionados[] = $this->producto_seleccionado;
            $this->cantidades[$this->producto_seleccionado] = $this->cantidad_producto;
            $this->precios[$this->producto_seleccionado] = $this->precio_producto;
            $this->lotes[$this->producto_seleccionado] = $this->lote_producto;
        }

        $this->reset(['producto_seleccionado', 'cantidad_producto', 'precio_producto', 'lote_producto']);
        $this->calcularTotales();
    }

    public function quitarProducto($productoId)
    {
        $key = array_search($productoId, $this->productos_seleccionados);
        if ($key !== false) {
            unset($this->productos_seleccionados[$key], $this->cantidades[$productoId], $this->precios[$productoId], $this->lotes[$productoId]);
            $this->productos_seleccionados = array_values($this->productos_seleccionados);
        }
        $this->calcularTotales();
    }

    public function updatedAlmacenId()
    {
        $this->actualizarProductosDisponibles();
        $this->reset(['productos_seleccionados', 'cantidades', 'precios', 'lotes']);
        $this->producto_seleccionado = null;
        $this->cantidad_producto = 1;
        $this->precio_producto = 0;
        $this->lote_producto = '';
        $this->calcularTotales();
    }

    public function updatedTipoMovimiento()
    {
        $this->actualizarProductosDisponibles();
        $this->reset(['productos_seleccionados', 'cantidades', 'precios', 'lotes']);
        $this->producto_seleccionado = null;
        $this->cantidad_producto = 1;
        $this->precio_producto = 0;
        $this->lote_producto = '';
        $this->calcularTotales();
    }

    public function updatedProductoSeleccionado()
    {
        // Asegurar que las variables estén definidas
        $this->asegurarProductoSeleccionado();

        if (!$this->producto_seleccionado) {
            return;
        }

        // Verificar que productos_disponibles sea una colección válida
        if (!$this->productos_disponibles || !$this->productos_disponibles->count()) {
            return;
        }

        // Capturar la variable en el scope de la función
        $productoSeleccionado = $this->producto_seleccionado;
        $productosDisponibles = $this->productos_disponibles;

        $producto = $productosDisponibles->first(function($p) use ($productoSeleccionado) {
            return $p->id == $productoSeleccionado;
        });

        if ($producto) {
            // Mostrar la cantidad existente en el almacén
            $this->cantidad_producto = 1;

            // Si es salida, limitar la cantidad al stock disponible
            if ($this->tipo_movimiento === 'salida') {
                $this->cantidad_producto = min($this->cantidad_producto, $producto->stock_actual);
            }

            // Establecer un precio por defecto si no hay uno
            if ($this->precio_producto <= 0) {
                $this->precio_producto = $producto->precio_venta ?? 0;
            }
        }
    }

    public function updatedFormaPago()
    {
        // Si la forma de pago no es crédito, limpiar la fecha de vencimiento
        if ($this->forma_pago !== 'credito') {
            $this->fecha_vencimiento = '';
        } else {
            // Si es crédito y no hay fecha de vencimiento, establecer una por defecto
            if (empty($this->fecha_vencimiento)) {
                $this->fecha_vencimiento = now()->addDays(30)->format('Y-m-d');
            }
        }
    }

    public function updatedCantidades()
    {
        $this->calcularTotales();
    }

    public function updatedPrecios()
    {
        $this->calcularTotales();
    }

    public function updatedDescuento()
    {
        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        $subtotal = 0;
        foreach ($this->productos_seleccionados as $productoId) {
            if (isset($this->cantidades[$productoId]) && isset($this->precios[$productoId])) {
                $subtotal += $this->cantidades[$productoId] * $this->precios[$productoId];
            }
        }

        $this->subtotal = $subtotal;
        $this->impuesto = $subtotal * 0.18; // IGV 18%
        $this->total = $this->subtotal + $this->impuesto - $this->descuento;
    }

    /**
     * Asegura que producto_seleccionado esté siempre definido
     */
    private function asegurarProductoSeleccionado()
    {
        if (!isset($this->producto_seleccionado)) {
            $this->producto_seleccionado = null;
        }

        if (!isset($this->cantidad_producto)) {
            $this->cantidad_producto = 1;
        }

        if (!isset($this->precio_producto)) {
            $this->precio_producto = 0;
        }

        if (!isset($this->lote_producto)) {
            $this->lote_producto = '';
        }
    }

    public function completarMovimiento($id)
    {
        try {
            DB::beginTransaction();

            $movimiento = MovimientoAlmacen::findOrFail($id);

            if ($movimiento->estado !== 'pendiente') {
                $this->error('Solo se pueden completar movimientos pendientes.');
                return;
            }

            // Validar stock antes de completar
            foreach ($movimiento->productos as $producto) {
                $productoModel = ProductoAlmacen::find($producto['id']);
                if ($productoModel) {
                    if ($movimiento->tipo_movimiento === 'salida') {
                        if (!$productoModel->tieneStockSuficiente($producto['cantidad'])) {
                            throw new \Exception("Stock insuficiente para el producto {$producto['code']}. Disponible: {$productoModel->stock_actual}, Solicitado: {$producto['cantidad']}");
                        }
                    }
                }
            }

            // Actualizar stock de productos cuando se completa el movimiento
            foreach ($movimiento->productos as $producto) {
                $productoModel = ProductoAlmacen::find($producto['id']);
                if ($productoModel) {
                    $stockAnterior = $productoModel->stock_actual;
                    $productoModel->actualizarStock($producto['cantidad'], $movimiento->tipo_movimiento);

                    Log::info('Stock actualizado al completar movimiento', [
                        'user_id' => Auth::id(),
                        'movimiento_id' => $movimiento->id,
                        'producto_id' => $producto['id'],
                        'producto_code' => $producto['code'],
                        'tipo_movimiento' => $movimiento->tipo_movimiento,
                        'cantidad' => $producto['cantidad'],
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $productoModel->stock_actual,
                        'lote' => $producto['lote'] ?? null
                    ]);
                }
            }

            $movimiento->estado = 'completado';
            $movimiento->save();

            DB::commit();

            Log::info('Movimiento completado', [
                'user_id' => Auth::id(),
                'movimiento_id' => $movimiento->id,
                'code' => $movimiento->code,
                'tipo_movimiento' => $movimiento->tipo_movimiento,
                'almacen_id' => $movimiento->almacen_id,
                'total' => $movimiento->total
            ]);

            $this->success('Movimiento completado correctamente. El stock ha sido actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al completar movimiento', [
                'user_id' => Auth::id(),
                'movimiento_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Error al completar el movimiento: ' . $e->getMessage());
        }
    }

    public function cancelarMovimiento($id)
    {
        try {
            DB::beginTransaction();

            $movimiento = MovimientoAlmacen::findOrFail($id);

            if ($movimiento->estado !== 'pendiente') {
                $this->error('Solo se pueden cancelar movimientos pendientes.');
                return;
            }

            $movimiento->estado = 'cancelado';
            $movimiento->save();

            DB::commit();

            Log::info('Movimiento cancelado', [
                'user_id' => Auth::id(),
                'movimiento_id' => $movimiento->id,
                'code' => $movimiento->code,
                'tipo_movimiento' => $movimiento->tipo_movimiento,
                'almacen_id' => $movimiento->almacen_id,
                'total' => $movimiento->total
            ]);

            $this->success('Movimiento cancelado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al cancelar movimiento', [
                'user_id' => Auth::id(),
                'movimiento_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Error al cancelar el movimiento: ' . $e->getMessage());
        }
    }

    public function verDetalleMovimiento($id)
    {
        $this->movimiento_detalle = MovimientoAlmacen::findOrFail($id);
        $this->modal_detalle_movimiento = true;
    }
}
