<?php

namespace App\Models\Crm;

use App\Models\Shared\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpportunityCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opportunities_crm';

    protected $fillable = [
        'nombre',
        'codigo_oportunidad',
        'estado',
        'valor',
        'etapa',
        'probabilidad',
        'fecha_cierre_esperada',
        'fuente',
        'descripcion',
        'notas',
        'image',
        'archivo',
        'tipo_negocio_id',
        'marca_id',
        'customer_id',
        'contact_id',
        'user_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'probabilidad' => 'integer',
        'fecha_cierre_esperada' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function tipoNegocio()
    {
        return $this->belongsTo(TipoNegocioCrm::class, 'tipo_negocio_id');
    }

    public function marca()
    {
        return $this->belongsTo(MarcaCrm::class, 'marca_id');
    }

    public function contacto()
    {
        return $this->belongsTo(ContactCrm::class, 'contact_id');
    }

    public function actividades()
    {
        return $this->hasMany(ActivityCrm::class, 'opportunity_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
