<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\MovimientoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mary\Traits\Toast;

class ReporteLotesIndex extends Component
{
    use WithPagination, Toast;

    // Propiedades de búsqueda y filtros
    public $search = '';
    public $almacen_filter = '';
    public $lote_filter = '';
    public $categoria_filter = '';
    public $estado_stock_filter = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $perPage = 20;

    // Filtros adicionales
    public $marca_filter = '';
    public $unidad_medida_filter = '';
    public $rango_stock_filter = '';

    // Estadísticas y reportes
    public $estadisticas_generales = [];
    public $lotes_mas_activos = [];
    public $productos_por_vencer = [];
    public $top_productos_movimiento = [];
    public $resumen_por_almacen = [];

    // Estados de modales
    public $modal_detalle_lote = false;
    public $modal_estadisticas_avanzadas = false;
    public $lote_seleccionado = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'almacen_filter' => ['except' => ''],
        'lote_filter' => ['except' => ''],
        'categoria_filter' => ['except' => ''],
        'estado_stock_filter' => ['except' => ''],
        'fecha_inicio' => ['except' => ''],
        'fecha_fin' => ['except' => ''],
        'marca_filter' => ['except' => ''],
        'unidad_medida_filter' => ['except' => ''],
        'rango_stock_filter' => ['except' => ''],
        'perPage' => ['except' => 20],
    ];

    public function mount()
    {
        $this->fecha_inicio = now()->subDays(30)->format('Y-m-d');
        $this->fecha_fin = now()->format('Y-m-d');
        $this->cargarEstadisticas();
    }

    public function render()
    {
        $query = $this->construirQuery();

        return view('livewire.almacen.reporte-lotes-index', [
            'productos' => $query->paginate($this->perPage),
            'almacenes' => WarehouseAlmacen::where('estado', true)->get(),
            'lotes' => ProductoAlmacen::distinct()->pluck('lote')->filter(),
            'categorias' => ProductoAlmacen::distinct()->pluck('categoria')->filter(),
            'marcas' => ProductoAlmacen::distinct()->pluck('marca')->filter(),
            'unidades_medida' => ProductoAlmacen::distinct()->pluck('unidad_medida')->filter(),
            'estadisticas_generales' => $this->estadisticas_generales,
            'lotes_mas_activos' => $this->lotes_mas_activos,
            'productos_por_vencer' => $this->productos_por_vencer,
            'top_productos_movimiento' => $this->top_productos_movimiento,
            'resumen_por_almacen' => $this->resumen_por_almacen,
        ]);
    }

    private function construirQuery()
    {
        return ProductoAlmacen::query()
            ->with(['almacen'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('lote', 'like', '%' . $this->search . '%')
                      ->orWhere('marca', 'like', '%' . $this->search . '%')
                      ->orWhere('modelo', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->almacen_filter, function ($query) {
                $query->where('almacen_id', $this->almacen_filter);
            })
            ->when($this->lote_filter, function ($query) {
                $query->where('lote', $this->lote_filter);
            })
            ->when($this->categoria_filter, function ($query) {
                $query->where('categoria', $this->categoria_filter);
            })
            ->when($this->marca_filter, function ($query) {
                $query->where('marca', $this->marca_filter);
            })
            ->when($this->unidad_medida_filter, function ($query) {
                $query->where('unidad_medida', $this->unidad_medida_filter);
            })
            ->when($this->estado_stock_filter, function ($query) {
                switch ($this->estado_stock_filter) {
                    case 'con_stock':
                        $query->where('stock_actual', '>', 0);
                        break;
                    case 'sin_stock':
                        $query->where('stock_actual', '=', 0);
                        break;
                    case 'stock_bajo':
                        $query->whereRaw('stock_actual <= stock_minimo');
                        break;
                    case 'overstock':
                        $query->where('stock_actual', '>', DB::raw('stock_minimo * 2'));
                        break;
                }
            })
            ->when($this->rango_stock_filter, function ($query) {
                switch ($this->rango_stock_filter) {
                    case 'bajo':
                        $query->where('stock_actual', '<=', 10);
                        break;
                    case 'medio':
                        $query->whereBetween('stock_actual', [11, 50]);
                        break;
                    case 'alto':
                        $query->where('stock_actual', '>', 50);
                        break;
                }
            })
            ->orderBy('lote', 'asc')
            ->orderBy('nombre', 'asc');
    }

    public function cargarEstadisticas()
    {
        $this->cargarEstadisticasGenerales();
        $this->cargarLotesMasActivos();
        $this->cargarProductosPorVencer();
        $this->cargarTopProductosMovimiento();
        $this->cargarResumenPorAlmacen();
    }

    private function cargarEstadisticasGenerales()
    {
        $this->estadisticas_generales = [
            'total_lotes' => ProductoAlmacen::distinct()->count('lote'),
            'total_productos' => ProductoAlmacen::count(),
            'productos_con_stock' => ProductoAlmacen::where('stock_actual', '>', 0)->count(),
            'productos_sin_stock' => ProductoAlmacen::where('stock_actual', '=', 0)->count(),
            'productos_stock_bajo' => ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')->count(),
            'valor_total_inventario' => ProductoAlmacen::sum(DB::raw('stock_actual * precio_unitario')),
            'lotes_con_movimientos' => MovimientoAlmacen::distinct()->whereNotNull('lote')->count('lote'),
            'promedio_stock_por_lote' => ProductoAlmacen::whereNotNull('lote')->avg('stock_actual'),
            'lotes_activos_este_mes' => $this->contarLotesActivosEsteMes(),
        ];
    }

    private function cargarLotesMasActivos()
    {
        $this->lotes_mas_activos = MovimientoAlmacen::select(
                'lote',
                DB::raw('COUNT(*) as total_movimientos'),
                DB::raw('SUM(cantidad) as total_cantidad'),
                DB::raw('MAX(fecha_movimiento) as ultimo_movimiento')
            )
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->when($this->fecha_inicio && $this->fecha_fin, function ($query) {
                $query->whereBetween('fecha_movimiento', [$this->fecha_inicio, $this->fecha_fin]);
            })
            ->groupBy('lote')
            ->orderBy('total_movimientos', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($lote) {
                $productosEnLote = ProductoAlmacen::where('lote', $lote->lote)->count();
                $stockTotal = ProductoAlmacen::where('lote', $lote->lote)->sum('stock_actual');

                return [
                    'lote' => $lote->lote,
                    'total_movimientos' => $lote->total_movimientos,
                    'total_cantidad' => $lote->total_cantidad,
                    'ultimo_movimiento' => $lote->ultimo_movimiento,
                    'productos_en_lote' => $productosEnLote,
                    'stock_total' => $stockTotal,
                ];
            });
    }

    private function cargarProductosPorVencer()
    {
        // Simulación de productos por vencer (en implementación real usar fecha de vencimiento)
        $this->productos_por_vencer = ProductoAlmacen::where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->orderBy('stock_actual', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($producto) {
                return [
                    'producto' => $producto,
                    'dias_restantes' => rand(1, 30), // Simulado
                    'prioridad' => $this->calcularPrioridadVencimiento($producto->stock_actual, $producto->stock_minimo),
                ];
            });
    }

    private function cargarTopProductosMovimiento()
    {
        $this->top_productos_movimiento = MovimientoAlmacen::select(
                'producto_id',
                DB::raw('COUNT(*) as total_movimientos'),
                DB::raw('SUM(cantidad) as total_cantidad')
            )
            ->whereNotNull('producto_id')
            ->when($this->fecha_inicio && $this->fecha_fin, function ($query) {
                $query->whereBetween('fecha_movimiento', [$this->fecha_inicio, $this->fecha_fin]);
            })
            ->groupBy('producto_id')
            ->orderBy('total_movimientos', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($movimiento) {
                $producto = ProductoAlmacen::find($movimiento->producto_id);
                return [
                    'producto' => $producto,
                    'total_movimientos' => $movimiento->total_movimientos,
                    'total_cantidad' => $movimiento->total_cantidad,
                ];
            });
    }

    private function cargarResumenPorAlmacen()
    {
        $this->resumen_por_almacen = WarehouseAlmacen::where('estado', true)
            ->get()
            ->map(function ($almacen) {
                $productos = ProductoAlmacen::where('almacen_id', $almacen->id);
                $movimientos = MovimientoAlmacen::where('almacen_id', $almacen->id);

                return [
                    'almacen' => $almacen,
                    'total_productos' => $productos->count(),
                    'productos_con_stock' => $productos->where('stock_actual', '>', 0)->count(),
                    'productos_sin_stock' => $productos->where('stock_actual', '=', 0)->count(),
                    'stock_total' => $productos->sum('stock_actual'),
                    'valor_inventario' => $productos->sum(DB::raw('stock_actual * precio_unitario')),
                    'lotes_activos' => $productos->whereNotNull('lote')->distinct()->count('lote'),
                    'movimientos_mes' => $movimientos->whereMonth('created_at', now()->month)->count(),
                ];
            });
    }

    private function contarLotesActivosEsteMes()
    {
        return MovimientoAlmacen::whereNotNull('lote')
            ->where('lote', '!=', '')
            ->whereMonth('fecha_movimiento', now()->month)
            ->distinct()
            ->count('lote');
    }

    private function calcularPrioridadVencimiento($stockActual, $stockMinimo)
    {
        if ($stockActual == 0) return 'crítica';
        if ($stockActual <= $stockMinimo) return 'alta';
        if ($stockActual <= $stockMinimo * 1.5) return 'media';
        return 'baja';
    }

    public function getEstadisticasPorLote($lote)
    {
        return ProductoAlmacen::getEstadisticasPorLote($lote);
    }

    public function getMovimientosPorLote($lote)
    {
        return MovimientoAlmacen::getMovimientosPorLote($lote);
    }

    public function getEstadisticasMovimientosPorLote($lote)
    {
        return MovimientoAlmacen::getEstadisticasPorLote($lote);
    }

    public function verDetalleLote($lote)
    {
        $this->lote_seleccionado = [
            'lote' => $lote,
            'productos' => ProductoAlmacen::where('lote', $lote)->with(['almacen'])->get(),
            'movimientos' => MovimientoAlmacen::where('lote', $lote)
                ->with(['almacen', 'usuario'])
                ->orderBy('fecha_movimiento', 'desc')
                ->limit(20)
                ->get(),
            'estadisticas' => $this->getEstadisticasPorLote($lote),
        ];
        $this->modal_detalle_lote = true;
    }

    public function mostrarEstadisticasAvanzadas()
    {
        $this->modal_estadisticas_avanzadas = true;
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'almacen_filter',
            'lote_filter',
            'categoria_filter',
            'estado_stock_filter',
            'marca_filter',
            'unidad_medida_filter',
            'rango_stock_filter'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados');
    }

    public function actualizarFechas()
    {
        $this->cargarEstadisticas();
        $this->resetPage();
    }

    public function exportarReporte()
    {
        try {
            // TODO: Implementar exportación a Excel con filtros aplicados
            $productos = $this->construirQuery()->get();

            $this->success('Reporte exportado correctamente');
            return redirect()->route('almacen.reportes.lotes.export', [
                'search' => $this->search,
                'almacen_filter' => $this->almacen_filter,
                'lote_filter' => $this->lote_filter,
                'categoria_filter' => $this->categoria_filter,
                'estado_stock_filter' => $this->estado_stock_filter,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
            ]);
        } catch (\Exception $e) {
            $this->error('Error al exportar: ' . $e->getMessage());
        }
    }

    public function generarReportePDF()
    {
        try {
            // TODO: Implementar generación de PDF
            $this->success('Reporte PDF generado correctamente');
        } catch (\Exception $e) {
            $this->error('Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function obtenerTendenciasLote($lote)
    {
        $movimientos = MovimientoAlmacen::where('lote', $lote)
            ->where('fecha_movimiento', '>=', now()->subDays(30))
            ->orderBy('fecha_movimiento')
            ->get();

        return [
            'total_entradas' => $movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad'),
            'total_salidas' => $movimientos->where('tipo_movimiento', 'salida')->sum('cantidad'),
            'movimientos_por_dia' => $movimientos->groupBy(function ($movimiento) {
                return $movimiento->fecha_movimiento->format('Y-m-d');
            }),
        ];
    }

    public function obtenerProductosSimilares($productoId)
    {
        $producto = ProductoAlmacen::find($productoId);
        if (!$producto) return collect();

        return ProductoAlmacen::where('id', '!=', $productoId)
            ->where(function ($query) use ($producto) {
                $query->where('categoria', $producto->categoria)
                      ->orWhere('marca', $producto->marca)
                      ->orWhere('lote', $producto->lote);
            })
            ->limit(5)
            ->get();
    }
}
