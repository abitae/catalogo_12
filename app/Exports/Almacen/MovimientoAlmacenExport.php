<?php

namespace App\Exports\Almacen;

use App\Models\Almacen\MovimientoAlmacen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MovimientoAlmacenExport implements FromCollection, WithHeadings, WithMapping
{
    protected $movimientos;

    public function __construct($movimientos)
    {
        $this->movimientos = $movimientos;
    }

    public function collection()
    {
        return $this->movimientos;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Almacén',
            'Producto',
            'Cantidad',
            'Tipo',
            'Fecha Movimiento',
            'Observaciones',
            'Motivo Movimiento'
        ];
    }

    public function map($movimiento): array
    {
        return [
            $movimiento->code,
            $movimiento->almacen->nombre,
            $movimiento->producto->nombre,
            $movimiento->cantidad,
            $movimiento->tipo,
            $movimiento->fecha_movimiento,
            $movimiento->observaciones,
            $movimiento->motivo_movimiento
        ];
    }
}
