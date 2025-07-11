<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Usuarios
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',

            // Catálogo
            'view catalog',
            'create catalog',
            'edit catalog',
            'delete catalog',

            // CRM
            'view crm',
            'create crm',
            'edit crm',
            'delete crm',

            // Almacén
            'view warehouse',
            'create warehouse',
            'edit warehouse',
            'delete warehouse',

            // Reportes
            'view reports',
            'export reports',

            // Configuración
            'view settings',
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $roles = [
            'Super Admin' => $permissions,
            'Administrador' => [
                'view users', 'create users', 'edit users',
                'view catalog', 'create catalog', 'edit catalog', 'delete catalog',
                'view crm', 'create crm', 'edit crm', 'delete crm',
                'view warehouse', 'create warehouse', 'edit warehouse', 'delete warehouse',
                'view reports', 'export reports',
                'view settings', 'edit settings',
            ],
            'Vendedor' => [
                'view catalog',
                'view crm', 'create crm', 'edit crm',
                'view reports',
            ],
            'Almacén' => [
                'view warehouse', 'create warehouse', 'edit warehouse',
                'view catalog',
                'view reports',
            ],
            'Cliente' => [
                'view catalog',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
