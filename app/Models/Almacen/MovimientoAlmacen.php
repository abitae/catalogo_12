<?php

namespace App\Models\Almacen;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class MovimientoAlmacen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimientos_almacen';
    /** @use HasFactory<\Database\Factories\Almacen\MovimientoAlmacenFactory> */
    protected $fillable = [
        'code',
        'tipo_movimiento', //Entrada, Salida, Transferencia, Devolucion, Ajuste, Venta, Compra, etc.
        'almacen_id',
        'producto_id',
        'lote',
        'cantidad',
        'fecha_movimiento',
        'motivo',
        'documento_referencia',
        'estado',
        'observaciones',
        'usuario_id',
        'transferencia_id',
        'user_id',
        'tipo_pago',
        'tipo_documento',
        'numero_documento',
        'tipo_operacion',
        'forma_pago',
        'tipo_moneda',
        'fecha_emision',
        'fecha_vencimiento',
        'productos',
        'subtotal',
        'descuento',
        'impuesto',
        'total'
    ];
    protected $casts = [
        'productos' => 'array',
        'fecha_movimiento' => 'datetime',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'cantidad' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    // Constantes para tipos de movimiento
    const TIPO_ENTRADA = 'entrada';
    const TIPO_SALIDA = 'salida';
    const TIPO_TRANSFERENCIA = 'transferencia';
    const TIPO_DEVOLUCION = 'devolucion';
    const TIPO_AJUSTE = 'ajuste';
    const TIPO_VENTA = 'venta';
    const TIPO_COMPRA = 'compra';

    // Constantes para estados
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transferencia()
    {
        return $this->belongsTo(TransferenciaAlmacen::class, 'transferencia_id');
    }

    // Scopes
    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    public function scopePorAlmacen(Builder $query, int $almacenId): Builder
    {
        return $query->where('almacen_id', $almacenId);
    }

    public function scopePorProducto(Builder $query, int $productoId): Builder
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopePorLote(Builder $query, string $lote): Builder
    {
        return $query->where('lote', $lote);
    }

    public function scopePorEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }

    public function scopePorFecha(Builder $query, string $fechaInicio, string $fechaFin = null): Builder
    {
        $query->where('fecha_movimiento', '>=', $fechaInicio);
        if ($fechaFin) {
            $query->where('fecha_movimiento', '<=', $fechaFin);
        }
        return $query;
    }

    public function scopeEntradas(Builder $query): Builder
    {
        return $query->where('tipo_movimiento', self::TIPO_ENTRADA);
    }

    public function scopeSalidas(Builder $query): Builder
    {
        return $query->where('tipo_movimiento', self::TIPO_SALIDA);
    }

    // Métodos estáticos
    /**
     * Genera un código único para el movimiento
     * @param int $usuarioId ID del usuario
     * @return string Código único
     */
    public static function generarCodigo(int $usuarioId): string
    {
        $numero = 1;
        do {
            $codigo = 'MOV' . str_pad($numero, 4, '0', STR_PAD_LEFT) . '-' . str_pad($usuarioId, 2, '0', STR_PAD_LEFT);
            $existe = self::where('code', $codigo)->exists();
            $numero++;
        } while ($existe);

        return $codigo;
    }

    /**
     * Obtiene movimientos por lote
     * @param string $lote Número de lote
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getMovimientosPorLote($lote)
    {
        return self::porLote($lote)->with(['producto', 'almacen', 'usuario'])->get();
    }

    /**
     * Obtiene estadísticas de movimientos por lote
     * @param string $lote Número de lote
     * @return array Estadísticas
     */
    public static function getEstadisticasPorLote($lote)
    {
        $movimientos = self::porLote($lote);

        return [
            'total_movimientos' => $movimientos->count(),
            'entradas' => $movimientos->entradas()->sum('cantidad'),
            'salidas' => $movimientos->salidas()->sum('cantidad'),
            'neto' => $movimientos->entradas()->sum('cantidad') - $movimientos->salidas()->sum('cantidad'),
            'valor_total_entradas' => $movimientos->entradas()->sum('total'),
            'valor_total_salidas' => $movimientos->salidas()->sum('total')
        ];
    }

    /**
     * Verifica si hay movimientos para un lote específico
     * @param string $lote Número de lote
     * @return bool True si hay movimientos
     */
    public static function tieneMovimientosPorLote($lote)
    {
        return self::porLote($lote)->exists();
    }
}
