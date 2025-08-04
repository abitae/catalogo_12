<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;

class SunatLeyenda extends Model
{
    protected $table = 'sunat_52';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion'
    ];

    public $timestamps = false;

    /**
     * Obtener todas las leyendas
     */
    public static function getAll()
    {
        return self::orderBy('codigo')->get();
    }

    /**
     * Obtener descripción completa de la leyenda
     */
    public function getDescripcionCompletaAttribute()
    {
        return $this->codigo . ' - ' . $this->descripcion;
    }
}
