<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FacturacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando seeder de Facturación...');

        // Ejecutar seeders en orden de dependencias
        $this->call([
            AddressSeeder::class,
            CompanySeeder::class,
            SucursalSeeder::class,
            ClientSeeder::class,
            InvoiceSeeder::class,
            InvoiceDetailSeeder::class,
            NoteSeeder::class,
            NoteDetailSeeder::class,
            DespatchSeeder::class,
            DespatchDetailSeeder::class,
        ]);

        $this->command->info('Seeder de Facturación completado exitosamente.');
    }
}
