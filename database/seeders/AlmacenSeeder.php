<?php

namespace Database\Seeders;

use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\TransferenciaAlmacen;
use App\Models\Almacen\MovimientoAlmacen;
use Illuminate\Database\Seeder;

class AlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear datos de prueba para almacén
        WarehouseAlmacen::factory(4)->create();
        ProductoAlmacen::factory(200)->create();
        TransferenciaAlmacen::factory(100)->create();
        MovimientoAlmacen::factory(200)->create();
    }
}
