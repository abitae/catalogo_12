<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;

class SunatTipoOperacion extends Model
{
    protected $table = 'sunat_51';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo_de_comprobante_asociado'
    ];

    public $timestamps = false;

    /**
     * Obtener tipos de operación filtrados por tipo de comprobante
     */
    public static function getByTipoComprobante($tipoDoc)
    {
        $tiposComprobante = [];
        
        switch ($tipoDoc) {
            case '01': // Factura
                $tiposComprobante = ['Factura', 'Factura, Boletas'];
                break;
            case '03': // Boleta
                $tiposComprobante = ['Boleta', 'Factura, Boletas'];
                break;
            default:
                $tiposComprobante = ['Factura, Boletas'];
        }

        return self::whereIn('tipo_de_comprobante_asociado', $tiposComprobante)
            ->orderBy('codigo')
            ->get();
    }

    /**
     * Obtener descripción completa del tipo de operación
     */
    public function getDescripcionCompletaAttribute()
    {
        return $this->codigo . ' - ' . $this->descripcion;
    }
} 