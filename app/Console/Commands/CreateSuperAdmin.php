<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super-admin {--name=Super Admin} {--email=admin@example.com} {--password=12345678}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuario Super Admin con todos los permisos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creando usuario Super Admin...');

        // Verificar si ya existe un Super Admin
        $existingSuperAdmin = User::whereHas('roles', function ($query) {
            $query->where('name', 'Super Admin');
        })->first();

        if ($existingSuperAdmin) {
            $this->error('Ya existe un usuario Super Admin: ' . $existingSuperAdmin->email);
            return 1;
        }

        // Crear o verificar el rol Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], [
            'description' => 'Usuario con todos los permisos del sistema'
        ]);

        // Asignar todos los permisos al rol Super Admin
        $permissions = Permission::all();
        $superAdminRole->syncPermissions($permissions);

        // Crear el usuario Super Admin
        $user = User::create([
            'name' => $this->option('name'),
            'email' => $this->option('email'),
            'password' => Hash::make($this->option('password')),
            'is_active' => true,
            'notes' => 'Usuario Super Admin creado automáticamente'
        ]);

        // Asignar el rol Super Admin
        $user->assignRole('Super Admin');

        $this->info('✅ Usuario Super Admin creado exitosamente!');
        $this->info('📧 Email: ' . $user->email);
        $this->info('🔑 Contraseña: ' . $this->option('password'));
        $this->info('👤 Nombre: ' . $user->name);
        $this->info('🔐 Rol: Super Admin');
        $this->info('📋 Permisos asignados: ' . $permissions->count());

        $this->warn('⚠️  IMPORTANTE: Cambia la contraseña después del primer inicio de sesión');

        return 0;
    }
}
