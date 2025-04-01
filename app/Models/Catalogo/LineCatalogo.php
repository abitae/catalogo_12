<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class LineCatalogo extends Model
{
    protected $fillable = [
        'code',
        'name',
        'logo',
        'fondo',
        'firma_autorizacion',
        'fondo_autorizacion',
        'fondo_rotulo',
        'archivo',
        'isActive',
    ];
    public function productos()
    {
        return $this->hasMany(ProductoCatalogo::class);
    }
}
