<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;

class SunatTipoAfectacionIgv extends Model
{
    protected $table = 'sunat_07';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion'
    ];

    public $timestamps = false;

    /**
     * Obtener todos los tipos de afectación IGV
     */
    public static function getAll()
    {
        return self::orderBy('codigo')->get();
    }

    /**
     * Obtener descripción completa del tipo de afectación
     */
    public function getDescripcionCompletaAttribute()
    {
        return $this->codigo . ' - ' . $this->descripcion;
    }
}
