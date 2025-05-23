<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoCatalogo extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_id',
        'category_id',
        'line_id',
        'code',
        'code_fabrica',
        'code_peru',
        'price_compra',
        'price_venta',
        'stock',
        'dias_entrega',
        'description',
        'garantia',
        'observaciones',
        'image',
        'archivo',
        'archivo2',
        'caracteristicas',
        'isActive',
    ];
    public function category()
    {
        return $this->belongsTo(CategoryCatalogo::class);
    }
    public function brand()
    {
        return $this->belongsTo(BrandCatalogo::class);
    }
    public function line()
    {
        return $this->belongsTo(LineCatalogo::class);
    }
}
