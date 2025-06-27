<?php

namespace App\Models\Crm;

use App\Models\User;
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
        'estado',
        'prioridad',
        'image',
        'archivo',
        'opportunity_id',
        'contact_id',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function oportunidad()
    {
        return $this->belongsTo(OpportunityCrm::class, 'opportunity_id');
    }

    public function contacto()
    {
        return $this->belongsTo(ContactCrm::class, 'contact_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
