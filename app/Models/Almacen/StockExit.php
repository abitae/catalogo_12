<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code_id',
        'warehouse_id',
        'quantity',
        'exit_date'
    ];

    protected $casts = [
        'exit_date' => 'datetime'
    ];

    public function productCode(): BelongsTo
    {
        return $this->belongsTo(ProductCode::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
