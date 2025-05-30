<?php

namespace App\Exports\Almacen;

use App\Models\Almacen\TransferenciaAlmacen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransferenciaAlmacenExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transferencias;

    public function __construct($transferencias)
    {
        $this->transferencias = $transferencias;
    }

    public function collection()
    {
        return $this->transferencias;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Almacén Origen',
            'Almacén Destino',
            'Producto',
            'Cantidad',
            'Fecha Transferencia',
            'Estado',
            'Observaciones',
            'Motivo Transferencia'
        ];
    }

    public function map($transferencia): array
    {
        return [
            $transferencia->code,
            $transferencia->almacenOrigen->nombre,
            $transferencia->almacenDestino->nombre,
            $transferencia->producto->nombre,
            $transferencia->cantidad,
            $transferencia->fecha_transferencia,
            $transferencia->estado,
            $transferencia->observaciones,
            $transferencia->motivo_transferencia
        ];
    }
}
