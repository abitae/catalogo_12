<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal Form Role
    public $modal_form_role = false;
    public $modal_form_eliminar_role = false;
    public $role_id = '';
    public $role = null;

    // Variables para el formulario
    public $name = '';
    public $description = '';
    public $permissions = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role_id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre del rol es requerido',
            'name.unique' => 'Este nombre de rol ya existe',
            'permissions.array' => 'Los permisos deben ser un array',
        ];
    }

    public function updatingSearch()
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
            ['key' => 'description', 'label' => 'Descripción', 'sortable' => false],
            ['key' => 'permissions_count', 'label' => 'Permisos', 'sortable' => false],
            ['key' => 'users_count', 'label' => 'Usuarios', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Acciones', 'sortable' => false],
        ];
    }

    public function render()
    {
        $query = Role::with('permissions')
            ->withCount(['users', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            // Excluir el rol Super Admin
            ->where('name', '!=', 'Super Admin')
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.shared.role-index', [
            'roles' => $query->paginate($this->perPage),
            'permissions' => Permission::all(),
        ]);
    }

    public function nuevoRole()
    {
        $this->resetForm();
        $this->modal_form_role = true;
    }

    public function editarRole($id)
    {
        $this->role_id = $id;
        $this->role = Role::with('permissions')->find($id);

        if ($this->role) {
            $this->name = $this->role->name;
            $this->description = $this->role->description ?? '';
            $this->permissions = $this->role->permissions->pluck('id')->toArray();
        }

        $this->modal_form_role = true;
    }

    public function eliminarRole($id)
    {
        $this->role_id = $id;
        $this->role = Role::find($id);

        if ($this->role) {
            // No permitir eliminar roles del sistema
            if (in_array($this->role->name, ['Super Admin', 'Administrador'])) {
                $this->error('No se puede eliminar este rol del sistema');
                return;
            }
            $this->modal_form_eliminar_role = true;
        }
    }

    public function confirmarEliminarRole()
    {
        if ($this->role) {
            $this->role->delete();
            $this->success('Rol eliminado correctamente');
        } else {
            $this->error('No se encontró el rol a eliminar');
        }

        $this->modal_form_eliminar_role = false;
        $this->reset(['role_id', 'role']);
    }

    private function resetForm()
    {
        $this->reset([
            'role_id',
            'role',
            'name',
            'description',
            'permissions'
        ]);
        $this->resetValidation();
    }

    public function guardarRole()
    {
        $data = $this->validate();

        // Prevenir creación de rol Super Admin
        if ($data['name'] === 'Super Admin') {
            $this->error('No se puede crear un rol con el nombre Super Admin');
            return;
        }

        // Remover array de permisos
        unset($data['permissions']);

        try {
            if ($this->role_id) {
                $role = Role::find($this->role_id);
                $role->update($data);

                // Sincronizar permisos
                $role->syncPermissions($this->permissions);

                // Log de auditoría para actualización de rol
                Log::info('Auditoría: Rol actualizado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_role',
                    'role_id' => $this->role_id,
                    'role_name' => $data['name'],
                    'permissions_assigned' => $this->permissions,
                    'timestamp' => now()
                ]);

                $this->success('Rol actualizado correctamente');
            } else {
                $role = Role::create($data);

                // Asignar permisos
                $role->givePermissionTo($this->permissions);

                // Log de auditoría para creación de rol
                Log::info('Auditoría: Rol creado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_role',
                    'role_id' => $role->id,
                    'role_name' => $data['name'],
                    'permissions_assigned' => $this->permissions,
                    'timestamp' => now()
                ]);

                $this->success('Rol creado correctamente');
            }

            $this->modal_form_role = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el rol: ' . $e->getMessage());
        }
    }
}
