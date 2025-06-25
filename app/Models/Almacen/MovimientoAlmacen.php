<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoAlmacen extends Model
{
    /** @use HasFactory<\Database\Factories\Almacen\MovimientoAlmacenFactory> */
    use HasFactory;
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
}
