<?php

namespace App\Exports\Almacen;

use App\Models\Almacen\MovimientoAlmacen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MovimientoAlmacenExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'Subtotal (S/)',
            'Descuento (S/)',
            'Impuesto (S/)',
            'Total (S/)',
            'Observaciones',
            'Usuario',
            'Productos y Lotes'
        ];
    }

    public function map($movimiento): array
    {
        // Formatear productos como string con información de lotes
        $productos = collect($movimiento->productos)->map(function($producto) {
            $loteInfo = isset($producto['lote']) && !empty($producto['lote']) ? " (Lote: {$producto['lote']})" : "";
            return "{$producto['code']} - {$producto['nombre']}{$loteInfo} - Cant: {$producto['cantidad']} {$producto['unidad_medida']} - Precio: S/ {$producto['precio']}";
        })->implode("\n");

        return [
            $movimiento->code,
            ucfirst($movimiento->tipo_movimiento),
            $movimiento->almacen->nombre,
            ucfirst($movimiento->tipo_documento),
            $movimiento->numero_documento,
            ucfirst($movimiento->tipo_operacion),
            $movimiento->fecha_emision->format('d/m/Y H:i'),
            ucfirst($movimiento->estado),
            number_format($movimiento->subtotal, 2),
            number_format($movimiento->descuento, 2),
            number_format($movimiento->impuesto, 2),
            number_format($movimiento->total, 2),
            $movimiento->observaciones ?: 'Sin observaciones',
            $movimiento->user->name ?? 'N/A',
            $productos
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Código
            'B' => 18, // Tipo de Movimiento
            'C' => 25, // Almacén
            'D' => 20, // Tipo de Documento
            'E' => 20, // Número de Documento
            'F' => 18, // Tipo de Operación
            'G' => 18, // Fecha de Emisión
            'H' => 12, // Estado
            'I' => 15, // Subtotal
            'J' => 15, // Descuento
            'K' => 15, // Impuesto
            'L' => 15, // Total
            'M' => 30, // Observaciones
            'N' => 20, // Usuario
            'O' => 60, // Productos y Lotes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Estilo para las filas de datos
        $sheet->getStyle('A2:O' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
            ],
        ]);

        // Estilo específico para la columna de productos (wrap text)
        $sheet->getStyle('O2:O' . ($sheet->getHighestRow()))->getAlignment()->setWrapText(true);

        // Estilo para columnas numéricas
        $sheet->getStyle('I2:L' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Estilo para la columna de estado
        $sheet->getStyle('H2:H' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Bordes para toda la tabla
        $sheet->getStyle('A1:O' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        return $sheet;
    }
}
