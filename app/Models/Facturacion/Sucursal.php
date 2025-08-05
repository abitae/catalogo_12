<?php

namespace App\Models\Facturacion;

use App\Models\Catalogo\CotizacionCatalogo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\SucursalFactory> */
    use HasFactory;
    protected $table = 'sucursals';
    protected $fillable = [
        'name',
        'ruc',
        'razonSocial',
        'nombreComercial',
        'email',
        'telephone',
        'address_id',
        'company_id',
        'isActive',
        'logo_path',
        'series_suffix',
    ];
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
   
}
