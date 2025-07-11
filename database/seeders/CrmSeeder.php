<?php

namespace Database\Seeders;

use App\Models\Crm\TipoNegocioCrm;
use App\Models\Crm\MarcaCrm;
use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\ContactCrm;
use App\Models\Crm\ActivityCrm;
use Illuminate\Database\Seeder;

class CrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear datos de prueba para el CRM
        TipoNegocioCrm::factory(10)->create();
        MarcaCrm::factory(10)->create();
        OpportunityCrm::factory(100)->create();
        ContactCrm::factory(100)->create();
        ActivityCrm::factory(200)->create();
    }
}
