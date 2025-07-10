<?php

namespace App\Models\Catalogo;

use App\Models\Shared\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CotizacionCatalogo extends Model
{
    /** @use HasFactory<\Database\Factories\Catalogo\CotizacionCatalogoFactory> */
    use HasFactory;

    protected $fillable = [
        'codigo_cotizacion',
        'customer_id',
        'cliente_nombre',
        'cliente_email',
        'cliente_telefono',
        'observaciones',
        'subtotal',
        'igv',
        'total',
        'estado',
        'fecha_cotizacion',
        'fecha_vencimiento',
        'validez_dias',
        'condiciones_pago',
        'condiciones_entrega',
        'user_id',
    ];

    protected $casts = [
        'fecha_cotizacion' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCotizacionCatalogo::class, 'cotizacion_id');
    }

    public function productos()
    {
        return $this->belongsToMany(ProductoCatalogo::class, 'detalle_cotizacion_catalogos', 'cotizacion_id', 'producto_id')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal', 'observaciones')
            ->withTimestamps();
    }

    public function calcularTotales()
    {
        $subtotal = $this->detalles->sum('subtotal');
        // El precio ya incluye IGV, por lo que el subtotal es realmente el total
        $total = $subtotal;
        // El IGV se calcula como el 18% del total (que ya incluye IGV)
        $igv = $total * 0.18;
        // El subtotal real (sin IGV) sería el total menos el IGV
        $subtotalSinIgv = $total - $igv;

        $this->update([
            'subtotal' => $subtotalSinIgv,
            'igv' => $igv,
            'total' => $total,
        ]);
    }

    public function generarCodigo()
    {
        $ultimaCotizacion = self::orderBy('id', 'desc')->first();
        $numero = $ultimaCotizacion ? $ultimaCotizacion->id + 1 : 1;
        return 'COT-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function getSubtotalSinIgvAttribute()
    {
        return $this->subtotal;
    }

    public function getIgvAttribute()
    {
        // Si el campo igv no existe en la base de datos, calcularlo
        if (!$this->attributes['igv'] && $this->total) {
            return $this->total * 0.18;
        }
        return $this->attributes['igv'] ?? 0;
    }
}
