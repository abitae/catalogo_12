<?php

namespace App\Livewire\Shared;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mary\Traits\Toast;

class ColaboradorIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $status_filter = '';

    // Modal Form Colaborador
    public $modal_form_colaborador = false;
    public $modal_form_eliminar_colaborador = false;
    public $colaborador_id = '';
    public $colaborador = null;

    // Variables para el formulario
    public $name = '';
    public $email = '';
    public $is_active = true;
    public $notes = '';

    // Manejo de archivos
    public $tempImage = null;
    public $imagePreview = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->colaborador_id,
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'tempImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe tener un formato válido',
            'email.unique' => 'Este email ya está registrado',
            'tempImage.image' => 'El archivo debe ser una imagen',
            'tempImage.mimes' => 'La imagen debe ser JPEG, PNG, JPG, GIF o SVG',
            'tempImage.max' => 'La imagen no debe exceder 20MB',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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
            'status_filter',
            'sortField',
            'sortDirection',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados');
    }

    public function getHeadersProperty()
    {
        return [
            ['key' => 'name', 'label' => 'Nombre', 'sortable' => true],
            ['key' => 'email', 'label' => 'Email', 'sortable' => true],
            ['key' => 'status', 'label' => 'Estado', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Acciones', 'sortable' => false],
        ];
    }

    public function render()
    {
        $query = User::when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status_filter !== '', function ($query) {
                $query->where('is_active', $this->status_filter);
            })
            // Excluir usuarios con rol Super Admin
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            })
            ->latest()
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.shared.colaborador-index', [
            'colaboradores' => $query->paginate($this->perPage),
            'status_options' => [
                '' => 'Todos',
                '1' => 'Activo',
                '0' => 'Inactivo'
            ]
        ]);
    }

    public function nuevoColaborador()
    {
        $this->resetForm();
        $this->modal_form_colaborador = true;
    }

    public function editarColaborador($id)
    {
        $this->colaborador_id = $id;
        $this->colaborador = User::find($id);

        if ($this->colaborador) {
            $this->name = $this->colaborador->name;
            $this->email = $this->colaborador->email;
            $this->is_active = $this->colaborador->is_active ?? true;
            $this->notes = $this->colaborador->notes ?? '';

            if ($this->colaborador->profile_image) {
                $this->imagePreview = Storage::url($this->colaborador->profile_image);
            }
        }

        $this->modal_form_colaborador = true;
    }

    public function eliminarColaborador($id)
    {
        $this->colaborador_id = $id;
        $this->colaborador = User::find($id);

        if ($this->colaborador) {
            // No permitir eliminar el usuario actual
            if ($this->colaborador->id === Auth::id()) {
                $this->error('No puedes eliminar tu propia cuenta');
                return;
            }
            $this->modal_form_eliminar_colaborador = true;
        }
    }

    public function confirmarEliminarColaborador()
    {
        if ($this->colaborador) {
            // Eliminar imagen de perfil si existe
            if ($this->colaborador->profile_image && Storage::disk('public')->exists($this->colaborador->profile_image)) {
                Storage::disk('public')->delete($this->colaborador->profile_image);
            }

            $this->colaborador->delete();
            $this->success('Colaborador eliminado correctamente');
        } else {
            $this->error('No se encontró el colaborador a eliminar');
        }

        $this->modal_form_eliminar_colaborador = false;
        $this->reset(['colaborador_id', 'colaborador']);
    }

    public function updatedTempImage()
    {
        if ($this->tempImage) {
            $this->imagePreview = $this->tempImage->temporaryUrl();
        }
    }

    public function removeImage()
    {
        $this->tempImage = null;
        $this->imagePreview = null;
    }

    private function resetForm()
    {
        $this->reset([
            'colaborador_id',
            'colaborador',
            'name',
            'email',
            'is_active',
            'notes',
            'tempImage',
            'imagePreview'
        ]);
        $this->resetValidation();
    }

    public function guardarColaborador()
    {
        $data = $this->validate();

        // Manejar imagen de perfil
        if ($this->tempImage) {
            // Eliminar imagen anterior si existe
            if ($this->colaborador_id && $this->colaborador && $this->colaborador->profile_image) {
                Storage::disk('public')->delete($this->colaborador->profile_image);
            }

            $imagePath = $this->tempImage->store('users/profile', 'public');
            $data['profile_image'] = $imagePath;
        }

        // Remover campos temporales
        unset($data['tempImage']);

        try {
            if ($this->colaborador_id) {
                $colaborador = User::find($this->colaborador_id);
                $colaborador->update($data);

                // Log de auditoría para actualización de colaborador
                Log::info('Auditoría: Colaborador actualizado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_colaborador',
                    'target_colaborador_id' => $this->colaborador_id,
                    'target_colaborador_name' => $data['name'],
                    'target_colaborador_email' => $data['email'],
                    'timestamp' => now()
                ]);

                $this->success('Colaborador actualizado correctamente');
            } else {
                $data['password'] = Hash::make('12345678');
                $colaborador = User::create($data);

                // Log de auditoría para creación de colaborador
                Log::info('Auditoría: Colaborador creado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_colaborador',
                    'new_colaborador_id' => $colaborador->id,
                    'new_colaborador_name' => $data['name'],
                    'new_colaborador_email' => $data['email'],
                    'timestamp' => now()
                ]);

                $this->success('Colaborador creado correctamente');
            }

            $this->modal_form_colaborador = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el colaborador: ' . $e->getMessage());
        }
    }

    public function toggleColaboradorStatus($id)
    {
        $colaborador = User::find($id);

        if ($colaborador) {
            // No permitir desactivar el usuario actual
            if ($colaborador->id === Auth::id()) {
                $this->error('No puedes desactivar tu propia cuenta');
                return;
            }

            $colaborador->update(['is_active' => !$colaborador->is_active]);

            $status = $colaborador->is_active ? 'activado' : 'desactivado';
            $this->success("Colaborador {$status} correctamente");

            // Log de auditoría
            Log::info('Auditoría: Estado de colaborador cambiado', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'toggle_colaborador_status',
                'target_colaborador_id' => $colaborador->id,
                'target_colaborador_name' => $colaborador->name,
                'new_status' => $colaborador->is_active,
                'timestamp' => now()
            ]);
        }
    }
}
