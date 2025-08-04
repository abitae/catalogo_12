<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facturacion\DespatchDetail;
use App\Models\Facturacion\Despatch;

class DespatchDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan guías de remisión
        $despatches = Despatch::all();

        if ($despatches->isEmpty()) {
            $this->command->warn('No se pueden crear detalles de guías de remisión sin guías. Ejecute primero DespatchSeeder.');
            return;
        }

        // Crear entre 2 y 8 detalles por cada guía de remisión
        foreach ($despatches as $despatch) {
            $numDetails = rand(2, 8);
            
            DespatchDetail::factory($numDetails)->create([
                'despatch_id' => $despatch->id,
            ]);
        }

        $totalDetails = DespatchDetail::count();
        $this->command->info("Se crearon {$totalDetails} detalles de guías de remisión exitosamente.");
    }
}
