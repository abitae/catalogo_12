<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads_crm';

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'empresa',
        'estado',
        'origen',
        'notas',
        'asignado_a',
        'creado_por',
        'ultima_fecha_contacto'
    ];

    protected $casts = [
        'ultima_fecha_contacto' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function actividades()
    {
        return $this->hasMany(ActivityCrm::class, 'lead_id');
    }

    public function oportunidades()
    {
        return $this->hasMany(OpportunityCrm::class, 'lead_id');
    }

    public function contactos()
    {
        return $this->hasMany(ContactCrm::class, 'lead_id');
    }
}
