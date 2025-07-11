<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PermissionIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal Form Permission
    public $modal_form_permission = false;
    public $modal_form_eliminar_permission = false;
    public $permission_id = '';
    public $permission = null;

    // Variables para el formulario
    public $name = '';
    public $description = '';
    public $guard_name = 'web';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $this->permission_id,
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre del permiso es requerido',
            'name.unique' => 'Este nombre de permiso ya existe',
            'guard_name.required' => 'El guard es requerido',
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
            ['key' => 'guard_name', 'label' => 'Guard', 'sortable' => false],
            ['key' => 'roles_count', 'label' => 'Roles Asignados', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Acciones', 'sortable' => false],
        ];
    }

    public function render()
    {
        $query = Permission::with('roles')
            ->withCount('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.shared.permission-index', [
            'permissions' => $query->paginate($this->perPage),
            'roles' => Role::all(),
        ]);
    }

    public function nuevoPermission()
    {
        $this->resetForm();
        $this->modal_form_permission = true;
    }

    public function editarPermission($id)
    {
        $this->permission_id = $id;
        $this->permission = Permission::find($id);

        if ($this->permission) {
            $this->name = $this->permission->name;
            $this->description = $this->permission->description ?? '';
            $this->guard_name = $this->permission->guard_name;
        }

        $this->modal_form_permission = true;
    }

    public function eliminarPermission($id)
    {
        $this->permission_id = $id;
        $this->permission = Permission::find($id);

        if ($this->permission) {
            // Verificar si el permiso está en uso
            if ($this->permission->roles()->count() > 0) {
                $this->error('No se puede eliminar este permiso porque está asignado a roles');
                return;
            }
            $this->modal_form_eliminar_permission = true;
        }
    }

    public function confirmarEliminarPermission()
    {
        if ($this->permission) {
            $this->permission->delete();
            $this->success('Permiso eliminado correctamente');
        } else {
            $this->error('No se encontró el permiso a eliminar');
        }

        $this->modal_form_eliminar_permission = false;
        $this->reset(['permission_id', 'permission']);
    }

    private function resetForm()
    {
        $this->reset([
            'permission_id',
            'permission',
            'name',
            'description',
            'guard_name'
        ]);
        $this->resetValidation();
    }

    public function guardarPermission()
    {
        $data = $this->validate();

        try {
            if ($this->permission_id) {
                $permission = Permission::find($this->permission_id);
                $permission->update($data);

                // Log de auditoría para actualización de permiso
                Log::info('Auditoría: Permiso actualizado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_permission',
                    'permission_id' => $this->permission_id,
                    'permission_name' => $data['name'],
                    'timestamp' => now()
                ]);

                $this->success('Permiso actualizado correctamente');
            } else {
                $permission = Permission::create($data);

                // Log de auditoría para creación de permiso
                Log::info('Auditoría: Permiso creado', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_permission',
                    'permission_id' => $permission->id,
                    'permission_name' => $data['name'],
                    'timestamp' => now()
                ]);

                $this->success('Permiso creado correctamente');
            }

            $this->modal_form_permission = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el permiso: ' . $e->getMessage());
        }
    }
}
