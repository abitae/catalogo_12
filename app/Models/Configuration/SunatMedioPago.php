<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;

class SunatMedioPago extends Model
{
    protected $table = 'sunat_59';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion'
    ];

    public $timestamps = false;

    /**
     * Obtener todos los medios de pago
     */
    public static function getAll()
    {
        return self::orderBy('codigo')->get();
    }

    /**
     * Obtener descripción completa del medio de pago
     */
    public function getDescripcionCompletaAttribute()
    {
        return $this->codigo . ' - ' . $this->descripcion;
    }
}
