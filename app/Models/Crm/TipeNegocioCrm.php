<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipeNegocioCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_negocio_crm';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function oportunidades()
    {
        return $this->hasMany(OpportunityCrm::class, 'tipo_negocio_id');
    }
}
