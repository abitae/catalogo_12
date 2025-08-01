<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\InvoiceFactory> */
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
        'mtoOperGravadas',
        'mtoIGV',
        'totalImpuestos',
        'valorVenta',
        'subTotal',
        'mtoImpVenta',
        'monto_letras',
        'codBienDetraccion',
        'codMedioPago',
        'ctaBanco',
        'setPercent',
        'setMount',
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
    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}
