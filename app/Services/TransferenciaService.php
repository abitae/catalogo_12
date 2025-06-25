<?php

namespace App\Services;

use App\Models\Almacen\TransferenciaAlmacen;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\MovimientoAlmacen;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TransferenciaService
{
    /**
     * Crea una nueva transferencia
     * @param array $data Datos de la transferencia
     * @param User $usuario Usuario que crea la transferencia
     * @return TransferenciaAlmacen
     * @throws Exception
     */
    public function crearTransferencia(array $data, User $usuario)
    {
        return DB::transaction(function () use ($data, $usuario) {
            try {
                // Validar datos
                $this->validarDatosTransferencia($data);

                // Generar código único
                $data['code'] = $this->generarCodigoUnico($usuario->id);

                // Crear transferencia
                $transferencia = TransferenciaAlmacen::create(array_merge($data, [
                    'usuario_id' => $usuario->id,
                    'estado' => TransferenciaAlmacen::ESTADO_PENDIENTE
                ]));

                // Validar transferencia
                $errores = $transferencia->validarTransferencia();
                if (!empty($errores)) {
                    throw new Exception('Errores de validación: ' . implode(', ', $errores));
                }

                Log::info("Transferencia {$transferencia->code} creada exitosamente por usuario {$usuario->id}");
                return $transferencia;

            } catch (Exception $e) {
                Log::error("Error al crear transferencia: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Actualiza una transferencia existente
     * @param TransferenciaAlmacen $transferencia Transferencia a actualizar
     * @param array $data Nuevos datos
     * @return TransferenciaAlmacen
     * @throws Exception
     */
    public function actualizarTransferencia(TransferenciaAlmacen $transferencia, array $data)
    {
        return DB::transaction(function () use ($transferencia, $data) {
            try {
                if (!$transferencia->puedeEditarse()) {
                    throw new Exception('La transferencia no puede ser editada en su estado actual');
                }

                // Restaurar stock de productos eliminados
                $this->restaurarStockProductosEliminados($transferencia, $data['productos'] ?? []);

                // Actualizar transferencia
                $transferencia->update($data);

                // Validar transferencia actualizada
                $errores = $transferencia->validarTransferencia();
                if (!empty($errores)) {
                    throw new Exception('Errores de validación: ' . implode(', ', $errores));
                }

                Log::info("Transferencia {$transferencia->code} actualizada exitosamente");
                return $transferencia;

            } catch (Exception $e) {
                Log::error("Error al actualizar transferencia {$transferencia->code}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Procesa la completación de una transferencia
     * @param TransferenciaAlmacen $transferencia Transferencia a completar
     * @return bool
     * @throws Exception
     */
    public function completarTransferencia(TransferenciaAlmacen $transferencia)
    {
        try {
            return $transferencia->completar();
        } catch (Exception $e) {
            Log::error("Error al completar transferencia {$transferencia->code}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Procesa la cancelación de una transferencia
     * @param TransferenciaAlmacen $transferencia Transferencia a cancelar
     * @return bool
     * @throws Exception
     */
    public function cancelarTransferencia(TransferenciaAlmacen $transferencia)
    {
        try {
            return $transferencia->cancelar();
        } catch (Exception $e) {
            Log::error("Error al cancelar transferencia {$transferencia->code}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Inicia una transferencia (cambia a estado en tránsito)
     * @param TransferenciaAlmacen $transferencia Transferencia a iniciar
     * @return bool
     * @throws Exception
     */
    public function iniciarTransferencia(TransferenciaAlmacen $transferencia)
    {
        try {
            return $transferencia->iniciarTransferencia();
        } catch (Exception $e) {
            Log::error("Error al iniciar transferencia {$transferencia->code}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Procesa la devolución de productos de una transferencia
     * @param TransferenciaAlmacen $transferencia Transferencia de la cual devolver productos
     * @param array $productosDevueltos Productos a devolver
     * @return bool
     * @throws Exception
     */
    public function devolverProductos(TransferenciaAlmacen $transferencia, array $productosDevueltos)
    {
        try {
            return $transferencia->devolverProductos($productosDevueltos);
        } catch (Exception $e) {
            Log::error("Error al devolver productos de transferencia {$transferencia->code}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Valida los datos de una transferencia antes de crearla
     * @param array $data Datos a validar
     * @throws Exception
     */
    private function validarDatosTransferencia(array $data)
    {
        $errores = [];

        // Validar almacenes
        if (empty($data['almacen_origen_id']) || empty($data['almacen_destino_id'])) {
            $errores[] = 'Los almacenes origen y destino son requeridos';
        }

        if ($data['almacen_origen_id'] === $data['almacen_destino_id']) {
            $errores[] = 'El almacén origen y destino no pueden ser el mismo';
        }

        // Validar productos
        if (empty($data['productos']) || !is_array($data['productos'])) {
            $errores[] = 'La transferencia debe contener al menos un producto';
        }

        // Validar fecha
        if (empty($data['fecha_transferencia'])) {
            $errores[] = 'La fecha de transferencia es requerida';
        }

        if (!empty($errores)) {
            throw new Exception(implode(', ', $errores));
        }
    }

    /**
     * Genera un código único para la transferencia
     * @param int $usuarioId ID del usuario
     * @return string Código único
     */
    private function generarCodigoUnico(int $usuarioId): string
    {
        $numero = 1;
        do {
            $codigo = 'TRF' . str_pad($numero, 3, '0', STR_PAD_LEFT) . '-' . str_pad($usuarioId, 2, '0', STR_PAD_LEFT);
            $existe = TransferenciaAlmacen::where('code', $codigo)->exists();
            $numero++;
        } while ($existe);

        return $codigo;
    }

    /**
     * Restaura el stock de productos eliminados de una transferencia
     * @param TransferenciaAlmacen $transferencia Transferencia original
     * @param array $nuevosProductos Nuevos productos
     */
    private function restaurarStockProductosEliminados(TransferenciaAlmacen $transferencia, array $nuevosProductos)
    {
        $productosAnteriores = collect($transferencia->productos);
        $productosEliminados = $productosAnteriores->whereNotIn('id', collect($nuevosProductos)->pluck('id'));

        foreach ($productosEliminados as $producto) {
            $productoModel = ProductoAlmacen::find($producto['id']);
            if ($productoModel) {
                $productoModel->actualizarStock($producto['cantidad'], 'entrada');
            }
        }
    }

    /**
     * Obtiene estadísticas de transferencias
     * @param array $filtros Filtros adicionales
     * @return array Estadísticas
     */
    public function obtenerEstadisticas(array $filtros = [])
    {
        $query = TransferenciaAlmacen::query();

        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $query->where('fecha_transferencia', '>=', $filtros['fecha_inicio']);
        }

        if (!empty($filtros['fecha_fin'])) {
            $query->where('fecha_transferencia', '<=', $filtros['fecha_fin']);
        }

        if (!empty($filtros['almacen_origen_id'])) {
            $query->where('almacen_origen_id', $filtros['almacen_origen_id']);
        }

        if (!empty($filtros['almacen_destino_id'])) {
            $query->where('almacen_destino_id', $filtros['almacen_destino_id']);
        }

        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', $filtros['usuario_id']);
        }

        $transferencias = $query->get();

        return [
            'total' => $transferencias->count(),
            'pendientes' => $transferencias->where('estado', TransferenciaAlmacen::ESTADO_PENDIENTE)->count(),
            'en_transito' => $transferencias->where('estado', TransferenciaAlmacen::ESTADO_EN_TRANSITO)->count(),
            'completadas' => $transferencias->where('estado', TransferenciaAlmacen::ESTADO_COMPLETADA)->count(),
            'canceladas' => $transferencias->where('estado', TransferenciaAlmacen::ESTADO_CANCELADA)->count(),
            'valor_total' => $transferencias->where('estado', TransferenciaAlmacen::ESTADO_COMPLETADA)->sum(function ($transferencia) {
                return $transferencia->getValorTotal();
            }),
            'productos_transferidos' => $transferencias->where('estado', TransferenciaAlmacen::ESTADO_COMPLETADA)->sum(function ($transferencia) {
                return $transferencia->getTotalProductos();
            })
        ];
    }

    /**
     * Obtiene transferencias con filtros avanzados
     * @param array $filtros Filtros a aplicar
     * @param int $perPage Elementos por página
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function obtenerTransferenciasConFiltros(array $filtros = [], int $perPage = 15)
    {
        $query = TransferenciaAlmacen::with(['almacenOrigen', 'almacenDestino', 'usuario']);

        // Filtros de búsqueda
        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'like', "%{$search}%")
                  ->orWhere('motivo_transferencia', 'like', "%{$search}%");
            });
        }

        // Filtros de estado
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        // Filtros de fecha
        if (!empty($filtros['fecha_inicio'])) {
            $query->where('fecha_transferencia', '>=', $filtros['fecha_inicio']);
        }

        if (!empty($filtros['fecha_fin'])) {
            $query->where('fecha_transferencia', '<=', $filtros['fecha_fin']);
        }

        // Filtros de almacén
        if (!empty($filtros['almacen_origen_id'])) {
            $query->where('almacen_origen_id', $filtros['almacen_origen_id']);
        }

        if (!empty($filtros['almacen_destino_id'])) {
            $query->where('almacen_destino_id', $filtros['almacen_destino_id']);
        }

        // Filtros de usuario
        if (!empty($filtros['usuario_id'])) {
            $query->where('usuario_id', $filtros['usuario_id']);
        }

        // Ordenamiento
        $sortField = $filtros['sort_field'] ?? 'fecha_transferencia';
        $sortDirection = $filtros['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Verifica el stock disponible para una transferencia
     * @param int $almacenId ID del almacén
     * @param array $productos Productos a verificar
     * @return array Resultado de la verificación
     */
    public function verificarStockDisponible(int $almacenId, array $productos)
    {
        $resultado = [
            'valido' => true,
            'errores' => [],
            'productos_verificados' => []
        ];

        foreach ($productos as $producto) {
            $productoModel = ProductoAlmacen::where('almacen_id', $almacenId)
                ->where('id', $producto['id'])
                ->first();

            if (!$productoModel) {
                $resultado['valido'] = false;
                $resultado['errores'][] = "El producto {$producto['nombre']} no existe en el almacén";
                continue;
            }

            $tieneStock = $productoModel->tieneStockSuficiente($producto['cantidad']);

            $resultado['productos_verificados'][] = [
                'id' => $productoModel->id,
                'nombre' => $productoModel->nombre,
                'stock_disponible' => $productoModel->stock_actual,
                'cantidad_solicitada' => $producto['cantidad'],
                'tiene_stock_suficiente' => $tieneStock,
                'necesita_reposicion' => $productoModel->necesitaReposicion()
            ];

            if (!$tieneStock) {
                $resultado['valido'] = false;
                $resultado['errores'][] = "Stock insuficiente para {$productoModel->nombre}. Disponible: {$productoModel->stock_actual}, Solicitado: {$producto['cantidad']}";
            }
        }

        return $resultado;
    }
}
