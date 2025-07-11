<?php

namespace App\Imports;

use App\Models\Pc\ProductoAcuerdoMarco;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProductsImport implements ToModel, WithBatchInserts, WithChunkReading, SkipsOnError, WithStartRow
{
    use SkipsErrors;

    private $importStats = [
        'processed' => 0,
        'skipped' => 0,
        'errors' => 0
    ];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Validar que el registro no esté vacío (usando índice 39 para numero_entrega)
            if (empty($row[39]) || $row[39] == "0") {
                $this->importStats['skipped']++;
                return null;
            }

            // Procesar el código del acuerdo marco
            $codAcuerdoMarco = $this->processAcuerdoMarco($row[1] ?? '');

            // Procesar fechas
            $fechaPublicacion = $this->processDate($row[24] ?? '');
            $fechaAceptacion = $this->processDate($row[25] ?? '');
            $fechaInicio = $this->processDate($row[40] ?? '');
            $fechaFin = $this->processDate($row[42] ?? '');

            // Procesar valores numéricos
            $totalEntrega = $this->processNumeric($row[12] ?? 0);
            $subTotalOrden = $this->processNumeric($row[17] ?? 0);
            $igvOrden = $this->processNumeric($row[18] ?? 0);
            $totalOrden = $this->processNumeric($row[19] ?? 0);
            $montoDocumentoEstado = $this->processNumeric($row[31] ?? 0);
            $montoFlete = $this->processNumeric($row[38] ?? 0);
            $cantidad = $this->processNumeric($row[48] ?? 0);
            $entregaAfectoIgv = $this->processNumeric($row[49] ?? 0);
            $precioUnitario = $this->processNumeric($row[50] ?? 0);
            $subTotal = $this->processNumeric($row[51] ?? 0);
            $igvEntrega = $this->processNumeric($row[52] ?? 0);
            $totalMonto = $this->processNumeric($row[53] ?? 0);

            $this->importStats['processed']++;

            return new ProductoAcuerdoMarco([
                'cod_acuerdo_marco' => $codAcuerdoMarco,
                'ruc_proveedor' => $this->cleanString($row[2] ?? ''),
                'razon_proveedor' => $this->cleanString($row[3] ?? ''),
                'ruc_entidad' => $this->cleanString($row[4] ?? ''),
                'razon_entidad' => $this->cleanString($row[5] ?? ''),
                'unidad_ejecutora' => $this->cleanString($row[6] ?? ''),
                'procedimiento' => $this->cleanString($row[7] ?? ''),
                'tipo' => $this->cleanString($row[8] ?? ''),
                'orden_electronica' => $this->cleanString($row[9] ?? ''),
                'estado_orden_electronica' => $this->cleanString($row[10] ?? ''),
                'link_documento' => $this->cleanString($row[11] ?? ''),
                'total_entrega' => $totalEntrega,
                'num_doc_estado' => $this->cleanString($row[13] ?? ''),
                'orden_fisica' => $this->cleanString($row[14] ?? ''),
                'fecha_doc_estado' => $this->cleanString($row[15] ?? ''),
                'fecha_estado_oc' => $this->cleanString($row[16] ?? ''),
                'sub_total_orden' => $subTotalOrden,
                'igv_orden' => $igvOrden,
                'total_orden' => $totalOrden,
                'orden_digital_fisica' => $this->cleanString($row[20] ?? ''),
                'sustento_fisica' => $this->cleanString($row[21] ?? ''),
                'fecha_publicacion' => $fechaPublicacion,
                'fecha_aceptacion' => $fechaAceptacion,
                'usuario_create_oc' => $this->cleanString($row[27] ?? ''),
                'acuerdo_marco' => $this->cleanString($row[28] ?? ''),
                'ubigeo_proveedor' => $this->cleanString($row[29] ?? ''),
                'direccion_proveedor' => $this->cleanString($row[30] ?? ''),
                'monto_documento_estado' => $montoDocumentoEstado,
                'catalogo' => $this->cleanString($row[32] ?? ''),
                'categoria' => $this->cleanString($row[33] ?? ''),
                'descripcion_ficha_producto' => $this->cleanString($row[34] ?? ''),
                'marca_ficha_producto' => $this->cleanString($row[35] ?? ''),
                'numero_parte' => $this->cleanString($row[36] ?? ''),
                'link_ficha_producto' => $this->cleanString($row[37] ?? ''),
                'monto_flete' => $montoFlete,
                'numero_entrega' => $this->cleanString($row[39] ?? ''),
                'fecha_inicio' => $fechaInicio,
                'plazo_entrega' => $this->cleanString($row[41] ?? ''),
                'fecha_fin' => $fechaFin,
                'cantidad' => $cantidad,
                'entrega_afecto_igv' => $entregaAfectoIgv,
                'precio_unitario' => $precioUnitario,
                'sub_total' => $subTotal,
                'igv_entrega' => $igvEntrega,
                'total_monto' => $totalMonto,
            ]);

        } catch (\Exception $e) {
            $this->importStats['errors']++;

            Log::error('Error procesando fila en importación de Convenio Marco', [
                'row_data' => $row,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return null;
        }
    }

    /**
     * Fila desde donde comenzar a leer (saltar las primeras 6 filas)
     */
    public function startRow(): int
    {
        return 7; // Comienza desde la fila 7 (salta las primeras 6)
    }

    /**
     * Tamaño del batch para inserción
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * Tamaño del chunk para lectura
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Manejar errores de validación
     */
    public function onError(\Throwable $e)
    {
        $this->importStats['errors']++;

        Log::warning('Error de validación en importación de Convenio Marco', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
    }

    /**
     * Obtener estadísticas de importación
     */
    public function getImportStats()
    {
        return $this->importStats;
    }

    /**
     * Procesar el código del acuerdo marco
     */
    private function processAcuerdoMarco($ordenElectronica)
    {
        if (empty($ordenElectronica)) {
            return 'EXT-CE-2024-11';
        }

        $porciones = explode(" ", $ordenElectronica);
        return $porciones[0] ?? 'EXT-CE-2024-11';
    }

    /**
     * Procesar fechas
     */
    private function processDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Intentar diferentes formatos de fecha
            $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $dateString)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Si no se puede parsear, retornar null
            return null;
        } catch (\Exception $e) {
            Log::warning('Error procesando fecha en importación', [
                'date_string' => $dateString,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Procesar valores numéricos
     */
    private function processNumeric($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Remover caracteres no numéricos excepto punto y coma
        $cleanValue = preg_replace('/[^0-9.,]/', '', $value);

        // Convertir coma a punto para decimales
        $cleanValue = str_replace(',', '.', $cleanValue);

        // Si hay múltiples puntos, mantener solo el último
        $parts = explode('.', $cleanValue);
        if (count($parts) > 2) {
            $cleanValue = implode('', array_slice($parts, 0, -1)) . '.' . end($parts);
        }

        return (float) $cleanValue;
    }

    /**
     * Limpiar strings
     */
    private function cleanString($value)
    {
        if (empty($value)) {
            return '';
        }

        // Remover caracteres especiales y normalizar espacios
        $cleanValue = trim($value);
        $cleanValue = preg_replace('/\s+/', ' ', $cleanValue); // Múltiples espacios a uno
        $cleanValue = htmlspecialchars($cleanValue, ENT_QUOTES, 'UTF-8');

        return $cleanValue;
    }
}
