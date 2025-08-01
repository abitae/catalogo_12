<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SunatUnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'sunat_03';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion'
    ];

    public $timestamps = false;

    /**
     * Obtener todas las unidades de medida ordenadas por descripción
     */
    public static function getUnidadesOrdenadas()
    {
        return self::orderBy('descripcion')->get();
    }

    /**
     * Obtener unidades de medida como array para selects
     */
    public static function getUnidadesForSelect()
    {
        return self::orderBy('descripcion')
            ->pluck('descripcion', 'codigo')
            ->toArray();
    }
}
