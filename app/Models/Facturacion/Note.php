<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\NoteFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'sucursal_id',
        'client_id',
        'tipoDoc',
        'tipoOperacion',
        'serie',
        'correlativo',
        'fechaEmision',
        'formaPago_moneda',
        'formaPago_tipo',
        'tipoMoneda',
        'tipoDocModifica',
        'serieModifica',
        'correlativoModifica',
        'fechaEmisionModifica',
        'tipoMonedaModifica',
        'codMotivo',
        'desMotivo',
        'mtoOperGravadas',
        'mtoIGV',
        'totalImpuestos',
        'valorVenta',
        'subTotal',
        'mtoImpVenta',
        'monto_letras',
        'observacion',
        'legends',
        'note_reference',
        'xml_path',
        'xml_hash',
        'cdr_description',
        'cdr_code',
        'cdr_note',
        'cdr_path',
        'errorCode',
        'errorMessage',
    ];

    protected $casts = [
        'legends' => 'array',
        'mtoOperGravadas' => 'decimal:2',
        'mtoIGV' => 'decimal:2',
        'totalImpuestos' => 'decimal:2',
        'valorVenta' => 'decimal:2',
        'subTotal' => 'decimal:2',
        'mtoImpVenta' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function noteDetails()
    {
        return $this->hasMany(NoteDetail::class);
    }
}
