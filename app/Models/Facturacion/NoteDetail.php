<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteDetail extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\NoteDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'note_id',
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
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'mtoValorUnitario' => 'decimal:2',
        'mtoValorVenta' => 'decimal:2',
        'descuento' => 'decimal:2',
        'mtoBaseIgv' => 'decimal:2',
        'totalImpuestos' => 'decimal:2',
        'porcentajeIgv' => 'decimal:2',
        'igv' => 'decimal:2',
        'mtoPrecioUnitario' => 'decimal:2',
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}
