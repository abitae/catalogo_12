<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckSunatUnidades extends Command
{
    protected $signature = 'sunat:check-unidades';
    protected $description = 'Verificar que la tabla sunat_03 existe y tiene datos';

    public function handle()
    {
        $this->info('Verificando tabla sunat_03...');

        // Verificar si la tabla existe
        if (!Schema::hasTable('sunat_03')) {
            $this->error('La tabla sunat_03 no existe.');
            $this->info('Ejecutando SQL para crear la tabla...');

            $sqlPath = database_path('data/03_unidad_medida.sql');
            if (file_exists($sqlPath)) {
                $sql = file_get_contents($sqlPath);
                DB::unprepared($sql);
                $this->info('Tabla sunat_03 creada exitosamente.');
            } else {
                $this->error('No se encontró el archivo SQL: ' . $sqlPath);
                return 1;
            }
        } else {
            $this->info('La tabla sunat_03 existe.');
        }

        // Verificar si tiene datos
        $count = DB::table('sunat_03')->count();
        $this->info("La tabla sunat_03 tiene {$count} registros.");

        if ($count == 0) {
            $this->warn('La tabla está vacía. Cargando datos...');
            $sqlPath = database_path('data/03_unidad_medida.sql');
            if (file_exists($sqlPath)) {
                $sql = file_get_contents($sqlPath);
                DB::unprepared($sql);
                $this->info('Datos cargados exitosamente.');
            }
        }

        // Mostrar algunas unidades como ejemplo
        $unidades = DB::table('sunat_03')->take(10)->get();
        $this->info('Primeras 10 unidades de medida:');
        foreach ($unidades as $unidad) {
            $this->line("  {$unidad->codigo} - {$unidad->descripcion}");
        }

        $this->info('Verificación completada.');
        return 0;
    }
}
