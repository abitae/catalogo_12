<?php

namespace Database\Seeders;

use App\Models\Pc\AcuerdoMarco;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcuerdoMarcoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear acuerdos marco de ejemplo
        $acuerdos = [
            [
                'code' => 'AM-2024-001',
                'name' => 'Acuerdo Marco de Tecnología 2024',
                'isActive' => true,
            ],
            [
                'code' => 'AM-2024-002',
                'name' => 'Acuerdo Marco de Servicios de Consultoría',
                'isActive' => true,
            ],
            [
                'code' => 'AM-2024-003',
                'name' => 'Acuerdo Marco de Suministros de Oficina',
                'isActive' => true,
            ],
            [
                'code' => 'AM-2023-001',
                'name' => 'Acuerdo Marco de Mantenimiento 2023',
                'isActive' => false,
            ],
            [
                'code' => 'AM-2024-004',
                'name' => 'Acuerdo Marco de Servicios de Limpieza',
                'isActive' => true,
            ],
        ];

        foreach ($acuerdos as $acuerdo) {
            AcuerdoMarco::create($acuerdo);
        }

        // Crear acuerdos marco adicionales usando el factory
        AcuerdoMarco::factory(10)->create();
    }
}
