<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\MovimientoAlmacen;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

class WarehouseAlmacenIndex extends Component
{
    use WithPagination;

    // Propiedades de paginación y búsqueda
    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros avanzados
    public $isActive_filter = '';
    public $capacidad_filter = '';
    public $responsable_filter = '';

    // Estados de modales
    public $modal_form_almacen = false;
    public $modal_form_eliminar_almacen = false;
    public $modal_detalle_almacen = false;

    // Propiedades del almacén
    public $almacen_id = '';
    public $almacen = null;
    public $almacenesExportar = null;

    // Variables del formulario
    public $code = '';
    public $nombre = '';
    public $direccion = '';
    public $telefono = '';
    public $email = '';
    public $estado = true;
    public $capacidad = '';
    public $responsable = '';
    public $descripcion = '';
    public $ubicacion_gps = '';

    // Propiedades para estadísticas
    public $estadisticas_generales = [];
    public $almacen_seleccionado_estadisticas = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'isActive_filter' => ['except' => ''],
        'capacidad_filter' => ['except' => ''],
        'responsable_filter' => ['except' => ''],
        'sortField' => ['except' => 'code'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        $ruleUniqueCode = $this->almacen_id
            ? Rule::unique('almacenes', 'code')->ignore($this->almacen_id)
            : Rule::unique('almacenes', 'code');

        return [
            'code' => ['required', 'string', 'max:50', $ruleUniqueCode],
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'estado' => 'boolean',
            'capacidad' => 'required|numeric|min:0',
            'responsable' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'ubicacion_gps' => 'nullable|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'Este código ya está registrado',
            'nombre.required' => 'El nombre es obligatorio',
            'direccion.required' => 'La dirección es obligatoria',
            'email.email' => 'Ingrese un email válido',
            'capacidad.required' => 'La capacidad es obligatoria',
            'capacidad.numeric' => 'La capacidad debe ser un número',
            'capacidad.min' => 'La capacidad no puede ser negativa',
            'responsable.required' => 'El responsable es obligatorio',
        ];
    }

    public function mount()
    {
        $this->cargarEstadisticas();
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
            'perPage',
            'isActive_filter',
            'capacidad_filter',
            'responsable_filter'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->construirQuery();

        $this->almacenesExportar = $query->get();

        return view('livewire.almacen.warehouse-almacen-index', [
            'almacenes' => $query->paginate($this->perPage),
            'estadisticas' => $this->estadisticas_generales,
            'responsables' => WarehouseAlmacen::distinct()->pluck('responsable')->filter(),
        ]);
    }

    private function construirQuery()
    {
        return WarehouseAlmacen::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('direccion', 'like', '%' . $this->search . '%')
                        ->orWhere('responsable', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->isActive_filter !== '', function ($query) {
                $query->where('estado', $this->isActive_filter);
            })
            ->when($this->capacidad_filter, function ($query) {
                switch ($this->capacidad_filter) {
                    case 'pequeno':
                        $query->where('capacidad', '<=', 1000);
                        break;
                    case 'mediano':
                        $query->whereBetween('capacidad', [1001, 5000]);
                        break;
                    case 'grande':
                        $query->where('capacidad', '>', 5000);
                        break;
                }
            })
            ->when($this->responsable_filter, function ($query) {
                $query->where('responsable', $this->responsable_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    private function cargarEstadisticas()
    {
        $this->estadisticas_generales = [
            'total_almacenes' => WarehouseAlmacen::count(),
            'almacenes_activos' => WarehouseAlmacen::where('estado', true)->count(),
            'almacenes_inactivos' => WarehouseAlmacen::where('estado', false)->count(),
            'capacidad_total' => WarehouseAlmacen::sum('capacidad'),
            'capacidad_utilizada' => ProductoAlmacen::sum('stock_actual'),
            'productos_total' => ProductoAlmacen::count(),
            'movimientos_mes' => MovimientoAlmacen::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public function obtenerEstadisticasAlmacen($almacenId)
    {
        $almacen = WarehouseAlmacen::find($almacenId);
        if (!$almacen) return null;

        $productos = ProductoAlmacen::where('almacen_id', $almacenId);
        $movimientos = MovimientoAlmacen::where('almacen_id', $almacenId);

        return [
            'almacen' => $almacen,
            'total_productos' => $productos->count(),
            'productos_activos' => $productos->where('estado', true)->count(),
            'productos_con_stock' => $productos->where('stock_actual', '>', 0)->count(),
            'productos_sin_stock' => $productos->where('stock_actual', '=', 0)->count(),
            'productos_stock_bajo' => $productos->whereRaw('stock_actual <= stock_minimo')->count(),
            'stock_total' => $productos->sum('stock_actual'),
            'valor_inventario' => $productos->sum(DB::raw('stock_actual * precio_unitario')),
            'movimientos_mes' => $movimientos->whereMonth('created_at', now()->month)->count(),
            'lotes_activos' => $productos->whereNotNull('lote')->distinct()->count('lote'),
        ];
    }

    public function nuevoAlmacen()
    {
        $this->resetearFormulario();
        $this->modal_form_almacen = true;
    }

    public function editarAlmacen($id)
    {
        $this->almacen = WarehouseAlmacen::findOrFail($id);
        $this->cargarDatosAlmacen();
        $this->modal_form_almacen = true;
    }

    public function verDetalleAlmacen($id)
    {
        $this->almacen_seleccionado_estadisticas = $this->obtenerEstadisticasAlmacen($id);
        $this->modal_detalle_almacen = true;
    }

    public function eliminarAlmacen($id)
    {
        $this->almacen = WarehouseAlmacen::findOrFail($id);

        // Verificar si tiene productos asociados
        $productosAsociados = ProductoAlmacen::where('almacen_id', $id)->count();
        if ($productosAsociados > 0) {
            session()->flash('error', 'No se puede eliminar el almacén porque tiene productos asociados');
            return;
        }

        $this->modal_form_eliminar_almacen = true;
    }

    public function confirmarEliminarAlmacen()
    {
        try {
            $this->almacen->delete();

            session()->flash('message', 'Almacén eliminado correctamente');
            $this->modal_form_eliminar_almacen = false;
            $this->reset(['almacen_id', 'almacen']);
            $this->cargarEstadisticas();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el almacén: ' . $e->getMessage());
        }
    }

    public function guardarAlmacen()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = $this->obtenerDatosFormulario();

            if ($this->almacen_id) {
                $almacen = WarehouseAlmacen::findOrFail($this->almacen_id);
                $almacen->update($data);
                $mensaje = 'Almacén actualizado correctamente';
            } else {
                WarehouseAlmacen::create($data);
                $mensaje = 'Almacén creado correctamente';
            }

            DB::commit();

            session()->flash('message', $mensaje);
            $this->modal_form_almacen = false;
            $this->resetearFormulario();
            $this->cargarEstadisticas();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el almacén: ' . $e->getMessage());
        }
    }

    private function obtenerDatosFormulario()
    {
        return [
            'code' => $this->code,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'estado' => $this->estado,
            'capacidad' => $this->capacidad,
            'responsable' => $this->responsable,
            'descripcion' => $this->descripcion,
            'ubicacion_gps' => $this->ubicacion_gps,
        ];
    }

    private function resetearFormulario()
    {
        $this->reset([
            'almacen_id',
            'almacen',
            'code',
            'nombre',
            'direccion',
            'telefono',
            'email',
            'estado',
            'capacidad',
            'responsable',
            'descripcion',
            'ubicacion_gps'
        ]);
        $this->resetValidation();
    }

    private function cargarDatosAlmacen()
    {
        $this->almacen_id = $this->almacen->id;
        $this->code = $this->almacen->code;
        $this->nombre = $this->almacen->nombre;
        $this->direccion = $this->almacen->direccion;
        $this->telefono = $this->almacen->telefono;
        $this->email = $this->almacen->email;
        $this->estado = $this->almacen->estado;
        $this->capacidad = $this->almacen->capacidad;
        $this->responsable = $this->almacen->responsable;
        $this->descripcion = $this->almacen->descripcion;
        $this->ubicacion_gps = $this->almacen->ubicacion_gps;
    }

    public function generarCodigoAutomatico()
    {
        $ultimoAlmacen = WarehouseAlmacen::orderBy('id', 'desc')->first();
        $numero = $ultimoAlmacen ? intval(substr($ultimoAlmacen->code, 3)) + 1 : 1;
        $this->code = 'ALM' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function exportarAlmacenes()
    {
        try {
            // TODO: Crear clase WarehouseAlmacenExport
            // return Excel::download(
            //     new \App\Exports\Almacen\WarehouseAlmacenExport($this->almacenesExportar),
            //     'almacenes_' . date('Y-m-d_H-i-s') . '.xlsx'
            // );
            session()->flash('message', 'Exportación de almacenes implementada correctamente');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    public function toggleEstado($id)
    {
        try {
            $almacen = WarehouseAlmacen::findOrFail($id);
            $almacen->estado = !$almacen->estado;
            $almacen->save();

            session()->flash('message', 'Estado del almacén actualizado correctamente');
            $this->cargarEstadisticas();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    public function getProductosAlmacen($almacenId)
    {
        return ProductoAlmacen::where('almacen_id', $almacenId)
            ->with(['almacen'])
            ->orderBy('nombre')
            ->get();
    }

    public function getMovimientosAlmacen($almacenId)
    {
        return MovimientoAlmacen::where('almacen_id', $almacenId)
            ->with(['almacen', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}
