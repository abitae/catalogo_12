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
    protected $casts = [
        'caracteristicas' => 'array',
    ];
    public function category()
    {
        return $this->belongsTo(CategoryCatalogo::class, 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(BrandCatalogo::class, 'brand_id');
    }
    public function line()
    {
        return $this->belongsTo(LineCatalogo::class, 'line_id');
    }
    
    // Relación temporal deshabilitada hasta agregar la columna unidad_medida_id
    // public function unidadMedida()
    // {
    //     return $this->belongsTo(\App\Models\Configuration\SunatUnidadMedida::class, 'unidad_medida_id');
    // }
}
