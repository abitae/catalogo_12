<?php

namespace App\Livewire\Crm;

use App\Models\Crm\TipoNegocioCrm;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class TipeNegocioCrmIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $activo_filter = '';

    // Modal Form Tipo de Negocio
    public $modal_form_tipo_negocio = false;
    public $modal_form_eliminar_tipo_negocio = false;
    public $tipo_negocio_id = '';
    public $tipo_negocio = null;

    // Variables para el formulario
    public $nombre = '';
    public $codigo = '';
    public $descripcion = '';
    public $activo = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'activo_filter' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:tipos_negocio_crm,nombre,' . $this->tipo_negocio_id,
            'codigo' => 'nullable|string|max:50|unique:tipos_negocio_crm,codigo,' . $this->tipo_negocio_id,
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'nombre.unique' => 'Este nombre ya está registrado',
            'codigo.unique' => 'Este código ya está registrado',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActivoFilter()
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
            'activo_filter',
            'sortField',
            'sortDirection',
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
                        ->orWhere('codigo', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->activo_filter !== '', function ($query) {
                $query->where('activo', $this->activo_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.tipe-negocio-crm-index', [
            'tipos_negocio' => $query->paginate($this->perPage),
        ]);
    }

    public function nuevoTipoNegocio()
    {
        $this->resetForm();
        $this->modal_form_tipo_negocio = true;
    }

    public function editarTipoNegocio($id)
    {
        $this->tipo_negocio_id = $id;
        $this->tipo_negocio = TipoNegocioCrm::find($id);

        if ($this->tipo_negocio) {
            $this->nombre = $this->tipo_negocio->nombre;
            $this->codigo = $this->tipo_negocio->codigo;
            $this->descripcion = $this->tipo_negocio->descripcion;
            $this->activo = $this->tipo_negocio->activo;
        }

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
            if ($this->tipo_negocio) {
                $this->tipo_negocio->delete();
                $this->success('Tipo de negocio eliminado correctamente');
            }
        } catch (\Exception $e) {
            $this->error('Error al eliminar el tipo de negocio: ' . $e->getMessage());
        }

        $this->modal_form_eliminar_tipo_negocio = false;
        $this->reset(['tipo_negocio_id', 'tipo_negocio']);
    }

    private function resetForm()
    {
        $this->reset([
            'tipo_negocio_id',
            'tipo_negocio',
            'nombre',
            'codigo',
            'descripcion',
            'activo'
        ]);
        $this->resetValidation();
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
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el tipo de negocio: ' . $e->getMessage());
        }
    }
}
