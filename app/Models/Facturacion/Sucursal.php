<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\SucursalFactory> */
    use HasFactory;
    protected $table = 'sucursals';
    protected $fillable = [
        'ruc',
        'razonSocial',
        'nombreComercial',
        'email',
        'telephone',
        'address_id',
        'company_id',
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
