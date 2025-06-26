<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <flux:heading size="lg">Catálogo de Productos</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">Administra y consulta los productos registrados
                    en el sistema.</flux:text>
            </div>
            <div class="flex items-center justify-end gap-4 w-full md:w-auto">
                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar productos..." wire:model.live="search"
                        icon="magnifying-glass" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button wire:click="exportarProductos" icon="arrow-down-tray">
                        Exportar
                    </flux:button>
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="nuevoProducto" icon="plus">
                        Nuevo Producto
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Productos</p>
                    <p class="text-2xl font-bold">{{ $estadisticas['total'] }}</p>
                </div>
                <flux:icon name="cube" class="w-8 h-8 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Activos</p>
                    <p class="text-2xl font-bold">{{ $estadisticas['activos'] }}</p>
                </div>
                <flux:icon name="check-circle" class="w-8 h-8 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Inactivos</p>
                    <p class="text-2xl font-bold">{{ $estadisticas['inactivos'] }}</p>
                </div>
                <flux:icon name="x-circle" class="w-8 h-8 opacity-80" />
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-zinc-500 to-zinc-700 rounded-lg p-4 text-white col-span-1 md:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Sin Stock</p>
                    <p class="text-2xl font-bold">{{ $estadisticas['sin_stock'] }}</p>
                </div>
                <flux:icon name="minus-circle" class="w-8 h-8 opacity-80" />
            </div>
        </div>

    </div>

    <!-- Filtros Avanzados -->

    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Marca -->
            <div>
                <flux:label>Marca</flux:label>
                <flux:select wire:model.live="brand_filter" class="w-full">
                    <option value="">Todas las marcas</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Categoría -->
            <div>
                <flux:label>Categoría</flux:label>
                <flux:select wire:model.live="category_filter" class="w-full">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Línea -->
            <div>
                <flux:label>Línea</flux:label>
                <flux:select wire:model.live="line_filter" class="w-full">
                    <option value="">Todas las líneas</option>
                    @foreach ($lines as $line)
                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <!-- Estado de Stock -->
            <div>
                <flux:label>Estado de Stock</flux:label>
                <flux:select wire:model.live="stock_status" class="w-full">
                    <option value="">Todos</option>
                    <option value="in_stock">En Stock</option>
                    <option value="out_of_stock">Sin Stock</option>
                </flux:select>
            </div>
            <!-- Rango de Precio -->
            <div>
                <flux:label>Rango de Precio</flux:label>
                <flux:select wire:model.live="price_range" class="w-full">
                    <option value="">Todos los precios</option>
                    <option value="low">Hasta S/ 100</option>
                    <option value="medium">S/ 100 - S/ 500</option>
                    <option value="high">Más de S/ 500</option>
                </flux:select>
            </div>
            <!-- Estado -->
            <div>
                <flux:label>Estado</flux:label>
                <flux:select wire:model.live="isActive_filter" class="w-full">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </flux:select>
            </div>
            <!-- Registros por página -->
            <div>
                <flux:label>Registros por página</flux:label>
                <flux:select wire:model.live="perPage" class="w-full">
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
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-1">
                                <span>Código</span>
                                <flux:icon
                                    name="{{ $sortField === 'code' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Imagen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('description')">
                            <div class="flex items-center space-x-1">
                                <span>Descripción</span>
                                <flux:icon
                                    name="{{ $sortField === 'description' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Categorización</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('stock')">
                            <div class="flex items-center space-x-1">
                                <span>Stock</span>
                                <flux:icon
                                    name="{{ $sortField === 'stock' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Documentos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('price_venta')">
                            <div class="flex items-center space-x-1">
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
                                <div class="relative group">
                                    <img src="{{ $producto->image ? asset('storage/' . $producto->image) : 'https://placehold.co/600x400/e2e8f0/64748b?text=Sin+Imagen' }}"
                                        alt="Imagen del producto"
                                        class="w-16 h-16 rounded-lg object-cover border-2 border-zinc-200 dark:border-zinc-600 hover:border-blue-300 transition-colors">
                                    @if ($producto->image)
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <flux:icon name="eye" class="w-6 h-6 text-white" />
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
                            <flux:label>Código</flux:label>
                            <flux:input type="text" wire:model.live="code" placeholder="Ej: P-001" />
                        </div>
                        <div>
                            <flux:label>Código de Fábrica</flux:label>
                            <flux:input type="text" wire:model.live="code_fabrica" placeholder="Ej: FAB-123" />
                        </div>
                        <div>
                            <flux:label>Código de Perú</flux:label>
                            <flux:input type="text" wire:model.live="code_peru" placeholder="Ej: PER-456" />
                        </div>
                        <div  class="md:col-span-2">
                            <flux:label>Descripción</flux:label>
                            <flux:textarea wire:model.live="description" rows="3"
                                placeholder="Descripción breve del producto" />
                        </div>
                        <div>
                            <flux:label>Garantía</flux:label>
                            <flux:input type="text" wire:model.live="garantia" placeholder="Ej: 1 año" />
                        </div>
                        <div>
                            <flux:label>Días de Entrega</flux:label>
                            <flux:input type="number" wire:model.live="dias_entrega" placeholder="Ej: 7" />
                        </div>
                        <div class="md:col-span-2">
                            <flux:label>Observaciones</flux:label>
                            <flux:textarea wire:model.live="observaciones" rows="2"
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
                        </div>
                        <div>
                            <flux:label>Categoría</flux:label>
                            <flux:select wire:model.live="category_id">
                                <option value="">Seleccione una categoría</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div>
                            <flux:label>Línea</flux:label>
                            <flux:select wire:model.live="line_id">
                                <option value="">Seleccione una línea</option>
                                @foreach ($lines as $line)
                                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                                @endforeach
                            </flux:select>
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
                            <flux:label>Precio de Compra</flux:label>
                            <flux:input type="number" step="0.01" min="0" wire:model.live="price_compra"
                                placeholder="Ej: 100.00" />
                        </div>
                        <div>
                            <flux:label>Precio de Venta</flux:label>
                            <flux:input type="number" step="0.01" min="0" wire:model.live="price_venta"
                                placeholder="Ej: 150.00" />
                        </div>
                        <div>
                            <flux:label>Stock</flux:label>
                            <flux:input type="number" min="0" wire:model.live="stock"
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
                                <flux:input type="text" wire:model.live="caracteristicas.{{ $i }}.key"
                                    placeholder="Clave" class="w-1/3" />
                                <flux:input type="text"
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

</div>
