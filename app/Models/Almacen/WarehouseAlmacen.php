<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseAlmacen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'almacenes';

    protected $fillable = [
        'code',
        'nombre',
        'direccion',
        'telefono',
        'email',
        'estado',
        'capacidad',
        'responsable'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'capacidad' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relaciones
    public function productos()
    {
        return $this->hasMany(ProductoAlmacen::class, 'almacen_id');
    }

    public function transferenciasOrigen()
    {
        return $this->hasMany(TransferenciaAlmacen::class, 'almacen_origen_id');
    }

    public function transferenciasDestino()
    {
        return $this->hasMany(TransferenciaAlmacen::class, 'almacen_destino_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoAlmacen::class, 'almacen_id');
    }

    // Métodos
    /**
     * Verifica si hay suficiente stock de un producto específico
     * @param int $productoId ID del producto a verificar
     * @param float $cantidad Cantidad requerida
     * @return bool True si hay stock suficiente, False en caso contrario
     */
    public function tieneStockSuficiente($productoId, $cantidad)
    {
        return $this->productos()
            ->where('id', $productoId)
            ->where('stock_actual', '>=', $cantidad)
            ->exists();
    }

    /**
     * Calcula el stock total de todos los productos en el almacén
     * @return float Suma total del stock actual
     */
    public function getStockTotal()
    {
        return $this->productos()->sum('stock_actual');
    }

    /**
     * Calcula el porcentaje de capacidad utilizada del almacén
     * @return float Porcentaje de capacidad utilizada
     */
    public function getCapacidadUtilizada()
    {
        return ($this->getStockTotal() / $this->capacidad) * 100;
    }

    /**
     * Obtiene todos los almacenes activos
     * @return \Illuminate\Database\Eloquent\Collection Colección de almacenes activos
     */
    public function getAlmacenesActivos()
    {
        return $this->where('estado', true)->get();
    }

    /**
     * Obtiene los almacenes que tienen productos con stock
     * @return \Illuminate\Database\Eloquent\Collection Colección de almacenes con stock
     */
    public function getAlmacenesConStock()
    {
        return $this->whereHas('productos', function($q) {
            $q->where('stock_actual', '>', 0);
        })->get();
    }
}
