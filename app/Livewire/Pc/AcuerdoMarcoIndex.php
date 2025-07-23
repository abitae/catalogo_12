<?php

namespace App\Livewire\Pc;

use App\Models\Pc\AcuerdoMarco;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
        try {
            $acuerdo = AcuerdoMarco::findOrFail($this->acuerdo_id);
            $acuerdoName = $acuerdo->name;
            $acuerdo->delete();

            Log::info('Auditoría: Acuerdo marco eliminado', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'delete_acuerdo_marco',
                'acuerdo_id' => $this->acuerdo_id,
                'acuerdo_name' => $acuerdoName,
                'timestamp' => now()
            ]);

            $this->modal_form_eliminar_acuerdo = false;
            $this->success('Acuerdo marco eliminado correctamente');
        } catch (\Exception $e) {
            $this->error('Error al eliminar el acuerdo marco: ' . $e->getMessage());
            Log::error('Error en eliminación de acuerdo marco', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'acuerdo_id' => $this->acuerdo_id ?? null
            ]);
        }
    }

    public function guardarAcuerdo()
    {
        try {
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
                $acuerdo->code = $this->code;
                $acuerdo->name = $this->name;
                $acuerdo->isActive = $this->isActive;
                $acuerdo->save();

                Log::info('Auditoría: Acuerdo marco actualizado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_acuerdo_marco',
                    'acuerdo_id' => $this->acuerdo_id,
                    'acuerdo_code' => $this->code,
                    'acuerdo_name' => $this->name,
                    'isActive' => $this->isActive,
                    'timestamp' => now()
                ]);

                $this->success('Acuerdo marco actualizado correctamente');
            } else {
                $acuerdo = new AcuerdoMarco();
                $acuerdo->code = $this->code;
                $acuerdo->name = $this->name;
                $acuerdo->isActive = $this->isActive;
                $acuerdo->save();

                Log::info('Auditoría: Acuerdo marco creado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_acuerdo_marco',
                    'acuerdo_id' => $acuerdo->id,
                    'acuerdo_code' => $this->code,
                    'acuerdo_name' => $this->name,
                    'isActive' => $this->isActive,
                    'timestamp' => now()
                ]);

                $this->success('Acuerdo marco creado correctamente');
            }

            $this->modal_form_acuerdo = false;
        } catch (\Exception $e) {
            $this->error('Error al guardar el acuerdo marco: ' . $e->getMessage());
            Log::error('Error en guardado de acuerdo marco', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'acuerdo_id' => $this->acuerdo_id ?? null
            ]);
        }
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
