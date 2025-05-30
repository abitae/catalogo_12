<?php

namespace App\Livewire\Crm;

use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\TipeNegocioCrm;
use App\Models\Crm\MarcaCrm;
use App\Models\Crm\LeadCrm;
use Livewire\Component;
use Livewire\WithPagination;

class OpportunityCrmIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $estado_filter = '';
    public $tipo_negocio_filter = '';
    public $marca_filter = '';
    public $lead_filter = '';
    public $etapa_filter = '';

    // Modal Form Oportunidad
    public $modal_form_opportunity = false;
    public $modal_form_eliminar_opportunity = false;
    public $opportunity_id = '';
    public $opportunity = null;

    // Variables para el formulario
    public $nombre = '';
    public $estado = 'nueva';
    public $tipo_negocio_id = '';
    public $marca_id = '';
    public $lead_id = '';
    public $valor = '';
    public $etapa = 'inicial';
    public $probabilidad = '';
    public $fecha_cierre_esperada = '';
    public $descripcion = '';
    public $asignado_a = '';
    public $ultima_fecha_actividad = '';

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
            'tipo_negocio_filter',
            'marca_filter',
            'lead_filter',
            'etapa_filter',
            'perPage'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = OpportunityCrm::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->estado_filter, function ($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->when($this->tipo_negocio_filter, function ($query) {
                $query->where('tipo_negocio_id', $this->tipo_negocio_filter);
            })
            ->when($this->marca_filter, function ($query) {
                $query->where('marca_id', $this->marca_filter);
            })
            ->when($this->lead_filter, function ($query) {
                $query->where('lead_id', $this->lead_filter);
            })
            ->when($this->etapa_filter, function ($query) {
                $query->where('etapa', $this->etapa_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.opportunity-crm-index', [
            'opportunities' => $query->paginate($this->perPage),
            'tipos_negocio' => TipeNegocioCrm::all(),
            'marcas' => MarcaCrm::all(),
            'leads' => LeadCrm::all()
        ]);
    }

    public function nuevaOpportunity()
    {
        $this->modal_form_opportunity = true;
    }

    public function editarOpportunity($id)
    {
        $this->opportunity_id = $id;
        $this->opportunity = OpportunityCrm::find($id);
        $this->nombre = $this->opportunity->nombre;
        $this->estado = $this->opportunity->estado;
        $this->tipo_negocio_id = $this->opportunity->tipo_negocio_id;
        $this->marca_id = $this->opportunity->marca_id;
        $this->lead_id = $this->opportunity->lead_id;
        $this->valor = $this->opportunity->valor;
        $this->etapa = $this->opportunity->etapa;
        $this->probabilidad = $this->opportunity->probabilidad;
        $this->fecha_cierre_esperada = $this->opportunity->fecha_cierre_esperada;
        $this->descripcion = $this->opportunity->descripcion;
        $this->asignado_a = $this->opportunity->asignado_a;
        $this->ultima_fecha_actividad = $this->opportunity->ultima_fecha_actividad;

        $this->modal_form_opportunity = true;
    }

    public function eliminarOpportunity($id)
    {
        $this->opportunity_id = $id;
        $this->opportunity = OpportunityCrm::find($id);
        if ($this->opportunity) {
            $this->modal_form_eliminar_opportunity = true;
        }
    }

    public function confirmarEliminarOpportunity()
    {
        $this->opportunity->delete();
        $this->modal_form_eliminar_opportunity = false;
        $this->reset(['opportunity_id', 'opportunity']);
    }

    public function guardarOpportunity()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'estado' => 'required|string|in:nueva,en_proceso,ganada,perdida',
            'tipo_negocio_id' => 'required|exists:tipos_negocio_crm,id',
            'marca_id' => 'required|exists:marcas_crm,id',
            'lead_id' => 'required|exists:leads_crm,id',
            'valor' => 'required|numeric|min:0',
            'etapa' => 'required|string|in:inicial,negociacion,propuesta,cierre',
            'probabilidad' => 'required|integer|min:0|max:100',
            'fecha_cierre_esperada' => 'required|date',
            'descripcion' => 'nullable|string',
            'asignado_a' => 'nullable|integer',
            'ultima_fecha_actividad' => 'nullable|date'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado seleccionado no es válido',
            'tipo_negocio_id.required' => 'El tipo de negocio es requerido',
            'tipo_negocio_id.exists' => 'El tipo de negocio seleccionado no existe',
            'marca_id.required' => 'La marca es requerida',
            'marca_id.exists' => 'La marca seleccionada no existe',
            'lead_id.required' => 'El lead es requerido',
            'lead_id.exists' => 'El lead seleccionado no existe',
            'valor.required' => 'El valor es requerido',
            'valor.numeric' => 'El valor debe ser un número',
            'valor.min' => 'El valor debe ser mayor o igual a 0',
            'etapa.required' => 'La etapa es requerida',
            'etapa.in' => 'La etapa seleccionada no es válida',
            'probabilidad.required' => 'La probabilidad es requerida',
            'probabilidad.integer' => 'La probabilidad debe ser un número entero',
            'probabilidad.min' => 'La probabilidad debe ser mayor o igual a 0',
            'probabilidad.max' => 'La probabilidad debe ser menor o igual a 100',
            'fecha_cierre_esperada.required' => 'La fecha de cierre esperada es requerida',
            'fecha_cierre_esperada.date' => 'La fecha de cierre esperada debe ser una fecha válida'
        ];

        $data = $this->validate($rules, $messages);

        if ($this->opportunity_id) {
            $opportunity = OpportunityCrm::find($this->opportunity_id);
            $opportunity->update($data);
        } else {
            OpportunityCrm::create($data);
        }

        $this->modal_form_opportunity = false;
        $this->reset([
            'opportunity_id',
            'opportunity',
            'nombre',
            'estado',
            'tipo_negocio_id',
            'marca_id',
            'lead_id',
            'valor',
            'etapa',
            'probabilidad',
            'fecha_cierre_esperada',
            'descripcion',
            'asignado_a',
            'ultima_fecha_actividad'
        ]);
        $this->resetValidation();
    }
}
