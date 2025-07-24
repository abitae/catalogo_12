<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\AddressFactory> */
    use HasFactory;
    protected $table = 'addresses';
    protected $fillable = [
        'ubigueo',
        'codigoPais',
        'departamento',
        'provincia',
        'distrito',
        'urbanizacion',
        'direccion',
        'codLocal',
    ];
    public function company()
    {
        return $this->hasOne(Company::class);
    }
    public function client()
    {
        return $this->hasOne(Client::class);
    }
    public function sucursal()
    {
        return $this->hasOne(Sucursal::class);
    }
}
