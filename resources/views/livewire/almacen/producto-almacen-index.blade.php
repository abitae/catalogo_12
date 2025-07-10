<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado con Estadísticas -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Productos</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra el inventario de productos en todos los almacenes</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button wire:click="exportarProductos" icon="arrow-down-tray">
                    Exportar
                </flux:button>
                <flux:button variant="primary" wire:click="nuevoProducto" icon="plus">
                    Nuevo Producto
                </flux:button>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Productos</p>
                        <p class="text-3xl font-bold">{{ $productos->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-400/20 rounded-full">
                        <flux:icon name="cube" class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">En Stock</p>
                        <p class="text-3xl font-bold">{{ $productos->where('stock_actual', '>', 0)->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-400/20 rounded-full">
                        <flux:icon name="check-circle" class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Stock Bajo</p>
                        <p class="text-3xl font-bold">{{ $productos->filter(fn($p) => $p->stock_actual <= $p->stock_minimo)->count() }}</p>
                    </div>
                    <div class="p-3 bg-orange-400/20 rounded-full">
                        <flux:icon name="exclamation-triangle" class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Valor Total</p>
                        <p class="text-3xl font-bold">S/ {{ number_format($productos->sum(fn($p) => $p->stock_actual * $p->precio_unitario), 2) }}</p>
                    </div>
                    <div class="p-3 bg-purple-400/20 rounded-full">
                        <flux:icon name="currency-dollar" class="w-8 h-8" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="mb-6 bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="p-6">
            <!-- Búsqueda -->
            <div class="mb-6">
                <flux:input
                    type="search"
                    placeholder="Buscar productos por código, nombre, categoría o lote..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Almacén</flux:label>
                    <flux:select wire:model.live="almacen_filter" class="w-full mt-1">
                        <option value="">Todos los almacenes</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Categoría</flux:label>
                    <flux:select wire:model.live="categoria_filter" class="w-full mt-1">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria }}">{{ $categoria }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado Stock</flux:label>
                    <flux:select wire:model.live="stock_status" class="w-full mt-1">
                        <option value="">Todos</option>
                        <option value="in_stock">En Stock</option>
                        <option value="out_of_stock">Sin Stock</option>
                        <option value="low_stock">Stock Bajo</option>
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Lote</flux:label>
                    <flux:select wire:model.live="lote_filter" class="w-full mt-1">
                        <option value="">Todos los lotes</option>
                        @foreach ($lotes as $lote)
                            <option value="{{ $lote }}">{{ $lote }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                    <flux:select wire:model.live="isActive_filter" class="w-full mt-1">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </flux:select>
                </div>

                <div class="flex items-end">
                    <flux:button wire:click="clearFilters" color="red" icon="trash" class="w-full">
                        Limpiar Filtros
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Inventario de Productos</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $productos->count() }} productos encontrados</span>
                    <flux:select wire:model.live="perPage" class="w-32">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-2">
                                <span>Producto</span>
                                @if ($sortField === 'code')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Imagen
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('nombre')">
                            <div class="flex items-center space-x-2">
                                <span>Información</span>
                                @if ($sortField === 'nombre')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Ubicación
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Stock
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Precio
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($productos as $producto)
                        <tr wire:key="producto-{{ $producto->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <flux:icon name="cube" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $producto->code }}</div>
                                        @if($producto->codes_exit && count($producto->codes_exit) > 0)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach (array_slice($producto->codes_exit, 0, 2) as $code)
                                                    <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 rounded-full">
                                                        {{ $code }}
                                                    </span>
                                                @endforeach
                                                @if(count($producto->codes_exit) > 2)
                                                    <span class="px-2 py-1 text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-full">
                                                        +{{ count($producto->codes_exit) - 2 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                         alt="Imagen del producto"
                                         class="w-16 h-16 object-cover rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-600">
                                @else
                                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center border border-zinc-200 dark:border-zinc-600">
                                        <flux:icon name="photo" class="w-8 h-8 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $producto->nombre }}</div>
                                @if($producto->descripcion)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ Str::limit($producto->descripcion, 50) }}</div>
                                @endif
                                @if($producto->lote)
                                    <div class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-1">
                                        Lote: {{ $producto->lote }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $producto->almacen->nombre ?? 'N/A' }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $producto->categoria }}</div>
                                @if($producto->marca)
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $producto->marca }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <flux:icon.cube class="w-4 h-4 text-blue-500" />
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">En almacén:</span>
                                        <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ number_format($producto->stock_actual) }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $producto->unidad_medida }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="archive-box" class="w-4 h-4 text-green-500" />
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Total:</span>
                                        @php
                                            $stock_total = App\Models\Almacen\ProductoAlmacen::where('code', $producto->code)->sum('stock_actual') ?? 0;
                                        @endphp
                                        <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ number_format($stock_total) }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $producto->unidad_medida }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon.arrow-trending-down class="w-4 h-4 text-orange-500" />
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Mín:</span>
                                        <span class="text-xs font-medium text-orange-600 dark:text-orange-400">{{ number_format($producto->stock_minimo) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">S/ {{ number_format($producto->precio_unitario, 2) }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    Total: S/ {{ number_format($producto->stock_actual * $producto->precio_unitario, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $producto->estado ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        <flux:icon name="{{ $producto->estado ? 'check-circle' : 'x-circle' }}" class="w-3 h-3 mr-1" />
                                        {{ $producto->estado ? 'Activo' : 'Inactivo' }}
                                    </span>

                                    @if($producto->stock_actual <= 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            <flux:icon name="x-circle" class="w-3 h-3 mr-1" />
                                            Sin Stock
                                        </span>
                                    @elseif($producto->stock_actual <= $producto->stock_minimo)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                            <flux:icon name="exclamation-triangle" class="w-3 h-3 mr-1" />
                                            Stock Bajo
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <flux:button
                                        wire:click="editarProducto({{ $producto->id }})"
                                        size="xs"
                                        variant="primary"
                                        icon="pencil"
                                        title="Editar producto"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button
                                        wire:click="eliminarProducto({{ $producto->id }})"
                                        size="xs"
                                        variant="danger"
                                        icon="trash"
                                        title="Eliminar producto"
                                        class="hover:bg-red-600 transition-colors">
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                        <flux:icon name="cube" class="w-8 h-8 text-zinc-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">No hay productos</h3>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-4">Comienza agregando productos a tu inventario</p>
                                    <flux:button variant="primary" wire:click="nuevoProducto" icon="plus">
                                        Agregar Producto
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if($productos->hasPages())
        <div class="mt-6">
            {{ $productos->links() }}
        </div>
    @endif

    <!-- Modal Form Producto -->
    <flux:modal wire:model="modal_form_producto" variant="flyout" class="w-2/3 max-w-6xl">
        <div class="space-y-6">
            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <flux:heading size="lg" class="flex items-center gap-2">
                    <flux:icon name="{{ $producto_id ? 'pencil' : 'plus' }}" class="w-6 h-6" />
                    {{ $producto_id ? 'Editar Producto' : 'Nuevo Producto' }}
                </flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">Complete los datos del producto para continuar.</flux:text>
            </div>

            <form wire:submit.prevent="guardarProducto">
                <!-- Información Básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <flux:select label="Almacén *" wire:model="almacen_id" required>
                            <option value="">Seleccione un almacén</option>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                            @endforeach
                        </flux:select>
                        <flux:input label="Código *" wire:model="code" placeholder="Ej: PROD-001" required />
                        <flux:input label="Nombre *" wire:model="nombre" placeholder="Ej: Producto Ejemplo" required />
                        <flux:input label="Descripción" wire:model="descripcion" placeholder="Descripción del producto" />
                    </div>
                    <div class="space-y-4">
                        <flux:input label="Categoría *" wire:model="categoria" placeholder="Ej: Electrónicos" required />
                        <flux:input label="Marca" wire:model="marca" placeholder="Ej: Samsung" />
                        <flux:input label="Modelo" wire:model="modelo" placeholder="Ej: Galaxy S21" />
                        <flux:input label="Unidad de Medida *" wire:model="unidad_medida" placeholder="Ej: Unidad" required />
                    </div>
                    <div class="space-y-4">
                        <flux:input label="Stock Mínimo *" type="number" wire:model="stock_minimo" placeholder="Ej: 10" required />
                        <flux:input label="Stock Actual *" type="number" wire:model="stock_actual" placeholder="Ej: 50" required />
                        <flux:input label="Precio Unitario *" type="number" step="0.01" wire:model="precio_unitario" placeholder="Ej: 99.99" required />
                        <flux:input label="Lote" wire:model="lote" placeholder="Ej: LOTE-2024-001" />
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <flux:input label="Código de Barras" wire:model="codigo_barras" placeholder="Ej: 1234567890123" />
                </div>

                <!-- Sección de Códigos de Salida -->
                <div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="flex items-center gap-2 mb-4">
                        <flux:icon name="tag" class="w-5 h-5" />
                        Códigos de Salida
                    </flux:heading>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <flux:input
                                wire:model="nuevo_codigo_salida"
                                placeholder="Ingrese nuevo código de salida"
                                class="flex-1"
                            />
                            <flux:button
                                wire:click="agregarCodigoSalida"
                                variant="primary"
                                icon="plus"
                                class="whitespace-nowrap"
                            >
                                Agregar
                            </flux:button>
                        </div>

                        <!-- Lista de códigos existentes -->
                        @if(count($codes_exit) > 0)
                            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach ($codes_exit as $code)
                                        <div class="flex items-center justify-between p-2 bg-white dark:bg-zinc-700 rounded-md border border-zinc-200 dark:border-zinc-600">
                                            <span class="text-sm text-zinc-900 dark:text-white">{{ $code }}</span>
                                            <flux:button
                                                wire:click="eliminarCodigoSalida('{{ $code }}')"
                                                size="xs"
                                                variant="danger"
                                                icon="trash"
                                                title="Eliminar código"
                                                class="hover:bg-red-600 transition-colors">
                                            </flux:button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Imagen del Producto -->
                <div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="flex items-center gap-2 mb-4">
                        <flux:icon name="photo" class="w-5 h-5" />
                        Imagen del Producto
                    </flux:heading>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" wire:model.live="tempImage" class="hidden" id="image-upload" accept="image/*">
                            <label for="image-upload" class="cursor-pointer inline-flex items-center px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md shadow-sm text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <flux:icon name="photo" class="w-4 h-4 mr-2" />
                                Seleccionar Imagen
                            </label>
                        </div>
                        @if ($imagePreview)
                            <div class="relative group">
                                <img src="{{ $imagePreview }}" alt="Vista previa" class="h-20 w-20 object-cover rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-600">
                                <button type="button" wire:click="removeImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                                    <flux:icon name="x-mark" class="h-4 w-4" />
                                </button>
                            </div>
                        @endif
                    </div>
                    @error('tempImage')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado del Producto -->
                <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:checkbox label="Producto activo" wire:model="estado" />
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2">Los productos inactivos no aparecerán en el inventario</p>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="button" wire:click="$set('modal_form_producto', false)">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="{{ $producto_id ? 'check' : 'plus' }}">
                        {{ $producto_id ? 'Actualizar Producto' : 'Crear Producto' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminación -->
    @if ($producto_id)
        <flux:modal wire:model="modal_form_eliminar_producto" class="w-2/3 max-w-md">
            <div class="space-y-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <flux:icon name="exclamation-triangle" class="w-8 h-8 text-red-600 dark:text-red-400" />
                    </div>
                    <flux:heading size="lg">Eliminar Producto</flux:heading>
                    <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                        ¿Está seguro de querer eliminar este producto? Esta acción no se puede deshacer.
                    </flux:text>
                </div>
                <div class="flex justify-end gap-3">
                    <flux:button type="button" wire:click="$set('modal_form_eliminar_producto', false)">
                        Cancelar
                    </flux:button>
                    <flux:button variant="danger" wire:click="confirmarEliminarProducto" icon="trash">
                        Eliminar Producto
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
