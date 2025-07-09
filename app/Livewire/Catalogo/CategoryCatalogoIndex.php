<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\CategoryCatalogo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
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
        $categoria = CategoryCatalogo::findOrFail($this->categoria_id);

        // Eliminar archivos si existen
        if ($categoria->logo) {
            Storage::disk('public')->delete($categoria->logo);
        }
        if ($categoria->fondo) {
            Storage::disk('public')->delete($categoria->fondo);
        }
        if ($categoria->archivo) {
            Storage::disk('public')->delete($categoria->archivo);
        }

        $categoria->delete();

        $this->modal_form_eliminar_categoria = false;
        $this->success('Categoría eliminada correctamente');
    }

    public function guardarCategoria()
    {
        $rules = [
            'name' => 'required|min:3|max:255|unique:category_catalogos,name,' . ($this->categoria_id ?? ''),
            'tempLogo' => 'nullable|image|max:20480', // 2MB max
            'tempFondo' => 'nullable|image|max:20480', // 2MB max
            'tempArchivo' => 'nullable|file|max:10240', // 10MB max
        ];

        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'name.unique' => 'Ya existe una categoría con este nombre',
            'tempLogo.image' => 'El logo debe ser una imagen',
            'tempFondo.image' => 'El fondo debe ser una imagen',
            'tempArchivo.file' => 'El archivo debe ser válido',
        ];

        $this->validate($rules, $messages);

        if ($this->categoria_id) {
            $categoria = CategoryCatalogo::findOrFail($this->categoria_id);

            // Eliminar archivos anteriores si existen y se suben nuevos
            if ($this->tempLogo && $categoria->logo) {
                Storage::disk('public')->delete($categoria->logo);
            }
            if ($this->tempFondo && $categoria->fondo) {
                Storage::disk('public')->delete($categoria->fondo);
            }
            if ($this->tempArchivo && $categoria->archivo) {
                Storage::disk('public')->delete($categoria->archivo);
            }
        } else {
            $categoria = new CategoryCatalogo();
        }

        $categoria->name = $this->name;
        $categoria->isActive = $this->isActive;

        // Procesar logo si se subió uno nuevo
        if ($this->tempLogo) {
            $path = $this->tempLogo->store('categorias/logos', 'public');
            $categoria->logo = $path;
        }

        // Procesar fondo si se subió uno nuevo
        if ($this->tempFondo) {
            $path = $this->tempFondo->store('categorias/fondos', 'public');
            $categoria->fondo = $path;
        }

        // Procesar archivo si se subió uno nuevo
        if ($this->tempArchivo) {
            $path = $this->tempArchivo->store('categorias/archivos', 'public');
            $categoria->archivo = $path;
        }

        $categoria->save();

        $this->modal_form_categoria = false;
        $this->success($this->categoria_id ? 'Categoría actualizada correctamente' : 'Categoría creada correctamente');
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
