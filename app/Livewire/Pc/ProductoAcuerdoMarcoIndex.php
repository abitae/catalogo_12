<?php

namespace App\Livewire\Pc;

use App\Models\Pc\ProductoAcuerdoMarco;
use App\Models\Pc\AcuerdoMarco;
use App\Exports\ProductoAcuerdoMarcoExport;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Maatwebsite\Excel\Facades\Excel;

class ProductoAcuerdoMarcoIndex extends Component
{
    use WithPagination, Toast;

    // Propiedades para búsqueda y filtros
    public $search = '';
    public $sortField = 'cod_acuerdo_marco';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $groupByOrdenElectronica = false;

    // Filtros específicos
    public $cod_acuerdo_marco_filter = '';

    // Estados de modales
    public $modal_detalle_producto = false;
    public $producto_id = '';

    // Propiedades para el detalle del producto
    public $producto = null;
    public $productosOrden = [];

    // Lista de códigos de acuerdo marco disponibles
    public $codigos_acuerdo_marco = [];

    public function mount()
    {
        // Obtener códigos únicos de acuerdo marco para el filtro
        $this->codigos_acuerdo_marco = ProductoAcuerdoMarco::select('cod_acuerdo_marco')
            ->distinct()
            ->orderBy('cod_acuerdo_marco')
            ->pluck('cod_acuerdo_marco')
            ->toArray();

        // Inicializar propiedades
        $this->producto = null;
        $this->productosOrden = collect();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCodAcuerdoMarcoFilter()
    {
        $this->resetPage();
    }



    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function toggleGroupByOrdenElectronica()
    {
        $this->groupByOrdenElectronica = !$this->groupByOrdenElectronica;
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function verDetalleProducto($id)
    {
        $this->producto = ProductoAcuerdoMarco::find($id);

        if (!$this->producto) {
            $this->toast('Producto no encontrado', 'error');
            return;
        }

        // Obtener todos los productos de la misma orden electrónica
        $this->productosOrden = ProductoAcuerdoMarco::where('orden_electronica', $this->producto->orden_electronica)
            ->orderBy('descripcion_ficha_producto')
            ->get();

        // Asegurar que siempre sea una colección
        if (!$this->productosOrden) {
            $this->productosOrden = collect();
        }

        $this->modal_detalle_producto = true;
    }

    public function cerrarModal()
    {
        $this->modal_detalle_producto = false;
        $this->producto = null;
        $this->productosOrden = collect();
    }

    public function limpiarFiltros()
    {
        $this->reset([
            'search',
            'cod_acuerdo_marco_filter'
        ]);
        $this->resetPage();
    }

    public function exportarProductos()
    {
        $productos = $this->getProductosQuery()->get();

        return Excel::download(
            new ProductoAcuerdoMarcoExport($productos),
            'productos_acuerdo_marco_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    private function getProductosQuery()
    {
        return ProductoAcuerdoMarco::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('cod_acuerdo_marco', 'like', '%' . $this->search . '%')
                      ->orWhere('orden_electronica', 'like', '%' . $this->search . '%')
                      ->orWhere('ruc_proveedor', 'like', '%' . $this->search . '%')
                      ->orWhere('razon_proveedor', 'like', '%' . $this->search . '%')
                      ->orWhere('ruc_entidad', 'like', '%' . $this->search . '%')
                      ->orWhere('razon_entidad', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion_ficha_producto', 'like', '%' . $this->search . '%')
                      ->orWhere('marca_ficha_producto', 'like', '%' . $this->search . '%')
                      ->orWhere('numero_parte', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->cod_acuerdo_marco_filter, function ($query) {
                $query->where('cod_acuerdo_marco', $this->cod_acuerdo_marco_filter);
            })

            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $productos = $this->getProductosQuery()->paginate($this->perPage);

        // Estadísticas
        $totalProductos = ProductoAcuerdoMarco::count();
        $totalAcuerdosMarco = ProductoAcuerdoMarco::distinct('cod_acuerdo_marco')->count('cod_acuerdo_marco');
        $totalProveedores = ProductoAcuerdoMarco::distinct('razon_proveedor')->count('razon_proveedor');

        // Agrupar productos por orden electrónica si está activado
        $productosAgrupados = null;
        if ($this->groupByOrdenElectronica && $productos && $productos->count() > 0) {
            $productosAgrupados = $productos->groupBy('orden_electronica');
        }

        return view('livewire.pc.producto-acuerdo-marco', [
            'productos' => $productos,
            'productosAgrupados' => $productosAgrupados,
            'totalProductos' => $totalProductos,
            'totalAcuerdosMarco' => $totalAcuerdosMarco,
            'totalProveedores' => $totalProveedores
        ]);
    }
}
