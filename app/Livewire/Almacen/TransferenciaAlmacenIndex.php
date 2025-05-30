<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\TransferenciaAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Exports\Almacen\TransferenciaAlmacenExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransferenciaAlmacenIndex extends Component
{
    use WithPagination, WithFileUploads;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'code'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'almacen_origen_filter' => ['except' => ''],
        'almacen_destino_filter' => ['except' => ''],
        'estado_filter' => ['except' => '']
    ];

    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $almacen_origen_filter = '';
    public $almacen_destino_filter = '';
    public $estado_filter = 'pendiente';
    public $fecha_inicio = '';
    public $fecha_fin = '';

    // Modal Form Transferencia
    public $modal_form_transferencia = false;
    public $transferencia_id = null;
    public $transferencia = null;
    public $transferenciasExportar = null;

    // Variables para el formulario
    public $code = '';
    public $almacen_origen_id = '';
    public $almacen_destino_id = '';
    public $productos = [];
    public $fecha = '';
    public $estado = 'pendiente';
    public $observaciones = '';
    public $motivo_transferencia = '';

    // Productos seleccionados
    public $productos_seleccionados = [];
    public $cantidades = [];
    public $productos_disponibles = [];
    public $producto_seleccionado = null;
    public $cantidad_producto = 1;

    protected function rules()
    {
        return [
            'code' => 'required|min:3|max:50',
            'almacen_origen_id' => 'required|exists:almacenes,id',
            'almacen_destino_id' => [
                'required',
                'exists:almacenes,id',
                'different:almacen_origen_id'
            ],
            'productos_seleccionados' => 'required|array|min:1',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|numeric|min:1',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,completada,cancelada',
            'observaciones' => 'nullable|string|max:500'
        ];
    }

    protected $messages = [
        'code.required' => 'El código es requerido',
        'code.min' => 'El código debe tener al menos 3 caracteres',
        'code.max' => 'El código no debe exceder los 50 caracteres',
        'almacen_origen_id.required' => 'El almacén origen es requerido',
        'almacen_origen_id.exists' => 'El almacén origen seleccionado no existe',
        'almacen_destino_id.required' => 'El almacén destino es requerido',
        'almacen_destino_id.exists' => 'El almacén destino seleccionado no existe',
        'almacen_destino_id.different' => 'El almacén destino debe ser diferente al almacén origen',
        'productos_seleccionados.required' => 'Debe seleccionar al menos un producto',
        'productos_seleccionados.array' => 'Los productos deben ser un array',
        'productos_seleccionados.min' => 'Debe seleccionar al menos un producto',
        'cantidades.required' => 'Las cantidades son requeridas',
        'cantidades.array' => 'Las cantidades deben ser un array',
        'cantidades.*.required' => 'La cantidad es requerida',
        'cantidades.*.numeric' => 'La cantidad debe ser un número',
        'cantidades.*.min' => 'La cantidad debe ser mayor a 0',
        'fecha.required' => 'La fecha es requerida',
        'fecha.date' => 'La fecha debe ser válida',
        'estado.required' => 'El estado es requerido',
        'estado.in' => 'El estado debe ser válido',
        'observaciones.max' => 'Las observaciones no deben exceder los 500 caracteres'
    ];

    protected $listeners = ['actualizarProductosDisponibles'];

    public function mount()
    {
        $this->fecha_inicio = now()->subDays(7)->format('Y-m-d');
        $this->fecha_fin = now()->format('Y-m-d');
        $this->resetForm();
        $this->actualizarProductosDisponibles();
        $this->generarCodigo();
    }

    public function resetForm()
    {
        $this->reset([
            'code',
            'almacen_origen_id',
            'almacen_destino_id',
            'productos_seleccionados',
            'cantidades',
            'fecha',
            'estado',
            'observaciones',
            'transferencia_id',
            'producto_seleccionado',
            'cantidad_producto'
        ]);
        $this->fecha = now()->format('Y-m-d\TH:i');
        $this->estado = 'pendiente';
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
            'almacen_origen_filter',
            'almacen_destino_filter',
            'estado_filter',
            'perPage'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = TransferenciaAlmacen::query()
            ->with(['almacenOrigen', 'almacenDestino'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('observaciones', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->almacen_origen_filter, fn($q) => $q->where('almacen_origen_id', $this->almacen_origen_filter))
            ->when($this->almacen_destino_filter, fn($q) => $q->where('almacen_destino_id', $this->almacen_destino_filter))
            ->when($this->estado_filter, fn($q) => $q->where('estado', $this->estado_filter))
            ->when($this->fecha_inicio, fn($q) => $q->where('fecha_transferencia', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->where('fecha_transferencia', '<=', $this->fecha_fin))
            ->orderBy($this->sortField, $this->sortDirection);

        $this->transferenciasExportar = $query->get();

        return view('livewire.almacen.transferencia-almacen-index', [
            'transferencias' => $query->paginate($this->perPage),
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
            $codigo = 'TRF' . str_pad($numero, 6, '0', STR_PAD_LEFT) . '-' . str_pad($usuarioId, 4, '0', STR_PAD_LEFT);

            // Verificar si el código ya existe
            $existeCodigo = TransferenciaAlmacen::where('code', $codigo)->exists();

            if (!$existeCodigo) {
                $this->code = $codigo;
                $codigoGenerado = true;
            } else {
                $numero++;
            }
        }
    }

    public function nuevaTransferencia()
    {
        $this->resetForm();
        $this->generarCodigo();
        $this->modal_form_transferencia = true;
    }

    public function editarTransferencia($id)
    {
        $this->transferencia_id = $id;
        $this->transferencia = TransferenciaAlmacen::with(['almacenOrigen', 'almacenDestino'])->find($id);

        // Verificar si la transferencia está pendiente
        if ($this->transferencia->estado !== 'pendiente') {
            session()->flash('error', 'Solo se pueden editar transferencias en estado pendiente.');
            return;
        }

        // Cargar datos básicos de la transferencia
        $this->code = $this->transferencia->code;
        $this->almacen_origen_id = $this->transferencia->almacen_origen_id;
        $this->almacen_destino_id = $this->transferencia->almacen_destino_id;
        $this->fecha = $this->transferencia->fecha_transferencia->format('Y-m-d\TH:i');
        $this->estado = $this->transferencia->estado;
        $this->observaciones = $this->transferencia->observaciones;

        // Cargar productos disponibles del almacén origen
        $this->productos_disponibles = ProductoAlmacen::where('almacen_id', $this->almacen_origen_id)
            ->where('estado', true)
            ->get();

        // Cargar productos seleccionados con sus cantidades
        $productosTransferencia = collect($this->transferencia->productos);
        $this->productos_seleccionados = $productosTransferencia->pluck('id')->toArray();
        $this->cantidades = $productosTransferencia->pluck('cantidad', 'id')->toArray();

        // Actualizar el stock disponible para cada producto
        foreach ($this->productos_disponibles as $producto) {
            $producto->stock_disponible = $producto->stock_actual;
            if (in_array($producto->id, $this->productos_seleccionados)) {
                $producto->stock_disponible += $this->cantidades[$producto->id];
            }
        }

        $this->modal_form_transferencia = true;
    }

    public function guardarTransferencia()
    {
        $this->validate();

        // Verificar si es una edición y si la transferencia está pendiente
        if ($this->transferencia_id) {
            $transferencia = TransferenciaAlmacen::findOrFail($this->transferencia_id);
            if ($transferencia->estado !== 'pendiente') {
                session()->flash('error', 'Solo se pueden editar transferencias en estado pendiente.');
                return;
            }
        }

        $productos = collect($this->productos_seleccionados)
            ->map(function ($productoId) {
                $producto = $this->productos_disponibles->first(function($p) use ($productoId) {
                    return $p->id == $productoId;
                });

                if (!$producto) {
                    $producto = ProductoAlmacen::find($productoId);
                }

                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'cantidad' => $this->cantidades[$productoId],
                    'unidad_medida' => $producto->unidad_medida
                ];
            })
            ->toArray();

        $data = [
            'code' => $this->code,
            'almacen_origen_id' => $this->almacen_origen_id,
            'almacen_destino_id' => $this->almacen_destino_id,
            'productos' => $productos,
            'fecha_transferencia' => $this->fecha,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'usuario_id' => Auth::user()->id
        ];

        try {
            if ($this->transferencia_id) {
                // Restaurar el stock de los productos eliminados
                $productosAnteriores = collect($transferencia->productos);
                $productosEliminados = $productosAnteriores->whereNotIn('id', $this->productos_seleccionados);

                foreach ($productosEliminados as $producto) {
                    $productoModel = ProductoAlmacen::find($producto['id']);
                    if ($productoModel) {
                        $productoModel->actualizarStock($producto['cantidad'], 'entrada');
                    }
                }

                $transferencia->update($data);
                $mensaje = 'Transferencia actualizada correctamente.';
            } else {
                TransferenciaAlmacen::create($data);
                $mensaje = 'Transferencia creada correctamente.';
            }

            // Actualizar stock de productos
            foreach ($productos as $producto) {
                $productoModel = ProductoAlmacen::find($producto['id']);
                if ($productoModel) {
                    $productoModel->actualizarStock($producto['cantidad'], 'salida');
                }
            }

            $this->modal_form_transferencia = false;
            $this->resetForm();
            session()->flash('message', $mensaje);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la transferencia: ' . $e->getMessage());
        }
    }

    public function exportarTransferencias()
    {
        return Excel::download(new TransferenciaAlmacenExport($this->transferenciasExportar), 'transferencias_' . date('Y-m-d_H-i-s') . '.xlsx');
        $this->reset(['transferenciasExportar']);
    }

    public function actualizarProductosDisponibles()
    {
        if (!$this->almacen_origen_id) {
            $this->productos_disponibles = collect();
            return;
        }

        $this->productos_disponibles = ProductoAlmacen::query()
            ->where('estado', true)
            ->where('almacen_id', $this->almacen_origen_id)
            ->where('stock_actual', '>', 0)
            ->get();
    }

    public function agregarProducto()
    {
        $this->validate([
            'producto_seleccionado' => 'required|exists:productos_almacen,id',
            'cantidad_producto' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $producto = $this->productos_disponibles->first(function($p) {
                        return $p->id == $this->producto_seleccionado;
                    });

                    if (!$producto || !$producto->tieneStockSuficiente($value)) {
                        $fail("La cantidad excede el stock disponible ({$producto->stock_actual})");
                    }
                }
            ]
        ]);

        if (!in_array($this->producto_seleccionado, $this->productos_seleccionados)) {
            $this->productos_seleccionados[] = $this->producto_seleccionado;
            $this->cantidades[$this->producto_seleccionado] = $this->cantidad_producto;
        }

        $this->reset(['producto_seleccionado', 'cantidad_producto']);
    }

    public function quitarProducto($productoId)
    {
        $key = array_search($productoId, $this->productos_seleccionados);
        if ($key !== false) {
            unset($this->productos_seleccionados[$key], $this->cantidades[$productoId]);
            $this->productos_seleccionados = array_values($this->productos_seleccionados);
        }
    }

    public function updatedAlmacenOrigenId()
    {
        $this->actualizarProductosDisponibles();
        $this->reset(['productos_seleccionados', 'cantidades']);
    }

    public function updatedProductoSeleccionado()
    {
        if (!$this->producto_seleccionado) {
            return;
        }

        $producto = $this->productos_disponibles->first(function($p) {
            return $p->id == $this->producto_seleccionado;
        });

        if ($producto) {
            $this->cantidad_producto = min($this->cantidad_producto, $producto->stock_actual);
        }
    }

    public function completarTransferencia($id)
    {
        try {
            $transferencia = TransferenciaAlmacen::findOrFail($id);

            if ($transferencia->estado !== 'pendiente') {
                session()->flash('error', 'Solo se pueden completar transferencias pendientes.');
                return;
            }

            // Actualizar stock en el almacén destino
            foreach ($transferencia->productos as $producto) {
                $productoModel = ProductoAlmacen::where('almacen_id', $transferencia->almacen_destino_id)
                    ->where('id', $producto['id'])
                    ->first();

                if ($productoModel) {
                    $productoModel->actualizarStock($producto['cantidad'], 'entrada');
                }
            }

            $transferencia->estado = 'completada';
            $transferencia->save();
            session()->flash('message', 'Transferencia completada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al completar la transferencia: ' . $e->getMessage());
        }
    }

    public function cancelarTransferencia($id)
    {
        try {
            $transferencia = TransferenciaAlmacen::findOrFail($id);

            if ($transferencia->estado !== 'pendiente') {
                session()->flash('error', 'Solo se pueden cancelar transferencias pendientes.');
                return;
            }

            // Restaurar stock en el almacén origen
            foreach ($transferencia->productos as $producto) {
                $productoModel = ProductoAlmacen::find($producto['id']);
                if ($productoModel) {
                    $productoModel->actualizarStock($producto['cantidad'], 'entrada');
                }
            }

            $transferencia->estado = 'cancelada';
            $transferencia->save();
            session()->flash('message', 'Transferencia cancelada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cancelar la transferencia: ' . $e->getMessage());
        }
    }
}
