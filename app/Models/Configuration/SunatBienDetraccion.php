<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SunatBienDetraccion extends Model
{
    use HasFactory;

    protected $table = 'sunat_54';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion',
        'porcentaje'
    ];

    public $timestamps = false;

    /**
     * Obtener todos los bienes sujetos a detracción
     */
    public static function getAll()
    {
        return self::orderBy('codigo')->get();
    }

    /**
     * Obtener descripción completa del bien
     */
    public function getDescripcionCompletaAttribute()
    {
        return $this->codigo . ' - ' . $this->descripcion . ' (' . $this->porcentaje . '%)';
    }
}
