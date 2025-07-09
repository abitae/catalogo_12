<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\LineCatalogo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mary\Traits\Toast;

class LineCatalogoIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    // Propiedades para el modal
    public $modal_form_linea = false;
    public $modal_form_eliminar_linea = false;
    public $linea_id;

    // Propiedades para el formulario
    public $name;
    public $code;
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

    public function nuevaLinea()
    {
        $this->reset(['name', 'code', 'isActive', 'tempLogo', 'tempFondo', 'tempArchivo', 'logoPreview', 'fondoPreview', 'linea_id']);
        $this->isActive = true; // Asegurar que esté activo por defecto
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
            $this->logoPreview = asset('storage/' . $linea->logo);
        }

        if ($linea->fondo) {
            $this->fondoPreview = asset('storage/' . $linea->fondo);
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

        // Eliminar archivos si existen
        if ($linea->logo) {
            Storage::disk('public')->delete($linea->logo);
        }
        if ($linea->fondo) {
            Storage::disk('public')->delete($linea->fondo);
        }
        if ($linea->archivo) {
            Storage::disk('public')->delete($linea->archivo);
        }

        $linea->delete();

        $this->modal_form_eliminar_linea = false;
        $this->success('Línea eliminada correctamente');
    }

        public function updatedName()
    {
        // Limpiar errores previos del nombre
        $this->resetErrorBag('name');

        // Generar código automáticamente basado en el nombre
        if ($this->name) {
            $this->code = $this->generateUniqueCode($this->name);

            // Solo validar en tiempo real si estamos creando una nueva línea
            if (!$this->linea_id) {
                $existingLine = LineCatalogo::where('code', $this->code)->first();

                if ($existingLine) {
                    $this->addError('name', 'Ya existe una línea con este nombre. Por favor, elige un nombre diferente.');
                }
            }
        }
    }

    private function generateUniqueCode($name)
    {
        return Str::slug($name);
    }

        public function guardarLinea()
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'tempLogo' => 'nullable|image|max:20480', // 2MB max
            'tempFondo' => 'nullable|image|max:20480', // 2MB max
            'tempArchivo' => 'nullable|file|max:10240', // 10MB max
        ];

        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'tempLogo.image' => 'El logo debe ser una imagen',
            'tempFondo.image' => 'El fondo debe ser una imagen',
            'tempArchivo.file' => 'El archivo debe ser válido',
        ];

        $this->validate($rules, $messages);

        // Generar código automáticamente basado en el nombre
        $this->code = $this->generateUniqueCode($this->name);

        // Verificar que el código sea único
        $existingLine = LineCatalogo::where('code', $this->code)
            ->when($this->linea_id, function ($query) {
                $query->where('id', '!=', $this->linea_id);
            })
            ->first();

        if ($existingLine) {
            $this->addError('name', 'Ya existe una línea con este nombre. Por favor, elige un nombre diferente.');
            return;
        }

        if ($this->linea_id) {
            $linea = LineCatalogo::findOrFail($this->linea_id);

            // Eliminar archivos anteriores si existen y se suben nuevos
            if ($this->tempLogo && $linea->logo) {
                Storage::disk('public')->delete($linea->logo);
            }
            if ($this->tempFondo && $linea->fondo) {
                Storage::disk('public')->delete($linea->fondo);
            }
            if ($this->tempArchivo && $linea->archivo) {
                Storage::disk('public')->delete($linea->archivo);
            }
        } else {
            $linea = new LineCatalogo();
        }

        $linea->name = $this->name;
        $linea->code = $this->code;
        $linea->isActive = $this->isActive;

        // Procesar logo si se subió uno nuevo
        if ($this->tempLogo) {
            $path = $this->tempLogo->store('lineas/logos', 'public');
            $linea->logo = $path;
        }

        // Procesar fondo si se subió uno nuevo
        if ($this->tempFondo) {
            $path = $this->tempFondo->store('lineas/fondos', 'public');
            $linea->fondo = $path;
        }

        // Procesar archivo si se subió uno nuevo
        if ($this->tempArchivo) {
            $path = $this->tempArchivo->store('lineas/archivos', 'public');
            $linea->archivo = $path;
        }

        $linea->save();

        $this->modal_form_linea = false;
        $this->success($this->linea_id ? 'Línea actualizada correctamente' : 'Línea creada correctamente');
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
            ->when($this->isActiveFilter !== '', function ($query) {
                $query->where('isActive', $this->isActiveFilter);
            })
            ->latest()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.catalogo.line-catalogo-index', [
            'lines' => $lines
        ]);
    }
}

