<?php

namespace App\Exports;

use App\Models\Catalogo\ProductoCatalogo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProducCatalogoExport implements FromView
{

    public $productos;

    public function __construct($productos)
    {
        $this->productos = $productos;
    }

    public function view(): View
    {
        return view('export.excel.product', [
            'products' => $this->productos
        ]);
    }
}
