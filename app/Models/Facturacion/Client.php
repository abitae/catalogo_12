<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\Facturacion\ClientFactory> */
    use HasFactory;
    protected $table = 'clients';
    protected $fillable = [
        'tipoDoc',
        'numDoc',
        'rznSocial',
        'email',
        'telephone',
        'address_id',
    ];
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
