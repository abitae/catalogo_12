<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Agregar el seeder de roles y permisos
        $this->call(RolesAndPermissionsSeeder::class);

        // Agregar el seeder de usuario administrador
        $this->call(UserSeeder::class);

        // Agregar el seeder de clientes
        $this->call(CustomerSeeder::class);

        // Agregar el seeder de catálogo
        $this->call(CatalogoSeeder::class);

        // Agregar el seeder de almacén
        $this->call(AlmacenSeeder::class);

        // Agregar el seeder de cotización
        $this->call(CotizacionSeeder::class);

        // Agregar el seeder de CRM
        $this->call(CrmSeeder::class);

        // Agregar el seeder de acuerdos marco
        $this->call(AcuerdoMarcoSeeder::class);
    }
}
