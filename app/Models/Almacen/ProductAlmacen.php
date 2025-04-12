<?php

namespace App\Models\Almacen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAlmacen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unique_entry_code'
    ];

    public function productCodes(): HasMany
    {
        return $this->hasMany(ProductCode::class);
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }
}
