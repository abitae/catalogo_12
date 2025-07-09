<?php

namespace App\Livewire\Crm;

use App\Models\Crm\MarcaCrm;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class MarcaCrmIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $activo_filter = '';
    public $categoria_filter = '';

    // Modal Form Marca
    public $modal_form_marca = false;
    public $modal_form_eliminar_marca = false;
    public $marca_id = '';
    public $marca = null;

    // Variables para el formulario
    public $nombre = '';
    public $codigo = '';
    public $categoria = '';
    public $descripcion = '';
    public $activo = true;

    // Manejo de archivos
    public $tempLogo = null;
    public $logoPreview = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'activo_filter' => ['except' => ''],
        'categoria_filter' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:marcas_crm,nombre,' . $this->marca_id,
            'codigo' => 'nullable|string|max:50|unique:marcas_crm,codigo,' . $this->marca_id,
            'categoria' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
            'tempLogo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ];
    }

    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'nombre.unique' => 'Este nombre ya está registrado',
            'codigo.unique' => 'Este código ya está registrado',
            'tempLogo.image' => 'El archivo debe ser una imagen',
            'tempLogo.mimes' => 'La imagen debe ser JPEG, PNG, JPG, GIF o SVG',
            'tempLogo.max' => 'La imagen no debe exceder 20MB',
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

    public function updatingCategoriaFilter()
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
            'categoria_filter',
            'sortField',
            'sortDirection',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados correctamente');
    }

    public function render()
    {
        $query = MarcaCrm::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('codigo', 'like', '%' . $this->search . '%')
                        ->orWhere('categoria', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->activo_filter !== '', function ($query) {
                $query->where('activo', $this->activo_filter);
            })
            ->when($this->categoria_filter, function ($query) {
                $query->where('categoria', 'like', '%' . $this->categoria_filter . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.marca-crm-index', [
            'marcas' => $query->paginate($this->perPage),
            'categorias' => MarcaCrm::whereNotNull('categoria')->distinct()->pluck('categoria')->filter(),
        ]);
    }

    public function nuevaMarca()
    {
        $this->resetForm();
        $this->modal_form_marca = true;
    }

    public function editarMarca($id)
    {
        $this->marca_id = $id;
        $this->marca = MarcaCrm::find($id);

        if ($this->marca) {
            $this->nombre = $this->marca->nombre;
            $this->codigo = $this->marca->codigo;
            $this->categoria = $this->marca->categoria;
            $this->descripcion = $this->marca->descripcion;
            $this->activo = $this->marca->activo;

            if ($this->marca->logo) {
                $this->logoPreview = Storage::url($this->marca->logo);
            }
        }

        $this->modal_form_marca = true;
    }

    public function eliminarMarca($id)
    {
        $this->marca_id = $id;
        $this->marca = MarcaCrm::find($id);
        if ($this->marca) {
            $this->modal_form_eliminar_marca = true;
        }
    }

    public function confirmarEliminarMarca()
    {
        try {
            if ($this->marca) {
                // Eliminar logo si existe
                if ($this->marca->logo && Storage::disk('public')->exists($this->marca->logo)) {
                    Storage::disk('public')->delete($this->marca->logo);
                }

                $this->marca->delete();
                $this->success('Marca eliminada correctamente');
            }
        } catch (\Exception $e) {
            $this->error('Error al eliminar la marca: ' . $e->getMessage());
        }

        $this->modal_form_eliminar_marca = false;
        $this->reset(['marca_id', 'marca']);
    }

    public function updatedTempLogo()
    {
        if ($this->tempLogo) {
            $this->logoPreview = $this->tempLogo->temporaryUrl();
        }
    }

    public function removeLogo()
    {
        $this->tempLogo = null;
        $this->logoPreview = null;
    }

    private function resetForm()
    {
        $this->reset([
            'marca_id',
            'marca',
            'nombre',
            'codigo',
            'categoria',
            'descripcion',
            'activo',
            'tempLogo',
            'logoPreview'
        ]);
        $this->resetValidation();
    }

    public function guardarMarca()
    {
        try {
            $data = $this->validate();

            // Manejar logo
            if ($this->tempLogo) {
                // Eliminar logo anterior si existe
                if ($this->marca_id && $this->marca && $this->marca->logo) {
                    Storage::disk('public')->delete($this->marca->logo);
                }

                $logoPath = $this->tempLogo->store('marcas/logos', 'public');
                $data['logo'] = $logoPath;
            }

            // Remover campo temporal
            unset($data['tempLogo']);

            if ($this->marca_id) {
                $marca = MarcaCrm::find($this->marca_id);
                $marca->update($data);
                $this->success('Marca actualizada correctamente');
            } else {
                MarcaCrm::create($data);
                $this->success('Marca creada correctamente');
            }

            $this->modal_form_marca = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar la marca: ' . $e->getMessage());
        }
    }
}
