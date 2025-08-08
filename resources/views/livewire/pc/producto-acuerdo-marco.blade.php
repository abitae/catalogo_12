<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">Administrador de Productos Acuerdo
                    Marco</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">Gestiona los productos registrados en los
                    acuerdos marco del sistema.</flux:text>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="outline" wire:click="exportarProductos" icon="arrow-down-tray">Exportar</flux:button>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Total Productos</p>
                    <p class="text-3xl font-bold">{{ $totalProductos }}</p>
                </div>
                <flux:icon name="shopping-bag" class="w-10 h-10 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Acuerdos Marco</p>
                    <p class="text-3xl font-bold">{{ $totalAcuerdosMarco }}</p>
                </div>
                <flux:icon name="document" class="w-10 h-10 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Proveedores</p>
                    <p class="text-3xl font-bold">{{ $totalProveedores }}</p>
                </div>
                <flux:icon name="building-storefront" class="w-10 h-10 opacity-80" />
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados Mejorados -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <flux:icon.funnel class="w-5 h-5 text-zinc-500" />
                <flux:heading size="md" class="text-zinc-700 dark:text-zinc-300">Filtros Avanzados</flux:heading>
            </div>
            <flux:button wire:click="limpiarFiltros" color="red" icon="trash" size="sm">
                Limpiar Filtros
            </flux:button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4">
            <!-- Búsqueda General -->
            <div class="lg:col-span-4">
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Búsqueda General</flux:label>
                <flux:input type="search" wire:model.live="search"
                    placeholder="Código, orden, RUC, proveedor, entidad, descripción..."
                    icon="magnifying-glass" class="w-full mt-1" />
            </div>
            <!-- Búsqueda Marca -->
            <div class="lg:col-span-2">
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Marca</flux:label>
                <flux:input type="search" wire:model.live="search_marca"
                    placeholder="Marca..."
                    icon="tag" class="w-full mt-1" />
            </div>
            <!-- Código Acuerdo Marco -->
            <div class="lg:col-span-2">
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Acuerdo Marco</flux:label>
                <flux:select wire:model.live="cod_acuerdo_marco_filter" class="w-full mt-1">
                    <option value="">Todos los acuerdos</option>
                    @foreach ($codigos_acuerdo_marco as $codigo)
                        <option value="{{ $codigo }}">{{ $codigo }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Fecha Publicación Desde -->
            <div class="lg:col-span-2">
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Publicación Desde</flux:label>
                <flux:input type="date" wire:model.live="fecha_publicacion_inicio" class="w-full mt-1" />
            </div>
            <!-- Fecha Publicación Hasta -->
            <div class="lg:col-span-2">
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Publicación Hasta</flux:label>
                <flux:input type="date" wire:model.live="fecha_publicacion_fin" class="w-full mt-1" />
            </div>
        </div>
    </div>

    <!-- Tabla de Productos Acuerdo Marco -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Productos Acuerdo Marco</h3>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <flux:button wire:click="toggleGroupByOrdenElectronica"
                            variant="{{ $groupByOrdenElectronica ? 'primary' : 'outline' }}" size="sm"
                            icon="view-columns">
                            {{ $groupByOrdenElectronica ? 'Desagrupar' : 'Agrupar por Orden' }}
                        </flux:button>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Mostrar:</flux:label>
                        <flux:select wire:model.live="perPage" class="w-20">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </flux:select>
                    </div>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $productos ? $productos->count() : 0 }}
                        productos encontrados</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('cod_acuerdo_marco')">
                            <div class="flex items-center space-x-2">
                                <span>Código Acuerdo</span>
                                <flux:icon
                                    name="{{ $sortField === 'cod_acuerdo_marco' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('razon_proveedor')">
                            <div class="flex items-center space-x-2">
                                <span>Proveedor</span>
                                <flux:icon
                                    name="{{ $sortField === 'razon_proveedor' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('razon_entidad')">
                            <div class="flex items-center space-x-2">
                                <span>Entidad</span>
                                <flux:icon
                                    name="{{ $sortField === 'razon_entidad' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('descripcion_ficha_producto')">
                            <div class="flex items-center space-x-2">
                                <span>Descripción</span>
                                <flux:icon
                                    name="{{ $sortField === 'descripcion_ficha_producto' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('marca_ficha_producto')">
                            <div class="flex items-center space-x-2">
                                <span>Marca</span>
                                <flux:icon
                                    name="{{ $sortField === 'marca_ficha_producto' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('fecha_publicacion')">
                            <div class="flex items-center space-x-2">
                                <span>Fecha Publicación</span>
                                <flux:icon
                                    name="{{ $sortField === 'fecha_publicacion' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('precio_unitario')">
                            <div class="flex items-center space-x-2">
                                <span>Precio Unit.</span>
                                <flux:icon
                                    name="{{ $sortField === 'precio_unitario' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @if ($groupByOrdenElectronica && $productosAgrupados)
                        @forelse ($productosAgrupados as $ordenElectronica => $productosGrupo)
                            <!-- Encabezado del grupo -->
                            <tr class="bg-blue-50 dark:bg-blue-900/20">
                                <td colspan="8" class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="document-text" class="w-5 h-5 text-blue-600" />
                                        <span class="font-semibold text-blue-800 dark:text-blue-200">Orden Electrónica:
                                            {{ $ordenElectronica }}</span>
                                        <span
                                            class="text-sm text-blue-600 dark:text-blue-300">({{ $productosGrupo ? $productosGrupo->count() : 0 }}
                                            productos)</span>
                                    </div>
                                </td>
                            </tr>
                            <!-- Productos del grupo -->
                            @foreach ($productosGrupo as $producto)
                                <tr wire:key="producto-{{ $producto->id }}"
                                    class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                        <div class="flex items-center gap-2">
                                            <flux:icon name="document" class="w-5 h-5 text-blue-500" />
                                            <span class="font-medium">{{ $producto->cod_acuerdo_marco }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                        <div class="max-w-xs truncate" title="{{ $producto->razon_proveedor }}">
                                            {{ $producto->razon_proveedor }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                        <div class="max-w-xs truncate" title="{{ $producto->razon_entidad }}">
                                            {{ $producto->razon_entidad }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                        <div class="max-w-xs truncate"
                                            title="{{ $producto->descripcion_ficha_producto }}">
                                            {{ Str::limit($producto->descripcion_ficha_producto, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                            {{ $producto->marca_ficha_producto }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                        <span class="text-zinc-600 dark:text-zinc-400">
                                            {{ $producto->fecha_publicacion ? \Carbon\Carbon::parse($producto->fecha_publicacion)->format('d/m/Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                        <span class="font-medium">S/
                                            {{ number_format($producto->precio_unitario, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <flux:button wire:click="verDetalleProducto({{ $producto->id }})"
                                                size="xs" variant="primary" icon="eye" title="Ver detalle"
                                                class="hover:bg-blue-600 transition-colors">Ver Detalle</flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                    <div class="flex flex-col items-center gap-3">
                                        <flux:icon.inbox class="w-16 h-16 text-zinc-300" />
                                        <span class="text-lg font-medium">No se encontraron productos</span>
                                        <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @else
                        @forelse ($productos as $producto)
                            <tr wire:key="producto-{{ $producto->id }}"
                                class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="document" class="w-5 h-5 text-blue-500" />
                                        <span class="font-medium">{{ $producto->cod_acuerdo_marco }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                    <div class="max-w-xs truncate" title="{{ $producto->razon_proveedor }}">
                                        {{ $producto->razon_proveedor }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                    <div class="max-w-xs truncate" title="{{ $producto->razon_entidad }}">
                                        {{ $producto->razon_entidad }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                    <div class="max-w-xs truncate"
                                        title="{{ $producto->descripcion_ficha_producto }}">
                                        {{ Str::limit($producto->descripcion_ficha_producto, 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                        {{ $producto->marca_ficha_producto }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                    <span class="text-zinc-600 dark:text-zinc-400">
                                        {{ $producto->fecha_publicacion ? \Carbon\Carbon::parse($producto->fecha_publicacion)->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                    <span class="font-medium">S/
                                        {{ number_format($producto->precio_unitario, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <flux:button wire:click="verDetalleProducto({{ $producto->id }})"
                                            size="xs" variant="primary" icon="eye" title="Ver detalle"
                                            class="hover:bg-blue-600 transition-colors">Ver Detalle</flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                    <div class="flex flex-col items-center gap-3">
                                        <flux:icon.inbox class="w-16 h-16 text-zinc-300" />
                                        <span class="text-lg font-medium">No se encontraron productos</span>
                                        <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        @if ($productos->hasPages())
            <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $productos->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detalle Producto -->
    <flux:modal wire:model="modal_detalle_producto" variant="flyout" class="w-3/4 max-w-4xl">
        @if ($producto)
            <div class="space-y-6">
                <div class="border-b pb-4 mb-2 flex items-center gap-3">
                    <flux:icon name="shopping-bag" class="w-8 h-8 text-blue-500" />
                    <div>
                        <flux:heading size="lg">Detalle del Producto</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">Información completa del producto de acuerdo marco.
                        </flux:text>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Todos los Productos de la Orden -->
                    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                        <div class="flex items-center gap-2 mb-4">
                            <flux:icon name="list-bullet" class="w-5 h-5 text-purple-400" />
                            <flux:heading size="md">Todos los Productos de la Orden:
                                {{ $producto->orden_electronica }}</flux:heading>
                            <span
                                class="text-sm text-zinc-500">({{ $productosOrden && $productosOrden->count() > 0 ? $productosOrden->count() : 0 }}
                                productos)</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-zinc-100 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">
                                            Descripción</th>
                                        <th class="px-3 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">
                                            Marca</th>
                                        <th class="px-3 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">
                                            Cantidad</th>
                                        <th class="px-3 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">
                                            Precio Unit.</th>
                                        <th class="px-3 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @if ($productosOrden && $productosOrden->count() > 0)
                                        @foreach ($productosOrden as $productoOrden)
                                            <tr
                                                class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 {{ $productoOrden->id === $producto->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                                <td class="px-3 py-2 text-zinc-900 dark:text-zinc-300">
                                                    <div class="max-w-xs truncate"
                                                        title="{{ $productoOrden->descripcion_ficha_producto }}">
                                                        {{ Str::limit($productoOrden->descripcion_ficha_producto, 40) }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 text-zinc-900 dark:text-zinc-300">
                                                    <span
                                                        class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                                        {{ $productoOrden->marca_ficha_producto }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-zinc-900 dark:text-zinc-300">
                                                    {{ $productoOrden->cantidad }}
                                                </td>
                                                <td class="px-3 py-2 text-zinc-900 dark:text-zinc-300">
                                                    S/ {{ number_format($productoOrden->precio_unitario, 2) }}
                                                </td>
                                                <td class="px-3 py-2 text-zinc-900 dark:text-zinc-300 font-medium">
                                                    S/ {{ number_format($productoOrden->total_monto, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5"
                                                class="px-3 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                                <div class="flex flex-col items-center gap-2">
                                                    <flux:icon.inbox class="w-8 h-8 text-zinc-300" />
                                                    <span class="text-sm">No hay otros productos en esta orden</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Información Económica -->
                    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                        <div class="flex items-center gap-2 mb-4">
                            <flux:icon name="banknotes" class="w-5 h-5 text-green-400" />
                            <flux:heading size="md">Información Económica</flux:heading>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <flux:label class="text-xs font-medium text-zinc-600">Precio Unitario</flux:label>
                                <flux:text class="text-lg font-bold text-green-600">S/
                                    {{ number_format($producto->precio_unitario, 2) }}</flux:text>
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium text-zinc-600">Cantidad</flux:label>
                                <flux:text class="text-lg font-bold">{{ $producto->cantidad }}</flux:text>
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium text-zinc-600">Total</flux:label>
                                <flux:text class="text-lg font-bold text-blue-600">S/
                                    {{ number_format($producto->total_monto, 2) }}</flux:text>
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium text-zinc-600">Sub Total</flux:label>
                                <flux:text class="text-sm">S/ {{ number_format($producto->sub_total, 2) }}</flux:text>
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium text-zinc-600">IGV</flux:label>
                                <flux:text class="text-sm">S/ {{ number_format($producto->igv_entrega, 2) }}
                                </flux:text>
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium text-zinc-600">Flete</flux:label>
                                <flux:text class="text-sm">S/ {{ number_format($producto->monto_flete, 2) }}
                                </flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <flux:button type="button" wire:click="cerrarModal" variant="outline">
                        Cerrar
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

</div>
