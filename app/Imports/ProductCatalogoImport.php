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
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class ProductCatalogoImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsEmptyRows, WithBatchInserts
{
    private $rowCount = 0;
    private $errors = [];
    private $importedCount = 0;
    private $skippedCount = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->rowCount++;

        // Validar que los campos requeridos no estén vacíos
        if (empty($row['brand']) || empty($row['category']) || empty($row['line']) || empty($row['code'])) {
            $this->skippedCount++;
            $this->errors[] = "Fila {$this->rowCount}: Campos requeridos vacíos";
            return null;
        }

        // Buscar las relaciones
        $brand = BrandCatalogo::where('name', trim($row['brand']))->first();
        $category = CategoryCatalogo::where('name', trim($row['category']))->first();
        $line = LineCatalogo::where('name', trim($row['line']))->first();

        // Si no se encuentran las relaciones, retornar null
        if (!$brand || !$category || !$line) {
            $this->skippedCount++;
            $this->errors[] = "Fila {$this->rowCount}: No se encontraron las relaciones (brand: {$row['brand']}, category: {$row['category']}, line: {$row['line']})";
            Log::warning('No se encontraron relaciones para el producto', [
                'brand' => $row['brand'] ?? 'N/A',
                'category' => $row['category'] ?? 'N/A',
                'line' => $row['line'] ?? 'N/A',
                'code' => $row['code'] ?? 'N/A'
            ]);
            return null;
        }

        // Validar que el código no exista ya
        $existingProduct = ProductoCatalogo::where('code', trim($row['code']))->first();
        if ($existingProduct) {
            $this->skippedCount++;
            $this->errors[] = "Fila {$this->rowCount}: Producto con código '{$row['code']}' ya existe";
            Log::info('Producto ya existe, saltando', ['code' => $row['code']]);
            return null;
        }

        $this->importedCount++;

        return new ProductoCatalogo([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'line_id' => $line->id,
            'code' => $this->parseCode($row['code']),
            'code_fabrica' => $this->parseCode($row['code_fabrica'] ?? ''),
            'code_peru' => $this->parseCode($row['code_peru'] ?? ''),
            'price_compra' => $this->parsePrice($row['price_compra'] ?? 0),
            'price_venta' => $this->parsePrice($row['price_venta'] ?? 0),
            'stock' => $this->parseStock($row['stock'] ?? 0),
            'dias_entrega' => $this->parseDays($row['dias_entrega'] ?? 0),
            'description' => trim($row['description'] ?? ''),
            'garantia' => trim($row['garantia'] ?? ''),
            'observaciones' => trim($row['observaciones'] ?? ''),
        ]);
    }

    /**
     * Obtener el número total de filas procesadas
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Obtener el número de productos importados exitosamente
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Obtener el número de filas omitidas
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    /**
     * Obtener los errores durante la importación
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Obtener estadísticas de la importación
     */
    public function getImportStats(): array
    {
        return [
            'total_rows' => $this->rowCount,
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors,
            'error_count' => count($this->errors)
        ];
    }

    /**
     * Reglas de validación para los datos
     */
    public function rules(): array
    {
        return [
            'brand' => 'required|string',
            'category' => 'required|string',
            'line' => 'required|string',
            'code' => 'required|string|max:255',
            'code_fabrica' => 'nullable|string|max:255',
            'code_peru' => 'nullable|string|max:255',
            'price_compra' => 'nullable|numeric|min:0',
            'price_venta' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'dias_entrega' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'garantia' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    public function customValidationMessages()
    {
        return [
            'brand.required' => 'La marca es obligatoria',
            'category.required' => 'La categoría es obligatoria',
            'line.required' => 'La línea es obligatoria',
            'code.required' => 'El código es obligatorio',
            'code.string' => 'El código debe ser texto (numérico o alfanumérico)',
            'code.max' => 'El código no puede exceder 255 caracteres',
            'code_fabrica.string' => 'El código de fábrica debe ser texto (numérico o alfanumérico)',
            'code_fabrica.max' => 'El código de fábrica no puede exceder 255 caracteres',
            'code_peru.string' => 'El código Perú debe ser texto (numérico o alfanumérico)',
            'code_peru.max' => 'El código Perú no puede exceder 255 caracteres',
            'price_compra.numeric' => 'El precio de compra debe ser un número',
            'price_venta.numeric' => 'El precio de venta debe ser un número',
            'stock.numeric' => 'El stock debe ser un número',
            'dias_entrega.numeric' => 'Los días de entrega deben ser un número',
        ];
    }

    /**
     * Manejar errores de validación
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('Error de validación en importación', [
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
    public function onError(\Throwable $e)
    {
        Log::error('Error durante la importación de productos', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    /**
     * Tamaño del lote para inserción
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Parsear precio
     */
    private function parsePrice($value)
    {
        if (empty($value)) return 0;

        // Remover caracteres no numéricos excepto punto y coma
        $value = preg_replace('/[^0-9.,]/', '', $value);

        // Convertir coma a punto si es necesario
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }

    /**
     * Parsear stock
     */
    private function parseStock($value)
    {
        if (empty($value)) return 0;

        // Remover caracteres no numéricos
        $value = preg_replace('/[^0-9]/', '', $value);

        return (int) $value;
    }

    /**
     * Parsear días de entrega
     */
    private function parseDays($value)
    {
        if (empty($value)) return 0;

        // Remover caracteres no numéricos
        $value = preg_replace('/[^0-9]/', '', $value);

        return (int) $value;
    }

    /**
     * Parsear códigos (numéricos o alfanuméricos)
     * Convierte el valor a string y lo limpia de espacios
     */
    private function parseCode($value)
    {
        if (empty($value)) return '';

        // Convertir a string y limpiar espacios
        $code = trim((string) $value);

        // Si el código es numérico, mantenerlo como string para preservar ceros a la izquierda
        // Si es alfanumérico, mantenerlo tal como está
        return $code;
    }
}
