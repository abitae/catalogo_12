<?php

namespace App\Imports;

use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductCatalogoImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsEmptyRows,
    WithBatchInserts,
    WithChunkReading
{
    private $rowCount = 0;
    private $errors = [];
    private $importedCount = 0;
    private $skippedCount = 0;
    private $updatedCount = 0;

    // Cache para relaciones
    private $brandsCache = [];
    private $categoriesCache = [];
    private $linesCache = [];
    private $existingCodes = [];

    // Configuración
    private $updateExisting = false;
    private $skipDuplicates = true;

    public function __construct(bool $updateExisting = false, bool $skipDuplicates = true)
    {
        $this->updateExisting = $updateExisting;
        $this->skipDuplicates = $skipDuplicates;
        $this->preloadCache();
    }

    /**
     * Precargar cache de relaciones y códigos existentes
     */
    private function preloadCache()
    {
        // Cargar todas las marcas
        $this->brandsCache = BrandCatalogo::pluck('id', 'name')->toArray();

        // Cargar todas las categorías
        $this->categoriesCache = CategoryCatalogo::pluck('id', 'name')->toArray();

        // Cargar todas las líneas
        $this->linesCache = LineCatalogo::pluck('id', 'name')->toArray();

        // Cargar códigos existentes si no se van a actualizar
        if (!$this->updateExisting) {
            $this->existingCodes = ProductoCatalogo::pluck('id', 'code')->toArray();
        }
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->rowCount++;

        try {
            // Validar campos requeridos
            if (!$this->validateRequiredFields($row)) {
                return null;
            }

            // Normalizar datos
            $normalizedData = $this->normalizeRowData($row);

            // Validar relaciones
            $relations = $this->validateRelations($normalizedData);
            if (!$relations) {
                return null;
            }

            // Validar código único
            if (!$this->validateUniqueCode($normalizedData['code'])) {
                return null;
            }

            $this->importedCount++;

            return new ProductoCatalogo([
                'brand_id' => $relations['brand_id'],
                'category_id' => $relations['category_id'],
                'line_id' => $relations['line_id'],
                'code' => $normalizedData['code'],
                'code_fabrica' => $normalizedData['code_fabrica'],
                'code_peru' => $normalizedData['code_peru'],
                'price_compra' => $normalizedData['price_compra'],
                'price_venta' => $normalizedData['price_venta'],
                'stock' => $normalizedData['stock'],
                'dias_entrega' => $normalizedData['dias_entrega'],
                'description' => $normalizedData['description'],
                'garantia' => $normalizedData['garantia'],
                'observaciones' => $normalizedData['observaciones'],
            ]);

        } catch (\Exception $e) {
            $this->logError($e, $row);
            return null;
        }
    }

    /**
     * Validar campos requeridos
     */
    private function validateRequiredFields(array $row): bool
    {
        $requiredFields = ['brand', 'category', 'line', 'code'];

        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                $this->addError("Fila {$this->rowCount}: Campo requerido '{$field}' está vacío");
                return false;
            }
        }

        return true;
    }

    /**
     * Normalizar datos de la fila
     */
    private function normalizeRowData(array $row): array
    {
        return [
            'brand' => $this->normalizeString($row['brand']),
            'category' => $this->normalizeString($row['category']),
            'line' => $this->normalizeString($row['line']),
            'code' => $this->parseCode($row['code']),
            'code_fabrica' => $this->parseCode($row['code_fabrica'] ?? ''),
            'code_peru' => $this->parseCode($row['code_peru'] ?? ''),
            'price_compra' => $this->parsePrice($row['price_compra'] ?? 0),
            'price_venta' => $this->parsePrice($row['price_venta'] ?? 0),
            'stock' => $this->parseStock($row['stock'] ?? 0),
            'dias_entrega' => $this->parseDays($row['dias_entrega'] ?? 0),
            'description' => $this->normalizeString($row['description'] ?? ''),
            'garantia' => $this->normalizeString($row['garantia'] ?? ''),
            'observaciones' => $this->normalizeString($row['observaciones'] ?? ''),
        ];
    }

    /**
     * Validar relaciones
     */
    private function validateRelations(array $data): ?array
    {
        $brandId = $this->brandsCache[$data['brand']] ?? null;
        $categoryId = $this->categoriesCache[$data['category']] ?? null;
        $lineId = $this->linesCache[$data['line']] ?? null;

        if (!$brandId || !$categoryId || !$lineId) {
            $missingRelations = [];
            if (!$brandId) $missingRelations[] = "marca: {$data['brand']}";
            if (!$categoryId) $missingRelations[] = "categoría: {$data['category']}";
            if (!$lineId) $missingRelations[] = "línea: {$data['line']}";

            $this->addError("Fila {$this->rowCount}: No se encontraron las relaciones (" . implode(', ', $missingRelations) . ")");
            return null;
        }

        return [
            'brand_id' => $brandId,
            'category_id' => $categoryId,
            'line_id' => $lineId,
        ];
    }

    /**
     * Validar código único
     */
    private function validateUniqueCode(string $code): bool
    {
        if ($this->updateExisting) {
            return true; // Permitir actualizaciones
        }

        if (isset($this->existingCodes[$code])) {
            if ($this->skipDuplicates) {
                $this->addError("Fila {$this->rowCount}: Producto con código '{$code}' ya existe");
                return false;
            }
        }

        return true;
    }

    /**
     * Normalizar string
     */
    private function normalizeString($value): string
    {
        if (empty($value)) return '';
        return trim((string) $value);
    }

    /**
     * Parsear códigos (numéricos o alfanuméricos)
     */
    private function parseCode($value): string
    {
        if (empty($value)) return '';

        $code = trim((string) $value);

        // Validar longitud máxima
        if (strlen($code) > 255) {
            $code = substr($code, 0, 255);
        }

        return $code;
    }

    /**
     * Parsear precio con mejor manejo de formatos
     */
    private function parsePrice($value): float
    {
        if (empty($value)) return 0.0;

        // Convertir a string y limpiar
        $value = (string) $value;

        // Remover símbolos de moneda y espacios
        $value = preg_replace('/[^\d.,\-]/', '', $value);

        // Manejar diferentes separadores decimales
        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
            // Formato europeo: 1.234,56
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            // Solo coma como separador decimal
            $value = str_replace(',', '.', $value);
        }

        $result = (float) $value;

        // Validar que sea un número válido
        if (is_nan($result) || $result < 0) {
            return 0.0;
        }

        return round($result, 2);
    }

    /**
     * Parsear stock
     */
    private function parseStock($value): int
    {
        if (empty($value)) return 0;

        $value = (string) $value;
        $value = preg_replace('/[^0-9\-]/', '', $value);

        $result = (int) $value;

        // No permitir stock negativo
        return max(0, $result);
    }

    /**
     * Parsear días de entrega
     */
    private function parseDays($value): int
    {
        if (empty($value)) return 0;

        $value = (string) $value;
        $value = preg_replace('/[^0-9]/', '', $value);

        $result = (int) $value;

        // No permitir días negativos
        return max(0, $result);
    }

    /**
     * Agregar error
     */
    private function addError(string $message): void
    {
        $this->errors[] = $message;
        $this->skippedCount++;
    }

    /**
     * Log de errores
     */
    private function logError(\Exception $e, array $row): void
    {
        $this->addError("Fila {$this->rowCount}: Error inesperado - " . $e->getMessage());

        Log::error('Error en importación de productos', [
            'row' => $this->rowCount,
            'message' => $e->getMessage(),
            'data' => $row,
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    /**
     * Obtener estadísticas de la importación
     */
    public function getImportStats(): array
    {
        return [
            'total_rows' => $this->rowCount,
            'imported' => $this->importedCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors,
            'error_count' => count($this->errors),
            'success_rate' => $this->rowCount > 0 ? round(($this->importedCount / $this->rowCount) * 100, 2) : 0
        ];
    }

    /**
     * Reglas de validación optimizadas
     */
    public function rules(): array
    {
        return [
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'line' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'code_fabrica' => 'nullable|string|max:255',
            'code_peru' => 'nullable|string|max:255',
            'price_compra' => 'nullable|numeric|min:0|max:999999999.99',
            'price_venta' => 'nullable|numeric|min:0|max:999999999.99',
            'stock' => 'nullable|integer|min:0|max:2147483647',
            'dias_entrega' => 'nullable|integer|min:0|max:365',
            'description' => 'nullable|string|max:65535',
            'garantia' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:65535',
        ];
    }

    /**
     * Mensajes de validación mejorados
     */
    public function customValidationMessages(): array
    {
        return [
            'brand.required' => 'La marca es obligatoria',
            'brand.max' => 'La marca no puede exceder 255 caracteres',
            'category.required' => 'La categoría es obligatoria',
            'category.max' => 'La categoría no puede exceder 255 caracteres',
            'line.required' => 'La línea es obligatoria',
            'line.max' => 'La línea no puede exceder 255 caracteres',
            'code.required' => 'El código es obligatorio',
            'code.max' => 'El código no puede exceder 255 caracteres',
            'code_fabrica.max' => 'El código de fábrica no puede exceder 255 caracteres',
            'code_peru.max' => 'El código Perú no puede exceder 255 caracteres',
            'price_compra.numeric' => 'El precio de compra debe ser un número válido',
            'price_compra.min' => 'El precio de compra no puede ser negativo',
            'price_compra.max' => 'El precio de compra es demasiado alto',
            'price_venta.numeric' => 'El precio de venta debe ser un número válido',
            'price_venta.min' => 'El precio de venta no puede ser negativo',
            'price_venta.max' => 'El precio de venta es demasiado alto',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock no puede ser negativo',
            'stock.max' => 'El stock es demasiado alto',
            'dias_entrega.integer' => 'Los días de entrega deben ser un número entero',
            'dias_entrega.min' => 'Los días de entrega no pueden ser negativos',
            'dias_entrega.max' => 'Los días de entrega no pueden exceder 365 días',
        ];
    }

    /**
     * Manejar errores de validación
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->addError("Fila {$failure->row()}: Error en campo '{$failure->attribute()}' - " . implode(', ', $failure->errors()));

            Log::warning('Error de validación en importación', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }

    /**
     * Manejar errores durante la importación
     */
    public function onError(\Throwable $e): void
    {
        $this->addError("Error general: " . $e->getMessage());

        Log::error('Error durante la importación de productos', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Tamaño del lote optimizado
     */
    public function batchSize(): int
    {
        return 500; // Aumentado para mejor rendimiento
    }

    /**
     * Tamaño del chunk para lectura
     */
    public function chunkSize(): int
    {
        return 1000; // Chunk más grande para mejor rendimiento
    }

    // Métodos getter para compatibilidad
    public function getRowCount(): int { return $this->rowCount; }
    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
    public function getErrors(): array { return $this->errors; }


}
