<?php

namespace App\Models\Shared;

use App\Models\Crm\ContactCrm;
use App\Models\Crm\OpportunityCrm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\Shared\CustomerFactory> */
    use HasFactory, SoftDeletes;
    protected $table = 'customers';
    protected $fillable = [
        'tipoDoc',
        'numDoc',
        'rznSocial',
        'nombreComercial',
        'email',
        'telefono',
        'direccion',
        'codigoPostal',
        'image',
        'archivo',
        'notas',
        'tipo_customer_id',
    ];
    public function contactos()
    {
        return $this->hasMany(ContactCrm::class, 'customer_id');
    }
    public function oportunidades()
    {
        return $this->hasMany(OpportunityCrm::class, 'customer_id');
    }
    public function tipoCustomer()
    {
        return $this->belongsTo(TipoCustomer::class, 'tipo_customer_id');
    }
}
