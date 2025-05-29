<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\LineCatalogo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class LineCatalogoIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Propiedades para el modal
    public $modal_form_linea = false;
    public $modal_form_eliminar_linea = false;
    public $linea_id;

    // Propiedades para el formulario
    public $name;
    public $code;
    public $isActive = true;

    // Propiedades para archivos temporales
    public $tempImage;
    public $imagePreview;

    // Propiedades para búsqueda y ordenamiento
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $rules = [

        'tempImage' => 'nullable|image|max:20480', // 2MB max
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

    public function updatedTempImage()
    {
        $this->validateOnly('tempImage');

        if ($this->tempImage) {
            $this->imagePreview = $this->tempImage->temporaryUrl();
        }
    }

    public function removeImage()
    {
        $this->tempImage = null;
        $this->imagePreview = null;
    }

    public function nuevaLinea()
    {
        $this->reset(['name', 'code', 'isActive', 'tempImage', 'imagePreview', 'linea_id']);
        $this->modal_form_linea = true;
    }

    public function editarLinea($id)
    {
        $linea = LineCatalogo::findOrFail($id);
        $this->linea_id = $linea->id;
        $this->name = $linea->name;
        $this->code = $linea->code;
        $this->isActive = $linea->isActive;

        if ($linea->logo) {
            $this->imagePreview = asset('storage/' . $linea->logo);
        }

        $this->modal_form_linea = true;
    }

    public function eliminarLinea($id)
    {
        $this->linea_id = $id;
        $this->modal_form_eliminar_linea = true;
    }

    public function confirmarEliminarLinea()
    {
        $linea = LineCatalogo::findOrFail($this->linea_id);

        // Eliminar imagen si existe
        if ($linea->logo) {
            Storage::disk('public')->delete($linea->logo);
        }

        $linea->delete();

        $this->modal_form_eliminar_linea = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Línea eliminada correctamente'
        ]);
    }

    public function guardarLinea()
    {

        // Validar código único excluyendo el registro actual
        $IsUnique = $this->linea_id ? 'required|min:3|max:50|unique:line_catalogos,code,' . $this->linea_id : 'required|min:3|max:50|unique:line_catalogos,code';
        $rules = [
            'name' => 'required|min:3|max:255',
            'code' => $IsUnique ,
            'tempImage' => 'nullable|image|max:20480', // 2MB max
        ];
        $messages = [
            'code.unique' => 'El código ya existe',
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'code.required' => 'El código es requerido',
            'code.min' => 'El código debe tener al menos 3 caracteres',
            'code.max' => 'El código debe tener menos de 50 caracteres',
            'tempImage.image' => 'El archivo debe ser una imagen',
        ];

        $this->validate($rules, $messages);

        if ($this->linea_id) {
            $linea = LineCatalogo::findOrFail($this->linea_id);


            // Eliminar imagen anterior si existe y se sube una nueva
            if ($this->tempImage && $linea->logo) {
                Storage::disk('public')->delete($linea->logo);
            }
        } else {
            $linea = new LineCatalogo();
        }


        $linea->name = $this->name;
        $linea->code = $this->code;
        $linea->isActive = $this->isActive;

        // Procesar imagen si se subió una nueva
        if ($this->tempImage) {
            $path = $this->tempImage->store('lineas/images', 'public');
            $linea->logo = $path;
        }

        $linea->save();

        $this->modal_form_linea = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->linea_id ? 'Línea actualizada correctamente' : 'Línea creada correctamente'
        ]);
    }

    public function render()
    {
        $lines = LineCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.catalogo.line-catalogo-index', [
            'lines' => $lines
        ]);
    }
}
