<?php

namespace App\Models\Almacen;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoAlmacen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimientos_almacen';

    protected $fillable = [
        'code',
        'tipo_movimiento',
        'almacen_id',
        'producto_id',
        'cantidad',
        'fecha_movimiento',
        'motivo',
        'documento_referencia',
        'estado',
        'observaciones',
        'usuario_id',
        'valor_unitario',
        'valor_total'
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'cantidad' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'estado' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    const TIPO_ENTRADA = 'entrada';
    const TIPO_SALIDA = 'salida';
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_COMPLETADO = 'completado';
    const ESTADO_CANCELADO = 'cancelado';

    // Relaciones
    public function almacen()
    {
        return $this->belongsTo(WarehouseAlmacen::class, 'almacen_id');
    }

    public function producto()
    {
        return $this->belongsTo(ProductoAlmacen::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Métodos
    /**
     * Completa el movimiento y calcula los valores
     * @return bool True si se completó correctamente
     */
    public function completar()
    {
        if ($this->estado === 'pendiente') {
            $this->estado = 'completado';
            $this->calcularValores();
            return $this->save();
        }
        return false;
    }

    /**
     * Cancela el movimiento si está pendiente
     * @return bool True si se canceló correctamente
     */
    public function cancelar()
    {
        if ($this->estado === 'pendiente') {
            $this->estado = 'cancelado';
            return $this->save();
        }
        return false;
    }

    /**
     * Calcula los valores unitario y total del movimiento
     */
    public function calcularValores()
    {
        if ($this->producto) {
            $this->valor_unitario = $this->producto->precio_unitario;
            $this->valor_total = $this->cantidad * $this->valor_unitario;
        }
    }

    /**
     * Verifica si el movimiento es de entrada
     * @return bool True si es entrada
     */
    public function esEntrada()
    {
        return $this->tipo_movimiento === 'entrada';
    }

    /**
     * Verifica si el movimiento es de salida
     * @return bool True si es salida
     */
    public function esSalida()
    {
        return $this->tipo_movimiento === 'salida';
    }

    /**
     * Verifica si el movimiento puede ser completado
     * @return bool True si puede ser completado
     */
    public function puedeCompletarse()
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Obtiene los movimientos de entrada
     * @return \Illuminate\Database\Eloquent\Collection Colección de movimientos de entrada
     */
    public function getMovimientosEntrada()
    {
        return $this->where('tipo_movimiento', 'entrada')->get();
    }

    /**
     * Obtiene los movimientos de salida
     * @return \Illuminate\Database\Eloquent\Collection Colección de movimientos de salida
     */
    public function getMovimientosSalida()
    {
        return $this->where('tipo_movimiento', 'salida')->get();
    }

    /**
     * Obtiene los movimientos completados
     * @return \Illuminate\Database\Eloquent\Collection Colección de movimientos completados
     */
    public function getMovimientosCompletados()
    {
        return $this->where('estado', 'completado')->get();
    }

    /**
     * Obtiene los movimientos por almacén
     * @param int $almacenId ID del almacén
     * @return \Illuminate\Database\Eloquent\Collection Colección de movimientos
     */
    public function getMovimientosPorAlmacen($almacenId)
    {
        return $this->where('almacen_id', $almacenId)->get();
    }

    /**
     * Obtiene los movimientos por producto
     * @param int $productoId ID del producto
     * @return \Illuminate\Database\Eloquent\Collection Colección de movimientos
     */
    public function getMovimientosPorProducto($productoId)
    {
        return $this->where('producto_id', $productoId)->get();
    }
}
