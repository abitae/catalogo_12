<?php

namespace App\Models\Crm;

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
        'lead_id',
        'customer_id',
        'notas',
        'creado_por',
        'ultima_fecha_contacto',
        'es_principal'
    ];

    protected $casts = [
        'ultima_fecha_contacto' => 'datetime',
        'es_principal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function lead()
    {
        return $this->belongsTo(LeadCrm::class, 'lead_id');
    }

    public function cliente()
    {
        return $this->belongsTo(CustomerCrm::class, 'customer_id');
    }

    public function actividades()
    {
        return $this->hasMany(ActivityCrm::class, 'contact_id');
    }
}
