<?php

namespace App\Exports;

use App\Models\Pc\ProductoAcuerdoMarco;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductoAcuerdoMarcoExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $productos;

    public function __construct($productos)
    {
        $this->productos = $productos;
    }

    public function collection()
    {
        return $this->productos;
    }

    public function headings(): array
    {
        return [
            'Código Acuerdo Marco',
            'RUC Proveedor',
            'Razón Social Proveedor',
            'RUC Entidad',
            'Razón Social Entidad',
            'Unidad Ejecutora',
            'Procedimiento',
            'Tipo',
            'Orden Electrónica',
            'Estado Orden Electrónica',
            'Link Documento',
            'Total Entrega',
            'Número Doc Estado',
            'Orden Física',
            'Fecha Doc Estado',
            'Fecha Estado OC',
            'Sub Total Orden',
            'IGV Orden',
            'Total Orden',
            'Orden Digital/Física',
            'Sustento Física',
            'Fecha Publicación',
            'Fecha Aceptación',
            'Usuario Create OC',
            'Acuerdo Marco',
            'Ubigeo Proveedor',
            'Dirección Proveedor',
            'Monto Documento Estado',
            'Catálogo',
            'Categoría',
            'Descripción Ficha Producto',
            'Marca Ficha Producto',
            'Número Parte',
            'Link Ficha Producto',
            'Monto Flete',
            'Número Entrega',
            'Fecha Inicio',
            'Plazo Entrega',
            'Fecha Fin',
            'Cantidad',
            'Entrega Afecto IGV',
            'Precio Unitario',
            'Sub Total',
            'IGV Entrega',
            'Total Monto'
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->cod_acuerdo_marco,
            $producto->ruc_proveedor,
            $producto->razon_proveedor,
            $producto->ruc_entidad,
            $producto->razon_entidad,
            $producto->unidad_ejecutora,
            $producto->procedimiento,
            $producto->tipo,
            $producto->orden_electronica,
            $producto->estado_orden_electronica,
            $producto->link_documento,
            $producto->total_entrega,
            $producto->num_doc_estado,
            $producto->orden_fisica,
            $producto->fecha_doc_estado,
            $producto->fecha_estado_oc,
            $producto->sub_total_orden,
            $producto->igv_orden,
            $producto->total_orden,
            $producto->orden_digital_fisica,
            $producto->sustento_fisica,
            $producto->fecha_publicacion,
            $producto->fecha_aceptacion,
            $producto->usuario_create_oc,
            $producto->acuerdo_marco,
            $producto->ubigeo_proveedor,
            $producto->direccion_proveedor,
            $producto->monto_documento_estado,
            $producto->catalogo,
            $producto->categoria,
            $producto->descripcion_ficha_producto,
            $producto->marca_ficha_producto,
            $producto->numero_parte,
            $producto->link_ficha_producto,
            $producto->monto_flete,
            $producto->numero_entrega,
            $producto->fecha_inicio,
            $producto->plazo_entrega,
            $producto->fecha_fin,
            $producto->cantidad,
            $producto->entrega_afecto_igv,
            $producto->precio_unitario,
            $producto->sub_total,
            $producto->igv_entrega,
            $producto->total_monto
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6'],
                ],
            ],
        ];
    }
}
