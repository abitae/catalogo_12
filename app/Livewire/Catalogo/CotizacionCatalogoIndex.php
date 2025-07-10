<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\CotizacionCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Shared\Customer;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Carbon\Carbon;

class CotizacionCatalogoIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $estado = '';
    public $customer_filter = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $modal_cotizacion = false;
    public $editingCotizacion = null;
    public $selectedProductos = [];
    public $cantidades = [];
    public $precios = [];
    public $observaciones = [];
    public $searchProducto = '';
    public $showProductosList = false;
    public $modoVisualizacion = false;

    // Campos de la cotización
    public $codigo_cotizacion;
    public $customer_id;
    public $cliente_nombre;
    public $cliente_email;
    public $cliente_telefono;
    public $observaciones_general;
    public $fecha_cotizacion;
    public $fecha_vencimiento;
    public $validez_dias = '15';
    public $condiciones_pago;
    public $condiciones_entrega;
    public $user_id;
    public $estado_cotizacion = 'borrador';

    protected function rules()
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'cliente_nombre' => 'required|string|max:255',
            'cliente_email' => 'nullable|email',
            'cliente_telefono' => 'nullable|string|max:20',
            'fecha_cotizacion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after:fecha_cotizacion',
            'validez_dias' => 'required|integer|min:1|max:30',
            'user_id' => 'required|exists:users,id',
        ];

        if (!$this->editingCotizacion) {
            $rules['codigo_cotizacion'] = 'required|unique:cotizacion_catalogos,codigo_cotizacion';
        } else {
            $rules['codigo_cotizacion'] = 'required|unique:cotizacion_catalogos,codigo_cotizacion,' . $this->editingCotizacion->id;
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'customer_id.required' => 'Debe seleccionar un cliente.',
            'customer_id.exists' => 'El cliente seleccionado no existe.',
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
            'cliente_nombre.max' => 'El nombre del cliente no puede exceder 255 caracteres.',
            'cliente_email.email' => 'El formato del email no es válido.',
            'cliente_telefono.max' => 'El teléfono no puede exceder 20 caracteres.',
            'fecha_cotizacion.required' => 'La fecha de cotización es obligatoria.',
            'fecha_cotizacion.date' => 'La fecha de cotización debe tener un formato válido.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe tener un formato válido.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de cotización.',
            'validez_dias.required' => 'La validez en días es obligatoria.',
            'validez_dias.integer' => 'La validez debe ser un número entero.',
            'validez_dias.min' => 'La validez debe ser al menos 1 día.',
            'validez_dias.max' => 'La validez no puede exceder 30 días.',
            'user_id.required' => 'Debe seleccionar un vendedor.',
            'user_id.exists' => 'El vendedor seleccionado no existe.',
            'codigo_cotizacion.required' => 'El código de cotización es obligatorio.',
            'codigo_cotizacion.unique' => 'El código de cotización ya existe.',
        ];
    }

    public function mount()
    {
        // Fechas para el modal de cotización
        $this->fecha_cotizacion = Carbon::now()->format('Y-m-d');
        $this->fecha_vencimiento = Carbon::now()->addDays(15)->format('Y-m-d');
        $this->validez_dias = 15;
        $this->user_id = 1; // Usuario por defecto

        // Fechas para los filtros (vacías para mostrar todas las cotizaciones)
        $this->fecha_desde = Carbon::now()->subDays(15)->format('Y-m-d');
        $this->fecha_hasta = Carbon::now()->format('Y-m-d');
    }

    public function calcularFechaVencimiento()
    {
        if ($this->fecha_cotizacion && $this->validez_dias) {
            $this->fecha_vencimiento = Carbon::parse($this->fecha_cotizacion)
                ->addDays((int)$this->validez_dias)
                ->format('Y-m-d');
        }
    }

    public function calcularValidezDias()
    {
        if ($this->fecha_cotizacion && $this->fecha_vencimiento) {
            $fecha_cotizacion = Carbon::parse($this->fecha_cotizacion);
            $fecha_vencimiento = Carbon::parse($this->fecha_vencimiento);
            $this->validez_dias = $fecha_cotizacion->diffInDays($fecha_vencimiento);
        }
    }

    public function calcularSubtotalSinIgv()
    {
        $totalConIgv = 0;
        foreach ($this->selectedProductos as $productoId) {
            $cantidad = $this->cantidades[$productoId] ?? 0;
            $precio = $this->precios[$productoId] ?? 0;
            $totalConIgv += $cantidad * $precio;
        }

        // El precio ya incluye IGV, por lo que el subtotal sin IGV es el total menos el IGV
        $igv = $totalConIgv * 0.18;
        return $totalConIgv - $igv;
    }

    public function calcularIgv()
    {
        $totalConIgv = 0;
        foreach ($this->selectedProductos as $productoId) {
            $cantidad = $this->cantidades[$productoId] ?? 0;
            $precio = $this->precios[$productoId] ?? 0;
            $totalConIgv += $cantidad * $precio;
        }

        return $totalConIgv * 0.18;
    }

    public function calcularTotal()
    {
        $total = 0;
        foreach ($this->selectedProductos as $productoId) {
            $cantidad = $this->cantidades[$productoId] ?? 0;
            $precio = $this->precios[$productoId] ?? 0;
            $total += $cantidad * $precio;
        }

        return $total;
    }

    public function cargarDatosCliente()
    {
        if ($this->customer_id) {
            $customer = Customer::find($this->customer_id);
            if ($customer) {
                $this->cliente_nombre = $customer->rznSocial;
                $this->cliente_email = $customer->email;
                $this->cliente_telefono = $customer->telefono;
            }
        }
    }

    public function render()
    {
        $cotizaciones = CotizacionCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo_cotizacion', 'like', '%' . $this->search . '%')
                      ->orWhere('cliente_nombre', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->customer_filter, function ($query) {
                $query->where('customer_id', $this->customer_filter);
            })
            ->when($this->fecha_desde, function ($query) {
                $query->where('fecha_cotizacion', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function ($query) {
                $query->where('fecha_cotizacion', '<=', $this->fecha_hasta);
            })
            ->with(['customer', 'detalles.producto'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $customers = Customer::orderBy('rznSocial')->get();
        $users = User::orderBy('name')->get();
        $productos = ProductoCatalogo::where('isActive', true)
            ->when($this->searchProducto, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->searchProducto . '%')
                      ->orWhere('description', 'like', '%' . $this->searchProducto . '%');
                });
            })
            ->with(['brand', 'category', 'line'])
            ->orderBy('code')
            ->get();

        return view('livewire.catalogo.cotizacion-catalogo-index', [
            'cotizaciones' => $cotizaciones,
            'customers' => $customers,
            'users' => $users,
            'productos' => $productos,
        ]);
    }

    public function crearCotizacion()
    {
        $this->resetForm();
        $this->codigo_cotizacion = (new CotizacionCatalogo())->generarCodigo();
        $this->modal_cotizacion = true;
        // Inicializar las fechas de cotización y vencimiento
        $this->fecha_cotizacion = now()->format('Y-m-d');
        $this->fecha_vencimiento = now()->addDays(15)->format('Y-m-d');
        $this->validez_dias = 15;
    }

    public function editarCotizacion($id)
    {
        $cotizacion = CotizacionCatalogo::with(['detalles.producto'])->find($id);
        if (!$cotizacion) {
            $this->toast('Cotización no encontrada', 'error');
            return;
        }

        // No permitir editar cotizaciones aprobadas
        if ($cotizacion->estado === 'aprobada') {
            $this->toast('No se puede editar una cotización aprobada', 'error');
            return;
        }

        $this->editingCotizacion = $cotizacion;
        $this->codigo_cotizacion = $cotizacion->codigo_cotizacion;
        $this->customer_id = $cotizacion->customer_id;
        $this->cliente_nombre = $cotizacion->cliente_nombre;
        $this->cliente_email = $cotizacion->cliente_email;
        $this->cliente_telefono = $cotizacion->cliente_telefono;
        $this->observaciones_general = $cotizacion->observaciones;
        $this->fecha_cotizacion = $cotizacion->fecha_cotizacion->format('Y-m-d');
        $this->fecha_vencimiento = $cotizacion->fecha_vencimiento?->format('Y-m-d');
        $this->validez_dias = $cotizacion->validez_dias;
        $this->condiciones_pago = $cotizacion->condiciones_pago;
        $this->condiciones_entrega = $cotizacion->condiciones_entrega;
        $this->user_id = $cotizacion->user_id;
        $this->estado_cotizacion = $cotizacion->estado;

        // Cargar detalles
        foreach ($cotizacion->detalles as $detalle) {
            $this->selectedProductos[] = $detalle->producto_id;
            $this->cantidades[$detalle->producto_id] = $detalle->cantidad;
            $this->precios[$detalle->producto_id] = $detalle->precio_unitario;
            $this->observaciones[$detalle->producto_id] = $detalle->observaciones;
        }

        $this->modoVisualizacion = false;
        $this->modal_cotizacion = true;
    }

    public function visualizarCotizacion($id)
    {
        $cotizacion = CotizacionCatalogo::with(['detalles.producto'])->find($id);
        if (!$cotizacion) {
            $this->toast('Cotización no encontrada', 'error');
            return;
        }

        // Solo permitir visualizar cotizaciones aprobadas
        if ($cotizacion->estado !== 'aprobada') {
            $this->toast('Solo se pueden visualizar cotizaciones aprobadas', 'error');
            return;
        }

        $this->editingCotizacion = $cotizacion;
        $this->codigo_cotizacion = $cotizacion->codigo_cotizacion;
        $this->customer_id = $cotizacion->customer_id;
        $this->cliente_nombre = $cotizacion->cliente_nombre;
        $this->cliente_email = $cotizacion->cliente_email;
        $this->cliente_telefono = $cotizacion->cliente_telefono;
        $this->observaciones_general = $cotizacion->observaciones;
        $this->fecha_cotizacion = $cotizacion->fecha_cotizacion->format('Y-m-d');
        $this->fecha_vencimiento = $cotizacion->fecha_vencimiento?->format('Y-m-d');
        $this->validez_dias = $cotizacion->validez_dias;
        $this->condiciones_pago = $cotizacion->condiciones_pago;
        $this->condiciones_entrega = $cotizacion->condiciones_entrega;
        $this->user_id = $cotizacion->user_id;
        $this->estado_cotizacion = $cotizacion->estado;

        // Cargar detalles
        foreach ($cotizacion->detalles as $detalle) {
            $this->selectedProductos[] = $detalle->producto_id;
            $this->cantidades[$detalle->producto_id] = $detalle->cantidad;
            $this->precios[$detalle->producto_id] = $detalle->precio_unitario;
            $this->observaciones[$detalle->producto_id] = $detalle->observaciones;
        }

        $this->modoVisualizacion = true;
        $this->modoVisualizacion = false;
        $this->modal_cotizacion = true;
    }

    public function guardarCotizacion()
    {
        $this->validate($this->rules());

        // Verificar que no se esté editando una cotización aprobada
        if ($this->editingCotizacion && $this->editingCotizacion->estado === 'aprobada') {
            $this->toast('No se puede editar una cotización aprobada', 'error');
            return;
        }

        $data = [
            'codigo_cotizacion' => $this->codigo_cotizacion,
            'customer_id' => $this->customer_id,
            'cliente_nombre' => $this->cliente_nombre,
            'cliente_email' => $this->cliente_email,
            'cliente_telefono' => $this->cliente_telefono,
            'observaciones' => $this->observaciones_general,
            'fecha_cotizacion' => $this->fecha_cotizacion,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'validez_dias' => $this->validez_dias,
            'condiciones_pago' => $this->condiciones_pago,
            'condiciones_entrega' => $this->condiciones_entrega,
            'user_id' => $this->user_id,
            'estado' => $this->estado_cotizacion,
        ];

        if ($this->editingCotizacion) {
            $this->editingCotizacion->update($data);
            $cotizacion = $this->editingCotizacion;
        } else {
            $cotizacion = CotizacionCatalogo::create($data);
        }

        // Guardar detalles
        $cotizacion->detalles()->delete();
        foreach ($this->selectedProductos as $productoId) {
            if (isset($this->cantidades[$productoId]) && $this->cantidades[$productoId] > 0) {
                $subtotal = $this->cantidades[$productoId] * ($this->precios[$productoId] ?? 0);
                $cotizacion->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $this->cantidades[$productoId],
                    'precio_unitario' => $this->precios[$productoId] ?? 0,
                    'subtotal' => $subtotal,
                    'observaciones' => $this->observaciones[$productoId] ?? null,
                ]);
            }
        }

        $cotizacion->calcularTotales();

        $this->toast('Cotización guardada exitosamente', 'success');
        $this->cerrarModal();
    }

    public function eliminarCotizacion($id)
    {
        $cotizacion = CotizacionCatalogo::find($id);

        if (!$cotizacion) {
            $this->toast('Cotización no encontrada', 'error');
            return;
        }

        // Solo permitir eliminar cotizaciones en estado borrador
        if ($cotizacion->estado !== 'borrador') {
            $this->toast('Solo se pueden eliminar cotizaciones en estado borrador', 'error');
            return;
        }

        try {
            // Eliminar detalles primero
            $cotizacion->detalles()->delete();
            // Eliminar la cotización
            $cotizacion->delete();
            $this->toast('Cotización eliminada correctamente', 'success');
        } catch (\Exception $e) {
            $this->toast('Error al eliminar la cotización', 'error');
        }
    }

            public function cambiarEstado($id, $estado)
    {
        $cotizacion = CotizacionCatalogo::with(['detalles'])->find($id);

        if (!$cotizacion) {
            $this->toast('Cotización no encontrada', 'error');
            return;
        }

        // No permitir cambiar el estado de cotizaciones aprobadas
        if ($cotizacion->estado === 'aprobada') {
            $this->toast('No se puede cambiar el estado de una cotización aprobada', 'error');
            return;
        }

        // Validar que tenga al menos un producto para aprobar
        if ($estado === 'aprobada' && $cotizacion->detalles->count() === 0) {
            $this->toast('No se puede aprobar una cotización sin productos', 'error');
            return;
        }

        // Validar que el estado sea válido
        $estadosValidos = ['borrador', 'enviada', 'aprobada', 'rechazada'];
        if (!in_array($estado, $estadosValidos)) {
            $this->toast('Estado no válido', 'error');
            return;
        }

        try {
            $cotizacion->update(['estado' => $estado]);
            $this->toast('Estado actualizado exitosamente', 'success');
        } catch (\Exception $e) {
            $this->toast('Error al actualizar el estado', 'error');
        }
    }

    public function mostrarProductos()
    {
        $this->showProductosList = true;
    }

    public function ocultarProductos()
    {
        $this->showProductosList = false;
        $this->searchProducto = '';
    }

    public function agregarProducto($productoId)
    {
        if (!in_array($productoId, $this->selectedProductos)) {
            $this->selectedProductos[] = $productoId;
            $this->cantidades[$productoId] = 1;
            // El precio de venta ya incluye IGV
            $producto = ProductoCatalogo::find($productoId);
            $this->precios[$productoId] = $producto ? $producto->price_venta : 0;
        }
        $this->showProductosList = false;
        $this->searchProducto = '';
    }

    public function removerProducto($productoId)
    {
        $key = array_search($productoId, $this->selectedProductos);
        if ($key !== false) {
            unset($this->selectedProductos[$key]);
            unset($this->cantidades[$productoId]);
            unset($this->precios[$productoId]);
            unset($this->observaciones[$productoId]);
        }
    }

    public function cerrarModal()
    {
        $this->modal_cotizacion = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingCotizacion = null;
        $this->selectedProductos = [];
        $this->cantidades = [];
        $this->precios = [];
        $this->observaciones = [];
        $this->searchProducto = '';
        $this->showProductosList = false;
        $this->modoVisualizacion = false;
        $this->reset([
            'codigo_cotizacion', 'customer_id', 'cliente_nombre', 'cliente_email',
            'cliente_telefono', 'observaciones_general', 'fecha_cotizacion',
            'fecha_vencimiento', 'validez_dias', 'condiciones_pago',
            'condiciones_entrega', 'user_id', 'estado_cotizacion',
            'customer_filter', 'fecha_desde', 'fecha_hasta'
        ]);
        $this->resetErrorBag();
    }

    public function limpiarFiltros()
    {
        $this->reset([
            'search', 'estado', 'customer_filter'
        ]);
        $this->fecha_desde = Carbon::now()->subDays(15)->format('Y-m-d');
        $this->fecha_hasta = Carbon::now()->format('Y-m-d');
    }
}
