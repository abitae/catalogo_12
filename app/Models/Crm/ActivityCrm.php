<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activities_crm';

    protected $fillable = [
        'tipo',
        'asunto',
        'descripcion',
        'fecha_vencimiento',
        'estado',
        'prioridad',
        'lead_id',
        'opportunity_id',
        'contact_id',
        'deal_id',
        'asignado_a',
        'creado_por',
        'fecha_completado'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'fecha_completado' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function lead()
    {
        return $this->belongsTo(LeadCrm::class, 'lead_id');
    }

    public function oportunidad()
    {
        return $this->belongsTo(OpportunityCrm::class, 'opportunity_id');
    }

    public function contacto()
    {
        return $this->belongsTo(ContactCrm::class, 'contact_id');
    }

    public function negociacion()
    {
        return $this->belongsTo(DealCrm::class, 'deal_id');
    }
}
