<?php

namespace App\Models\Almacen;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoAlmacen extends Model
{
    protected $table = 'movimientos_almacen';
    /** @use HasFactory<\Database\Factories\Almacen\MovimientoAlmacenFactory> */
    use HasFactory;
    protected $fillable = [
        'code',
        'tipo_movimiento', //Entrada, Salida, Transferencia, Devolucion, Ajuste, Venta, Compra, etc.
        'almacen_id',
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
        'estado',
        'observaciones',
        'subtotal',
        'descuento',
        'impuesto',
        'total'
    ];
    protected $casts = [
        'productos' => 'array',
    ];
    public function almacen()
    {
        return $this->belongsTo(WarehouseAlmacen::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
