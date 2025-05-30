<?php

namespace App\Livewire\Crm;

use App\Models\Crm\LeadCrm;
use Livewire\Component;
use Livewire\WithPagination;

class LeadCrmIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $estado_filter = '';
    public $origen_filter = '';
    public $empresa_filter = '';

    // Modal Form Lead
    public $modal_form_lead = false;
    public $modal_form_eliminar_lead = false;
    public $lead_id = '';
    public $lead = null;

    // Variables para el formulario
    public $nombre = '';
    public $correo = '';
    public $telefono = '';
    public $empresa = '';
    public $estado = 'nuevo';
    public $origen = '';
    public $notas = '';
    public $asignado_a = '';
    public $ultima_fecha_contacto = '';

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
            'estado_filter',
            'origen_filter',
            'empresa_filter',
            'perPage'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = LeadCrm::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('correo', 'like', '%' . $this->search . '%')
                        ->orWhere('empresa', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->estado_filter, function ($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->when($this->origen_filter, function ($query) {
                $query->where('origen', $this->origen_filter);
            })
            ->when($this->empresa_filter, function ($query) {
                $query->where('empresa', 'like', '%' . $this->empresa_filter . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.lead-crm-index', [
            'leads' => $query->paginate($this->perPage)
        ]);
    }

    public function nuevoLead()
    {
        $this->modal_form_lead = true;
    }

    public function editarLead($id)
    {
        $this->lead_id = $id;
        $this->lead = LeadCrm::find($id);
        $this->nombre = $this->lead->nombre;
        $this->correo = $this->lead->correo;
        $this->telefono = $this->lead->telefono;
        $this->empresa = $this->lead->empresa;
        $this->estado = $this->lead->estado;
        $this->origen = $this->lead->origen;
        $this->notas = $this->lead->notas;
        $this->asignado_a = $this->lead->asignado_a;
        $this->ultima_fecha_contacto = $this->lead->ultima_fecha_contacto;

        $this->modal_form_lead = true;
    }

    public function eliminarLead($id)
    {
        $this->lead_id = $id;
        $this->lead = LeadCrm::find($id);
        if ($this->lead) {
            $this->modal_form_eliminar_lead = true;
        }
    }

    public function confirmarEliminarLead()
    {
        $this->lead->delete();
        $this->modal_form_eliminar_lead = false;
        $this->reset(['lead_id', 'lead']);
    }

    public function guardarLead()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'estado' => 'required|string|in:nuevo,en_proceso,calificado,perdido',
            'origen' => 'nullable|string|in:web,referido,evento,redes_sociales',
            'notas' => 'nullable|string',
            'asignado_a' => 'nullable|integer',
            'ultima_fecha_contacto' => 'nullable|date'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'correo.required' => 'El correo es requerido',
            'correo.email' => 'El correo debe ser válido',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado seleccionado no es válido',
            'origen.in' => 'El origen seleccionado no es válido'
        ];

        $data = $this->validate($rules, $messages);

        if ($this->lead_id) {
            $lead = LeadCrm::find($this->lead_id);
            $lead->update($data);
        } else {
            LeadCrm::create($data);
        }

        $this->modal_form_lead = false;
        $this->reset([
            'lead_id',
            'lead',
            'nombre',
            'correo',
            'telefono',
            'empresa',
            'estado',
            'origen',
            'notas',
            'asignado_a',
            'ultima_fecha_contacto'
        ]);
        $this->resetValidation();
    }
}
