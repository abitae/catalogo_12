<?php

namespace Database\Seeders;

use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\TransferenciaAlmacen;
use App\Models\Almacen\MovimientoAlmacen;
use App\Models\User;
use App\Models\Crm\MarcaCrm;
use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\ActivityCrm;
use App\Models\Crm\ContactCrm;
use App\Models\Crm\TipoNegocioCrm;
use App\Models\Shared\Customer;
use App\Models\Shared\TipoCustomer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Abel Arana',
            'email' => 'abel.arana@hotmail.com',
            'password' => Hash::make('lobomalo123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Crear datos clientes
        TipoCustomer::factory(5)->create();
        Customer::factory(100)->create();
        // Crear datos de prueba para el catálogo
        BrandCatalogo::factory(20)->create();
        CategoryCatalogo::factory(20)->create();
        LineCatalogo::factory(20)->create();
        ProductoCatalogo::factory(40)->create();

        WarehouseAlmacen::factory(4)->create();
        ProductoAlmacen::factory(200)->create();
        TransferenciaAlmacen::factory(100)->create();
        MovimientoAlmacen::factory(200)->create();

        // Agregar el seeder de cotización
        $this->call(\Database\Seeders\CotizacionSeeder::class);

        // Crear datos de prueba para el CRM
        TipoNegocioCrm::factory(10)->create();
        MarcaCrm::factory(10)->create();
        OpportunityCrm::factory(100)->create();
        ContactCrm::factory(100)->create();
        ActivityCrm::factory(200)->create();


    }
}
