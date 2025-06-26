<?php

namespace App\Exports\Almacen;

use App\Models\Almacen\TransferenciaAlmacen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransferenciaAlmacenExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'Fecha Transferencia',
            'Estado',
            'Observaciones',
            'Usuario',
            'Productos y Lotes'
        ];
    }

    public function map($transferencia): array
    {
        // Formatear productos como string con información de lotes
        $productos = collect($transferencia->productos)->map(function($producto) {
            $loteInfo = isset($producto['lote']) && !empty($producto['lote']) ? " (Lote: {$producto['lote']})" : "";
            return "{$producto['code']} - {$producto['nombre']}{$loteInfo} - Cant: {$producto['cantidad']} {$producto['unidad_medida']}";
        })->implode("\n");

        return [
            $transferencia->code,
            $transferencia->almacenOrigen->nombre,
            $transferencia->almacenDestino->nombre,
            $transferencia->fecha_transferencia->format('d/m/Y H:i'),
            ucfirst($transferencia->estado),
            $transferencia->observaciones ?: 'Sin observaciones',
            $transferencia->usuario->name ?? 'N/A',
            $productos
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Código
            'B' => 25, // Almacén Origen
            'C' => 25, // Almacén Destino
            'D' => 20, // Fecha Transferencia
            'E' => 12, // Estado
            'F' => 30, // Observaciones
            'G' => 20, // Usuario
            'H' => 60, // Productos y Lotes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Emerald
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Estilo para las filas de datos
        $sheet->getStyle('A2:H' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
            ],
        ]);

        // Estilo específico para la columna de productos (wrap text)
        $sheet->getStyle('H2:H' . ($sheet->getHighestRow()))->getAlignment()->setWrapText(true);

        // Estilo para la columna de estado
        $sheet->getStyle('E2:E' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Estilo para la columna de fecha
        $sheet->getStyle('D2:D' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Bordes para toda la tabla
        $sheet->getStyle('A1:H' . ($sheet->getHighestRow()))->applyFromArray([
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
