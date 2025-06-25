<?php

namespace App\Services;

use App\Models\Almacen\MovimientoAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class MovimientoService
{
    /**
     * Crear un nuevo movimiento de almacén
     */
    public function crearMovimiento(array $data): MovimientoAlmacen
    {
        return DB::transaction(function () use ($data) {
            // Validar datos antes de procesar
            $this->validarDatosCompletos($data);

            // Generar código si no se proporciona
            if (empty($data['code'])) {
                $data['code'] = MovimientoAlmacen::generarCodigo($data['usuario_id'] ?? Auth::id());
            }

            // Verificar que el código sea único
            if (MovimientoAlmacen::where('code', $data['code'])->exists()) {
                throw new \Exception('El código del movimiento ya existe');
            }

            // Validar stock disponible para salidas
            if ($data['tipo_movimiento'] === MovimientoAlmacen::TIPO_SALIDA) {
                $this->validarStockDisponible($data['producto_id'], $data['cantidad']);
            }

            // Verificar que el producto pertenezca al almacén
            $this->validarProductoEnAlmacen($data['producto_id'], $data['almacen_id']);

            // Verificar que el almacén esté activo
            $this->validarAlmacenActivo($data['almacen_id']);

            // Crear el movimiento
            $movimiento = MovimientoAlmacen::create($data);

            // Aplicar el movimiento al stock
            $this->aplicarMovimientoAlStock($movimiento);

            // Log de éxito
            Log::info('Movimiento creado exitosamente', [
                'movimiento_id' => $movimiento->id,
                'code' => $movimiento->code,
                'tipo_movimiento' => $movimiento->tipo_movimiento,
                'cantidad' => $movimiento->cantidad,
                'producto_id' => $movimiento->producto_id,
                'almacen_id' => $movimiento->almacen_id,
                'usuario_id' => $movimiento->usuario_id,
                'fecha_movimiento' => $movimiento->fecha_movimiento
            ]);

            return $movimiento;
        }, 5); // 5 intentos de reintento
    }

    /**
     * Actualizar un movimiento existente
     */
    public function actualizarMovimiento(int $movimientoId, array $data): MovimientoAlmacen
    {
        return DB::transaction(function () use ($movimientoId, $data) {
            // Validar datos antes de procesar
            $this->validarDatosCompletos($data);

            $movimiento = MovimientoAlmacen::findOrFail($movimientoId);

            // Verificar que el código sea único (excluyendo el movimiento actual)
            if (MovimientoAlmacen::where('code', $data['code'])->where('id', '!=', $movimientoId)->exists()) {
                throw new \Exception('El código del movimiento ya existe');
            }

            // Validar stock disponible para salidas
            if ($data['tipo_movimiento'] === MovimientoAlmacen::TIPO_SALIDA) {
                $this->validarStockDisponible($data['producto_id'], $data['cantidad'], $movimiento);
            }

            // Verificar que el producto pertenezca al almacén
            $this->validarProductoEnAlmacen($data['producto_id'], $data['almacen_id']);

            // Verificar que el almacén esté activo
            $this->validarAlmacenActivo($data['almacen_id']);

            // Guardar datos del movimiento anterior para logging
            $datosAnteriores = [
                'tipo_movimiento' => $movimiento->tipo_movimiento,
                'cantidad' => $movimiento->cantidad,
                'producto_id' => $movimiento->producto_id,
                'almacen_id' => $movimiento->almacen_id
            ];

            // Revertir el movimiento anterior
            $this->revertirMovimientoDelStock($movimiento);

            // Actualizar el movimiento
            $movimiento->update($data);

            // Aplicar el nuevo movimiento al stock
            $this->aplicarMovimientoAlStock($movimiento);

            // Log de éxito
            Log::info('Movimiento actualizado exitosamente', [
                'movimiento_id' => $movimiento->id,
                'code' => $movimiento->code,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => [
                    'tipo_movimiento' => $movimiento->tipo_movimiento,
                    'cantidad' => $movimiento->cantidad,
                    'producto_id' => $movimiento->producto_id,
                    'almacen_id' => $movimiento->almacen_id
                ],
                'usuario_id' => $movimiento->usuario_id,
                'fecha_movimiento' => $movimiento->fecha_movimiento
            ]);

            return $movimiento;
        }, 5); // 5 intentos de reintento
    }

    /**
     * Eliminar un movimiento
     */
    public function eliminarMovimiento(int $movimientoId): bool
    {
        return DB::transaction(function () use ($movimientoId) {
            $movimiento = MovimientoAlmacen::findOrFail($movimientoId);

            // Revertir el movimiento del stock
            $this->revertirMovimientoDelStock($movimiento);

            // Eliminar el movimiento
            $movimiento->delete();

            Log::info('Movimiento eliminado exitosamente', [
                'movimiento_id' => $movimiento->id,
                'code' => $movimiento->code,
                'tipo_movimiento' => $movimiento->tipo_movimiento,
                'cantidad' => $movimiento->cantidad,
                'producto_id' => $movimiento->producto_id
            ]);

            return true;
        });
    }

    /**
     * Aplicar movimiento al stock del producto
     */
    private function aplicarMovimientoAlStock(MovimientoAlmacen $movimiento): void
    {
        $producto = $movimiento->producto;

        if (!$producto) {
            throw new \Exception('Producto no encontrado para el movimiento');
        }

        if ($movimiento->esEntrada()) {
            $producto->stock_actual += $movimiento->cantidad;
        } elseif ($movimiento->esSalida()) {
            $producto->stock_actual -= $movimiento->cantidad;
        }

        $producto->save();

        Log::info('Stock actualizado por movimiento', [
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'movimiento_tipo' => $movimiento->tipo_movimiento,
            'cantidad_movimiento' => $movimiento->cantidad,
            'stock_anterior' => $producto->stock_actual - ($movimiento->esEntrada() ? $movimiento->cantidad : -$movimiento->cantidad),
            'stock_nuevo' => $producto->stock_actual
        ]);
    }

    /**
     * Revertir movimiento del stock del producto
     */
    private function revertirMovimientoDelStock(MovimientoAlmacen $movimiento): void
    {
        $producto = $movimiento->producto;

        if (!$producto) {
            throw new \Exception('Producto no encontrado para revertir movimiento');
        }

        if ($movimiento->esEntrada()) {
            // Revertir entrada = restar del stock
            if ($producto->stock_actual < $movimiento->cantidad) {
                throw new \Exception("Stock insuficiente para revertir entrada. Disponible: {$producto->stock_actual}, Requerido: {$movimiento->cantidad}");
            }
            $producto->stock_actual -= $movimiento->cantidad;
        } elseif ($movimiento->esSalida()) {
            // Revertir salida = sumar al stock
            $producto->stock_actual += $movimiento->cantidad;
        }

        $producto->save();

        Log::info('Stock revertido por movimiento', [
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'movimiento_tipo' => $movimiento->tipo_movimiento,
            'cantidad_movimiento' => $movimiento->cantidad,
            'stock_anterior' => $producto->stock_actual + ($movimiento->esEntrada() ? $movimiento->cantidad : -$movimiento->cantidad),
            'stock_nuevo' => $producto->stock_actual
        ]);
    }

    /**
     * Obtener productos disponibles por almacén
     */
    public function obtenerProductosDisponibles(int $almacenId, bool $soloConStock = false): Collection
    {
        $query = ProductoAlmacen::where('almacen_id', $almacenId)
            ->where('estado', true);

        if ($soloConStock) {
            $query->where('stock_actual', '>', 0);
        }

        return $query->get();
    }

    /**
     * Obtener resumen de movimientos por almacén
     */
    public function obtenerResumenPorAlmacen(int $almacenId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        return MovimientoAlmacen::obtenerResumenPorAlmacen($almacenId, $fechaInicio, $fechaFin);
    }

    /**
     * Obtener resumen de movimientos por producto
     */
    public function obtenerResumenPorProducto(int $productoId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        return MovimientoAlmacen::obtenerResumenPorProducto($productoId, $fechaInicio, $fechaFin);
    }

    /**
     * Obtener movimientos con filtros
     */
    public function obtenerMovimientos(array $filtros = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = MovimientoAlmacen::query()
            ->with(['almacen', 'producto', 'usuario']);

        // Aplicar filtros
        if (!empty($filtros['search'])) {
            $query->buscar($filtros['search']);
        }

        if (!empty($filtros['almacen_id'])) {
            $query->porAlmacen($filtros['almacen_id']);
        }

        if (!empty($filtros['producto_id'])) {
            $query->porProducto($filtros['producto_id']);
        }

        if (!empty($filtros['tipo_movimiento'])) {
            $query->where('tipo_movimiento', $filtros['tipo_movimiento']);
        }

        if (!empty($filtros['fecha_inicio'])) {
            $query->porFecha($filtros['fecha_inicio'], $filtros['fecha_fin'] ?? null);
        }

        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', $filtros['usuario_id']);
        }

        // Ordenamiento
        $sortField = $filtros['sort_field'] ?? 'created_at';
        $sortDirection = $filtros['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $filtros['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Obtener estadísticas de movimientos
     */
    public function obtenerEstadisticas(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $query = MovimientoAlmacen::query();

        if ($fechaInicio) {
            $query->porFecha($fechaInicio, $fechaFin);
        }

        $totalEntradas = $query->entradas()->sum('cantidad');
        $totalSalidas = $query->salidas()->sum('cantidad');
        $totalMovimientos = $query->count();
        $movimientosPorTipo = $query->selectRaw('tipo_movimiento, COUNT(*) as total')
            ->groupBy('tipo_movimiento')
            ->pluck('total', 'tipo_movimiento')
            ->toArray();

        return [
            'total_entradas' => $totalEntradas,
            'total_salidas' => $totalSalidas,
            'neto' => $totalEntradas - $totalSalidas,
            'total_movimientos' => $totalMovimientos,
            'movimientos_por_tipo' => $movimientosPorTipo,
            'promedio_por_movimiento' => $totalMovimientos > 0 ? ($totalEntradas + $totalSalidas) / $totalMovimientos : 0
        ];
    }

    /**
     * Validar datos del movimiento
     */
    public function validarDatosMovimiento(array $data): array
    {
        $errores = [];

        // Validar código
        if (empty($data['code'])) {
            $errores[] = 'El código es requerido';
        } elseif (strlen($data['code']) < 3) {
            $errores[] = 'El código debe tener al menos 3 caracteres';
        } elseif (strlen($data['code']) > 50) {
            $errores[] = 'El código no debe exceder los 50 caracteres';
        }

        // Validar almacén
        if (empty($data['almacen_id'])) {
            $errores[] = 'El almacén es requerido';
        } else {
            $almacen = WarehouseAlmacen::find($data['almacen_id']);
            if (!$almacen) {
                $errores[] = 'El almacén seleccionado no existe';
            } elseif (!$almacen->estado) {
                $errores[] = 'El almacén seleccionado no está activo';
            }
        }

        // Validar producto
        if (empty($data['producto_id'])) {
            $errores[] = 'El producto es requerido';
        } else {
            $producto = ProductoAlmacen::find($data['producto_id']);
            if (!$producto) {
                $errores[] = 'El producto seleccionado no existe';
            } elseif (!$producto->estado) {
                $errores[] = 'El producto seleccionado no está activo';
            }
        }

        // Validar que el producto pertenezca al almacén
        if (!empty($data['almacen_id']) && !empty($data['producto_id'])) {
            $producto = ProductoAlmacen::where('id', $data['producto_id'])
                ->where('almacen_id', $data['almacen_id'])
                ->first();

            if (!$producto) {
                $errores[] = 'El producto no pertenece al almacén seleccionado';
            }
        }

        // Validar cantidad
        if (empty($data['cantidad'])) {
            $errores[] = 'La cantidad es requerida';
        } elseif (!is_numeric($data['cantidad'])) {
            $errores[] = 'La cantidad debe ser un número';
        } elseif ($data['cantidad'] <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0';
        } elseif ($data['cantidad'] > 999999.99) {
            $errores[] = 'La cantidad no puede exceder 999,999.99';
        }

        // Validar tipo
        if (empty($data['tipo_movimiento'])) {
            $errores[] = 'El tipo de movimiento es requerido';
        } elseif (!in_array($data['tipo_movimiento'], [MovimientoAlmacen::TIPO_ENTRADA, MovimientoAlmacen::TIPO_SALIDA])) {
            $errores[] = 'El tipo de movimiento no es válido';
        }

        // Validar fecha
        if (empty($data['fecha_movimiento'])) {
            $errores[] = 'La fecha del movimiento es requerida';
        } elseif (!strtotime($data['fecha_movimiento'])) {
            $errores[] = 'La fecha del movimiento no es válida';
        } elseif (strtotime($data['fecha_movimiento']) > time()) {
            $errores[] = 'La fecha del movimiento no puede ser futura';
        }

        // Validar stock disponible para salidas
        if (!empty($data['tipo_movimiento']) && $data['tipo_movimiento'] === MovimientoAlmacen::TIPO_SALIDA &&
            !empty($data['producto_id']) && !empty($data['cantidad'])) {

            $producto = ProductoAlmacen::find($data['producto_id']);
            if ($producto && $producto->stock_actual < $data['cantidad']) {
                $errores[] = "Stock insuficiente. Disponible: {$producto->stock_actual} {$producto->unidad_medida}, Requerido: {$data['cantidad']}";
            }
        }

        // Validar observaciones
        if (!empty($data['observaciones']) && strlen($data['observaciones']) > 500) {
            $errores[] = 'Las observaciones no deben exceder los 500 caracteres';
        }

        // Validar motivo del movimiento
        if (!empty($data['motivo_movimiento']) && strlen($data['motivo_movimiento']) > 255) {
            $errores[] = 'El motivo del movimiento no debe exceder los 255 caracteres';
        }

        return $errores;
    }

    /**
     * Validar stock disponible para salidas con contexto de edición
     */
    private function validarStockDisponible(int $productoId, float $cantidad, ?MovimientoAlmacen $movimientoExcluir = null): void
    {
        $producto = ProductoAlmacen::findOrFail($productoId);

        // Calcular stock disponible considerando el movimiento a excluir
        $stockDisponible = $producto->stock_actual;

        if ($movimientoExcluir && $movimientoExcluir->esSalida()) {
            // Si estamos editando una salida, sumar la cantidad anterior al stock disponible
            $stockDisponible += $movimientoExcluir->cantidad;
        }

        if ($stockDisponible < $cantidad) {
            throw new \Exception("Stock insuficiente. Disponible: {$stockDisponible} {$producto->unidad_medida}, Requerido: {$cantidad}");
        }
    }

    /**
     * Validar que el producto pertenezca al almacén
     */
    private function validarProductoEnAlmacen(int $productoId, int $almacenId): void
    {
        $producto = ProductoAlmacen::where('id', $productoId)
            ->where('almacen_id', $almacenId)
            ->first();

        if (!$producto) {
            throw new \Exception('El producto no pertenece al almacén seleccionado');
        }

        if (!$producto->estado) {
            throw new \Exception('El producto seleccionado no está activo');
        }
    }

    /**
     * Validar almacén activo
     */
    private function validarAlmacenActivo(int $almacenId): void
    {
        $almacen = WarehouseAlmacen::findOrFail($almacenId);

        if (!$almacen->estado) {
            throw new \Exception('El almacén seleccionado no está activo');
        }
    }

    /**
     * Validar datos completos del movimiento
     */
    private function validarDatosCompletos(array $data): void
    {
        $camposRequeridos = ['almacen_id', 'producto_id', 'cantidad', 'tipo_movimiento', 'fecha_movimiento'];

        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                throw new \Exception("El campo {$campo} es requerido");
            }
        }

        // Validar tipos de datos
        if (!is_numeric($data['cantidad']) || $data['cantidad'] <= 0) {
            throw new \Exception('La cantidad debe ser un número positivo');
        }

        if (!in_array($data['tipo_movimiento'], [MovimientoAlmacen::TIPO_ENTRADA, MovimientoAlmacen::TIPO_SALIDA])) {
            throw new \Exception('El tipo de movimiento no es válido');
        }

        if (!strtotime($data['fecha_movimiento'])) {
            throw new \Exception('La fecha del movimiento no es válida');
        }

        if (strtotime($data['fecha_movimiento']) > time()) {
            throw new \Exception('La fecha del movimiento no puede ser futura');
        }
    }
}
