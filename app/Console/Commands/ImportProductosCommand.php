<?php

namespace App\Console\Commands;

use App\Imports\ProductCatalogoImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportProductosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:import
                            {file : Ruta del archivo Excel a importar}
                            {--update : Actualizar productos existentes en lugar de omitirlos}
                            {--force : Forzar la importación sin confirmación}
                            {--skip-duplicates : Omitir productos duplicados (por defecto)}
                            {--process-duplicates : Procesar productos duplicados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar productos desde un archivo Excel con optimizaciones avanzadas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $updateExisting = $this->option('update');
        $skipDuplicates = !$this->option('process-duplicates');
        $force = $this->option('force');

        // Validar que el archivo existe
        if (!file_exists($filePath)) {
            $this->error("❌ El archivo no existe: {$filePath}");
            return 1;
        }

        // Validar extensión del archivo
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['xlsx', 'xls'])) {
            $this->error("❌ El archivo debe ser un Excel (.xlsx o .xls)");
            return 1;
        }

        // Mostrar información de configuración
        $this->info("🚀 Iniciando importación optimizada de productos");
        $this->line("📁 Archivo: {$filePath}");
        $this->line("📊 Tamaño: " . $this->formatBytes(filesize($filePath)));
        $this->line("⚙️ Configuración:");
        $this->line("   - Actualizar existentes: " . ($updateExisting ? '✅ Sí' : '❌ No'));
        $this->line("   - Omitir duplicados: " . ($skipDuplicates ? '✅ Sí' : '❌ No'));

        // Confirmar si no se usa --force
        if (!$force) {
            if (!$this->confirm('¿Desea continuar con la importación?')) {
                $this->info("❌ Importación cancelada por el usuario");
                return 0;
            }
        }

        try {
            // Crear barra de progreso
            $this->info("📈 Procesando archivo...");

            // Configurar importación
            $import = new ProductCatalogoImport($updateExisting, $skipDuplicates);

            // Importar con chunk reading para archivos grandes
            Excel::import($import, $filePath);

            // Obtener estadísticas
            $stats = $import->getImportStats();

            // Mostrar resultados
            $this->displayResults($stats, $filePath);

            // Log de auditoría
            $this->logAudit($stats, $filePath, $updateExisting, $skipDuplicates);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error durante la importación: " . $e->getMessage());
            Log::error('Error en comando de importación de productos', [
                'file' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Mostrar resultados de la importación
     */
    private function displayResults(array $stats, string $filePath): void
    {
        $this->newLine();
        $this->info("📊 RESULTADOS DE LA IMPORTACIÓN");
        $this->line("=" . str_repeat("=", 50));

        $this->line("📦 Total de filas procesadas: " . $stats['total_rows']);
        $this->line("✅ Productos importados: " . $stats['imported']);

        if (isset($stats['updated']) && $stats['updated'] > 0) {
            $this->line("🔄 Productos actualizados: " . $stats['updated']);
        }

        $this->line("❌ Filas omitidas: " . $stats['skipped']);
        $this->line("📈 Tasa de éxito: " . $stats['success_rate'] . "%");

        if (!empty($stats['errors'])) {
            $this->newLine();
            $this->warn("⚠️ ERRORES ENCONTRADOS:");
            $this->line("-" . str_repeat("-", 30));

            // Mostrar solo los primeros 10 errores para no saturar la consola
            $errorsToShow = array_slice($stats['errors'], 0, 10);
            foreach ($errorsToShow as $error) {
                $this->line("• " . $error);
            }

            if (count($stats['errors']) > 10) {
                $this->line("... y " . (count($stats['errors']) - 10) . " errores más");
            }
        }

        $this->newLine();

        // Mostrar recomendaciones
        if ($stats['success_rate'] >= 90) {
            $this->info("🎉 ¡Excelente! La importación fue muy exitosa.");
        } elseif ($stats['success_rate'] >= 70) {
            $this->warn("⚠️ La importación fue exitosa pero con algunas advertencias.");
            $this->line("   Revise los errores para mejorar futuras importaciones.");
        } else {
            $this->error("❌ La importación tuvo muchos errores.");
            $this->line("   Revise el formato del archivo y los datos antes de reintentar.");
        }
    }

    /**
     * Log de auditoría
     */
    private function logAudit(array $stats, string $filePath, bool $updateExisting, bool $skipDuplicates): void
    {
        Log::info('Auditoría: Importación masiva de productos completada', [
            'file' => $filePath,
            'file_size' => filesize($filePath),
            'stats' => $stats,
            'configuration' => [
                'update_existing' => $updateExisting,
                'skip_duplicates' => $skipDuplicates
            ],
            'timestamp' => now(),
            'command' => 'productos:import'
        ]);
    }

    /**
     * Formatear bytes en formato legible
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
