<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\WarehouseAlmacen;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseAlmacenIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $isActive_filter = '';

    // Modal Form Almacén
    public $modal_form_almacen = false;
    public $modal_form_eliminar_almacen = false;
    public $almacen_id = '';
    public $almacen = null;
    public $almacenesExportar = null;

    // Variables para el formulario
    public $code = '';
    public $nombre = '';
    public $direccion = '';
    public $telefono = '';
    public $email = '';
    public $estado = true;
    public $capacidad = '';
    public $responsable = '';

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
            'isActive_filter'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = WarehouseAlmacen::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('direccion', 'like', '%' . $this->search . '%')
                        ->orWhere('responsable', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->isActive_filter !== '', function ($query) {
                $query->where('estado', $this->isActive_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $this->almacenesExportar = $query->get();

        return view('livewire.almacen.warehouse-almacen-index', [
            'almacenes' => $query->paginate($this->perPage)
        ]);
    }

    public function nuevoAlmacen()
    {
        $this->modal_form_almacen = true;
    }

    public function editarAlmacen($id)
    {
        $this->almacen_id = $id;
        $this->almacen = WarehouseAlmacen::find($id);
        $this->code = $this->almacen->code;
        $this->nombre = $this->almacen->nombre;
        $this->direccion = $this->almacen->direccion;
        $this->telefono = $this->almacen->telefono;
        $this->email = $this->almacen->email;
        $this->estado = $this->almacen->estado;
        $this->capacidad = $this->almacen->capacidad;
        $this->responsable = $this->almacen->responsable;

        $this->modal_form_almacen = true;
    }

    public function eliminarAlmacen($id)
    {
        $this->almacen_id = $id;
        $this->almacen = WarehouseAlmacen::find($id);
        if ($this->almacen) {
            $this->modal_form_eliminar_almacen = true;
        }
    }

    public function confirmarEliminarAlmacen()
    {
        $this->almacen->delete();
        $this->modal_form_eliminar_almacen = false;
        $this->reset(['almacen_id', 'almacen']);
    }

    public function guardarAlmacen()
    {
        $ruleUniqueCode = $this->almacen_id ? 'unique:almacenes,code,' . $this->almacen_id : 'unique:almacenes,code';

        $rules = [
            'code' => $ruleUniqueCode,
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'estado' => 'boolean',
            'capacidad' => 'required|numeric|min:0',
            'responsable' => 'required|string|max:255',
        ];

        $messages = [
            'code.required' => 'Por favor, ingrese el código del almacén',
            'code.unique' => 'Este código ya está registrado en el sistema',
            'nombre.required' => 'Por favor, ingrese el nombre del almacén',
            'direccion.required' => 'Por favor, ingrese la dirección del almacén',
            'email.email' => 'Por favor, ingrese un email válido',
            'capacidad.required' => 'Por favor, ingrese la capacidad del almacén',
            'capacidad.numeric' => 'La capacidad debe ser un número',
            'capacidad.min' => 'La capacidad no puede ser negativa',
            'responsable.required' => 'Por favor, ingrese el responsable del almacén',
        ];

        $data = $this->validate($rules, $messages);

        if ($this->almacen_id) {
            $almacen = WarehouseAlmacen::find($this->almacen_id);
            $almacen->update($data);
        } else {
            WarehouseAlmacen::create($data);
        }

        $this->modal_form_almacen = false;
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
            'responsable'
        ]);
        $this->resetValidation();
    }

    public function exportarAlmacenes()
    {
        //return Excel::download(new WarehouseAlmacenExport($this->almacenesExportar), 'almacenes_' . date('Y-m-d_H-i-s') . '.xlsx');
        $this->reset(['almacenesExportar']);
    }
}
