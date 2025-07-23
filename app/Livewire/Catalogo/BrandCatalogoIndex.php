<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\BrandCatalogo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class BrandCatalogoIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    // Propiedades para el modal
    public $modal_form_marca = false;
    public $modal_form_eliminar_marca = false;
    public $marca_id;

    // Propiedades para el formulario
    public $name;
    public $isActive = true;

    // Propiedades para archivos temporales
    public $tempLogo;
    public $tempArchivo;
    public $logoPreview;

    // Propiedades para búsqueda y ordenamiento
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $isActiveFilter = '';

    protected $rules = [
        'tempLogo' => 'nullable|image|max:20480', // 2MB max
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

    public function removeLogo()
    {
        $this->tempLogo = null;
        $this->logoPreview = null;
    }

    public function nuevoMarca()
    {
        $this->reset(['name', 'isActive', 'tempLogo', 'tempArchivo', 'logoPreview', 'marca_id']);
        $this->isActive = true; // Asegurar que esté activo por defecto
        $this->modal_form_marca = true;
    }

    public function editarMarca($id)
    {
        $marca = BrandCatalogo::findOrFail($id);
        $this->marca_id = $marca->id;
        $this->name = $marca->name;
        $this->isActive = $marca->isActive;

        if ($marca->logo) {
            $this->logoPreview = asset('storage/' . $marca->logo);
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

            // Eliminar archivos si existen
            if ($marca->logo) {
                Storage::disk('public')->delete($marca->logo);
            }
            if ($marca->archivo) {
                Storage::disk('public')->delete($marca->archivo);
            }

            $marca->delete();

            $this->modal_form_eliminar_marca = false;
            $this->success('Marca eliminada correctamente');

            Log::info('Auditoría: Marca eliminada', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'delete_marca',
                'marca_id' => $this->marca_id,
                'marca_name' => $marca->name,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            $this->error('Error al eliminar la marca: ' . $e->getMessage());
            Log::error('Error en eliminación de marca', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'marca_id' => $this->marca_id ?? null
            ]);
        }
    }

    public function guardarMarca()
    {
        try {
            $rules = [
                'name' => 'required|min:3|max:255|unique:brand_catalogos,name,' . ($this->marca_id ?? ''),
                'tempLogo' => 'nullable|image|max:20480', // 2MB max
                'tempArchivo' => 'nullable|file|max:10240', // 10MB max
            ];

            $messages = [
                'name.required' => 'El nombre es requerido',
                'name.min' => 'El nombre debe tener al menos 3 caracteres',
                'name.max' => 'El nombre debe tener menos de 255 caracteres',
                'name.unique' => 'Ya existe una marca con este nombre',
                'tempLogo.image' => 'El logo debe ser una imagen',
                'tempArchivo.file' => 'El archivo debe ser válido',
            ];

            $this->validate($rules, $messages);

            if ($this->marca_id) {
                $marca = BrandCatalogo::findOrFail($this->marca_id);

                // Eliminar archivos anteriores si existen y se suben nuevos
                if ($this->tempLogo && $marca->logo) {
                    Storage::disk('public')->delete($marca->logo);
                }
                if ($this->tempArchivo && $marca->archivo) {
                    Storage::disk('public')->delete($marca->archivo);
                }

                $marca->name = $this->name;
                $marca->isActive = $this->isActive;

                // Procesar logo si se subió uno nuevo
                if ($this->tempLogo) {
                    $path = $this->tempLogo->store('marcas/logos', 'public');
                    $marca->logo = $path;
                }

                // Procesar archivo si se subió uno nuevo
                if ($this->tempArchivo) {
                    $path = $this->tempArchivo->store('marcas/archivos', 'public');
                    $marca->archivo = $path;
                }

                $marca->save();

                Log::info('Auditoría: Marca actualizada', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_marca',
                    'marca_id' => $this->marca_id,
                    'marca_name' => $this->name,
                    'isActive' => $this->isActive,
                    'timestamp' => now()
                ]);

                $this->success('Marca actualizada correctamente');
            } else {
                $marca = new BrandCatalogo();
                $marca->name = $this->name;
                $marca->isActive = $this->isActive;

                // Procesar logo si se subió uno nuevo
                if ($this->tempLogo) {
                    $path = $this->tempLogo->store('marcas/logos', 'public');
                    $marca->logo = $path;
                }

                // Procesar archivo si se subió uno nuevo
                if ($this->tempArchivo) {
                    $path = $this->tempArchivo->store('marcas/archivos', 'public');
                    $marca->archivo = $path;
                }

                $marca->save();

                Log::info('Auditoría: Marca creada', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_marca',
                    'marca_id' => $marca->id,
                    'marca_name' => $this->name,
                    'isActive' => $this->isActive,
                    'timestamp' => now()
                ]);

                $this->success('Marca creada correctamente');
            }

            $this->modal_form_marca = false;
        } catch (\Exception $e) {
            $this->error('Error al guardar la marca: ' . $e->getMessage());
            Log::error('Error en guardado de marca', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'marca_id' => $this->marca_id ?? null
            ]);
        }
    }

    public function render()
    {
        $marcas = BrandCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->isActiveFilter !== '', function ($query) {
                $query->where('isActive', $this->isActiveFilter);
            })
            ->latest()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.catalogo.brand-catalogo-index', [
            'marcas' => $marcas
        ]);
    }
}
