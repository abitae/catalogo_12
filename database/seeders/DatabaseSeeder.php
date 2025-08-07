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
        // Agregar el seeder de ubigeo
        $this->call(SqlFileSeeder::class,);

        // Agregar el seeder de roles y permisos
        $this->call(RolesAndPermissionsSeeder::class);

        // Agregar el seeder de usuario administrador
        $this->call(UserSeeder::class);

       /*  // Agregar el seeder de clientes
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

        // Agregar el seeder de direcciones de facturación
        $this->call(AddressSeeder::class);
        // Agregar el seeder de compañías de facturación
        $this->call(CompanySeeder::class);
        // Agregar el seeder de sucursales de facturación
        $this->call(SucursalSeeder::class);
        // Agregar el seeder de clientes
        $this->call(ClientSeeder::class);
        // Agregar el seeder de facturas
        $this->call(InvoiceSeeder::class);
        // Agregar el seeder de detalles de facturas
        $this->call(InvoiceDetailSeeder::class);

        // Agregar el seeder de notas (crédito y débito)
        $this->call(NoteSeeder::class);
        // Agregar el seeder de detalles de notas
        $this->call(NoteDetailSeeder::class);

        // Agregar el seeder de guías de remisión
        $this->call(DespatchSeeder::class);
        // Agregar el seeder de detalles de guías de remisión
        $this->call(DespatchDetailSeeder::class); */
    }
}
