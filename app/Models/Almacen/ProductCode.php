<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'output_code'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductAlmacen::class);
    }

    public function stockExits(): HasMany
    {
        return $this->hasMany(StockExit::class);
    }
}
