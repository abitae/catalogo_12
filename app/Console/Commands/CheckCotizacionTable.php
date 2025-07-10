<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckCotizacionTable extends Command
{
    protected $signature = 'check:cotizacion-table';
    protected $description = 'Verificar la estructura de la tabla cotizacion_catalogos';

    public function handle()
    {
        $this->info('Verificando estructura de la tabla cotizacion_catalogos...');

        if (!Schema::hasTable('cotizacion_catalogos')) {
            $this->error('La tabla cotizacion_catalogos no existe');
            return 1;
        }

        $columns = Schema::getColumnListing('cotizacion_catalogos');
        $this->info('Columnas encontradas:');
        foreach ($columns as $column) {
            $this->line("- $column");
        }

        // Verificar si el campo igv existe
        if (in_array('igv', $columns)) {
            $this->info('✅ El campo igv existe en la tabla');
        } else {
            $this->error('❌ El campo igv NO existe en la tabla');
        }

        return 0;
    }
}
