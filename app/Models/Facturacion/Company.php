<?php

namespace App\Models\Facturacion;

use App\Models\Catalogo\CotizacionCatalogo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\CompanyFactory> */
    use HasFactory;
    protected $table = 'companies';
    protected $fillable = [
        'ruc',
        'razonSocial',
        'nombreComercial',
        'email',
        'telephone',
        'address_id',
        'ctaBanco',
        'nroMtc',
        'logo_path',
        'sol_user',
        'sol_pass',
        'cert_path',
        'client_id',
        'client_secret',
        'inicio_suscripcion',
        'fin_suscripcion',
        'inicio_produccion',
        'fin_produccion',
        'isProduction',
        'isActive',
    ];
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }
    public function cotizaciones()
    {
        return $this->hasMany(CotizacionCatalogo::class);
    }
}
