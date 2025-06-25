<?php

namespace App\Models\Almacen;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TransferenciaAlmacen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transferencias_almacen';

    protected $fillable = [
        'code',
        'almacen_origen_id',
        'almacen_destino_id',
        'productos',
        'fecha_transferencia',
        'estado',
        'observaciones',
        'usuario_id',
        'fecha_confirmacion',
        'motivo_transferencia'
    ];

    protected $casts = [
        'productos' => 'array',
        'fecha_transferencia' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'estado' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes para estados
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_EN_TRANSITO = 'en_transito';
    const ESTADO_COMPLETADA = 'completada';
    const ESTADO_CANCELADA = 'cancelada';
    const ESTADO_DEVUELTA = 'devuelta';

    // Constantes para tipos de transferencia
    const TIPO_NORMAL = 'normal';
    const TIPO_URGENTE = 'urgente';
    const TIPO_DEVOLUCION = 'devolucion';

    // Relaciones
    public function almacenOrigen()
    {
        return $this->belongsTo(WarehouseAlmacen::class, 'almacen_origen_id');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(WarehouseAlmacen::class, 'almacen_destino_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoAlmacen::class, 'transferencia_id');
    }

    // Scopes para consultas comunes
    public function scopePendientes(Builder $query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeEnTransito(Builder $query)
    {
        return $query->where('estado', self::ESTADO_EN_TRANSITO);
    }

    public function scopeCompletadas(Builder $query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADA);
    }

    public function scopeCanceladas(Builder $query)
    {
        return $query->where('estado', self::ESTADO_CANCELADA);
    }

    public function scopePorAlmacenOrigen(Builder $query, $almacenId)
    {
        return $query->where('almacen_origen_id', $almacenId);
    }

    public function scopePorAlmacenDestino(Builder $query, $almacenId)
    {
        return $query->where('almacen_destino_id', $almacenId);
    }

    public function scopePorFecha(Builder $query, $fechaInicio, $fechaFin = null)
    {
        $query->where('fecha_transferencia', '>=', $fechaInicio);
        if ($fechaFin) {
            $query->where('fecha_transferencia', '<=', $fechaFin);
        }
        return $query;
    }

    public function scopePorUsuario(Builder $query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // Métodos de validación
    /**
     * Valida que la transferencia pueda ser creada
     * @return array Array con errores de validación
     */
    public function validarTransferencia()
    {
        $errores = [];

        // Validar almacenes
        if ($this->almacen_origen_id === $this->almacen_destino_id) {
            $errores[] = 'El almacén origen y destino no pueden ser el mismo';
        }

        // Validar productos
        if (empty($this->productos)) {
            $errores[] = 'La transferencia debe contener al menos un producto';
        }

        // Validar stock disponible
        foreach ($this->productos as $producto) {
            $productoModel = ProductoAlmacen::where('almacen_id', $this->almacen_origen_id)
                ->where('id', $producto['id'])
                ->first();

            if (!$productoModel) {
                $errores[] = "El producto {$producto['nombre']} no existe en el almacén origen";
                continue;
            }

            if (!$productoModel->tieneStockSuficiente($producto['cantidad'])) {
                $errores[] = "Stock insuficiente para {$producto['nombre']}. Disponible: {$productoModel->stock_actual}, Solicitado: {$producto['cantidad']}";
            }
        }

        return $errores;
    }

    /**
     * Verifica si la transferencia puede ser completada
     * @return bool True si puede ser completada
     */
    public function puedeCompletarse()
    {
        return in_array($this->estado, [self::ESTADO_PENDIENTE, self::ESTADO_EN_TRANSITO]);
    }

    /**
     * Verifica si la transferencia puede ser cancelada
     * @return bool True si puede ser cancelada
     */
    public function puedeCancelarse()
    {
        return in_array($this->estado, [self::ESTADO_PENDIENTE, self::ESTADO_EN_TRANSITO]);
    }

    /**
     * Verifica si la transferencia puede ser editada
     * @return bool True si puede ser editada
     */
    public function puedeEditarse()
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    // Métodos de estado
    /**
     * Completa la transferencia y actualiza stocks
     * @return bool True si se completó correctamente
     */
    public function completar()
    {
        if (!$this->puedeCompletarse()) {
            throw new Exception('La transferencia no puede ser completada en su estado actual');
        }

        return DB::transaction(function () {
            try {
                // Actualizar stock en almacén destino
                foreach ($this->productos as $producto) {
                    $this->actualizarStockDestino($producto);
                }

                // Crear movimientos de inventario
                $this->crearMovimientosInventario();

                // Actualizar estado
                $this->estado = self::ESTADO_COMPLETADA;
                $this->fecha_confirmacion = now();

                if ($this->save()) {
                    Log::info("Transferencia {$this->code} completada exitosamente");
                    return true;
                }

                return false;
            } catch (Exception $e) {
                Log::error("Error al completar transferencia {$this->code}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Cancela la transferencia y restaura stocks si es necesario
     * @return bool True si se canceló correctamente
     */
    public function cancelar()
    {
        if (!$this->puedeCancelarse()) {
            throw new Exception('La transferencia no puede ser cancelada en su estado actual');
        }

        return DB::transaction(function () {
            try {
                // Si está en tránsito, restaurar stock en origen
                if ($this->estado === self::ESTADO_EN_TRANSITO) {
                    foreach ($this->productos as $producto) {
                        $this->restaurarStockOrigen($producto);
                    }
                }

                $this->estado = self::ESTADO_CANCELADA;

                if ($this->save()) {
                    Log::info("Transferencia {$this->code} cancelada exitosamente");
                    return true;
                }

                return false;
            } catch (Exception $e) {
                Log::error("Error al cancelar transferencia {$this->code}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Inicia la transferencia cambiando su estado a en tránsito
     * @return bool True si se inició correctamente
     */
    public function iniciarTransferencia()
    {
        if ($this->estado !== self::ESTADO_PENDIENTE) {
            throw new Exception('Solo se pueden iniciar transferencias pendientes');
        }

        return DB::transaction(function () {
            try {
                // Reservar stock en almacén origen
                foreach ($this->productos as $producto) {
                    $this->reservarStockOrigen($producto);
                }

                $this->estado = self::ESTADO_EN_TRANSITO;

                if ($this->save()) {
                    Log::info("Transferencia {$this->code} iniciada exitosamente");
                    return true;
                }

                return false;
            } catch (Exception $e) {
                Log::error("Error al iniciar transferencia {$this->code}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Devuelve productos de una transferencia completada
     * @param array $productosDevueltos Array con productos a devolver
     * @return bool True si se devolvió correctamente
     */
    public function devolverProductos($productosDevueltos)
    {
        if ($this->estado !== self::ESTADO_COMPLETADA) {
            throw new Exception('Solo se pueden devolver productos de transferencias completadas');
        }

        return DB::transaction(function () use ($productosDevueltos) {
            try {
                foreach ($productosDevueltos as $productoDevuelto) {
                    $this->procesarDevolucion($productoDevuelto);
                }

                $this->estado = self::ESTADO_DEVUELTA;

                if ($this->save()) {
                    Log::info("Devolución de transferencia {$this->code} procesada exitosamente");
                    return true;
                }

                return false;
            } catch (Exception $e) {
                Log::error("Error al devolver productos de transferencia {$this->code}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    // Métodos privados para manejo de stock
    /**
     * Actualiza el stock en el almacén destino
     * @param array $producto Datos del producto
     */
    private function actualizarStockDestino($producto)
    {
        $productoDestino = ProductoAlmacen::where('almacen_id', $this->almacen_destino_id)
            ->where('id', $producto['id'])
            ->first();

        if ($productoDestino) {
            // Producto ya existe en destino, actualizar stock
            $productoDestino->actualizarStock($producto['cantidad'], 'entrada');
        } else {
            // Producto no existe en destino, crearlo
            $productoOrigen = ProductoAlmacen::find($producto['id']);
            if ($productoOrigen) {
                $this->crearProductoEnDestino($productoOrigen, $producto['cantidad']);
            }
        }
    }

    /**
     * Crea un producto en el almacén destino
     * @param ProductoAlmacen $productoOrigen Producto del almacén origen
     * @param float $cantidad Cantidad a transferir
     */
    private function crearProductoEnDestino($productoOrigen, $cantidad)
    {
        ProductoAlmacen::create([
            'code' => $productoOrigen->code,
            'codes_exit' => $productoOrigen->codes_exit,
            'nombre' => $productoOrigen->nombre,
            'descripcion' => $productoOrigen->descripcion,
            'categoria' => $productoOrigen->categoria,
            'unidad_medida' => $productoOrigen->unidad_medida,
            'stock_minimo' => $productoOrigen->stock_minimo,
            'stock_actual' => $cantidad,
            'precio_unitario' => $productoOrigen->precio_unitario,
            'almacen_id' => $this->almacen_destino_id,
            'estado' => $productoOrigen->estado,
            'codigo_barras' => $productoOrigen->codigo_barras,
            'marca' => $productoOrigen->marca,
            'modelo' => $productoOrigen->modelo,
            'imagen' => $productoOrigen->imagen
        ]);
    }

    /**
     * Reserva stock en el almacén origen
     * @param array $producto Datos del producto
     */
    private function reservarStockOrigen($producto)
    {
        $productoOrigen = ProductoAlmacen::where('almacen_id', $this->almacen_origen_id)
            ->where('id', $producto['id'])
            ->first();

        if ($productoOrigen && $productoOrigen->tieneStockSuficiente($producto['cantidad'])) {
            $productoOrigen->actualizarStock($producto['cantidad'], 'salida');
        } else {
            throw new Exception("Stock insuficiente para {$producto['nombre']}");
        }
    }

    /**
     * Restaura stock en el almacén origen
     * @param array $producto Datos del producto
     */
    private function restaurarStockOrigen($producto)
    {
        $productoOrigen = ProductoAlmacen::where('almacen_id', $this->almacen_origen_id)
            ->where('id', $producto['id'])
            ->first();

        if ($productoOrigen) {
            $productoOrigen->actualizarStock($producto['cantidad'], 'entrada');
        }
    }

    /**
     * Procesa la devolución de un producto
     * @param array $productoDevuelto Datos del producto devuelto
     */
    private function procesarDevolucion($productoDevuelto)
    {
        // Reducir stock en destino
        $productoDestino = ProductoAlmacen::where('almacen_id', $this->almacen_destino_id)
            ->where('id', $productoDevuelto['id'])
            ->first();

        if ($productoDestino) {
            $productoDestino->actualizarStock($productoDevuelto['cantidad'], 'salida');
        }

        // Restaurar stock en origen
        $productoOrigen = ProductoAlmacen::where('almacen_id', $this->almacen_origen_id)
            ->where('id', $productoDevuelto['id'])
            ->first();

        if ($productoOrigen) {
            $productoOrigen->actualizarStock($productoDevuelto['cantidad'], 'entrada');
        }
    }

    /**
     * Crea movimientos de inventario para auditoría
     */
    private function crearMovimientosInventario()
    {
        foreach ($this->productos as $producto) {
            // Movimiento de salida en origen
            MovimientoAlmacen::create([
                'code' => 'TRF-' . $this->code . '-SAL',
                'tipo_movimiento' => 'salida',
                'almacen_id' => $this->almacen_origen_id,
                'producto_id' => $producto['id'],
                'cantidad' => $producto['cantidad'],
                'fecha_movimiento' => now(),
                'motivo' => "Transferencia {$this->code} - Salida",
                'documento_referencia' => $this->code,
                'estado' => 'completado',
                'observaciones' => "Transferencia a {$this->almacenDestino->nombre}",
                'usuario_id' => $this->usuario_id,
                'transferencia_id' => $this->id
            ]);

            // Movimiento de entrada en destino
            MovimientoAlmacen::create([
                'code' => 'TRF-' . $this->code . '-ENT',
                'tipo_movimiento' => 'entrada',
                'almacen_id' => $this->almacen_destino_id,
                'producto_id' => $producto['id'],
                'cantidad' => $producto['cantidad'],
                'fecha_movimiento' => now(),
                'motivo' => "Transferencia {$this->code} - Entrada",
                'documento_referencia' => $this->code,
                'estado' => 'completado',
                'observaciones' => "Transferencia desde {$this->almacenOrigen->nombre}",
                'usuario_id' => $this->usuario_id,
                'transferencia_id' => $this->id
            ]);
        }
    }

    // Métodos de consulta estáticos
    /**
     * Obtiene las transferencias pendientes
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias pendientes
     */
    public static function getTransferenciasPendientes()
    {
        return self::pendientes()->with(['almacenOrigen', 'almacenDestino', 'usuario'])->get();
    }

    /**
     * Obtiene las transferencias en tránsito
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias en tránsito
     */
    public static function getTransferenciasEnTransito()
    {
        return self::enTransito()->with(['almacenOrigen', 'almacenDestino', 'usuario'])->get();
    }

    /**
     * Obtiene las transferencias completadas
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias completadas
     */
    public static function getTransferenciasCompletadas()
    {
        return self::completadas()->with(['almacenOrigen', 'almacenDestino', 'usuario'])->get();
    }

    /**
     * Obtiene las transferencias por almacén de origen
     * @param int $almacenId ID del almacén de origen
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias
     */
    public static function getTransferenciasPorAlmacenOrigen($almacenId)
    {
        return self::porAlmacenOrigen($almacenId)->with(['almacenOrigen', 'almacenDestino', 'usuario'])->get();
    }

    /**
     * Obtiene las transferencias por almacén de destino
     * @param int $almacenId ID del almacén de destino
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias
     */
    public static function getTransferenciasPorAlmacenDestino($almacenId)
    {
        return self::porAlmacenDestino($almacenId)->with(['almacenOrigen', 'almacenDestino', 'usuario'])->get();
    }

    /**
     * Obtiene estadísticas de transferencias
     * @return array Array con estadísticas
     */
    public static function getEstadisticas()
    {
        return [
            'total' => self::count(),
            'pendientes' => self::pendientes()->count(),
            'en_transito' => self::enTransito()->count(),
            'completadas' => self::completadas()->count(),
            'canceladas' => self::canceladas()->count(),
            'valor_total' => self::completadas()->get()->sum(function ($transferencia) {
                return collect($transferencia->productos)->sum(function ($producto) {
                    $productoModel = ProductoAlmacen::find($producto['id']);
                    return $productoModel ? ($producto['cantidad'] * $productoModel->precio_unitario) : 0;
                });
            })
        ];
    }

    // Métodos de utilidad
    /**
     * Calcula el valor total de la transferencia
     * @return float Valor total
     */
    public function getValorTotal()
    {
        return collect($this->productos)->sum(function ($producto) {
            $productoModel = ProductoAlmacen::find($producto['id']);
            return $productoModel ? ($producto['cantidad'] * $productoModel->precio_unitario) : 0;
        });
    }

    /**
     * Obtiene el número total de productos en la transferencia
     * @return int Número total de productos
     */
    public function getTotalProductos()
    {
        return collect($this->productos)->sum('cantidad');
    }

    /**
     * Obtiene el número de tipos de productos diferentes
     * @return int Número de tipos de productos
     */
    public function getNumeroTiposProductos()
    {
        return count($this->productos);
    }

    /**
     * Verifica si la transferencia tiene productos con stock bajo
     * @return bool True si tiene productos con stock bajo
     */
    public function tieneProductosStockBajo()
    {
        foreach ($this->productos as $producto) {
            $productoModel = ProductoAlmacen::find($producto['id']);
            if ($productoModel && $productoModel->necesitaReposicion()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtiene productos con stock bajo en la transferencia
     * @return array Array de productos con stock bajo
     */
    public function getProductosStockBajo()
    {
        $productosStockBajo = [];
        foreach ($this->productos as $producto) {
            $productoModel = ProductoAlmacen::find($producto['id']);
            if ($productoModel && $productoModel->necesitaReposicion()) {
                $productosStockBajo[] = $producto;
            }
        }
        return $productosStockBajo;
    }
}
