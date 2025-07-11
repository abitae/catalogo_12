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
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $role_filter = '';
    public $status_filter = '';

    // Modal Form User
    public $modal_form_user = false;
    public $modal_form_eliminar_user = false;
    public $user_id = '';
    public $user = null;

    // Variables para el formulario
    public $name = '';
    public $email = '';
    public $role_name = '';
    public $is_active = true;
    public $notes = '';

    // Manejo de archivos
    public $tempImage = null;
    public $imagePreview = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'role_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'role_name' => 'nullable|exists:roles,name',
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
            'role_name.exists' => 'El rol seleccionado no existe',
            'tempImage.image' => 'El archivo debe ser una imagen',
            'tempImage.mimes' => 'La imagen debe ser JPEG, PNG, JPG, GIF o SVG',
            'tempImage.max' => 'La imagen no debe exceder 20MB',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
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
            'role_filter',
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
            ['key' => 'roles', 'label' => 'Roles', 'sortable' => false],
            ['key' => 'status', 'label' => 'Estado', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Acciones', 'sortable' => false],
        ];
    }

    public function render()
    {
        $query = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->role_filter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('id', $this->role_filter);
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

        return view('livewire.shared.user-index', [
            'users' => $query->paginate($this->perPage),
            'roles' => Role::where('name', '!=', 'Super Admin')->get(),
            'status_options' => [
                '' => 'Todos',
                '1' => 'Activo',
                '0' => 'Inactivo'
            ]
        ]);
    }

    public function nuevoUser()
    {
        $this->resetForm();
        $this->modal_form_user = true;
    }

    public function editarUser($id)
    {
        $this->user_id = $id;
        $this->user = User::with('roles')->find($id);

        if ($this->user) {
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->is_active = $this->user->is_active ?? true;
            $this->notes = $this->user->notes ?? '';
            $this->role_name = $this->user->roles->first()?->name ?? '';

            if ($this->user->profile_image) {
                $this->imagePreview = Storage::url($this->user->profile_image);
            }
        }

        $this->modal_form_user = true;
    }

    public function eliminarUser($id)
    {
        $this->user_id = $id;
        $this->user = User::find($id);

        if ($this->user) {
            // No permitir eliminar el usuario actual
            if ($this->user->id === Auth::id()) {
                $this->error('No puedes eliminar tu propia cuenta');
                return;
            }
            $this->modal_form_eliminar_user = true;
        }
    }

    public function confirmarEliminarUser()
    {
        if ($this->user) {
            // Eliminar imagen de perfil si existe
            if ($this->user->profile_image && Storage::disk('public')->exists($this->user->profile_image)) {
                Storage::disk('public')->delete($this->user->profile_image);
            }

            $this->user->delete();
            $this->success('Usuario eliminado correctamente');
        } else {
            $this->error('No se encontró el usuario a eliminar');
        }

        $this->modal_form_eliminar_user = false;
        $this->reset(['user_id', 'user']);
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
            'user_id',
            'user',
            'name',
            'email',
            'role_name',
            'is_active',
            'notes',
            'tempImage',
            'imagePreview'
        ]);
        $this->resetValidation();
    }

    public function guardarUser()
    {
        $data = $this->validate();

        // Prevenir asignación del rol Super Admin
        if ($this->role_name === 'Super Admin') {
            $this->error('No se puede asignar el rol Super Admin desde esta interfaz');
            return;
        }

        // Manejar imagen de perfil
        if ($this->tempImage) {
            // Eliminar imagen anterior si existe
            if ($this->user_id && $this->user && $this->user->profile_image) {
                Storage::disk('public')->delete($this->user->profile_image);
            }

            $imagePath = $this->tempImage->store('users/profile', 'public');
            $data['profile_image'] = $imagePath;
        }

        // Remover campos temporales
        unset($data['tempImage']);

        try {
            if ($this->user_id) {
                $user = User::find($this->user_id);
                $user->update($data);

                // Asignar rol único
                if ($this->role_name) {
                    $user->syncRoles([$this->role_name]);
                } else {
                    $user->syncRoles([]);
                }

                // Log de auditoría para actualización de usuario
                Log::info('Auditoría: Usuario actualizado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_user',
                    'target_user_id' => $this->user_id,
                    'target_user_name' => $data['name'],
                    'target_user_email' => $data['email'],
                    'role_assigned' => $this->role_name,
                    'timestamp' => now()
                ]);

                $this->success('Usuario actualizado correctamente');
            } else {
                $data['password'] = Hash::make('12345678');
                $user = User::create($data);

                // Asignar rol único
                if ($this->role_name) {
                    $user->assignRole($this->role_name);
                }

                // Log de auditoría para creación de usuario
                Log::info('Auditoría: Usuario creado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_user',
                    'new_user_id' => $user->id,
                    'new_user_name' => $data['name'],
                    'new_user_email' => $data['email'],
                    'role_assigned' => $this->role_name,
                    'timestamp' => now()
                ]);

                $this->success('Usuario creado correctamente');
            }

            $this->modal_form_user = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el usuario: ' . $e->getMessage());
        }
    }

    public function toggleUserStatus($id)
    {
        $user = User::find($id);

        if ($user) {
            // No permitir desactivar el usuario actual
            if ($user->id === Auth::id()) {
                $this->error('No puedes desactivar tu propia cuenta');
                return;
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'activado' : 'desactivado';
            $this->success("Usuario {$status} correctamente");

            // Log de auditoría
            Log::info('Auditoría: Estado de usuario cambiado', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'toggle_user_status',
                'target_user_id' => $user->id,
                'target_user_name' => $user->name,
                'new_status' => $user->is_active,
                'timestamp' => now()
            ]);
        }
    }
}
