<?php

namespace App\Livewire\Crm;

use App\Models\Crm\TipeNegocioCrm;
use App\Models\Crm\TipoNegocioCrm;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class TipoNegocioCrmIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $estado_filter = '';

    // Modal Form Tipo Negocio
    public $modal_form_tipo_negocio = false;
    public $modal_form_eliminar_tipo_negocio = false;
    public $tipo_negocio_id = '';
    public $tipo_negocio = null;

    // Variables para el formulario
    public $nombre = '';
    public $descripcion = '';
    public $estado = 'activo';

    protected $queryString = [
        'search' => ['except' => ''],
        'estado_filter' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:tipos_negocio_crm,nombre,' . $this->tipo_negocio_id,
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|string|in:activo,inactivo',
        ];
    }

    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'nombre.unique' => 'Este nombre ya está registrado',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado seleccionado no es válido',
        ];
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
            'estado_filter',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados correctamente');
    }

    public function render()
    {
        $query = TipoNegocioCrm::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->estado_filter, function ($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
        return view('livewire.crm.tipo-negocio-crm-index', [
            'tipos_negocio' => $query->paginate($this->perPage),
            'estados' => ['activo', 'inactivo'],
        ]);
    }

    public function nuevoTipoNegocio()
    {
        $this->modal_form_tipo_negocio = true;
    }

    public function editarTipoNegocio($id)
    {
        $this->tipo_negocio_id = $id;
        $this->tipo_negocio = TipoNegocioCrm::find($id);
        $this->nombre = $this->tipo_negocio->nombre;
        $this->descripcion = $this->tipo_negocio->descripcion;
        $this->estado = $this->tipo_negocio->estado;

        $this->modal_form_tipo_negocio = true;
    }

    public function eliminarTipoNegocio($id)
    {
        $this->tipo_negocio_id = $id;
        $this->tipo_negocio = TipoNegocioCrm::find($id);
        if ($this->tipo_negocio) {
            $this->modal_form_eliminar_tipo_negocio = true;
        }
    }

    public function confirmarEliminarTipoNegocio()
    {
        try {
            $this->tipo_negocio->delete();
            $this->modal_form_eliminar_tipo_negocio = false;
            $this->reset(['tipo_negocio_id', 'tipo_negocio']);
            $this->success('Tipo de negocio eliminado correctamente');
        } catch (\Exception $e) {
            $this->error('Error al eliminar el tipo de negocio: ' . $e->getMessage());
        }
    }

    public function guardarTipoNegocio()
    {
        try {
            $data = $this->validate();

            if ($this->tipo_negocio_id) {
                $tipo_negocio = TipoNegocioCrm::find($this->tipo_negocio_id);
                $tipo_negocio->update($data);
                $this->success('Tipo de negocio actualizado correctamente');
            } else {
                TipoNegocioCrm::create($data);
                $this->success('Tipo de negocio creado correctamente');
            }

            $this->modal_form_tipo_negocio = false;
            $this->reset([
                'tipo_negocio_id',
                'tipo_negocio',
                'nombre',
                'descripcion',
                'estado'
            ]);
            $this->resetValidation();
        } catch (\Exception $e) {
            $this->error('Error al guardar el tipo de negocio: ' . $e->getMessage());
        }
    }
}
