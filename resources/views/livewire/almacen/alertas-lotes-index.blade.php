<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Alertas de Lotes</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar alertas..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:input type="number" label="Días vencimiento" wire:model.live="dias_vencimiento" min="1" max="365" class="w-32" />
                <flux:button wire:click="actualizarDiasVencimiento" icon="arrow-path">
                    Actualizar
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <!-- Tipo de Alerta -->
            <div>
                <flux:label>Tipo de Alerta</flux:label>
                <flux:select wire:model.live="tipo_alerta_filter" class="w-full">
                    <option value="">Todas las alertas</option>
                    <option value="vencimiento">Por Vencimiento</option>
                    <option value="stock_bajo">Stock Bajo</option>
                    <option value="movimiento_inusual">Movimientos Inusuales</option>
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

    <!-- Alertas de Vencimiento -->
    @if(count($alertas_vencimiento) > 0)
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-red-200 dark:border-red-800">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 flex items-center">
                    <flux:icon name="exclamation-triangle" class="w-5 h-5 mr-2" />
                    Alertas de Vencimiento ({{ count($alertas_vencimiento) }})
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach ($alertas_vencimiento as $alerta)
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-red-200 dark:border-red-800">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-red-900 dark:text-red-100">{{ $alerta['mensaje'] }}</p>
                                    <p class="text-sm text-red-600 dark:text-red-400">
                                        Almacén: {{ $alerta['producto']->almacen->nombre ?? 'N/A' }} |
                                        Fecha: {{ $alerta['fecha_alerta']->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <flux:button wire:click="marcarAlertaComoLeida('vencimiento', {{ $alerta['producto']->id }})"
                                    size="xs" variant="primary" icon="check">
                                    Marcar Leída
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de Productos con Alertas -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Productos con Alertas</h3>
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
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
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
                            <td class="px-6 py-4 text-sm">
                                @if ($producto->stock_actual <= 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Sin Stock
                                    </span>
                                @elseif ($producto->stock_actual <= $producto->stock_minimo)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Stock Bajo
                                    </span>
                                @elseif ($producto->stock_actual <= 10)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                        Poco Stock
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="marcarAlertaComoLeida('producto', {{ $producto->id }})"
                                        size="xs" variant="primary" icon="check" title="Marcar alerta como leída">
                                    </flux:button>
                                </div>
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
