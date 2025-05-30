<?php

namespace App\Livewire\Crm;

use App\Models\Crm\DealCrm;
use App\Models\Crm\OpportunityCrm;
use Livewire\Component;
use Livewire\WithPagination;

class DealCrmIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $estado_filter = '';
    public $opportunity_filter = '';
    public $etapa_filter = '';

    // Modal Form Deal
    public $modal_form_deal = false;
    public $modal_form_eliminar_deal = false;
    public $deal_id = '';
    public $deal = null;

    // Variables para el formulario
    public $nombre = '';
    public $opportunity_id = '';
    public $valor = '';
    public $etapa = 'inicial';
    public $fecha_cierre = '';
    public $descripcion = '';
    public $terminos = '';
    public $asignado_a = '';
    public $estado = 'activo';
    public $probabilidad = '';
    public $ingreso_esperado = '';

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
            'opportunity_filter',
            'etapa_filter',
            'perPage'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = DealCrm::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->estado_filter, function ($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->when($this->opportunity_filter, function ($query) {
                $query->where('opportunity_id', $this->opportunity_filter);
            })
            ->when($this->etapa_filter, function ($query) {
                $query->where('etapa', $this->etapa_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.deal-crm-index', [
            'deals' => $query->paginate($this->perPage),
            'opportunities' => OpportunityCrm::all()
        ]);
    }

    public function nuevoDeal()
    {
        $this->modal_form_deal = true;
    }

    public function editarDeal($id)
    {
        $this->deal_id = $id;
        $this->deal = DealCrm::find($id);
        $this->nombre = $this->deal->nombre;
        $this->opportunity_id = $this->deal->opportunity_id;
        $this->valor = $this->deal->valor;
        $this->etapa = $this->deal->etapa;
        $this->fecha_cierre = $this->deal->fecha_cierre;
        $this->descripcion = $this->deal->descripcion;
        $this->terminos = $this->deal->terminos;
        $this->asignado_a = $this->deal->asignado_a;
        $this->estado = $this->deal->estado;
        $this->probabilidad = $this->deal->probabilidad;
        $this->ingreso_esperado = $this->deal->ingreso_esperado;

        $this->modal_form_deal = true;
    }

    public function eliminarDeal($id)
    {
        $this->deal_id = $id;
        $this->deal = DealCrm::find($id);
        if ($this->deal) {
            $this->modal_form_eliminar_deal = true;
        }
    }

    public function confirmarEliminarDeal()
    {
        $this->deal->delete();
        $this->modal_form_eliminar_deal = false;
        $this->reset(['deal_id', 'deal']);
    }

    public function guardarDeal()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'opportunity_id' => 'required|exists:opportunities_crm,id',
            'valor' => 'required|numeric|min:0',
            'etapa' => 'required|string|in:inicial,negociacion,propuesta,cierre',
            'fecha_cierre' => 'required|date',
            'descripcion' => 'nullable|string',
            'terminos' => 'nullable|string',
            'asignado_a' => 'nullable|integer',
            'estado' => 'required|string|in:activo,cerrado,cancelado',
            'probabilidad' => 'required|integer|min:0|max:100',
            'ingreso_esperado' => 'required|numeric|min:0'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'opportunity_id.required' => 'La oportunidad es requerida',
            'opportunity_id.exists' => 'La oportunidad seleccionada no existe',
            'valor.required' => 'El valor es requerido',
            'valor.numeric' => 'El valor debe ser un número',
            'valor.min' => 'El valor debe ser mayor o igual a 0',
            'etapa.required' => 'La etapa es requerida',
            'etapa.in' => 'La etapa seleccionada no es válida',
            'fecha_cierre.required' => 'La fecha de cierre es requerida',
            'fecha_cierre.date' => 'La fecha de cierre debe ser una fecha válida',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado seleccionado no es válido',
            'probabilidad.required' => 'La probabilidad es requerida',
            'probabilidad.integer' => 'La probabilidad debe ser un número entero',
            'probabilidad.min' => 'La probabilidad debe ser mayor o igual a 0',
            'probabilidad.max' => 'La probabilidad debe ser menor o igual a 100',
            'ingreso_esperado.required' => 'El ingreso esperado es requerido',
            'ingreso_esperado.numeric' => 'El ingreso esperado debe ser un número',
            'ingreso_esperado.min' => 'El ingreso esperado debe ser mayor o igual a 0'
        ];

        $data = $this->validate($rules, $messages);

        if ($this->deal_id) {
            $deal = DealCrm::find($this->deal_id);
            $deal->update($data);
        } else {
            DealCrm::create($data);
        }

        $this->modal_form_deal = false;
        $this->reset([
            'deal_id',
            'deal',
            'nombre',
            'opportunity_id',
            'valor',
            'etapa',
            'fecha_cierre',
            'descripcion',
            'terminos',
            'asignado_a',
            'estado',
            'probabilidad',
            'ingreso_esperado'
        ]);
        $this->resetValidation();
    }
}
