<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Reporte de Lotes</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar por producto, código o lote..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarReporte" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <div class="flex items-center">
                <flux:icon name="cube" class="w-8 h-8 text-blue-500" />
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Lotes</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $estadisticas_generales['total_lotes'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
            <div class="flex items-center">
                <flux:icon name="cube" class="w-8 h-8 text-green-500" />
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Productos con Stock</p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $estadisticas_generales['productos_con_stock'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
            <div class="flex items-center">
                <flux:icon name="exclamation-triangle" class="w-8 h-8 text-yellow-500" />
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Stock Bajo</p>
                    <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $estadisticas_generales['productos_stock_bajo'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
            <div class="flex items-center">
                <flux:icon name="currency-dollar" class="w-8 h-8 text-purple-500" />
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Valor Total</p>
                    <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">S/ {{ number_format($estadisticas_generales['valor_total_inventario'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Almacén -->
            <div>
                <flux:label>Almacén</flux:label>
                <flux:select wire:model.live="almacen_filter" class="w-full">
                    <option value="">Todos los almacenes</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Lote -->
            <div>
                <flux:label>Lote</flux:label>
                <flux:select wire:model.live="lote_filter" class="w-full">
                    <option value="">Todos los lotes</option>
                    @foreach ($lotes as $lote)
                        <option value="{{ $lote }}">{{ $lote }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Categoría -->
            <div>
                <flux:label>Categoría</flux:label>
                <flux:select wire:model.live="categoria_filter" class="w-full">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria }}">{{ $categoria }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Estado de Stock -->
            <div>
                <flux:label>Estado de Stock</flux:label>
                <flux:select wire:model.live="estado_stock_filter" class="w-full">
                    <option value="">Todos</option>
                    <option value="con_stock">Con Stock</option>
                    <option value="sin_stock">Sin Stock</option>
                    <option value="stock_bajo">Stock Bajo</option>
                </flux:select>
            </div>

            <!-- Botón Limpiar Filtros -->
            <div class="flex items-end">
                <flux:button wire:click="clearFilters" color="red" icon="trash" class="w-full">
                    Limpiar Filtros
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Lotes Más Activos -->
    <div class="mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Lotes Más Activos</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($lotes_mas_activos as $lote)
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $lote['lote'] }}</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $lote['total_movimientos'] }} movimientos</p>
                            </div>
                            <flux:icon name="chart-bar" class="w-6 h-6 text-blue-500" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tabla de Productos por Lotes -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Productos por Lotes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Producto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Lote
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Almacén
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Stock
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Precio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Valor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($productos as $producto)
                        <tr wire:key="producto-{{ $producto->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div>
                                    <p class="font-medium">{{ $producto->nombre }}</p>
                                    <p class="text-xs text-zinc-500">{{ $producto->code }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $producto->lote }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $producto->almacen->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span>Actual: {{ $producto->stock_actual }}</span>
                                    <span>Mínimo: {{ $producto->stock_minimo }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                S/ {{ number_format($producto->precio_unitario, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                S/ {{ number_format($producto->stock_actual * $producto->precio_unitario, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($producto->stock_actual <= 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Sin Stock
                                    </span>
                                @elseif ($producto->stock_actual <= $producto->stock_minimo)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Stock Bajo
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Normal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
