<?php

namespace App\Livewire\Catalogo;

use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use Livewire\Component;
use Livewire\WithPagination;

class ProductoCatalogoTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $brand_id = '';
    public $category_id = '';
    public $line_id = '';
    public $min_price = '';
    public $max_price = '';
    public $stock_status = '';
    public $isActive = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'code'],
        'sortDirection' => ['except' => 'asc'],
        'brand_id' => ['except' => ''],
        'category_id' => ['except' => ''],
        'line_id' => ['except' => ''],
        'min_price' => ['except' => ''],
        'max_price' => ['except' => ''],
        'stock_status' => ['except' => ''],
        'isActive' => ['except' => ''],
    ];

    public function updatingSearch()
    {
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

    public function clearFilters()
    {
        $this->reset([
            'brand_id',
            'category_id',
            'line_id',
            'min_price',
            'max_price',
            'stock_status',
            'isActive'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = ProductoCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('code_fabrica', 'like', '%' . $this->search . '%')
                        ->orWhere('code_peru', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->brand_id, function ($query) {
                $query->where('brand_id', $this->brand_id);
            })
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->when($this->line_id, function ($query) {
                $query->where('line_id', $this->line_id);
            })
            ->when($this->min_price, function ($query) {
                $query->where('price_venta', '>=', $this->min_price);
            })
            ->when($this->max_price, function ($query) {
                $query->where('price_venta', '<=', $this->max_price);
            })
            ->when($this->stock_status, function ($query) {
                if ($this->stock_status === 'in_stock') {
                    $query->where('stock', '>', 0);
                } elseif ($this->stock_status === 'out_of_stock') {
                    $query->where('stock', '=', 0);
                }
            })
            ->when($this->isActive !== '', function ($query) {
                $query->where('isActive', $this->isActive);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.catalogo.producto-catalogo-table', [
            'productos' => $query->paginate($this->perPage),
            'brands' => BrandCatalogo::where('isActive', true)->get(),
            'categories' => CategoryCatalogo::where('isActive', true)->get(),
            'lines' => LineCatalogo::where('isActive', true)->get(),
        ]);
    }
}
