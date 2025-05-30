<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    // Métodos
    /**
     * Actualiza el stock del producto según el tipo de movimiento
     * @param float $cantidad Cantidad a actualizar
     * @param string $tipo Tipo de movimiento ('entrada' o 'salida')
     * @return bool True si se actualizó correctamente
     */
    public function actualizarStock($cantidad, $tipo = 'entrada')
    {
        if ($tipo === 'entrada') {
            $this->stock_actual += $cantidad;
        } else {
            $this->stock_actual -= $cantidad;
        }
        return $this->save();
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
     * Calcula el valor total del stock actual
     * @return float Valor total del stock
     */
    public function getValorTotal()
    {
        return $this->stock_actual * $this->precio_unitario;
    }

    /**
     * Obtiene todos los productos activos
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos activos
     */
    public function getProductosActivos()
    {
        return $this->where('estado', true)->get();
    }

    /**
     * Obtiene los productos que tienen stock
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos con stock
     */
    public function getProductosConStock()
    {
        return $this->where('stock_actual', '>', 0)->get();
    }

    /**
     * Obtiene los productos con stock bajo
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos con stock bajo
     */
    public function getProductosStockBajo()
    {
        return $this->whereRaw('stock_actual <= stock_minimo')->get();
    }

    /**
     * Obtiene los productos por categoría
     * @param string $categoria Categoría a filtrar
     * @return \Illuminate\Database\Eloquent\Collection Colección de productos por categoría
     */
    public function getProductosPorCategoria($categoria)
    {
        return $this->where('categoria', $categoria)->get();
    }
}
