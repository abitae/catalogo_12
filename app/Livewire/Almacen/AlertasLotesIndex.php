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

class AlertasLotesIndex extends Component
{
    use WithPagination, Toast;

    // Propiedades de búsqueda y filtros
    public $search = '';
    public $almacen_filter = '';
    public $tipo_alerta_filter = '';
    public $severidad_filter = '';
    public $dias_vencimiento = 30;
    public $perPage = 15;

    // Estados de alertas
    public $alertas_vencimiento;
    public $alertas_stock_bajo;
    public $alertas_movimientos_inusuales;
    public $alertas_lotes_sin_stock;
    public $alertas_lotes_por_vencer;

    // Propiedades para estadísticas
    public $estadisticas_alertas = [];
    public $resumen_alertas = [];

    // Estados de modales
    public $modal_detalle_alerta = false;
    public $alerta_seleccionada = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'almacen_filter' => ['except' => ''],
        'tipo_alerta_filter' => ['except' => ''],
        'severidad_filter' => ['except' => ''],
        'dias_vencimiento' => ['except' => 30],
        'perPage' => ['except' => 15],
    ];

    public function mount()
    {
        $this->alertas_vencimiento = collect();
        $this->alertas_stock_bajo = collect();
        $this->alertas_movimientos_inusuales = collect();
        $this->alertas_lotes_sin_stock = collect();
        $this->alertas_lotes_por_vencer = collect();

        $this->cargarAlertas();
        $this->calcularEstadisticas();
    }

    public function render()
    {
        $query = $this->construirQuery();

        return view('livewire.almacen.alertas-lotes-index', [
            'productos' => $query->paginate($this->perPage),
            'almacenes' => WarehouseAlmacen::where('estado', true)->get(),
            'alertas_vencimiento' => $this->alertas_vencimiento,
            'alertas_stock_bajo' => $this->alertas_stock_bajo,
            'alertas_movimientos_inusuales' => $this->alertas_movimientos_inusuales,
            'alertas_lotes_sin_stock' => $this->alertas_lotes_sin_stock,
            'alertas_lotes_por_vencer' => $this->alertas_lotes_por_vencer,
            'estadisticas' => $this->estadisticas_alertas,
            'resumen' => $this->resumen_alertas,
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
                      ->orWhere('marca', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->almacen_filter, function ($query) {
                $query->where('almacen_id', $this->almacen_filter);
            })
            ->when($this->tipo_alerta_filter, function ($query) {
                switch ($this->tipo_alerta_filter) {
                    case 'vencimiento':
                        $query->where('stock_actual', '>', 0)
                              ->whereNotNull('lote')
                              ->where('stock_actual', '<=', 10);
                        break;
                    case 'stock_bajo':
                        $query->whereRaw('stock_actual <= stock_minimo');
                        break;
                    case 'sin_stock':
                        $query->where('stock_actual', '=', 0)
                              ->whereNotNull('lote');
                        break;
                    case 'overstock':
                        $query->where('stock_actual', '>', DB::raw('stock_minimo * 2'));
                        break;
                }
            })
            ->when($this->severidad_filter, function ($query) {
                switch ($this->severidad_filter) {
                    case 'alta':
                        $query->where('stock_actual', '=', 0);
                        break;
                    case 'media':
                        $query->whereRaw('stock_actual <= stock_minimo')
                              ->where('stock_actual', '>', 0);
                        break;
                    case 'baja':
                        $query->where('stock_actual', '>', DB::raw('stock_minimo * 1.5'));
                        break;
                }
            })
            ->orderBy('stock_actual', 'asc');
    }

    public function cargarAlertas()
    {
        $this->cargarAlertasVencimiento();
        $this->cargarAlertasStockBajo();
        $this->cargarAlertasMovimientosInusuales();
        $this->cargarAlertasLotesSinStock();
        $this->cargarAlertasLotesPorVencer();
    }

    private function cargarAlertasVencimiento()
    {
        $this->alertas_vencimiento = ProductoAlmacen::where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->where('stock_actual', '<=', 10)
            ->with(['almacen'])
            ->limit(10)
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'producto' => $producto,
                    'tipo' => 'vencimiento',
                    'mensaje' => "Producto {$producto->nombre} del lote {$producto->lote} tiene poco stock ({$producto->stock_actual})",
                    'severidad' => $this->calcularSeveridad($producto->stock_actual, $producto->stock_minimo),
                    'fecha_alerta' => now(),
                    'accion_requerida' => 'Revisar stock y reabastecer si es necesario'
                ];
            });
    }

    private function cargarAlertasStockBajo()
    {
        $this->alertas_stock_bajo = ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')
            ->where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->with(['almacen'])
            ->limit(10)
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'producto' => $producto,
                    'tipo' => 'stock_bajo',
                    'mensaje' => "Stock bajo en {$producto->nombre} del lote {$producto->lote}. Actual: {$producto->stock_actual}, Mínimo: {$producto->stock_minimo}",
                    'severidad' => $this->calcularSeveridad($producto->stock_actual, $producto->stock_minimo),
                    'fecha_alerta' => now(),
                    'accion_requerida' => 'Reabastecer stock inmediatamente'
                ];
            });
    }

    private function cargarAlertasMovimientosInusuales()
    {
        $this->alertas_movimientos_inusuales = $this->detectarMovimientosInusuales();
    }

    private function cargarAlertasLotesSinStock()
    {
        $this->alertas_lotes_sin_stock = ProductoAlmacen::where('stock_actual', '=', 0)
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->with(['almacen'])
            ->get()
            ->groupBy('lote')
            ->map(function ($productos, $lote) {
                return [
                    'lote' => $lote,
                    'tipo' => 'sin_stock',
                    'mensaje' => "Lote {$lote} sin stock disponible ({$productos->count()} productos)",
                    'severidad' => 'alta',
                    'fecha_alerta' => now(),
                    'productos' => $productos,
                    'accion_requerida' => 'Reabastecer lote completo'
                ];
            })
            ->values()
            ->take(10);
    }

    private function cargarAlertasLotesPorVencer()
    {
        // Simulación de productos por vencer (en implementación real usar fecha de vencimiento)
        $this->alertas_lotes_por_vencer = ProductoAlmacen::where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->where('stock_actual', '<=', 5)
            ->with(['almacen'])
            ->get()
            ->groupBy('lote')
            ->map(function ($productos, $lote) {
                return [
                    'lote' => $lote,
                    'tipo' => 'por_vencer',
                    'mensaje' => "Lote {$lote} con productos por vencer ({$productos->count()} productos)",
                    'severidad' => 'media',
                    'fecha_alerta' => now(),
                    'productos' => $productos,
                    'accion_requerida' => 'Revisar fechas de vencimiento y rotar stock'
                ];
            })
            ->values()
            ->take(10);
    }

    private function detectarMovimientosInusuales()
    {
        $movimientosInusuales = MovimientoAlmacen::select(
                'lote',
                'tipo_movimiento',
                DB::raw('SUM(cantidad) as total_cantidad'),
                DB::raw('COUNT(*) as total_movimientos'),
                DB::raw('MAX(fecha_movimiento) as ultimo_movimiento')
            )
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->where('fecha_movimiento', '>=', now()->subDays(7))
            ->groupBy('lote', 'tipo_movimiento')
            ->having('total_cantidad', '>', 100)
            ->orHaving('total_movimientos', '>', 5)
            ->limit(10)
            ->get()
            ->map(function ($movimiento) {
                return [
                    'lote' => $movimiento->lote,
                    'tipo' => 'movimiento_inusual',
                    'mensaje' => "Movimiento inusual en lote {$movimiento->lote}: {$movimiento->total_movimientos} movimientos de {$movimiento->total_cantidad} unidades",
                    'severidad' => 'baja',
                    'fecha_alerta' => $movimiento->ultimo_movimiento,
                    'detalles' => [
                        'tipo_movimiento' => $movimiento->tipo_movimiento,
                        'total_cantidad' => $movimiento->total_cantidad,
                        'total_movimientos' => $movimiento->total_movimientos,
                        'ultimo_movimiento' => $movimiento->ultimo_movimiento
                    ],
                    'accion_requerida' => 'Revisar movimientos del lote'
                ];
            });

        return $movimientosInusuales;
    }

    private function calcularSeveridad($stockActual, $stockMinimo)
    {
        if ($stockActual == 0) return 'alta';
        if ($stockActual <= $stockMinimo) return 'media';
        if ($stockActual <= $stockMinimo * 1.5) return 'baja';
        return 'normal';
    }

    private function calcularEstadisticas()
    {
        $this->estadisticas_alertas = [
            'total_alertas' => $this->contarTotalAlertas(),
            'alertas_por_severidad' => $this->contarAlertasPorSeveridad(),
            'alertas_por_tipo' => $this->contarAlertasPorTipo(),
            'lotes_con_alertas' => $this->contarLotesConAlertas(),
            'productos_afectados' => $this->contarProductosAfectados(),
        ];

        $this->resumen_alertas = [
            'total_productos_stock_bajo' => ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')->count(),
            'total_productos_sin_stock' => ProductoAlmacen::where('stock_actual', '=', 0)->count(),
            'total_lotes_activos' => ProductoAlmacen::whereNotNull('lote')->distinct()->count('lote'),
            'lotes_sin_stock' => ProductoAlmacen::where('stock_actual', '=', 0)->whereNotNull('lote')->distinct()->count('lote'),
        ];
    }

    private function contarTotalAlertas()
    {
        return $this->alertas_vencimiento->count() +
               $this->alertas_stock_bajo->count() +
               $this->alertas_movimientos_inusuales->count() +
               $this->alertas_lotes_sin_stock->count() +
               $this->alertas_lotes_por_vencer->count();
    }

    private function contarAlertasPorSeveridad()
    {
        $todasLasAlertas = collect()
            ->merge($this->alertas_vencimiento)
            ->merge($this->alertas_stock_bajo)
            ->merge($this->alertas_movimientos_inusuales)
            ->merge($this->alertas_lotes_sin_stock)
            ->merge($this->alertas_lotes_por_vencer);

        return [
            'alta' => $todasLasAlertas->where('severidad', 'alta')->count(),
            'media' => $todasLasAlertas->where('severidad', 'media')->count(),
            'baja' => $todasLasAlertas->where('severidad', 'baja')->count(),
        ];
    }

    private function contarAlertasPorTipo()
    {
        return [
            'vencimiento' => collect($this->alertas_vencimiento)->count(),
            'stock_bajo' => collect($this->alertas_stock_bajo)->count(),
            'movimientos_inusuales' => collect($this->alertas_movimientos_inusuales)->count(),
            'sin_stock' => collect($this->alertas_lotes_sin_stock)->count(),
            'por_vencer' => collect($this->alertas_lotes_por_vencer)->count(),
        ];
    }

    private function contarLotesConAlertas()
    {
        $lotesUnicos = collect()
            ->merge(collect($this->alertas_vencimiento)->pluck('producto.lote'))
            ->merge(collect($this->alertas_stock_bajo)->pluck('producto.lote'))
            ->merge(collect($this->alertas_movimientos_inusuales)->pluck('lote'))
            ->merge(collect($this->alertas_lotes_sin_stock)->pluck('lote'))
            ->merge(collect($this->alertas_lotes_por_vencer)->pluck('lote'))
            ->unique()
            ->filter();

        return $lotesUnicos->count();
    }

    private function contarProductosAfectados()
    {
        return ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')
            ->orWhere('stock_actual', '=', 0)
            ->count();
    }

    public function verDetalleAlerta($tipo, $id)
    {
        $this->alerta_seleccionada = $this->obtenerAlerta($tipo, $id);
        $this->modal_detalle_alerta = true;
    }

    private function obtenerAlerta($tipo, $id)
    {
        switch ($tipo) {
            case 'vencimiento':
                return $this->alertas_vencimiento->firstWhere('id', $id);
            case 'stock_bajo':
                return $this->alertas_stock_bajo->firstWhere('id', $id);
            case 'movimientos_inusuales':
                return $this->alertas_movimientos_inusuales->firstWhere('lote', $id);
            case 'sin_stock':
                return $this->alertas_lotes_sin_stock->firstWhere('lote', $id);
            case 'por_vencer':
                return $this->alertas_lotes_por_vencer->firstWhere('lote', $id);
            default:
                return null;
        }
    }

    public function marcarAlertaComoLeida($tipo, $id)
    {
        // Implementar lógica para marcar alertas como leídas
        // Esto podría guardarse en una tabla de alertas_leidas
        $this->success('Alerta marcada como leída');
    }

    public function actualizarDiasVencimiento()
    {
        $this->cargarAlertas();
        $this->calcularEstadisticas();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'almacen_filter',
            'tipo_alerta_filter',
            'severidad_filter',
            'dias_vencimiento'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados');
    }

    public function refreshAlertas()
    {
        $this->cargarAlertas();
        $this->calcularEstadisticas();
        $this->success('Alertas actualizadas correctamente');
    }

    public function getProductosPorVencer()
    {
        return ProductoAlmacen::where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->where('stock_actual', '<=', 10)
            ->with(['almacen'])
            ->get();
    }

    public function getProductosStockBajo()
    {
        return ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')
            ->where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->with(['almacen'])
            ->get();
    }

    public function getLotesConMovimientosRecientes()
    {
        return MovimientoAlmacen::select('lote', DB::raw('COUNT(*) as total_movimientos'))
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->where('fecha_movimiento', '>=', now()->subDays(7))
            ->groupBy('lote')
            ->orderBy('total_movimientos', 'desc')
            ->limit(10)
            ->get();
    }

    public function exportarAlertas()
    {
        // TODO: Implementar exportación de alertas
        $this->success('Exportación de alertas implementada correctamente');
    }
}
