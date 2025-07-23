<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">Catálogo de Productos</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">Administra y consulta los productos registrados
                    en el sistema.</flux:text>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full lg:w-auto">
                <div class="w-full sm:w-80">
                    <flux:input type="search" placeholder="Buscar productos..." wire:model.live="search"
                        icon="magnifying-glass" />
                </div>
                <div class="flex items-center gap-3">
                    <flux:button wire:click="exportarProductos" icon="arrow-down-tray">
                        Exportar
                    </flux:button>
                    <flux:button wire:click="importarProductos" icon="arrow-up-tray">
                        Importar
                    </flux:button>
                    <flux:button variant="primary" wire:click="nuevoProducto" icon="plus">
                        Nuevo
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Total Productos</p>
                    <p class="text-3xl font-bold">{{ $estadisticas['total'] }}</p>
                </div>
                <flux:icon.cube class="w-10 h-10 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Activos</p>
                    <p class="text-3xl font-bold">{{ $estadisticas['activos'] }}</p>
                </div>
                <flux:icon.check-circle class="w-10 h-10 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Inactivos</p>
                    <p class="text-3xl font-bold">{{ $estadisticas['inactivos'] }}</p>
                </div>
                <flux:icon.x-circle class="w-10 h-10 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-br from-zinc-500 to-zinc-700 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Sin Stock</p>
                    <p class="text-3xl font-bold">{{ $estadisticas['sin_stock'] }}</p>
                </div>
                <flux:icon.minus-circle class="w-10 h-10 opacity-80" />
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center gap-3 mb-4">
            <flux:icon.funnel class="w-5 h-5 text-zinc-500" />
            <flux:heading size="md" class="text-zinc-700 dark:text-zinc-300">Filtros Avanzados</flux:heading>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Marca -->
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Marca</flux:label>
                <flux:select wire:model.live="brand_filter" class="w-full mt-1">
                    <option value="">Todas las marcas</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Categoría -->
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Categoría</flux:label>
                <flux:select wire:model.live="category_filter" class="w-full mt-1">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Línea -->
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Línea</flux:label>
                <flux:select wire:model.live="line_filter" class="w-full mt-1">
                    <option value="">Todas las líneas</option>
                    @foreach ($lines as $line)
                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Estado de Stock -->
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado de Stock</flux:label>
                <flux:select wire:model.live="stock_status" class="w-full mt-1">
                    <option value="">Todos</option>
                    <option value="in_stock">En Stock</option>
                    <option value="out_of_stock">Sin Stock</option>
                </flux:select>
            </div>
            <!-- Rango de Precio -->
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Rango de Precio</flux:label>
                <flux:select wire:model.live="price_range" class="w-full mt-1">
                    <option value="">Todos los precios</option>
                    <option value="low">Hasta S/ 100</option>
                    <option value="medium">S/ 100 - S/ 500</option>
                    <option value="high">Más de S/ 500</option>
                </flux:select>
            </div>
            <!-- Estado -->
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                <flux:select wire:model.live="isActive_filter" class="w-full mt-1">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </flux:select>
            </div>
            <!-- Registros por página -->
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Registros por página
                </flux:label>
                <flux:select wire:model.live="perPage" class="w-full mt-1">
                    @foreach ([10, 25, 50, 100, 200, 500, 1000] as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
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

    <!-- Tabla de Productos -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Productos</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $productos->count() }} productos
                        encontrados</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-2">
                                <span>Código</span>
                                <flux:icon
                                    name="{{ $sortField === 'code' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Imagen
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('description')">
                            <div class="flex items-center space-x-2">
                                <span>Descripción</span>
                                <flux:icon
                                    name="{{ $sortField === 'description' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Categorización
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('stock')">
                            <div class="flex items-center space-x-2">
                                <span>Stock</span>
                                <flux:icon
                                    name="{{ $sortField === 'stock' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Documentos
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('price_venta')">
                            <div class="flex items-center space-x-2">
                                <span>Precio Venta</span>
                                <flux:icon
                                    name="{{ $sortField === 'price_venta' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($productos as $producto)
                        <tr wire:key="producto-{{ $producto->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $producto->code ?? 'N/A' }}</span>
                                    <span class="text-xs text-zinc-500">{{ $producto->code_fabrica ?? 'N/A' }}</span>
                                    <span class="text-xs text-zinc-500">{{ $producto->code_peru ?? 'N/A' }}</span>
                                    <span
                                        class="mt-1 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $producto->isActive ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="relative group w-20 h-20">
                                    <img src="{{ $producto->image ? asset('storage/' . $producto->image) : 'https://placehold.co/600x400/e2e8f0/64748b?text=Sin+Imagen' }}"
                                        alt="Imagen del producto"
                                        class="w-20 h-20 rounded-lg object-cover border-2 border-zinc-200 dark:border-zinc-600 hover:border-blue-300 transition-colors shadow-sm cursor-pointer"
                                        loading="lazy"
                                        @if ($producto->image) onclick="window.open('{{ asset('storage/' . $producto->image) }}', '_blank')" @endif>
                                    @if ($producto->image)
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 pointer-events-none">
                                            <flux:icon name="eye" class="w-7 h-7 text-white" />
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="max-w-xs">
                                    <div class="font-medium">{{ Str::limit($producto->description, 50) }}</div>
                                    @if ($producto->garantia)
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            <flux:icon name="shield-check" class="w-3 h-3 inline mr-1" />
                                            {{ $producto->garantia }}
                                        </div>
                                    @endif
                                    @if ($producto->dias_entrega > 0)
                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                            <flux:icon name="clock" class="w-3 h-3 inline mr-1" />
                                            {{ $producto->dias_entrega }} días
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1">
                                        <flux:icon name="tag" class="w-3 h-3 text-blue-500" />
                                        <span
                                            class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $producto->brand->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <flux:icon name="folder" class="w-3 h-3 text-green-500" />
                                        <span
                                            class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">{{ $producto->category->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <flux:icon name="cube" class="w-3 h-3 text-purple-500" />
                                        <span
                                            class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ $producto->line->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-medium {{ $producto->stock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $producto->stock }}
                                    </span>
                                    @if ($producto->stock > 0)
                                        <flux:icon name="check-circle" class="w-4 h-4 text-green-500" />
                                    @else
                                        <flux:icon name="x-circle" class="w-4 h-4 text-red-500" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex gap-1">
                                    @if ($producto->archivo)
                                        <a href="{{ asset('storage/' . $producto->archivo) }}" target="_blank"
                                            class="p-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                            title="Ver documento principal">
                                            <flux:icon name="document" class="w-5 h-5" />
                                        </a>
                                    @endif
                                    @if ($producto->archivo2)
                                        <a href="{{ asset('storage/' . $producto->archivo2) }}" target="_blank"
                                            class="p-1 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition-colors"
                                            title="Ver documento secundario">
                                            <flux:icon name="document-text" class="w-5 h-5" />
                                        </a>
                                    @endif
                                    @if (!$producto->archivo && !$producto->archivo2)
                                        <span class="text-zinc-400 text-xs">Sin documentos</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="space-y-1">
                                    <div class="font-medium text-green-600 dark:text-green-400">
                                        S/ {{ number_format($producto->price_venta, 2) }}
                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        Compra: S/ {{ number_format($producto->price_compra, 2) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarProducto({{ $producto->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar producto"
                                        class="hover:bg-blue-600 transition-colors"></flux:button>
                                    <flux:button wire:click="eliminarProducto({{ $producto->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar producto"
                                        class="hover:bg-red-600 transition-colors"></flux:button>
                                    <flux:button wire:click="toggleProductStatus({{ $producto->id }})" size="xs"
                                        variant="outline" icon="{{ $producto->isActive ? 'eye-slash' : 'eye' }}"
                                        title="{{ $producto->isActive ? 'Desactivar' : 'Activar' }}"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="inbox" class="w-12 h-12 text-zinc-300" />
                                    <span class="text-lg font-medium">No se encontraron productos</span>
                                    <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        @if ($productos->hasPages())
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $productos->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Producto -->
    <flux:modal wire:model="modal_form_producto" variant="flyout" class="w-2/3 max-w-4xl">
        <form wire:submit.prevent="guardarProducto">
            <div class="space-y-6">
                <!-- Cabecera -->
                <div class="border-b pb-4 mb-2 flex items-center gap-3">
                    <flux:icon name="cube" class="w-8 h-8 text-blue-500" />
                    <div>
                        <flux:heading size="lg">{{ $producto_id ? 'Editar Producto' : 'Nuevo Producto' }}
                        </flux:heading>
                        <flux:text class="mt-1 text-zinc-500">Complete los datos del producto del catálogo.</flux:text>
                    </div>
                </div>
                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Información Básica -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="information-circle" class="w-5 h-5 text-blue-400" />
                        <flux:heading size="md">Información Básica</flux:heading>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input type="text" label="Código" wire:model.live="code"
                                placeholder="Ej: P-001" />
                        </div>
                        <div>
                            <flux:input type="text" label="Código de Fábrica" wire:model.live="code_fabrica"
                                placeholder="Ej: FAB-123" />
                        </div>
                        <div>
                            <flux:input type="text" label="Código de Perú" wire:model.live="code_peru"
                                placeholder="Ej: PER-456" />
                        </div>
                        <div class="md:col-span-2">
                            <flux:textarea label="Descripción" wire:model.live="description" rows="3"
                                placeholder="Descripción breve del producto" />
                        </div>
                        <div>
                            <flux:input type="text" label="Garantía" wire:model.live="garantia"
                                placeholder="Ej: 1 año" />
                        </div>
                        <div>
                            <flux:input type="number" label="Días de Entrega" wire:model.live="dias_entrega"
                                placeholder="Ej: 7" required min="1" step="1" />
                        </div>
                        <div class="md:col-span-2">
                            <flux:textarea label="Observaciones" wire:model.live="observaciones" rows="2"
                                placeholder="Notas adicionales" />
                        </div>
                    </div>
                </div>

                <!-- Categorización -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="tag" class="w-5 h-5 text-green-400" />
                        <flux:heading size="md">Categorización</flux:heading>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <flux:label>Marca</flux:label>
                            <flux:select wire:model.live="brand_id">
                                <option value="">Seleccione una marca</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('brand_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <flux:label>Categoría</flux:label>
                            <flux:select wire:model.live="category_id">
                                <option value="">Seleccione una categoría</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('category_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <flux:label>Línea</flux:label>
                            <flux:select wire:model.live="line_id">
                                <option value="">Seleccione una línea</option>
                                @foreach ($lines as $line)
                                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('line_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Precios y Stock -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="currency-dollar" class="w-5 h-5 text-yellow-400" />
                        <flux:heading size="md">Precios y Stock</flux:heading>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <flux:input type="number" label="Precio de Compra" step="0.01" min="0"
                                wire:model.live="price_compra" placeholder="Ej: 100.00" />
                        </div>
                        <div>
                            <flux:input type="number" label="Precio de Venta" step="0.01" min="0"
                                wire:model.live="price_venta" placeholder="Ej: 150.00" />
                        </div>
                        <div>
                            <flux:input type="number" label="Stock" min="0" wire:model.live="stock"
                                placeholder="Ej: 10" />
                        </div>
                        <div class="flex items-center mt-6">
                            <flux:checkbox wire:model.live="isActive" label="Producto activo" />
                        </div>
                    </div>
                </div>

                <!-- Características -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="adjustments-horizontal" class="w-5 h-5 text-purple-400" />
                        <flux:heading size="md">Características</flux:heading>
                        <span class="text-xs text-zinc-400 ml-2">Agregue pares clave-valor para describir detalles
                            técnicos o atributos del producto.</span>
                    </div>
                    <div class="space-y-2">
                        @php
                            $caracteristicasArray = is_array($caracteristicas ?? null) ? $caracteristicas : [];
                        @endphp
                        @if (empty($caracteristicasArray))
                            <div class="text-zinc-400 text-sm">No hay características agregadas.</div>
                        @endif
                        @foreach ($caracteristicasArray as $i => $car)
                            <div class="flex gap-2 items-center">
                                <flux:input type="text" label="Clave"
                                    wire:model.live="caracteristicas.{{ $i }}.key" placeholder="Clave"
                                    class="w-1/3" />
                                <flux:input type="text" label="Valor"
                                    wire:model.live="caracteristicas.{{ $i }}.value" placeholder="Valor"
                                    class="w-1/2" />
                                <flux:button type="button" icon="trash" variant="danger" size="xs"
                                    wire:click="removeCaracteristica({{ $i }})" />
                            </div>
                        @endforeach
                        <div>
                            <flux:button type="button" icon="plus" size="sm"
                                wire:click="addCaracteristica">
                                Agregar característica
                            </flux:button>
                        </div>
                    </div>
                </div>

                <!-- Imagen -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="photo" class="w-5 h-5 text-blue-400" />
                        <flux:heading size="md">Imagen</flux:heading>
                        <span class="text-xs text-zinc-400 ml-2">Solo formatos JPG, PNG. Tamaño recomendado:
                            400x400px.</span>
                    </div>
                    <div>
                        <flux:label>Imagen del producto</flux:label>
                        <div class="mt-1">
                            @if ($imagePreview)
                                <div class="relative inline-block group">
                                    <img src="{{ $imagePreview }}" alt="Vista previa"
                                        class="w-32 h-32 rounded-lg object-cover border shadow" />
                                    <flux:button wire:click="removeImage" size="xs" variant="danger"
                                        icon="x-mark" class="absolute -top-2 -right-2" />
                                </div>
                            @endif
                            <flux:input wire:model="tempImage" type="file" accept="image/*" />
                            @error('tempImage')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="paper-clip" class="w-5 h-5 text-pink-400" />
                        <flux:heading size="md">Documentos</flux:heading>
                        <span class="text-xs text-zinc-400 ml-2">Formatos permitidos: PDF, DOC, XLS, PPT.</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:label>Documento Principal</flux:label>
                            <div class="mt-1">
                                @if ($archivoPreview)
                                    <div class="flex items-center gap-2 mb-2">
                                        <flux:icon name="document" class="w-4 h-4 text-blue-500" />
                                        <span class="text-sm">{{ $archivoPreview }}</span>
                                        <flux:button wire:click="removeArchivo" size="xs" variant="danger"
                                            icon="x-mark" />
                                    </div>
                                @endif
                                <flux:input wire:model="tempArchivo" type="file"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" />
                                @error('tempArchivo')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <flux:label>Documento Secundario</flux:label>
                            <div class="mt-1">
                                @if ($archivo2Preview)
                                    <div class="flex items-center gap-2 mb-2">
                                        <flux:icon name="document-text" class="w-4 h-4 text-green-500" />
                                        <span class="text-sm">{{ $archivo2Preview }}</span>
                                        <flux:button wire:click="removeArchivo2" size="xs" variant="danger"
                                            icon="x-mark" />
                                    </div>
                                @endif
                                <flux:input wire:model="tempArchivo2" type="file"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" />
                                @error('tempArchivo2')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Botones de acción -->
            <div class="flex justify-end gap-2 mt-8 border-t pt-4 bg-white dark:bg-zinc-900 sticky bottom-0 z-10">
                <flux:button wire:click="$set('modal_form_producto', false)">
                    Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $producto_id ? 'Actualizar' : 'Crear' }} Producto
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Confirmar Eliminación -->
    <flux:modal wire:model="modal_form_eliminar_producto" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Confirmar Eliminación</flux:heading>
                <flux:text class="mt-2">¿Estás seguro de querer eliminar este producto?</flux:text>
            </div>
            <div>
                <flux:button wire:click="eliminarProducto" variant="danger" icon="trash">Eliminar</flux:button>
                <flux:button wire:click="$set('modal_form_eliminar_producto', false)" variant="outline"
                    icon="x-circle">Cancelar</flux:button>
            </div>
        </div>
    </flux:modal>
    <!-- Modal Importar Productos -->
    <flux:modal wire:model="modal_form_importar_productos" class="w-2/3 max-w-4xl">
        <div class="space-y-6">
            <!-- Encabezado -->
            <div class="text-center">
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                    <flux:icon name="arrow-up-tray" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">Importar Productos</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                    Sube un archivo Excel con los productos que deseas importar al sistema.
                </flux:text>
            </div>

            @if (!$mostrarResultados)
                <!-- Instrucciones -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <flux:icon name="information-circle"
                            class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                        <div class="space-y-2 flex-1">
                            <div class="flex items-center justify-between">
                                <flux:heading size="sm" class="text-blue-900 dark:text-blue-100">Instrucciones de
                                    Importación</flux:heading>
                                <flux:button wire:click="descargarEjemplo" size="xs" variant="outline"
                                    icon="arrow-down-tray"
                                    class="text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-600 hover:bg-blue-100 dark:hover:bg-blue-800">
                                    Descargar Ejemplo
                                </flux:button>
                            </div>
                            <div class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                <p>• El archivo debe tener las siguientes columnas: <strong>brand</strong>,
                                    <strong>category</strong>, <strong>line</strong>, <strong>code</strong>
                                </p>
                                <p>• Las marcas, categorías y líneas deben existir previamente en el sistema</p>
                                <p>• Los códigos de producto deben ser únicos</p>
                                <p>• Formatos soportados: .xlsx, .xls (máximo 10MB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de importación -->
                <form wire:submit.prevent="procesarImportacion" class="space-y-6">
                    <!-- Área de carga de archivo -->
                    <div class="space-y-4">
                        <div>
                            <flux:label for="archivoExcel"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Seleccionar archivo Excel
                            </flux:label>
                            <div class="mt-2">
                                <div
                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-zinc-300 dark:border-zinc-600 border-dashed rounded-lg hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                                    <div class="space-y-2 text-center">
                                        <flux:icon name="document-arrow-up"
                                            class="mx-auto h-12 w-12 text-zinc-400 dark:text-zinc-500" />
                                        <div class="flex text-sm text-zinc-600 dark:text-zinc-400">
                                            <label for="archivoExcel"
                                                class="relative cursor-pointer bg-white dark:bg-zinc-800 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Subir archivo</span>
                                                <flux:input id="archivoExcel" wire:model="archivoExcel"
                                                    type="file" accept=".xlsx,.xls" class="sr-only" />
                                            </label>
                                            <p class="pl-1">o arrastrar y soltar</p>
                                        </div>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            Excel (.xlsx, .xls) hasta 10MB
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @error('archivoExcel')
                                <div class="mt-2 flex items-center gap-2 text-red-600 dark:text-red-400 text-sm">
                                    <flux:icon name="exclamation-triangle" class="w-4 h-4" />
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Vista previa del archivo -->
                        @if ($archivoExcel)
                            <div
                                class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3">
                                    <flux:icon name="check-circle"
                                        class="w-5 h-5 text-green-600 dark:text-green-400" />
                                    <div>
                                        <p class="text-sm font-medium text-green-900 dark:text-green-100">
                                            Archivo seleccionado: {{ $archivoExcel->getClientOriginalName() }}
                                        </p>
                                        <p class="text-xs text-green-700 dark:text-green-300">
                                            Tamaño: {{ number_format($archivoExcel->getSize() / 1024, 2) }} KB
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button wire:click="cancelarImportacion" variant="outline" icon="x-circle"
                            class="px-4 py-2">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary" icon="arrow-up-tray" class="px-4 py-2"
                            :disabled="!$archivoExcel">
                            <span wire:loading.remove wire:target="procesarImportacion">
                                Importar Productos
                            </span>
                            <span wire:loading wire:target="procesarImportacion" class="flex items-center gap-2">
                                <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                                Procesando...
                            </span>
                        </flux:button>
                    </div>
                </form>
            @else
                <!-- Resultados de la importación -->
                <div class="space-y-6">
                    <!-- Resumen de estadísticas -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <flux:heading size="md" class="mb-4">Resumen de Importación</flux:heading>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $importacionStats['total_rows'] ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Filas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $importacionStats['imported'] ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Importados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $importacionStats['skipped'] ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Omitidos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    {{ count($importacionErrores) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Errores</div>
                            </div>
                        </div>
                    </div>

                    <!-- Errores detallados -->
                    @if (!empty($importacionErrores))
                        <div
                            class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800">
                            <div class="flex items-center gap-2 mb-3">
                                <flux:icon name="exclamation-triangle"
                                    class="w-5 h-5 text-red-600 dark:text-red-400" />
                                <flux:heading size="sm" class="text-red-900 dark:text-red-100">Errores
                                    Encontrados</flux:heading>
                            </div>
                            <div class="max-h-40 overflow-y-auto space-y-2">
                                @foreach ($importacionErrores as $error)
                                    <div
                                        class="text-sm text-red-800 dark:text-red-200 bg-red-100 dark:bg-red-800/50 rounded p-2">
                                        {{ $error }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button wire:click="cerrarModalImportacion" variant="primary" class="px-4 py-2">
                            Cerrar
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
