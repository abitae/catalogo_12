<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pc\ProductoAcuerdoMarco;

class ProductoAcuerdoMarcoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 50 productos de ejemplo
        ProductoAcuerdoMarco::factory(50)->create();

        $this->command->info('Productos de Acuerdo Marco creados exitosamente.');
    }
}
