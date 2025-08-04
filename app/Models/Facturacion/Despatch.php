<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despatch extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\DespatchFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'sucursal_id',
        'client_id',
        'tipoDoc',
        'serie',
        'correlativo',
        'fechaEmision',
        'tipoMoneda',
        'tipoDocDestinatario',
        'numDocDestinatario',
        'rznSocialDestinatario',
        'direccionDestinatario',
        'ubigeoDestinatario',
        'tipoDocTransportista',
        'numDocTransportista',
        'rznSocialTransportista',
        'placaVehiculo',
        'codEstabDestino',
        'direccionPartida',
        'ubigeoPartida',
        'direccionLlegada',
        'ubigeoLlegada',
        'fechaInicioTraslado',
        'fechaFinTraslado',
        'codMotivoTraslado',
        'desMotivoTraslado',
        'indicadorTransbordo',
        'pesoBrutoTotal',
        'numeroBultos',
        'modalidadTraslado',
        'documentosRelacionados',
        'observacion',
        'legends',
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
        'documentosRelacionados' => 'array',
        'indicadorTransbordo' => 'boolean',
        'pesoBrutoTotal' => 'decimal:2',
        'numeroBultos' => 'integer',
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

    public function despatchDetails()
    {
        return $this->hasMany(DespatchDetail::class);
    }
}
