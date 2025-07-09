<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandCatalogo extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'logo',
        'archivo',
        'isActive',
    ];

    public function products()
    {
        return $this->hasMany(ProductoCatalogo::class, 'brand_id');
    }
}
