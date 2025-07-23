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
use Mary\Traits\Toast;

class DashboardLive extends Component
{
    use Toast;
    // Propiedades públicas para los gráficos y estadísticas
    public array $movimientosChart = [];
    public array $categoriasChart = [];
    public array $stockChart = [];
    public array $oportunidadesChart = [];
    public array $estadisticasCatalogo = [];
    public array $estadisticasAlmacen = [];
    public array $estadisticasCrm = [];

    public function mount()
    {
        $this->estadisticasCatalogo = $this->obtenerEstadisticasCatalogo();
        $this->estadisticasAlmacen = $this->obtenerEstadisticasAlmacen();
        $this->estadisticasCrm = $this->obtenerEstadisticasCrm();
        $this->inicializarGraficos();
    }

    public function render()
    {
        return view('livewire.shared.dashboard-live', [
            'estadisticasCatalogo' => $this->estadisticasCatalogo,
            'estadisticasAlmacen' => $this->estadisticasAlmacen,
            'estadisticasCrm' => $this->estadisticasCrm,
            'movimientosChart' => $this->movimientosChart,
            'categoriasChart' => $this->categoriasChart,
            'stockChart' => $this->stockChart,
            'oportunidadesChart' => $this->oportunidadesChart,
        ]);
    }

    // Inicializa los datos de los gráficos solo una vez
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

        // Gráfico de Productos por Categoría (ahora línea)
        $productosPorCategoria = CategoryCatalogo::select('name')
            ->selectRaw('(SELECT COUNT(*) FROM producto_catalogos WHERE producto_catalogos.category_id = category_catalogos.id AND producto_catalogos.isActive = 1) as products_count')
            ->where('isActive', true)
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();
        $this->categoriasChart = [
            'type' => 'line',
            'data' => [
                'labels' => $productosPorCategoria->pluck('name')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Productos',
                        'data' => $productosPorCategoria->pluck('products_count')->toArray(),
                        'borderColor' => 'rgb(147, 51, 234)',
                        'backgroundColor' => 'rgba(147, 51, 234, 0.1)',
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

        // Gráfico de Stock por Almacén (ahora línea)
        $stockPorAlmacen = WarehouseAlmacen::select('nombre')
            ->selectRaw('(SELECT SUM(stock_actual) FROM productos_almacen WHERE productos_almacen.almacen_id = almacenes.id AND productos_almacen.estado = 1) as productos_sum_stock_actual')
            ->where('estado', true)
            ->get();
        $this->stockChart = [
            'type' => 'line',
            'data' => [
                'labels' => $stockPorAlmacen->pluck('nombre')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Stock',
                        'data' => $stockPorAlmacen->pluck('productos_sum_stock_actual')->toArray(),
                        'borderColor' => 'rgb(34, 197, 94)',
                        'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
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

        // Gráfico de Oportunidades por Etapa (ahora línea)
        $oportunidadesPorEtapa = OpportunityCrm::select('etapa', DB::raw('count(*) as total'))
            ->groupBy('etapa')
            ->orderBy('total', 'desc')
            ->get();
        $this->oportunidadesChart = [
            'type' => 'line',
            'data' => [
                'labels' => $oportunidadesPorEtapa->pluck('etapa')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Oportunidades',
                        'data' => $oportunidadesPorEtapa->pluck('total')->toArray(),
                        'borderColor' => 'rgb(168, 85, 247)',
                        'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
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
    }

    // Métodos optimizados para obtener estadísticas
    private function obtenerEstadisticasCatalogo()
    {
        $totalProductos = ProductoCatalogo::count();
        $productosActivos = ProductoCatalogo::where('isActive', true)->count();
        $productosInactivos = $totalProductos - $productosActivos;
        $productosSinStock = ProductoCatalogo::where('stock', 0)->count();
        $productosStockBajo = ProductoCatalogo::where('stock', '<=', 5)->where('stock', '>', 0)->count();
        $valorTotalInventario = ProductoCatalogo::where('isActive', true)
            ->select(DB::raw('SUM(stock * price_venta) as total'))
            ->value('total') ?? 0;
        $productosPorCategoria = CategoryCatalogo::select('name')
            ->selectRaw('(SELECT COUNT(*) FROM producto_catalogos WHERE producto_catalogos.category_id = category_catalogos.id AND producto_catalogos.isActive = 1) as products_count')
            ->where('isActive', true)
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();
        $productosPorMarca = BrandCatalogo::select('name')
            ->selectRaw('(SELECT COUNT(*) FROM producto_catalogos WHERE producto_catalogos.brand_id = brand_catalogos.id AND producto_catalogos.isActive = 1) as products_count')
            ->where('isActive', true)
            ->orderByDesc('products_count')
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
        $valorTotalInventario = ProductoAlmacen::select(DB::raw('SUM(stock_actual * precio_unitario) as total'))->value('total') ?? 0;
        $totalAlmacenes = WarehouseAlmacen::count();
        $almacenesActivos = WarehouseAlmacen::where('estado', true)->count();
        $movimientosRecientes = MovimientoAlmacen::with(['producto', 'almacen'])
            ->orderBy('fecha_movimiento', 'desc')
            ->limit(10)
            ->get();
        $productosPorAlmacen = WarehouseAlmacen::select('nombre')
            ->selectRaw('(SELECT COUNT(*) FROM productos_almacen WHERE productos_almacen.almacen_id = almacenes.id AND productos_almacen.estado = 1) as productos_count')
            ->where('estado', true)
            ->orderByDesc('productos_count')
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
        $valorTotalOportunidades = OpportunityCrm::select(DB::raw('SUM(valor) as total'))->value('total') ?? 0;
        $valorOportunidadesAbiertas = OpportunityCrm::where('estado', 'abierta')->select(DB::raw('SUM(valor) as total'))->value('total') ?? 0;
        $totalContactos = ContactCrm::count();
        $totalClientes = Customer::count();
        $totalActividades = ActivityCrm::count();
        $oportunidadesPorEtapa = OpportunityCrm::select('etapa', DB::raw('count(*) as total'))
            ->groupBy('etapa')
            ->orderBy('total', 'desc')
            ->get();
        $oportunidadesPorMarca = MarcaCrm::select('nombre')
            ->selectRaw('(SELECT COUNT(*) FROM opportunities_crm WHERE opportunities_crm.marca_id = marcas_crm.id) as oportunidades_count')
            ->where('activo', true)
            ->orderByDesc('oportunidades_count')
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
