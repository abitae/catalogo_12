<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCustomer extends Model
{
    /** @use HasFactory<\Database\Factories\Shared\TipoCustomerFactory> */
    use HasFactory, SoftDeletes;
    protected $table = 'tipo_customers';
    protected $fillable = [
        'nombre',
        'descripcion',
    ];
    public function clientes()
    {
        return $this->hasMany(Customer::class, 'tipo_customer_id');
    }
}
