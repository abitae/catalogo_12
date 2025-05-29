<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\BrandCatalogo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BrandCatalogoIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Propiedades para el modal
    public $modal_form_marca = false;
    public $modal_form_eliminar_marca = false;
    public $marca_id;

    // Propiedades para el formulario
    public $name;
    public $logo;
    public $archivo;
    public $isActive = true;

    // Propiedades para archivos temporales
    public $tempLogo;
    public $tempArchivo;
    public $logoPreview;
    public $archivoPreview;

    // Propiedades para búsqueda y ordenamiento
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $rules = [
        'tempLogo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
    ];

    protected $messages = [
        'tempLogo.image' => 'El archivo debe ser una imagen válida',
        'tempLogo.mimes' => 'La imagen debe ser en formato: jpeg, png, jpg, gif o svg',
        'tempLogo.max' => 'La imagen no debe superar los 20MB',
        'tempArchivo.file' => 'El archivo adjunto no es válido',
        'tempArchivo.mimes' => 'El archivo debe ser en formato: pdf, doc, docx, xls, xlsx, ppt o pptx',
        'tempArchivo.max' => 'El archivo no debe superar los 20MB',
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

    public function removeArchivo()
    {
        if ($this->archivo && Storage::exists($this->archivo)) {
            Storage::delete($this->archivo);
        }
        $this->archivo = null;
        $this->tempArchivo = null;
        $this->archivoPreview = null;
    }

    public function nuevoMarca()
    {
        $this->reset([
            'name', 'logo', 'archivo', 'isActive',
            'tempLogo', 'tempArchivo',
            'logoPreview', 'archivoPreview',
            'marca_id'
        ]);
        $this->modal_form_marca = true;
    }

    public function editarMarca($id)
    {
        $marca = BrandCatalogo::findOrFail($id);
        $this->marca_id = $marca->id;
        $this->name = $marca->name;
        $this->logo = $marca->logo;
        $this->archivo = $marca->archivo;
        $this->isActive = $marca->isActive;

        if ($marca->logo) {
            $this->logoPreview = asset('storage/' . $marca->logo);
        }
        if ($marca->archivo) {
            $this->archivoPreview = basename($marca->archivo);
        }

        $this->modal_form_marca = true;
    }

    public function eliminarMarca($id)
    {
        $this->marca_id = $id;
        $this->modal_form_eliminar_marca = true;
    }

    public function confirmarEliminarMarca()
    {
        $marca = BrandCatalogo::findOrFail($this->marca_id);

        // Eliminar archivos si existen
        if ($marca->logo) {
            Storage::disk('public')->delete($marca->logo);
        }
        if ($marca->archivo) {
            Storage::disk('public')->delete($marca->archivo);
        }

        $marca->delete();

        $this->modal_form_eliminar_marca = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Marca eliminada correctamente'
        ]);
    }

    public function guardarMarca()
    {
        // Validaciones
        $rules = [
            'name' => 'required|min:3|max:255',
            'tempLogo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'isActive' => 'boolean',
        ];

        $messages = [
            'name.required' => 'Por favor, ingrese el nombre de la marca',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre no debe exceder los 255 caracteres',
            'tempLogo.image' => 'El archivo debe ser una imagen válida',
            'tempLogo.mimes' => 'La imagen debe ser en formato: jpeg, png, jpg, gif o svg',
            'tempLogo.max' => 'La imagen no debe superar los 20MB',
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
            $logoPath = $this->tempLogo->store('marcas/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Procesar archivo
        if ($this->tempArchivo) {
            // Eliminar archivo anterior si existe
            if ($this->archivo && Storage::exists($this->archivo)) {
                Storage::delete($this->archivo);
            }
            $archivoPath = $this->tempArchivo->store('marcas/archivos', 'public');
            $data['archivo'] = $archivoPath;
        }

        if ($this->marca_id) {
            $marca = BrandCatalogo::find($this->marca_id);
            $marca->update($data);
        } else {
            BrandCatalogo::create($data);
        }

        $this->modal_form_marca = false;
        $this->reset([
            'marca_id',
            'tempLogo',
            'tempArchivo',
            'logoPreview',
            'archivoPreview'
        ]);
        $this->resetValidation();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->marca_id ? 'Marca actualizada correctamente' : 'Marca creada correctamente'
        ]);
    }

    public function render()
    {
        $brands = BrandCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.catalogo.brand-catalogo-index', [
            'brands' => $brands
        ]);
    }
}
