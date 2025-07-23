<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineCatalogo extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'logo',
        'fondo',
        'archivo',
        'isActive',
    ];
    public function productos()
    {
        return $this->hasMany(ProductoCatalogo::class, 'line_id');
    }
    public function cotizaciones()
    {
        return $this->hasMany(CotizacionCatalogo::class, 'line_id');
    }
}
