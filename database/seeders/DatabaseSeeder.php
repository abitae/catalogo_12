<?php

namespace Database\Seeders;

use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\User;
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
    // Crear datos de prueba para el catálogo
    BrandCatalogo::factory(20)->create();
    CategoryCatalogo::factory(20)->create();
    LineCatalogo::factory(20)->create();
    ProductoCatalogo::factory(40)->create();

    }
}
