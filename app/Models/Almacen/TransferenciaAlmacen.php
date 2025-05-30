<?php

namespace App\Models\Almacen;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferenciaAlmacen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transferencias_almacen';

    protected $fillable = [
        'code',
        'almacen_origen_id',
        'almacen_destino_id',
        'productos',
        'fecha_transferencia',
        'estado',
        'observaciones',
        'usuario_id',
        'fecha_confirmacion',
        'motivo_transferencia'
    ];

    protected $casts = [
        'productos' => 'array',
        'fecha_transferencia' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'estado' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relaciones
    public function almacenOrigen()
    {
        return $this->belongsTo(WarehouseAlmacen::class, 'almacen_origen_id');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(WarehouseAlmacen::class, 'almacen_destino_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Métodos
    /**
     * Completa la transferencia y actualiza la fecha de confirmación
     * @return bool True si se completó correctamente
     */
    public function completar()
    {
        if ($this->estado === 'pendiente' || $this->estado === 'en_transito') {
            $this->estado = 'completada';
            $this->fecha_confirmacion = now();
            return $this->save();
        }
        return false;
    }

    /**
     * Cancela la transferencia si no está completada
     * @return bool True si se canceló correctamente
     */
    public function cancelar()
    {
        if ($this->estado !== 'completada') {
            $this->estado = 'cancelada';
            return $this->save();
        }
        return false;
    }

    /**
     * Inicia la transferencia cambiando su estado a en tránsito
     * @return bool True si se inició correctamente
     */
    public function iniciarTransferencia()
    {
        if ($this->estado === 'pendiente') {
            $this->estado = 'en_transito';
            return $this->save();
        }
        return false;
    }

    /**
     * Verifica si la transferencia puede ser completada
     * @return bool True si puede ser completada
     */
    public function puedeCompletarse()
    {
        return in_array($this->estado, ['pendiente', 'en_transito']);
    }

    /**
     * Obtiene las transferencias pendientes
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias pendientes
     */
    public function getTransferenciasPendientes()
    {
        return $this->where('estado', 'pendiente')->get();
    }

    /**
     * Obtiene las transferencias completadas
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias completadas
     */
    public function getTransferenciasCompletadas()
    {
        return $this->where('estado', 'completada')->get();
    }

    /**
     * Obtiene las transferencias por almacén de origen
     * @param int $almacenId ID del almacén de origen
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias
     */
    public function getTransferenciasPorAlmacenOrigen($almacenId)
    {
        return $this->where('almacen_origen_id', $almacenId)->get();
    }

    /**
     * Obtiene las transferencias por almacén de destino
     * @param int $almacenId ID del almacén de destino
     * @return \Illuminate\Database\Eloquent\Collection Colección de transferencias
     */
    public function getTransferenciasPorAlmacenDestino($almacenId)
    {
        return $this->where('almacen_destino_id', $almacenId)->get();
    }
}
