<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DealCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'deals_crm';

    protected $fillable = [
        'nombre',
        'opportunity_id',
        'valor',
        'etapa',
        'fecha_cierre',
        'descripcion',
        'terminos',
        'asignado_a',
        'creado_por',
        'estado',
        'probabilidad',
        'ingreso_esperado'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'ingreso_esperado' => 'decimal:2',
        'probabilidad' => 'integer',
        'fecha_cierre' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function oportunidad()
    {
        return $this->belongsTo(OpportunityCrm::class, 'opportunity_id');
    }

    public function actividades()
    {
        return $this->hasMany(ActivityCrm::class, 'deal_id');
    }
}
