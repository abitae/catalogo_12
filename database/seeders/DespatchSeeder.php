<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facturacion\Despatch;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;

class DespatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan las relaciones necesarias
        $companies = Company::all();
        $sucursales = Sucursal::all();
        $clients = Client::all();

        if ($companies->isEmpty() || $sucursales->isEmpty() || $clients->isEmpty()) {
            $this->command->warn('No se pueden crear guías de remisión sin companies, sucursales o clients. Ejecute primero esos seeders.');
            return;
        }

        // Crear 30 guías de remisión
        Despatch::factory(30)->create([
            'company_id' => fn() => $companies->random()->id,
            'sucursal_id' => fn() => $sucursales->random()->id,
            'client_id' => fn() => $clients->random()->id,
        ]);

        $this->command->info('Se crearon 30 guías de remisión exitosamente.');
    }
}
