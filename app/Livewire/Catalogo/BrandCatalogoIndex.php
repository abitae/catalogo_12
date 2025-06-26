<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\BrandCatalogo;
use App\Traits\FileUploadTrait;
use App\Traits\NotificationTrait;
use App\Traits\TableTrait;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BrandCatalogoIndex extends Component
{
    use WithPagination, WithFileUploads, TableTrait, FileUploadTrait, NotificationTrait;

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

    // Configuración de búsqueda
    protected $searchFields = ['name'];

    public function mount()
    {
        $this->sortField = 'name';
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'sortField',
            'sortDirection',
            'perPage',
        ]);
        $this->resetPage();
    }

    public function updatedTempLogo()
    {
        $this->validate($this->validateImage('tempLogo'));

        if ($this->tempLogo) {
            $this->logoPreview = $this->tempLogo->temporaryUrl();
        }
    }

    public function updatedTempArchivo()
    {
        $this->validate($this->validateFile('tempArchivo'));

        if ($this->tempArchivo) {
            $this->archivoPreview = $this->tempArchivo->getClientOriginalName();
        }
    }

    public function removeLogo()
    {
        $this->deleteFile($this->logo);
        $this->logo = null;
        $this->tempLogo = null;
        $this->logoPreview = null;
    }

    public function removeArchivo()
    {
        $this->deleteFile($this->archivo);
        $this->archivo = null;
        $this->tempArchivo = null;
        $this->archivoPreview = null;
    }

    public function nuevoMarca()
    {
        $this->resetValidation();
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
        $this->resetValidation();
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
        try {
            $marca = BrandCatalogo::findOrFail($this->marca_id);

            // Eliminar archivos asociados
            $this->deleteFile($marca->logo);
            $this->deleteFile($marca->archivo);

            $marca->delete();

            $this->modal_form_eliminar_marca = false;
            $this->reset(['marca_id']);

            $this->handleSuccess('Marca eliminada correctamente', 'eliminación de marca');
        } catch (\Exception $e) {
            $this->handleError($e, 'eliminación de marca');
        }
    }

    public function guardarMarca()
    {
        // Validaciones
        $rules = [
            'name' => 'required|min:3|max:255|unique:brand_catalogos,name,' . ($this->marca_id ?? ''),
            'isActive' => 'boolean',
        ];

        // Agregar validaciones de archivos
        $rules = array_merge($rules, $this->validateImage('tempLogo'));
        $rules = array_merge($rules, $this->validateFile('tempArchivo'));

        $messages = [
            'name.required' => 'Por favor, ingrese el nombre de la marca',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre no debe exceder los 255 caracteres',
            'name.unique' => 'Esta marca ya existe en el sistema',
        ];

        // Agregar mensajes de validación de archivos
        $messages = array_merge($messages, $this->getFileValidationMessages());

        $data = $this->validate($rules, $messages);

        try {
            // Procesar archivos usando el trait
            $data['logo'] = $this->processImage($this->tempLogo, $this->logo, 'marcas/logos');
            $data['archivo'] = $this->processFile($this->tempArchivo, $this->archivo, 'marcas/archivos');

            if ($this->marca_id) {
                $marca = BrandCatalogo::findOrFail($this->marca_id);
                $marca->update($data);
                $message = 'Marca actualizada correctamente';
                $context = 'actualización de marca';
            } else {
                BrandCatalogo::create($data);
                $message = 'Marca creada correctamente';
                $context = 'creación de marca';
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

            $this->handleSuccess($message, $context);
        } catch (\Exception $e) {
            $this->handleError($e, 'guardado de marca');
        }
    }

    public function toggleMarcaStatus($id)
    {
        try {
            $marca = BrandCatalogo::findOrFail($id);
            $marca->update(['isActive' => !$marca->isActive]);

            $this->handleSuccess('Estado de la marca actualizado correctamente', 'cambio de estado');
        } catch (\Exception $e) {
            $this->handleError($e, 'cambio de estado');
        }
    }

    public function render()
    {
        $brands = $this->applySearch(BrandCatalogo::query(), $this->searchFields);
        $brands = $this->applySorting($brands);
        $brands = $this->applyPagination($brands);

        return view('livewire.catalogo.brand-catalogo-index', [
            'brands' => $brands
        ]);
    }
}
