<?php

namespace Database\Seeders;

use App\Models\Shared\Customer;
use App\Models\Shared\TipoCustomer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tipos de cliente
        TipoCustomer::factory(5)->create();

        // Crear clientes
        Customer::factory(100)->create();
    }
}
