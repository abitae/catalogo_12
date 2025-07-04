<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductoAlmacen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos_almacen';

    protected $fillable = [
        'code',
        'codes_exit',
        'nombre',
        'descripcion',
        'categoria',
        'unidad_medida',
        'stock_minimo',
        'stock_actual',
        'precio_unitario',
        'almacen_id',
        'lote',
        'estado',
        'codigo_barras',
        'marca',
        'modelo',
        'imagen'
    ];

    protected $casts = [
        'stock_minimo' => 'decimal:2',
        'stock_actual' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'estado' => 'boolean',
        'codes_exit' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relaciones
    public function almacen()
    {
        return $this->belongsTo(WarehouseAlmacen::class, 'almacen_id');
    }

    public function transferencias()
    {
        return $this->hasMany(TransferenciaAlmacen::class, 'producto_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoAlmacen::class, 'producto_id');
    }

    // Scopes
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', true);
    }

    public function scopeConStock(Builder $query): Builder
    {
        return $query->where('stock_actual', '>', 0);
    }

    public function scopeStockBajo(Builder $query): Builder
    {
        return $query->whereRaw('stock_actual <= stock_minimo');
    }

    public function scopePorAlmacen(Builder $query, int $almacenId): Builder
    {
        return $query->where('almacen_id', $almacenId);
    }

    public function scopePorCategoria(Builder $query, string $categoria): Builder
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorLote(Builder $query, string $lote): Builder
    {
        return $query->where('lote', $lote);
    }

    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('code', 'like', "%{$termino}%")
              ->orWhere('codigo_barras', 'like', "%{$termino}%")
              ->orWhere('marca', 'like', "%{$termino}%")
              ->orWhere('modelo', 'like', "%{$termino}%")
              ->orWhere('lote', 'like', "%{$termino}%");
        });
    }

    public function scopePorMarca(Builder $query, string $marca): Builder
    {
        return $query->where('marca', $marca);
    }

    public function scopePorPrecio(Builder $query, float $minimo = null, float $maximo = null): Builder
    {
        if ($minimo !== null) {
            $query->where('precio_unitario', '>=', $minimo);
        }
        if ($maximo !== null) {
            $query->where('precio_unitario', '<=', $maximo);
        }
        return $query;
    }

    // Métodos para manejar códigos de salida
    /**
     * Agrega un código de salida al producto
     * @param string $code Código de salida a agregar
     * @return bool True si se agregó correctamente
     */
    public function agregarCodigoSalida($code)
    {
        $codes = $this->codes_exit ?? [];
        if (!in_array($code, $codes)) {
            $codes[] = $code;
            $this->codes_exit = $codes;
            return $this->save();
        }
        return false;
    }

    /**
     * Elimina un código de salida del producto
     * @param string $code Código de salida a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function eliminarCodigoSalida($code)
    {
        $codes = $this->codes_exit ?? [];
        if (($key = array_search($code, $codes)) !== false) {
            unset($codes[$key]);
            $this->codes_exit = array_values($codes);
            return $this->save();
        }
        return false;
    }

    /**
     * Verifica si un código de salida existe
     * @param string $code Código de salida a verificar
     * @return bool True si el código existe
     */
    public function tieneCodigoSalida($code)
    {
        return in_array($code, $this->codes_exit ?? []);
    }

    /**
     * Obtiene todos los códigos de salida
     * @return array Array de códigos de salida
     */
    public function getCodigosSalida()
    {
        return $this->codes_exit ?? [];
    }

    // Métodos de gestión de stock
    /**
     * Actualiza el stock del producto según el tipo de movimiento
     * @param float $cantidad Cantidad a actualizar
     * @param string $tipo Tipo de movimiento ('entrada' o 'salida')
     * @return bool True si se actualizó correctamente
     */
    public function actualizarStock($cantidad, $tipo = 'entrada')
    {
        try {
            $stockAnterior = $this->stock_actual;

            if ($tipo === 'entrada') {
                $this->stock_actual += $cantidad;
            } else {
                if ($this->stock_actual < $cantidad) {
                    throw new \Exception("Stock insuficiente. Disponible: {$this->stock_actual}, Requerido: {$cantidad}");
                }
                $this->stock_actual -= $cantidad;
            }

            $this->save();

            // Log de auditoría
            Log::info('Stock actualizado', [
                'producto_id' => $this->id,
                'producto_nombre' => $this->nombre,
                'tipo_movimiento' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $this->stock_actual,
                'fecha' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al actualizar stock', [
                'producto_id' => $this->id,
                'error' => $e->getMessage(),
                'cantidad' => $cantidad,
                'tipo' => $tipo
            ]);
            return false;
        }
    }

    /**
     * Verifica si hay suficiente stock para una cantidad específica
     * @param float $cantidad Cantidad a verificar
     * @return bool True si hay stock suficiente
     */
    public function tieneStockSuficiente($cantidad)
    {
        return $this->stock_actual >= $cantidad;
    }

    /**
     * Verifica si el producto necesita reposición
     * @return bool True si el stock está por debajo del mínimo
     */
    public function necesitaReposicion()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    /**
     * Obtiene el nivel de stock como porcentaje
     * @return float Porcentaje de stock disponible
     */
    public function getNivelStockPorcentaje()
    {
        if ($this->stock_minimo <= 0) {
            return 100;
        }
        return min(100, ($this->stock_actual / $this->stock_minimo) * 100);
    }

    /**
     * Obtiene el estado del stock
     * @return string Estado del stock ('normal', 'bajo', 'crítico', 'agotado')
     */
    public function getEstadoStock()
    {
        if ($this->stock_actual <= 0) {
            return 'agotado';
        } elseif ($this->stock_actual <= $this->stock_minimo * 0.5) {
            return 'crítico';
        } elseif ($this->stock_actual <= $this->stock_minimo) {
            return 'bajo';
        } else {
            return 'normal';
        }
    }

    /**
     * Calcula el valor total del stock actual
     * @return float Valor total del stock
     */
    public function getValorTotal()
    {
        return $this->stock_actual * $this->precio_unitario;
    }

    /**
     * Obtiene el historial de movimientos del producto
     * @param int $limite Número de movimientos a obtener
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHistorialMovimientos($limite = 50)
    {
        return $this->movimientos()
            ->with(['usuario:id,name', 'almacen:id,nombre'])
            ->orderBy('fecha_movimiento', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtiene estadísticas de movimientos del producto
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin
     * @return array Estadísticas de movimientos
     */
    public function getEstadisticasMovimientos($fechaInicio = null, $fechaFin = null)
    {
        $query = $this->movimientos();

        if ($fechaInicio) {
            $query->where('fecha_movimiento', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->where('fecha_movimiento', '<=', $fechaFin);
        }

        $entradas = $query->where('tipo', 'entrada')->sum('cantidad');
        $salidas = $query->where('tipo', 'salida')->sum('cantidad');
        $totalMovimientos = $query->count();

        return [
            'entradas' => $entradas,
            'salidas' => $salidas,
            'neto' => $entradas - $salidas,
            'total_movimientos' => $totalMovimientos,
            'promedio_por_movimiento' => $totalMovimientos > 0 ? ($entradas + $salidas) / $totalMovimientos : 0
        ];
    }

    // Métodos estáticos
    /**
     * Obtiene todos los productos activos
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos activos
     */
    public static function getProductosActivos()
    {
        return self::activos()->get();
    }

    /**
     * Obtiene los productos que tienen stock
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos con stock
     */
    public static function getProductosConStock()
    {
        return self::conStock()->get();
    }

    /**
     * Obtiene los productos con stock bajo
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos con stock bajo
     */
    public static function getProductosStockBajo()
    {
        return self::stockBajo()->get();
    }

    /**
     * Obtiene los productos por categoría
     * @param string $categoria Categoría a filtrar
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos por categoría
     */
    public static function getProductosPorCategoria($categoria)
    {
        return self::porCategoria($categoria)->get();
    }

    /**
     * Obtiene estadísticas generales de inventario
     * @return array Estadísticas del inventario
     */
    public static function obtenerEstadisticasInventario()
    {
        $totalProductos = self::count();
        $productosActivos = self::activos()->count();
        $productosConStock = self::conStock()->count();
        $productosStockBajo = self::stockBajo()->count();
        $valorTotalInventario = self::sum(DB::raw('stock_actual * precio_unitario'));

        return [
            'total_productos' => $totalProductos,
            'productos_activos' => $productosActivos,
            'productos_con_stock' => $productosConStock,
            'productos_stock_bajo' => $productosStockBajo,
            'valor_total_inventario' => $valorTotalInventario,
            'porcentaje_activos' => $totalProductos > 0 ? ($productosActivos / $totalProductos) * 100 : 0,
            'porcentaje_con_stock' => $totalProductos > 0 ? ($productosConStock / $totalProductos) * 100 : 0
        ];
    }

    /**
     * Obtiene productos que necesitan reposición
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function obtenerProductosNecesitanReposicion()
    {
        return self::stockBajo()
            ->with('almacen:id,nombre')
            ->orderBy('stock_actual', 'asc')
            ->get();
    }

    /**
     * Obtiene productos más valiosos por valor de inventario
     * @param int $limite Número de productos a obtener
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function obtenerProductosMasValiosos($limite = 10)
    {
        return self::selectRaw('*, (stock_actual * precio_unitario) as valor_inventario')
            ->orderBy('valor_inventario', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Verifica la integridad del inventario
     * @return array Errores encontrados
     */
    public static function verificarIntegridadInventario()
    {
        $errores = [];

        // Productos con stock negativo
        $productosStockNegativo = self::where('stock_actual', '<', 0)->count();
        if ($productosStockNegativo > 0) {
            $errores[] = "Hay {$productosStockNegativo} productos con stock negativo";
        }

        // Productos sin almacén
        $productosSinAlmacen = self::whereDoesntHave('almacen')->count();
        if ($productosSinAlmacen > 0) {
            $errores[] = "Hay {$productosSinAlmacen} productos sin almacén asociado";
        }

        // Productos con precio unitario negativo
        $productosPrecioNegativo = self::where('precio_unitario', '<', 0)->count();
        if ($productosPrecioNegativo > 0) {
            $errores[] = "Hay {$productosPrecioNegativo} productos con precio unitario negativo";
        }

        return $errores;
    }

    // Métodos específicos para manejo de lotes
    /**
     * Obtiene productos por lote específico
     * @param string $lote Número de lote
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductosPorLote($lote)
    {
        return self::porLote($lote)->get();
    }

    /**
     * Obtiene todos los lotes únicos
     * @return \Illuminate\Support\Collection
     */
    public static function getLotesUnicos()
    {
        return self::distinct()->pluck('lote')->filter();
    }

    /**
     * Obtiene productos con stock por lote
     * @param string $lote Número de lote
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductosConStockPorLote($lote)
    {
        return self::porLote($lote)->conStock()->get();
    }

    /**
     * Verifica si existe stock suficiente en un lote específico
     * @param string $lote Número de lote
     * @param float $cantidad Cantidad requerida
     * @return bool True si hay stock suficiente
     */
    public static function tieneStockSuficienteEnLote($lote, $cantidad)
    {
        return self::porLote($lote)->sum('stock_actual') >= $cantidad;
    }

    /**
     * Obtiene el stock total por lote
     * @param string $lote Número de lote
     * @return float Stock total del lote
     */
    public static function getStockTotalPorLote($lote)
    {
        return self::porLote($lote)->sum('stock_actual');
    }

    /**
     * Obtiene productos que necesitan reposición por lote
     * @param string $lote Número de lote
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductosNecesitanReposicionPorLote($lote)
    {
        return self::porLote($lote)->stockBajo()->get();
    }

    /**
     * Obtiene estadísticas por lote
     * @param string $lote Número de lote
     * @return array Estadísticas del lote
     */
    public static function getEstadisticasPorLote($lote)
    {
        $productos = self::porLote($lote);

        return [
            'total_productos' => $productos->count(),
            'productos_con_stock' => $productos->conStock()->count(),
            'productos_stock_bajo' => $productos->stockBajo()->count(),
            'stock_total' => $productos->sum('stock_actual'),
            'valor_total' => $productos->sum(DB::raw('stock_actual * precio_unitario')),
            'productos_activos' => $productos->activos()->count()
        ];
    }

    /**
     * Obtiene productos por lote y almacén
     * @param string $lote Número de lote
     * @param int $almacenId ID del almacén
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductosPorLoteYAlmacen($lote, $almacenId)
    {
        return self::porLote($lote)->porAlmacen($almacenId)->get();
    }

    /**
     * Verifica si existe stock suficiente en un lote específico de un almacén
     * @param string $lote Número de lote
     * @param int $almacenId ID del almacén
     * @param float $cantidad Cantidad requerida
     * @return bool True si hay stock suficiente
     */
    public static function tieneStockSuficienteEnLoteYAlmacen($lote, $almacenId, $cantidad)
    {
        return self::porLote($lote)->porAlmacen($almacenId)->sum('stock_actual') >= $cantidad;
    }

    /**
     * Obtiene el stock total por lote en un almacén específico
     * @param string $lote Número de lote
     * @param int $almacenId ID del almacén
     * @return float Stock total del lote en el almacén
     */
    public static function getStockTotalPorLoteYAlmacen($lote, $almacenId)
    {
        return self::porLote($lote)->porAlmacen($almacenId)->sum('stock_actual');
    }

    /**
     * Verifica si existe un producto con el mismo código en el mismo almacén y lote
     * @param string $code Código del producto
     * @param int $almacenId ID del almacén
     * @param string|null $lote Número de lote
     * @param int|null $excludeId ID del producto a excluir (para edición)
     * @return bool True si existe un duplicado
     */
    public static function existeDuplicado($code, $almacenId, $lote = null, $excludeId = null)
    {
        $query = self::where('code', $code)
            ->where('almacen_id', $almacenId)
            ->where('lote', $lote);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Obtiene productos por código, almacén y lote
     * @param string $code Código del producto
     * @param int $almacenId ID del almacén
     * @param string|null $lote Número de lote
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductosPorCodigoAlmacenLote($code, $almacenId, $lote = null)
    {
        return self::where('code', $code)
            ->where('almacen_id', $almacenId)
            ->where('lote', $lote)
            ->get();
    }

    /**
     * Obtiene todos los productos con el mismo código en diferentes lotes
     * @param string $code Código del producto
     * @param int $almacenId ID del almacén
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductosPorCodigoEnAlmacen($code, $almacenId)
    {
        return self::where('code', $code)
            ->where('almacen_id', $almacenId)
            ->orderBy('lote')
            ->get();
    }

    /**
     * Obtiene estadísticas de productos por código en un almacén
     * @param string $code Código del producto
     * @param int $almacenId ID del almacén
     * @return array Estadísticas del producto
     */
    public static function getEstadisticasProductoPorCodigo($code, $almacenId)
    {
        $productos = self::where('code', $code)
            ->where('almacen_id', $almacenId)
            ->get();

        $totalStock = $productos->sum('stock_actual');
        $totalValor = $productos->sum(function($producto) {
            return $producto->stock_actual * $producto->precio_unitario;
        });
        $lotesCount = $productos->count();
        $stockBajo = $productos->where('stock_actual', '<=', 'stock_minimo')->count();

        return [
            'total_stock' => $totalStock,
            'total_valor' => $totalValor,
            'lotes_count' => $lotesCount,
            'stock_bajo_count' => $stockBajo,
            'productos' => $productos
        ];
    }

    /**
     * Busca productos considerando código, almacén y lote
     * @param Builder $query
     * @param string $termino Término de búsqueda
     * @param int|null $almacenId ID del almacén (opcional)
     * @param string|null $lote Número de lote (opcional)
     * @return Builder
     */
    public function scopeBuscarAvanzado(Builder $query, string $termino, $almacenId = null, $lote = null): Builder
    {
        $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('code', 'like', "%{$termino}%")
              ->orWhere('codigo_barras', 'like', "%{$termino}%")
              ->orWhere('marca', 'like', "%{$termino}%")
              ->orWhere('modelo', 'like', "%{$termino}%")
              ->orWhere('lote', 'like', "%{$termino}%");
        });

        if ($almacenId) {
            $query->where('almacen_id', $almacenId);
        }

        if ($lote) {
            $query->where('lote', $lote);
        }

        return $query;
    }

    /**
     * Obtiene productos agrupados por código en un almacén
     * @param int $almacenId ID del almacén
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductosAgrupadosPorCodigo($almacenId)
    {
        return self::where('almacen_id', $almacenId)
            ->orderBy('code')
            ->orderBy('lote')
            ->get()
            ->groupBy('code');
    }

    /**
     * Obtiene el stock disponible en un lote específico
     * @param string $lote Número de lote
     * @param int $almacenId ID del almacén
     * @return float Stock disponible
     */
    public static function getStockPorLote($lote, $almacenId)
    {
        return self::where('lote', $lote)
            ->where('almacen_id', $almacenId)
            ->sum('stock_actual');
    }

    /**
     * Verifica si el producto tiene stock suficiente considerando el lote específico
     * @param float $cantidad Cantidad requerida
     * @param string|null $loteEspecifico Lote específico a verificar
     * @return bool True si tiene stock suficiente
     */
    public function tieneStockSuficienteEnLoteEspecifico($cantidad, $loteEspecifico = null)
    {
        if ($loteEspecifico && $this->lote !== $loteEspecifico) {
            return false;
        }

        return $this->stock_actual >= $cantidad;
    }

    // Eventos del modelo
    protected static function boot()
    {
        parent::boot();

        // Antes de guardar
        static::saving(function ($producto) {
            // Validar que el stock no sea negativo
            if ($producto->stock_actual < 0) {
                throw new \Exception('El stock no puede ser negativo');
            }

            // Validar que el precio unitario no sea negativo
            if ($producto->precio_unitario < 0) {
                throw new \Exception('El precio unitario no puede ser negativo');
            }
        });

        // Después de guardar
        static::saved(function ($producto) {
            // Log de cambios en stock
            if ($producto->wasChanged('stock_actual')) {
                Log::info('Stock de producto actualizado', [
                    'producto_id' => $producto->id,
                    'producto_nombre' => $producto->nombre,
                    'stock_anterior' => $producto->getOriginal('stock_actual'),
                    'stock_nuevo' => $producto->stock_actual,
                    'diferencia' => $producto->stock_actual - $producto->getOriginal('stock_actual'),
                    'fecha' => now()
                ]);
            }
        });
    }
}
