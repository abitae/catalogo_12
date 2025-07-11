<?php

namespace Database\Seeders;

use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear datos de prueba para el catálogo
        BrandCatalogo::factory(20)->create();
        CategoryCatalogo::factory(20)->create();
        LineCatalogo::factory(20)->create();
        ProductoCatalogo::factory(40)->create();
    }
}
