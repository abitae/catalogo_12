<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\MovimientoAlmacen;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes para Lotes
Route::middleware(['auth:sanctum'])->prefix('almacen/lotes')->group(function () {

    // Obtener todos los lotes
    Route::get('/', function () {
        $lotes = ProductoAlmacen::distinct()
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->pluck('lote')
            ->filter();

        return response()->json([
            'success' => true,
            'data' => $lotes,
            'total' => $lotes->count()
        ]);
    })->name('api.lotes.index');

    // Obtener productos por lote
    Route::get('/{lote}/productos', function ($lote) {
        $productos = ProductoAlmacen::where('lote', $lote)
            ->with(['almacen'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $productos,
            'total' => $productos->count(),
            'lote' => $lote
        ]);
    })->name('api.lotes.productos');

    // Obtener estadísticas de un lote
    Route::get('/{lote}/estadisticas', function ($lote) {
        $estadisticas = ProductoAlmacen::getEstadisticasPorLote($lote);

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
            'lote' => $lote
        ]);
    })->name('api.lotes.estadisticas');

    // Obtener movimientos de un lote
    Route::get('/{lote}/movimientos', function ($lote) {
        $movimientos = MovimientoAlmacen::where('lote', $lote)
            ->with(['producto', 'almacen', 'usuario'])
            ->orderBy('fecha_movimiento', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $movimientos,
            'total' => $movimientos->count(),
            'lote' => $lote
        ]);
    })->name('api.lotes.movimientos');

    // Verificar stock de un lote
    Route::get('/{lote}/stock', function (Request $request, $lote) {
        $almacenId = $request->get('almacen_id');
        $cantidad = $request->get('cantidad', 0);

        if ($almacenId) {
            $stockDisponible = ProductoAlmacen::getStockTotalPorLoteYAlmacen($lote, $almacenId);
            $tieneStock = ProductoAlmacen::tieneStockSuficienteEnLoteYAlmacen($lote, $almacenId, $cantidad);
        } else {
            $stockDisponible = ProductoAlmacen::getStockTotalPorLote($lote);
            $tieneStock = ProductoAlmacen::tieneStockSuficienteEnLote($lote, $cantidad);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'lote' => $lote,
                'stock_disponible' => $stockDisponible,
                'tiene_stock_suficiente' => $tieneStock,
                'almacen_id' => $almacenId,
                'cantidad_solicitada' => $cantidad
            ]
        ]);
    })->name('api.lotes.stock');

    // Obtener alertas de lotes
    Route::get('/alertas/vencimiento', function () {
        $productosPorVencer = ProductoAlmacen::where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->where('stock_actual', '<=', 10)
            ->with(['almacen'])
            ->get()
            ->map(function ($producto) {
                return [
                    'producto' => $producto,
                    'tipo' => 'vencimiento',
                    'mensaje' => "Producto {$producto->nombre} del lote {$producto->lote} tiene poco stock ({$producto->stock_actual})",
                    'severidad' => 'alta'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $productosPorVencer,
            'total' => $productosPorVencer->count()
        ]);
    })->name('api.lotes.alertas.vencimiento');

    // Obtener alertas de stock bajo
    Route::get('/alertas/stock-bajo', function () {
        $productosStockBajo = ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')
            ->where('stock_actual', '>', 0)
            ->whereNotNull('lote')
            ->with(['almacen'])
            ->get()
            ->map(function ($producto) {
                return [
                    'producto' => $producto,
                    'tipo' => 'stock_bajo',
                    'mensaje' => "Stock bajo en {$producto->nombre} del lote {$producto->lote}. Actual: {$producto->stock_actual}, Mínimo: {$producto->stock_minimo}",
                    'severidad' => 'media'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $productosStockBajo,
            'total' => $productosStockBajo->count()
        ]);
    })->name('api.lotes.alertas.stock-bajo');

    // Obtener movimientos inusuales
    Route::get('/alertas/movimientos-inusuales', function () {
        $movimientosInusuales = MovimientoAlmacen::select('lote', 'tipo_movimiento', DB::raw('SUM(cantidad) as total_cantidad'), DB::raw('COUNT(*) as total_movimientos'))
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->where('fecha_movimiento', '>=', now()->subDays(7))
            ->groupBy('lote', 'tipo_movimiento')
            ->having('total_cantidad', '>', 100)
            ->orHaving('total_movimientos', '>', 5)
            ->get()
            ->map(function ($movimiento) {
                return [
                    'lote' => $movimiento->lote,
                    'tipo' => 'movimiento_inusual',
                    'mensaje' => "Movimiento inusual en lote {$movimiento->lote}: {$movimiento->total_movimientos} movimientos de {$movimiento->total_cantidad} unidades",
                    'severidad' => 'baja'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $movimientosInusuales,
            'total' => $movimientosInusuales->count()
        ]);
    })->name('api.lotes.alertas.movimientos-inusuales');
});

// Rutas para funcionalidades de lotes
Route::prefix('lotes')->group(function () {
    // Obtener lotes disponibles en un almacén
    Route::get('/almacen/{almacenId}', function ($almacenId) {
        $lotes = \App\Models\Almacen\ProductoAlmacen::where('almacen_id', $almacenId)
            ->where('estado', true)
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->distinct()
            ->pluck('lote')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $lotes,
            'message' => 'Lotes obtenidos correctamente'
        ]);
    })->name('api.lotes.almacen');

    // Obtener productos en un lote específico
    Route::get('/{lote}/productos/{almacenId}', function ($lote, $almacenId) {
        $productos = \App\Models\Almacen\ProductoAlmacen::where('almacen_id', $almacenId)
            ->where('estado', true)
            ->where('lote', $lote)
            ->where('stock_actual', '>', 0)
            ->get(['id', 'code', 'nombre', 'stock_actual', 'unidad_medida', 'lote']);

        return response()->json([
            'success' => true,
            'data' => $productos,
            'message' => 'Productos del lote obtenidos correctamente'
        ]);
    })->name('api.lotes.productos');

    // Obtener estadísticas de un lote
    Route::get('/{lote}/estadisticas/{almacenId}', function ($lote, $almacenId) {
        $productos = \App\Models\Almacen\ProductoAlmacen::where('almacen_id', $almacenId)
            ->where('estado', true)
            ->where('lote', $lote);

        $totalProductos = $productos->count();
        $totalStock = $productos->sum('stock_actual');
        $productosBajoStock = $productos->where('stock_actual', '<=', \DB::raw('stock_minimo'))->count();

        return response()->json([
            'success' => true,
            'data' => [
                'lote' => $lote,
                'total_productos' => $totalProductos,
                'total_stock' => $totalStock,
                'productos_bajo_stock' => $productosBajoStock,
                'almacen_id' => $almacenId
            ],
            'message' => 'Estadísticas del lote obtenidas correctamente'
        ]);
    })->name('api.lotes.estadisticas');

    // Obtener movimientos de un lote
    Route::get('/{lote}/movimientos/{almacenId}', function ($lote, $almacenId) {
        $movimientos = \App\Models\Almacen\MovimientoAlmacen::where('almacen_id', $almacenId)
            ->whereJsonContains('productos', ['lote' => $lote])
            ->with(['almacen'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $movimientos,
            'message' => 'Movimientos del lote obtenidos correctamente'
        ]);
    })->name('api.lotes.movimientos');

    // Verificar stock disponible en un lote
    Route::post('/verificar-stock', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'lote' => 'required|string',
            'almacen_id' => 'required|integer|exists:almacenes,id',
            'cantidad' => 'required|numeric|min:0'
        ]);

        $stockDisponible = \App\Models\Almacen\ProductoAlmacen::getStockTotalPorLoteYAlmacen(
            $request->lote,
            $request->almacen_id
        );

        $tieneStock = $stockDisponible >= $request->cantidad;

        return response()->json([
            'success' => true,
            'data' => [
                'lote' => $request->lote,
                'almacen_id' => $request->almacen_id,
                'cantidad_solicitada' => $request->cantidad,
                'stock_disponible' => $stockDisponible,
                'tiene_stock_suficiente' => $tieneStock
            ],
            'message' => $tieneStock ? 'Stock suficiente disponible' : 'Stock insuficiente'
        ]);
    })->name('api.lotes.verificar-stock');

    // Obtener alertas de lotes
    Route::get('/alertas/{almacenId}', function ($almacenId) {
        $alertas = [];

        // Productos con stock bajo por lote
        $productosBajoStock = \App\Models\Almacen\ProductoAlmacen::where('almacen_id', $almacenId)
            ->where('estado', true)
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->where('stock_actual', '<=', \DB::raw('stock_minimo'))
            ->get()
            ->groupBy('lote');

        foreach ($productosBajoStock as $lote => $productos) {
            $alertas[] = [
                'tipo' => 'stock_bajo',
                'lote' => $lote,
                'productos' => $productos->count(),
                'stock_total' => $productos->sum('stock_actual'),
                'mensaje' => "Lote {$lote} tiene {$productos->count()} productos con stock bajo"
            ];
        }

        // Lotes sin stock
        $lotesSinStock = \App\Models\Almacen\ProductoAlmacen::where('almacen_id', $almacenId)
            ->where('estado', true)
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->where('stock_actual', 0)
            ->distinct()
            ->pluck('lote');

        foreach ($lotesSinStock as $lote) {
            $alertas[] = [
                'tipo' => 'sin_stock',
                'lote' => $lote,
                'mensaje' => "Lote {$lote} sin stock disponible"
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $alertas,
            'total_alertas' => count($alertas),
            'message' => 'Alertas de lotes obtenidas correctamente'
        ]);
    })->name('api.lotes.alertas');
});
