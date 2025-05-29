<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\CategoryCatalogo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class CategoryCatalogoIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Propiedades para el modal
    public $modal_form_categoria = false;
    public $modal_form_eliminar_categoria = false;
    public $categoria_id;

    // Propiedades para el formulario
    public $name;
    public $logo;
    public $fondo;
    public $archivo;
    public $isActive = true;

    // Propiedades para archivos temporales
    public $tempLogo;
    public $tempFondo;
    public $tempArchivo;
    public $logoPreview;
    public $fondoPreview;
    public $archivoPreview;

    // Propiedades para búsqueda y ordenamiento
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $rules = [
        'tempLogo' => 'nullable|image|max:20480', // 20MB max
        'tempFondo' => 'nullable|image|max:20480', // 20MB max
        'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480', // Solo PDF, 20MB max
    ];

    protected $messages = [
        'tempArchivo.mimes' => 'El archivo debe ser un PDF, DOC, DOCX, XLS, XLSX, PPT o PPTX',
        'tempArchivo.max' => 'El archivo no debe pesar más de 20MB',
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
        $this->validate([
            'tempLogo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480'
        ]);

        if ($this->tempLogo) {
            $this->logoPreview = $this->tempLogo->temporaryUrl();
        }
    }

    public function updatedTempFondo()
    {
        $this->validate([
            'tempFondo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480'
        ]);

        if ($this->tempFondo) {
            $this->fondoPreview = $this->tempFondo->temporaryUrl();
        }
    }

    public function updatedTempArchivo()
    {
        $this->validate([
            'tempArchivo' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480'
        ]);

        if ($this->tempArchivo) {
            $this->archivoPreview = $this->tempArchivo->getClientOriginalName();
        }
    }

    public function removeLogo()
    {
        if ($this->logo && Storage::exists($this->logo)) {
            Storage::delete($this->logo);
        }
        $this->logo = null;
        $this->tempLogo = null;
        $this->logoPreview = null;
    }

    public function removeFondo()
    {
        if ($this->fondo && Storage::exists($this->fondo)) {
            Storage::delete($this->fondo);
        }
        $this->fondo = null;
        $this->tempFondo = null;
        $this->fondoPreview = null;
    }

    public function removeArchivo()
    {
        if ($this->archivo && Storage::exists($this->archivo)) {
            Storage::delete($this->archivo);
        }
        $this->archivo = null;
        $this->tempArchivo = null;
        $this->archivoPreview = null;
    }

    public function nuevoCategoria()
    {
        $this->reset([
            'name', 'logo', 'fondo', 'archivo', 'isActive',
            'tempLogo', 'tempFondo', 'tempArchivo',
            'logoPreview', 'fondoPreview', 'archivoPreview',
            'categoria_id'
        ]);
        $this->modal_form_categoria = true;
    }

    public function editarCategoria($id)
    {
        $categoria = CategoryCatalogo::findOrFail($id);
        $this->categoria_id = $categoria->id;
        $this->name = $categoria->name;
        $this->logo = $categoria->logo;
        $this->fondo = $categoria->fondo;
        $this->archivo = $categoria->archivo;
        $this->isActive = $categoria->isActive;

        if ($categoria->logo) {
            $this->logoPreview = asset('storage/' . $categoria->logo);
        }
        if ($categoria->fondo) {
            $this->fondoPreview = asset('storage/' . $categoria->fondo);
        }
        if ($categoria->archivo) {
            $this->archivoPreview = basename($categoria->archivo);
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
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Categoría eliminada correctamente'
        ]);
    }

    public function guardarCategoria()
    {
        // Validaciones
        $rules = [
            'name' => 'required|min:3|max:255',
            'tempLogo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempFondo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'isActive' => 'boolean',
        ];

        $messages = [
            'name.required' => 'Por favor, ingrese el nombre de la categoría',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre no debe exceder los 255 caracteres',
            'tempLogo.image' => 'El archivo debe ser una imagen válida',
            'tempLogo.mimes' => 'La imagen debe ser en formato: jpeg, png, jpg, gif o svg',
            'tempLogo.max' => 'La imagen no debe superar los 20MB',
            'tempFondo.image' => 'El archivo debe ser una imagen válida',
            'tempFondo.mimes' => 'La imagen debe ser en formato: jpeg, png, jpg, gif o svg',
            'tempFondo.max' => 'La imagen no debe superar los 20MB',
            'tempArchivo.file' => 'El archivo adjunto no es válido',
            'tempArchivo.mimes' => 'El archivo debe ser en formato: pdf, doc, docx, xls, xlsx, ppt o pptx',
            'tempArchivo.max' => 'El archivo no debe superar los 20MB',
            'isActive.boolean' => 'El estado seleccionado no es válido',
        ];

        $data = $this->validate($rules, $messages);

        // Procesar logo
        if ($this->tempLogo) {
            // Eliminar logo anterior si existe
            if ($this->logo && Storage::exists($this->logo)) {
                Storage::delete($this->logo);
            }
            $logoPath = $this->tempLogo->store('categorias/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Procesar fondo
        if ($this->tempFondo) {
            // Eliminar fondo anterior si existe
            if ($this->fondo && Storage::exists($this->fondo)) {
                Storage::delete($this->fondo);
            }
            $fondoPath = $this->tempFondo->store('categorias/fondos', 'public');
            $data['fondo'] = $fondoPath;
        }

        // Procesar archivo
        if ($this->tempArchivo) {
            // Eliminar archivo anterior si existe
            if ($this->archivo && Storage::exists($this->archivo)) {
                Storage::delete($this->archivo);
            }
            $archivoPath = $this->tempArchivo->store('categorias/archivos', 'public');
            $data['archivo'] = $archivoPath;
        }

        if ($this->categoria_id) {
            $categoria = CategoryCatalogo::find($this->categoria_id);
            $categoria->update($data);
        } else {
            CategoryCatalogo::create($data);
        }

        $this->modal_form_categoria = false;
        $this->reset([
            'categoria_id',
            'tempLogo',
            'tempFondo',
            'tempArchivo',
            'logoPreview',
            'fondoPreview',
            'archivoPreview'
        ]);
        $this->resetValidation();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->categoria_id ? 'Categoría actualizada correctamente' : 'Categoría creada correctamente'
        ]);
    }

    public function render()
    {
        $categories = CategoryCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.catalogo.category-catalogo-index', [
            'categories' => $categories
        ]);
    }
}
