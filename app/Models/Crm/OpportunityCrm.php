<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpportunityCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opportunities_crm';

    protected $fillable = [
        'nombre',
        'estado',
        'tipo_negocio_id',
        'marca_id',
        'lead_id',
        'valor',
        'etapa',
        'probabilidad',
        'fecha_cierre_esperada',
        'descripcion',
        'asignado_a',
        'creado_por',
        'ultima_fecha_actividad'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'probabilidad' => 'integer',
        'fecha_cierre_esperada' => 'datetime',
        'ultima_fecha_actividad' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function tipoNegocio()
    {
        return $this->belongsTo(TipeNegocioCrm::class, 'tipo_negocio_id');
    }

    public function marca()
    {
        return $this->belongsTo(MarcaCrm::class, 'marca_id');
    }

    public function lead()
    {
        return $this->belongsTo(LeadCrm::class, 'lead_id');
    }

    public function actividades()
    {
        return $this->hasMany(ActivityCrm::class, 'opportunity_id');
    }

    public function negociaciones()
    {
        return $this->hasMany(DealCrm::class, 'opportunity_id');
    }
}
