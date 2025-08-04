<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DespatchDetail extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\DespatchDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'despatch_id',
        'unidad',
        'cantidad',
        'codProducto',
        'codProdSunat',
        'codProdGS1',
        'descripcion',
        'pesoBruto',
        'pesoNeto',
        'codLote',
        'fechaVencimiento',
        'codigoUnidadMedida',
        'codigoProductoSUNAT',
        'codigoProductoGS1',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'pesoBruto' => 'decimal:2',
        'pesoNeto' => 'decimal:2',
    ];

    public function despatch()
    {
        return $this->belongsTo(Despatch::class);
    }
}
