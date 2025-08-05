<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Client;
use App\Models\User;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'sucursal_id',
        'client_id',
        'tipoDoc',
        'tipoOperacion',
        'serie',
        'correlativo',
        'fechaEmision',
        'fechaVencimiento',
        'formaPago_moneda',
        'formaPago_tipo',
        'tipoMoneda',
        'estado_pago_invoice',
        'mtoOperGravadas',
        'mtoOperInafectas',
        'mtoOperExoneradas',
        'mtoOperGratuitas',
        'mtoIGV',
        'mtoIGVGratuitas',
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
        'codReg',
        'porcentajePer',
        'mtoBasePer',
        'mtoPer',
        'mtoTotalPer',
        'codRegRet',
        'mtoBaseRet',
        'factorRet',
        'mtoRet',
        'tipoVenta',
        'cuotas',
        'descuentos_mto',
        'cargos_mto',
        'anticipos_mto',
        'observacion',
        'legends',
        'guias',
        'relDocs',
        'anticipos',
        'descuentos',
        'cargos',
        'tributos',
        'note_reference',
        'xml_path',
        'xml_hash',
        'cdr_description',
        'cdr_code',
        'cdr_note',
        'cdr_path',
        'errorCode',
        'errorMessage',
        'exportacion',
    ];

    protected $casts = [
        'fechaEmision' => 'date',
        'fechaVencimiento' => 'date',
        'cuotas' => 'array',
        'legends' => 'array',
        'guias' => 'array',
        'relDocs' => 'array',
        'anticipos' => 'array',
        'descuentos' => 'array',
        'cargos' => 'array',
        'tributos' => 'array',
        'exportacion' => 'array',
        'mtoOperGravadas' => 'decimal:2',
        'mtoOperInafectas' => 'decimal:2',
        'mtoOperExoneradas' => 'decimal:2',
        'mtoOperGratuitas' => 'decimal:2',
        'mtoIGV' => 'decimal:2',
        'mtoIGVGratuitas' => 'decimal:2',
        'totalImpuestos' => 'decimal:2',
        'valorVenta' => 'decimal:2',
        'subTotal' => 'decimal:2',
        'mtoImpVenta' => 'decimal:2',
        'setPercent' => 'decimal:2',
        'setMount' => 'decimal:2',
        'porcentajePer' => 'decimal:2',
        'mtoBasePer' => 'decimal:2',
        'mtoPer' => 'decimal:2',
        'mtoTotalPer' => 'decimal:2',
        'mtoBaseRet' => 'decimal:2',
        'factorRet' => 'decimal:2',
        'mtoRet' => 'decimal:2',
        'descuentos_mto' => 'decimal:2',
        'cargos_mto' => 'decimal:2',
        'anticipos_mto' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
