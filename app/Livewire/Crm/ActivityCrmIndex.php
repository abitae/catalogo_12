<?php

namespace App\Livewire\Crm;

use App\Models\Crm\ActivityCrm;
use App\Models\Crm\LeadCrm;
use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\DealCrm;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityCrmIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'fecha_vencimiento';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $tipo_filter = '';
    public $estado_filter = '';
    public $prioridad_filter = '';
    public $lead_filter = '';
    public $opportunity_filter = '';
    public $deal_filter = '';

    // Modal Form Actividad
    public $modal_form_activity = false;
    public $modal_form_eliminar_activity = false;
    public $activity_id = '';
    public $activity = null;

    // Variables para el formulario
    public $tipo = '';
    public $asunto = '';
    public $descripcion = '';
    public $fecha_vencimiento = '';
    public $estado = 'pendiente';
    public $prioridad = 'normal';
    public $lead_id = '';
    public $opportunity_id = '';
    public $deal_id = '';
    public $asignado_a = '';
    public $fecha_completado = '';

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
            'tipo_filter',
            'estado_filter',
            'prioridad_filter',
            'lead_filter',
            'opportunity_filter',
            'deal_filter',
            'perPage'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityCrm::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('asunto', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->tipo_filter, function ($query) {
                $query->where('tipo', $this->tipo_filter);
            })
            ->when($this->estado_filter, function ($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->when($this->prioridad_filter, function ($query) {
                $query->where('prioridad', $this->prioridad_filter);
            })
            ->when($this->lead_filter, function ($query) {
                $query->where('lead_id', $this->lead_filter);
            })
            ->when($this->opportunity_filter, function ($query) {
                $query->where('opportunity_id', $this->opportunity_filter);
            })
            ->when($this->deal_filter, function ($query) {
                $query->where('deal_id', $this->deal_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.activity-crm-index', [
            'activities' => $query->paginate($this->perPage),
            'leads' => LeadCrm::all(),
            'opportunities' => OpportunityCrm::all(),
            'deals' => DealCrm::all()
        ]);
    }

    public function nuevaActivity()
    {
        $this->modal_form_activity = true;
    }

    public function editarActivity($id)
    {
        $this->activity_id = $id;
        $this->activity = ActivityCrm::find($id);
        $this->tipo = $this->activity->tipo;
        $this->asunto = $this->activity->asunto;
        $this->descripcion = $this->activity->descripcion;
        $this->fecha_vencimiento = $this->activity->fecha_vencimiento;
        $this->estado = $this->activity->estado;
        $this->prioridad = $this->activity->prioridad;
        $this->lead_id = $this->activity->lead_id;
        $this->opportunity_id = $this->activity->opportunity_id;
        $this->deal_id = $this->activity->deal_id;
        $this->asignado_a = $this->activity->asignado_a;
        $this->fecha_completado = $this->activity->fecha_completado;

        $this->modal_form_activity = true;
    }

    public function eliminarActivity($id)
    {
        $this->activity_id = $id;
        $this->activity = ActivityCrm::find($id);
        if ($this->activity) {
            $this->modal_form_eliminar_activity = true;
        }
    }

    public function confirmarEliminarActivity()
    {
        $this->activity->delete();
        $this->modal_form_eliminar_activity = false;
        $this->reset(['activity_id', 'activity']);
    }

    public function guardarActivity()
    {
        $rules = [
            'tipo' => 'required|string|in:llamada,reunion,email,tarea',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_vencimiento' => 'required|date',
            'estado' => 'required|string|in:pendiente,completada,cancelada',
            'prioridad' => 'required|string|in:baja,normal,alta,urgente',
            'lead_id' => 'nullable|exists:leads_crm,id',
            'opportunity_id' => 'nullable|exists:opportunities_crm,id',
            'deal_id' => 'nullable|exists:deals_crm,id',
            'asignado_a' => 'nullable|integer',
            'fecha_completado' => 'nullable|date'
        ];

        $messages = [
            'tipo.required' => 'El tipo es requerido',
            'tipo.in' => 'El tipo seleccionado no es válido',
            'asunto.required' => 'El asunto es requerido',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es requerida',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado seleccionado no es válido',
            'prioridad.required' => 'La prioridad es requerida',
            'prioridad.in' => 'La prioridad seleccionada no es válida',
            'lead_id.exists' => 'El lead seleccionado no existe',
            'opportunity_id.exists' => 'La oportunidad seleccionada no existe',
            'deal_id.exists' => 'El deal seleccionado no existe'
        ];

        $data = $this->validate($rules, $messages);

        if ($this->activity_id) {
            $activity = ActivityCrm::find($this->activity_id);
            $activity->update($data);
        } else {
            ActivityCrm::create($data);
        }

        $this->modal_form_activity = false;
        $this->reset([
            'activity_id',
            'activity',
            'tipo',
            'asunto',
            'descripcion',
            'fecha_vencimiento',
            'estado',
            'prioridad',
            'lead_id',
            'opportunity_id',
            'deal_id',
            'asignado_a',
            'fecha_completado'
        ]);
        $this->resetValidation();
    }
}
