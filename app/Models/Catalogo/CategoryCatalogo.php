<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryCatalogo extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'logo',
        'fondo',
        'archivo',
        'isActive',
    ];

    public function products()
    {
        return $this->hasMany(ProductoCatalogo::class, 'category_id');
    }
}
