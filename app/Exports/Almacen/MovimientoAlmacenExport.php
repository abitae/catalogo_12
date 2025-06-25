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
            'Tipo de Movimiento',
            'Almacén',
            'Tipo de Documento',
            'Número de Documento',
            'Tipo de Operación',
            'Fecha de Emisión',
            'Estado',
            'Subtotal',
            'Descuento',
            'Impuesto',
            'Total',
            'Observaciones',
            'Usuario',
            'Productos'
        ];
    }

    public function map($movimiento): array
    {
        // Formatear productos como string
        $productos = collect($movimiento->productos)->map(function($producto) {
            return "{$producto['code']} - {$producto['nombre']} (Cant: {$producto['cantidad']} {$producto['unidad_medida']}, Precio: S/ {$producto['precio']})";
        })->implode('; ');

        return [
            $movimiento->code,
            ucfirst($movimiento->tipo_movimiento),
            $movimiento->almacen->nombre,
            ucfirst($movimiento->tipo_documento),
            $movimiento->numero_documento,
            ucfirst($movimiento->tipo_operacion),
            $movimiento->fecha_emision->format('d/m/Y'),
            ucfirst($movimiento->estado),
            number_format($movimiento->subtotal, 2),
            number_format($movimiento->descuento, 2),
            number_format($movimiento->impuesto, 2),
            number_format($movimiento->total, 2),
            $movimiento->observaciones,
            $movimiento->user->name,
            $productos
        ];
    }
}
