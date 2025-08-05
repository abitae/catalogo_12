<?php

namespace App\Exports;

use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EjemploImportacionProductosExport implements WithMultipleSheets
{
    private $brands;
    private $categories;
    private $lines;

    public function __construct()
    {
        // Obtener datos reales del sistema con las columnas disponibles
        $this->brands = BrandCatalogo::where('isActive', true)->orderBy('name')->get(['id', 'name']);
        $this->categories = CategoryCatalogo::where('isActive', true)->orderBy('name')->get(['id', 'name']);
        $this->lines = LineCatalogo::where('isActive', true)->orderBy('name')->get(['id', 'name', 'code']);
    }

    public function sheets(): array
    {
        return [
            'Productos' => new ProductosSheet($this->brands, $this->categories, $this->lines),
            'Marcas' => new ReferenciaSheet($this->brands, 'Marcas Disponibles', 'basic'),
            'Categorías' => new ReferenciaSheet($this->categories, 'Categorías Disponibles', 'basic'),
            'Líneas' => new ReferenciaSheet($this->lines, 'Líneas Disponibles', 'lines'),
            'Instrucciones' => new InstruccionesSheet(),
        ];
    }
}

class ProductosSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    private $brands;
    private $categories;
    private $lines;

    public function __construct($brands, $categories, $lines)
    {
        $this->brands = $brands;
        $this->categories = $categories;
        $this->lines = $lines;
    }

    public function array(): array
    {
        return [
            [
                'brand' => $this->brands->first() ? $this->brands->first()->name : 'Marca Ejemplo',
                'category' => $this->categories->first() ? $this->categories->first()->name : 'Categoría Ejemplo',
                'line' => $this->lines->first() ? $this->lines->first()->name : 'Línea Ejemplo',
                'code' => 'PROD001',
                'code_fabrica' => 'FAB001',
                'code_peru' => 'PER001',
                'price_compra' => 100.00,
                'price_venta' => 150.00,
                'stock' => 50,
                'dias_entrega' => 3,
                'description' => 'Producto de ejemplo para importación',
                'garantia' => '1 año',
                'observaciones' => 'Observaciones del producto'
            ],
            [
                'brand' => $this->brands->count() > 1 ? $this->brands->get(1)->name : 'Otra Marca',
                'category' => $this->categories->count() > 1 ? $this->categories->get(1)->name : 'Otra Categoría',
                'line' => $this->lines->count() > 1 ? $this->lines->get(1)->name : 'Otra Línea',
                'code' => 'PROD002',
                'code_fabrica' => 'FAB002',
                'code_peru' => 'PER002',
                'price_compra' => 200.00,
                'price_venta' => 300.00,
                'stock' => 25,
                'dias_entrega' => 5,
                'description' => 'Segundo producto de ejemplo',
                'garantia' => '6 meses',
                'observaciones' => 'Más observaciones'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'brand',
            'category',
            'line',
            'code',
            'code_fabrica',
            'code_peru',
            'price_compra',
            'price_venta',
            'stock',
            'dias_entrega',
            'description',
            'garantia',
            'observaciones'
        ];
    }

    public function title(): string
    {
        return 'Productos';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }
}

class ReferenciaSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    private $data;
    private $title;
    private $type;

    public function __construct($data, $title, $type = 'basic')
    {
        $this->data = $data;
        $this->title = $title;
        $this->type = $type;
    }

    public function array(): array
    {
        if ($this->type === 'lines') {
            return $this->data->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code ?? ''
                ];
            })->toArray();
        } else {
            return $this->data->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })->toArray();
        }
    }

    public function headings(): array
    {
        if ($this->type === 'lines') {
            return ['id', 'name', 'code'];
        } else {
            return ['id', 'name'];
        }
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }
}

class InstruccionesSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            ['INSTRUCCIONES DE IMPORTACIÓN DE PRODUCTOS'],
            [''],
            ['📋 INFORMACIÓN GENERAL:'],
            ['• Este archivo contiene ejemplos y referencias para importar productos'],
            ['• Use la hoja "Productos" como plantilla para sus datos'],
            ['• Consulte las hojas de referencia para valores válidos'],
            [''],
            ['📝 CAMPOS REQUERIDOS:'],
            ['• brand: Nombre de la marca (debe existir en la hoja "Marcas")'],
            ['• category: Nombre de la categoría (debe existir en la hoja "Categorías")'],
            ['• line: Nombre de la línea (debe existir en la hoja "Líneas")'],
            ['• code: Código único del producto'],
            [''],
            ['📝 CAMPOS OPCIONALES:'],
            ['• code_fabrica: Código de fábrica'],
            ['• code_peru: Código Perú'],
            ['• price_compra: Precio de compra (formato: 100.50 o 1.234,56)'],
            ['• price_venta: Precio de venta (formato: 150.75 o 1.500,75)'],
            ['• stock: Cantidad en stock (número entero)'],
            ['• dias_entrega: Días de entrega (0-365)'],
            ['• description: Descripción del producto'],
            ['• garantia: Garantía del producto'],
            ['• observaciones: Observaciones adicionales'],
            [''],
            ['⚠️ IMPORTANTE:'],
            ['• Los nombres de marca, categoría y línea deben coincidir exactamente'],
            ['• Los códigos de producto deben ser únicos'],
            ['• Los precios pueden usar punto o coma como separador decimal'],
            ['• Los códigos pueden ser numéricos o alfanuméricos'],
            [''],
            ['🚀 CONSEJOS:'],
            ['• Copie y pegue los valores de las hojas de referencia'],
            ['• Use el formato de ejemplo como guía'],
            ['• Verifique que todos los campos requeridos estén completos'],
            ['• Revise los precios antes de importar'],
            [''],
            ['📞 SOPORTE:'],
            ['• Si tiene problemas, consulte la documentación del sistema'],
            ['• Los errores se mostrarán durante la importación'],
            ['• Use solo los valores de las hojas de referencia'],
        ];
    }

    public function title(): string
    {
        return 'Instrucciones';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C5504B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            3 => ['font' => ['bold' => true, 'color' => ['rgb' => '4472C4']]],
            8 => ['font' => ['bold' => true, 'color' => ['rgb' => '4472C4']]],
            15 => ['font' => ['bold' => true, 'color' => ['rgb' => '4472C4']]],
            22 => ['font' => ['bold' => true, 'color' => ['rgb' => '4472C4']]],
            28 => ['font' => ['bold' => true, 'color' => ['rgb' => '4472C4']]],
            35 => ['font' => ['bold' => true, 'color' => ['rgb' => '4472C4']]],
        ];
    }
}
