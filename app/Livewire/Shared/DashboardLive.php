<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\MovimientoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\ContactCrm;
use App\Models\Crm\ActivityCrm;
use App\Models\Crm\MarcaCrm;
use App\Models\Shared\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DashboardLive extends Component
{
    // Propiedades públicas para los gráficos de MaryUI
    public array $movimientosChart = [];
    public array $categoriasChart = [];
    public array $stockChart = [];
    public array $oportunidadesChart = [];

    public function mount()
    {
        $this->inicializarGraficos();
    }

    public function render()
    {
        $estadisticasCatalogo = $this->obtenerEstadisticasCatalogo();
        $estadisticasAlmacen = $this->obtenerEstadisticasAlmacen();
        $estadisticasCrm = $this->obtenerEstadisticasCrm();

        return view('livewire.shared.dashboard-live', [
            'estadisticasCatalogo' => $estadisticasCatalogo,
            'estadisticasAlmacen' => $estadisticasAlmacen,
            'estadisticasCrm' => $estadisticasCrm,
        ]);
    }

    private function inicializarGraficos()
    {
        // Gráfico de Movimientos por Mes
        $meses = collect();
        for ($i = 5; $i >= 0; $i--) {
            $meses->push(Carbon::now()->subMonths($i)->format('M Y'));
        }

        $movimientosPorMes = collect();
        foreach ($meses as $mes) {
            $fecha = Carbon::createFromFormat('M Y', $mes);
            $count = MovimientoAlmacen::whereYear('fecha_movimiento', $fecha->year)
                ->whereMonth('fecha_movimiento', $fecha->month)
                ->count();
            $movimientosPorMes->push($count);
        }

        $this->movimientosChart = [
            'type' => 'line',
            'data' => [
                'labels' => $meses->toArray(),
                'datasets' => [
                    [
                        'label' => 'Movimientos',
                        'data' => $movimientosPorMes->toArray(),
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'tension' => 0.4,
                        'fill' => true
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => false
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ]
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false
                        ]
                    ]
                ]
            ]
        ];

        // Gráfico de Productos por Categoría
        $productosPorCategoria = CategoryCatalogo::withCount('products')
            ->where('isActive', true)
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        $this->categoriasChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $productosPorCategoria->pluck('name')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Productos',
                        'data' => $productosPorCategoria->pluck('products_count')->toArray(),
                        'backgroundColor' => [
                            'rgb(147, 51, 234)',
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(251, 146, 60)',
                            'rgb(239, 68, 68)'
                        ]
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom'
                    ]
                ]
            ]
        ];

        // Gráfico de Stock por Almacén
        $stockPorAlmacen = WarehouseAlmacen::where('estado', true)
            ->withSum('productos', 'stock_actual')
            ->get();

        $this->stockChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $stockPorAlmacen->pluck('nombre')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Stock',
                        'data' => $stockPorAlmacen->pluck('productos_sum_stock_actual')->toArray(),
                        'backgroundColor' => 'rgb(34, 197, 94)',
                        'borderColor' => 'rgb(34, 197, 94)',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => false
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ]
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false
                        ]
                    ]
                ]
            ]
        ];

        // Gráfico de Oportunidades por Etapa
        $oportunidadesPorEtapa = OpportunityCrm::select('etapa', DB::raw('count(*) as total'))
            ->groupBy('etapa')
            ->orderBy('total', 'desc')
            ->get();

        $this->oportunidadesChart = [
            'type' => 'pie',
            'data' => [
                'labels' => $oportunidadesPorEtapa->pluck('etapa')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Oportunidades',
                        'data' => $oportunidadesPorEtapa->pluck('total')->toArray(),
                        'backgroundColor' => [
                            'rgb(168, 85, 247)',
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(251, 146, 60)',
                            'rgb(239, 68, 68)'
                        ]
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom'
                    ]
                ]
            ]
        ];
    }

    // Método para cambiar el tipo de gráfico (ejemplo)
    public function cambiarTipoGrafico($grafico)
    {
        $tipos = ['line', 'bar', 'pie', 'doughnut'];
        $tipoActual = $this->{$grafico}['type'];
        $nuevoTipo = $tipos[(array_search($tipoActual, $tipos) + 1) % count($tipos)];

        Arr::set($this->{$grafico}, 'type', $nuevoTipo);
    }

    private function obtenerEstadisticasCatalogo()
    {
        $totalProductos = ProductoCatalogo::count();
        $productosActivos = ProductoCatalogo::where('isActive', true)->count();
        $productosInactivos = ProductoCatalogo::where('isActive', false)->count();
        $productosSinStock = ProductoCatalogo::where('stock', 0)->count();
        $productosStockBajo = ProductoCatalogo::where('stock', '<=', 5)->where('stock', '>', 0)->count();

        $valorTotalInventario = ProductoCatalogo::where('isActive', true)
            ->get()
            ->sum(function ($producto) {
                return $producto->stock * $producto->price_venta;
            });

        $productosPorCategoria = CategoryCatalogo::withCount('products')
            ->where('isActive', true)
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        $productosPorMarca = BrandCatalogo::withCount('products')
            ->where('isActive', true)
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_productos' => $totalProductos,
            'productos_activos' => $productosActivos,
            'productos_inactivos' => $productosInactivos,
            'productos_sin_stock' => $productosSinStock,
            'productos_stock_bajo' => $productosStockBajo,
            'valor_total_inventario' => $valorTotalInventario,
            'productos_por_categoria' => $productosPorCategoria,
            'productos_por_marca' => $productosPorMarca,
        ];
    }

    private function obtenerEstadisticasAlmacen()
    {
        $totalProductos = ProductoAlmacen::count();
        $productosActivos = ProductoAlmacen::where('estado', true)->count();
        $productosConStock = ProductoAlmacen::where('stock_actual', '>', 0)->count();
        $productosStockBajo = ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')->count();
        $productosAgotados = ProductoAlmacen::where('stock_actual', 0)->count();

        $valorTotalInventario = ProductoAlmacen::sum(DB::raw('stock_actual * precio_unitario'));

        $totalAlmacenes = WarehouseAlmacen::count();
        $almacenesActivos = WarehouseAlmacen::where('estado', true)->count();

        $movimientosRecientes = MovimientoAlmacen::with(['producto', 'almacen'])
            ->orderBy('fecha_movimiento', 'desc')
            ->limit(10)
            ->get();

        $productosPorAlmacen = WarehouseAlmacen::withCount('productos')
            ->where('estado', true)
            ->orderBy('productos_count', 'desc')
            ->get();

        return [
            'total_productos' => $totalProductos,
            'productos_activos' => $productosActivos,
            'productos_con_stock' => $productosConStock,
            'productos_stock_bajo' => $productosStockBajo,
            'productos_agotados' => $productosAgotados,
            'valor_total_inventario' => $valorTotalInventario,
            'total_almacenes' => $totalAlmacenes,
            'almacenes_activos' => $almacenesActivos,
            'movimientos_recientes' => $movimientosRecientes,
            'productos_por_almacen' => $productosPorAlmacen,
        ];
    }

    private function obtenerEstadisticasCrm()
    {
        $totalOportunidades = OpportunityCrm::count();
        $oportunidadesAbiertas = OpportunityCrm::where('estado', 'abierta')->count();
        $oportunidadesCerradas = OpportunityCrm::where('estado', 'cerrada')->count();
        $valorTotalOportunidades = OpportunityCrm::sum('valor');
        $valorOportunidadesAbiertas = OpportunityCrm::where('estado', 'abierta')->sum('valor');

        $totalContactos = ContactCrm::count();
        $totalClientes = Customer::count();
        $totalActividades = ActivityCrm::count();

        $oportunidadesPorEtapa = OpportunityCrm::select('etapa', DB::raw('count(*) as total'))
            ->groupBy('etapa')
            ->orderBy('total', 'desc')
            ->get();

        $oportunidadesPorMarca = MarcaCrm::withCount('oportunidades')
            ->where('activo', true)
            ->orderBy('oportunidades_count', 'desc')
            ->limit(5)
            ->get();

        $actividadesRecientes = ActivityCrm::with(['contacto', 'oportunidad'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_oportunidades' => $totalOportunidades,
            'oportunidades_abiertas' => $oportunidadesAbiertas,
            'oportunidades_cerradas' => $oportunidadesCerradas,
            'valor_total_oportunidades' => $valorTotalOportunidades,
            'valor_oportunidades_abiertas' => $valorOportunidadesAbiertas,
            'total_contactos' => $totalContactos,
            'total_clientes' => $totalClientes,
            'total_actividades' => $totalActividades,
            'oportunidades_por_etapa' => $oportunidadesPorEtapa,
            'oportunidades_por_marca' => $oportunidadesPorMarca,
            'actividades_recientes' => $actividadesRecientes,
        ];
    }
}
