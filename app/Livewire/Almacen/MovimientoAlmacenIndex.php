<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\MovimientoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Exports\Almacen\MovimientoAlmacenExport;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class MovimientoAlmacenIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $almacen_filter = '';
    public $producto_filter = '';
    public $tipo_filter = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';

    // Modal Form Movimiento
    public $modal_form_movimiento = false;
    public $modal_form_eliminar_movimiento = false;
    public $movimiento_id = '';
    public $movimiento = null;
    public $movimientosExportar = null;

    // Variables para el formulario
    public $code = '';
    public $almacen_id = '';
    public $producto_id = '';
    public $cantidad = '';
    public $tipo = 'entrada';
    public $fecha_movimiento = '';
    public $observaciones = '';
    public $motivo_movimiento = '';

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
            'producto_filter',
            'tipo_filter',
            'fecha_inicio',
            'fecha_fin',
            'perPage'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = MovimientoAlmacen::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('observaciones', 'like', '%' . $this->search . '%')
                        ->orWhere('motivo_movimiento', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->almacen_filter, function ($query) {
                $query->where('almacen_id', $this->almacen_filter);
            })
            ->when($this->producto_filter, function ($query) {
                $query->where('producto_id', $this->producto_filter);
            })
            ->when($this->tipo_filter, function ($query) {
                $query->where('tipo', $this->tipo_filter);
            })
            ->when($this->fecha_inicio, function ($query) {
                $query->where('fecha_movimiento', '>=', $this->fecha_inicio);
            })
            ->when($this->fecha_fin, function ($query) {
                $query->where('fecha_movimiento', '<=', $this->fecha_fin);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $this->movimientosExportar = $query->get();

        return view('livewire.almacen.movimiento-almacen-index', [
            'movimientos' => $query->paginate($this->perPage),
            'almacenes' => WarehouseAlmacen::where('estado', true)->get(),
            'productos' => ProductoAlmacen::where('estado', true)->get()
        ]);
    }

    public function nuevoMovimiento()
    {
        $this->modal_form_movimiento = true;
    }

    public function editarMovimiento($id)
    {
        $this->movimiento_id = $id;
        $this->movimiento = MovimientoAlmacen::find($id);
        $this->code = $this->movimiento->code;
        $this->almacen_id = $this->movimiento->almacen_id;
        $this->producto_id = $this->movimiento->producto_id;
        $this->cantidad = $this->movimiento->cantidad;
        $this->tipo = $this->movimiento->tipo;
        $this->fecha_movimiento = $this->movimiento->fecha_movimiento;
        $this->observaciones = $this->movimiento->observaciones;
        $this->motivo_movimiento = $this->movimiento->motivo_movimiento;

        $this->modal_form_movimiento = true;
    }

    public function eliminarMovimiento($id)
    {
        $this->movimiento_id = $id;
        $this->movimiento = MovimientoAlmacen::find($id);
        if ($this->movimiento) {
            $this->modal_form_eliminar_movimiento = true;
        }
    }

    public function confirmarEliminarMovimiento()
    {
        $this->movimiento->delete();
        $this->modal_form_eliminar_movimiento = false;
        $this->reset(['movimiento_id', 'movimiento']);
    }

    public function guardarMovimiento()
    {
        $ruleUniqueCode = $this->movimiento_id ? 'unique:movimientos_almacen,code,' . $this->movimiento_id : 'unique:movimientos_almacen,code';

        $rules = [
            'code' => $ruleUniqueCode,
            'almacen_id' => 'required|exists:almacenes,id',
            'producto_id' => 'required|exists:productos_almacen,id',
            'cantidad' => 'required|numeric|min:0.01',
            'tipo' => 'required|in:entrada,salida',
            'fecha_movimiento' => 'required|date',
            'observaciones' => 'nullable|string',
            'motivo_movimiento' => 'nullable|string|max:255',
        ];

        $messages = [
            'code.required' => 'Por favor, ingrese el código del movimiento',
            'code.unique' => 'Este código ya está registrado en el sistema',
            'almacen_id.required' => 'Por favor, seleccione el almacén',
            'almacen_id.exists' => 'El almacén seleccionado no es válido',
            'producto_id.required' => 'Por favor, seleccione el producto',
            'producto_id.exists' => 'El producto seleccionado no es válido',
            'cantidad.required' => 'Por favor, ingrese la cantidad',
            'cantidad.numeric' => 'La cantidad debe ser un número',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'tipo.required' => 'Por favor, seleccione el tipo de movimiento',
            'tipo.in' => 'El tipo de movimiento seleccionado no es válido',
            'fecha_movimiento.required' => 'Por favor, ingrese la fecha del movimiento',
            'fecha_movimiento.date' => 'La fecha del movimiento no es válida',
        ];

        $data = $this->validate($rules, $messages);

        if ($this->movimiento_id) {
            $movimiento = MovimientoAlmacen::find($this->movimiento_id);
            $movimiento->update($data);
        } else {
            MovimientoAlmacen::create($data);
        }

        $this->modal_form_movimiento = false;
        $this->reset([
            'movimiento_id',
            'movimiento',
            'code',
            'almacen_id',
            'producto_id',
            'cantidad',
            'tipo',
            'fecha_movimiento',
            'observaciones',
            'motivo_movimiento'
        ]);
        $this->resetValidation();
    }

    public function exportarMovimientos()
    {
        return Excel::download(new MovimientoAlmacenExport($this->movimientosExportar), 'movimientos_' . date('Y-m-d_H-i-s') . '.xlsx');
        $this->reset(['movimientosExportar']);
    }
}
