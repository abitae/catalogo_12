<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\CategoryCatalogo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class CategoryCatalogoIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    // Propiedades para el modal
    public $modal_form_categoria = false;
    public $modal_form_eliminar_categoria = false;
    public $categoria_id;

    // Propiedades para el formulario
    public $name;
    public $isActive = true;

    // Propiedades para archivos temporales
    public $tempLogo;
    public $tempFondo;
    public $tempArchivo;
    public $logoPreview;
    public $fondoPreview;

    // Propiedades para búsqueda y ordenamiento
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $isActiveFilter = '';

    protected $rules = [
        'tempLogo' => 'nullable|image|max:20480', // 2MB max
        'tempFondo' => 'nullable|image|max:20480', // 2MB max
        'tempArchivo' => 'nullable|file|max:10240', // 10MB max
    ];

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

    public function updatedTempLogo()
    {
        $this->validateOnly('tempLogo');

        if ($this->tempLogo) {
            $this->logoPreview = $this->tempLogo->temporaryUrl();
        }
    }

    public function updatedTempFondo()
    {
        $this->validateOnly('tempFondo');

        if ($this->tempFondo) {
            $this->fondoPreview = $this->tempFondo->temporaryUrl();
        }
    }

    public function removeLogo()
    {
        $this->tempLogo = null;
        $this->logoPreview = null;
    }

    public function removeFondo()
    {
        $this->tempFondo = null;
        $this->fondoPreview = null;
    }

    public function nuevoCategoria()
    {
        $this->reset(['name', 'isActive', 'tempLogo', 'tempFondo', 'tempArchivo', 'logoPreview', 'fondoPreview', 'categoria_id']);
        $this->isActive = true; // Asegurar que esté activo por defecto
        $this->modal_form_categoria = true;
    }

    public function editarCategoria($id)
    {
        $categoria = CategoryCatalogo::findOrFail($id);
        $this->categoria_id = $categoria->id;
        $this->name = $categoria->name;
        $this->isActive = $categoria->isActive;

        if ($categoria->logo) {
            $this->logoPreview = asset('storage/' . $categoria->logo);
        }

        if ($categoria->fondo) {
            $this->fondoPreview = asset('storage/' . $categoria->fondo);
        }

        $this->modal_form_categoria = true;
    }

    public function eliminarCategoria($id)
    {
        $this->categoria_id = $id;
        $this->modal_form_eliminar_categoria = true;
    }

    public function confirmarEliminarCategoria()
    {
        try {
            $categoria = CategoryCatalogo::findOrFail($this->categoria_id);
            $categoria->delete();

            $this->modal_form_eliminar_categoria = false;
            $this->reset(['categoria_id']);

            $this->success('Categoría eliminada correctamente');

            Log::info('Auditoría: Categoría eliminada', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'delete_categoria',
                'categoria_id' => $this->categoria_id,
                'categoria_name' => $categoria->name,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            $this->error('Error al eliminar la categoría: ' . $e->getMessage());
            Log::error('Error en eliminación de categoría', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'categoria_id' => $this->categoria_id ?? null
            ]);
        }
    }

    public function guardarCategoria()
    {
        try {
            $data = $this->validate();

            // Procesar archivos si se subieron
            if ($this->tempLogo) {
                $data['logo'] = $this->tempLogo->store('categorias/logos', 'public');
            }
            if ($this->tempFondo) {
                $data['fondo'] = $this->tempFondo->store('categorias/fondos', 'public');
            }
            if ($this->tempArchivo) {
                $data['archivo'] = $this->tempArchivo->store('categorias/archivos', 'public');
            }

            if ($this->categoria_id) {
                $categoria = CategoryCatalogo::findOrFail($this->categoria_id);
                $categoria->update($data);

                Log::info('Auditoría: Categoría actualizada', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_categoria',
                    'categoria_id' => $this->categoria_id,
                    'categoria_name' => $data['name'],
                    'isActive' => $data['isActive'],
                    'timestamp' => now()
                ]);

                $this->success('Categoría actualizada correctamente');
            } else {
                $categoria = CategoryCatalogo::create($data);

                Log::info('Auditoría: Categoría creada', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_categoria',
                    'categoria_id' => $categoria->id,
                    'categoria_name' => $data['name'],
                    'isActive' => $data['isActive'],
                    'timestamp' => now()
                ]);

                $this->success('Categoría creada correctamente');
            }

            $this->modal_form_categoria = false;
            $this->reset(['name', 'isActive', 'tempLogo', 'tempFondo', 'tempArchivo', 'logoPreview', 'fondoPreview', 'categoria_id']);
        } catch (\Exception $e) {
            $this->error('Error al guardar la categoría: ' . $e->getMessage());
            Log::error('Error en guardado de categoría', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'categoria_id' => $this->categoria_id ?? null
            ]);
        }
    }

    public function render()
    {
        $categorias = CategoryCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->isActiveFilter !== '', function ($query) {
                $query->where('isActive', $this->isActiveFilter);
            })
            ->latest()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.catalogo.category-catalogo-index', [
            'categorias' => $categorias
        ]);
    }
}
