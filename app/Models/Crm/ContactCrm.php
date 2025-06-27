<?php

namespace App\Models\Crm;

use App\Models\Shared\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contacts_crm';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'cargo',
        'empresa',
        'notas',
        'es_principal',
        'customer_id',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];


    public function actividades()
    {
        return $this->hasMany(ActivityCrm::class, 'contact_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
