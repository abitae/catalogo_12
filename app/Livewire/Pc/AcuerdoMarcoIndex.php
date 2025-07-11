<?php

namespace App\Livewire\Pc;

use App\Models\Pc\AcuerdoMarco;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class AcuerdoMarcoIndex extends Component
{
    use WithPagination, Toast;

    // Propiedades para el modal
    public $modal_form_acuerdo = false;
    public $modal_form_eliminar_acuerdo = false;
    public $acuerdo_id;

    // Propiedades para el formulario
    public $code;
    public $name;
    public $isActive = true;

    // Propiedades para búsqueda y ordenamiento
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $isActiveFilter = '';

    public function updatedSearch()
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

    public function nuevoAcuerdo()
    {
        $this->reset(['code', 'name', 'isActive', 'acuerdo_id']);
        $this->isActive = true; // Asegurar que esté activo por defecto
        $this->modal_form_acuerdo = true;
    }

    public function editarAcuerdo($id)
    {
        $acuerdo = AcuerdoMarco::findOrFail($id);
        $this->acuerdo_id = $acuerdo->id;
        $this->code = $acuerdo->code;
        $this->name = $acuerdo->name;
        $this->isActive = $acuerdo->isActive;

        $this->modal_form_acuerdo = true;
    }

    public function eliminarAcuerdo($id)
    {
        $this->acuerdo_id = $id;
        $this->modal_form_eliminar_acuerdo = true;
    }

    public function confirmarEliminarAcuerdo()
    {
        $acuerdo = AcuerdoMarco::findOrFail($this->acuerdo_id);
        $acuerdo->delete();

        $this->modal_form_eliminar_acuerdo = false;
        $this->success('Acuerdo marco eliminado correctamente');
    }

    public function guardarAcuerdo()
    {
        $rules = [
            'code' => 'required|min:2|max:50|unique:acuerdo_marcos,code,' . ($this->acuerdo_id ?? ''),
            'name' => 'required|min:3|max:255|unique:acuerdo_marcos,name,' . ($this->acuerdo_id ?? ''),
        ];

        $messages = [
            'code.required' => 'El código es requerido',
            'code.min' => 'El código debe tener al menos 2 caracteres',
            'code.max' => 'El código debe tener menos de 50 caracteres',
            'code.unique' => 'Ya existe un acuerdo marco con este código',
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'name.unique' => 'Ya existe un acuerdo marco con este nombre',
        ];

        $this->validate($rules, $messages);

        if ($this->acuerdo_id) {
            $acuerdo = AcuerdoMarco::findOrFail($this->acuerdo_id);
        } else {
            $acuerdo = new AcuerdoMarco();
        }

        $acuerdo->code = $this->code;
        $acuerdo->name = $this->name;
        $acuerdo->isActive = $this->isActive;

        $acuerdo->save();

        $this->modal_form_acuerdo = false;
        $this->success($this->acuerdo_id ? 'Acuerdo marco actualizado correctamente' : 'Acuerdo marco creado correctamente');
    }

    public function render()
    {
        $acuerdos = AcuerdoMarco::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->isActiveFilter !== '', function ($query) {
                $query->where('isActive', $this->isActiveFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.pc.acuerdo-marco', [
            'acuerdos' => $acuerdos
        ]);
    }
}
