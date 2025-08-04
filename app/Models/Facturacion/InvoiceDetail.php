<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\InvoiceDetailFactory> */
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'unidad',
        'cantidad',
        'codProducto',
        'codProdSunat',
        'codProdGS1',
        'descripcion',
        'tipAfeIgv',
        'mtoValorUnitario',
        'mtoValorVenta',
        'descuento',
        'mtoBaseIgv',
        'totalImpuestos',
        'porcentajeIgv',
        'igv',
        'mtoPrecioUnitario',
        'mtoOperGratuitas',
        'mtoIGVGratuitas',
        'mtoOperInafectas',
        'mtoOperExoneradas',
        'anticipo_mtoBase',
        'anticipo_mto',
        'tributos',
        'codBienDetraccion',
        'codMedioPago',
        'ctaBanco',
    ];

    protected $casts = [
        'tributos' => 'array',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
