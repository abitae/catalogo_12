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
    public $activo = true;
    public $codigo = '';
    public $tempImage = null;
    public $imagePreview = null;

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
            'activo' => 'required|boolean',
            'codigo' => 'nullable|string|max:50|unique:tipos_negocio_crm,codigo,' . $this->tipo_negocio_id,
            'tempImage' => 'nullable|image|max:2048',
        ];
    }

    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'nombre.unique' => 'Este nombre ya está registrado',
            'activo.required' => 'El estado es requerido',
            'codigo.unique' => 'Este código ya está registrado',
            'tempImage.image' => 'El archivo debe ser una imagen',
            'tempImage.max' => 'La imagen no debe superar los 2MB',
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
                if ($this->estado_filter === 'activo') {
                    $query->where('activo', true);
                } elseif ($this->estado_filter === 'inactivo') {
                    $query->where('activo', false);
                }
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
        $this->activo = $this->tipo_negocio->activo;
        $this->codigo = $this->tipo_negocio->codigo ?? '';

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

    public function removeImage()
    {
        $this->tempImage = null;
        $this->imagePreview = null;
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
                'activo',
                'codigo',
                'tempImage',
                'imagePreview'
            ]);
            $this->resetValidation();
        } catch (\Exception $e) {
            $this->error('Error al guardar el tipo de negocio: ' . $e->getMessage());
        }
    }
}
