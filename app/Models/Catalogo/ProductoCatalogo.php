<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class ProductoCatalogo extends Model
{
    protected $fillable = [
        'brand_id',
        'category_id',
        'line_id',
        'code',
        'code_fabrica',
        'code_peru',
        'price_compra',
        'price_venta',
        'porcentaje',
        'stock',
        'dias_entrega',
        'description',
        'tipo',
        'color',
        'garantia',
        'observaciones',
        'image',
        'archivo',
        'archivo2',
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
