<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Catálogo de Productos</h1>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    {{ $productos->total() }} productos
                </span>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <div class="w-full sm:w-80">
                    <flux:input type="search" placeholder="Buscar productos..." wire:model.live="search" icon="magnifying-glass" />
                </div>

                <div class="flex gap-2">
                    <flux:button wire:click="toggleFilters" variant="outline" icon="funnel" class="whitespace-nowrap">
                        {{ $showFilters ? 'Ocultar' : 'Mostrar' }} Filtros
                    </flux:button>
                    <flux:button wire:click="exportarProductos" icon="arrow-down-tray" variant="outline" class="whitespace-nowrap">
                        Exportar
                    </flux:button>
                    <flux:button variant="primary" wire:click="nuevoProducto" icon="plus" class="whitespace-nowrap">
                        Nuevo Producto
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    @if($showFilters)
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
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
                    @foreach([10, 25, 50, 100, 200, 500, 1000] as $option)
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
    @endif

    <!-- Tabla de Productos -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-1">
                                <span>Códigos</span>
                                <flux:icon name="{{ $sortField === 'code' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Imagen
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('description')">
                            <div class="flex items-center space-x-1">
                                <span>Descripción</span>
                                <flux:icon name="{{ $sortField === 'description' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Categorización
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('stock')">
                            <div class="flex items-center space-x-1">
                                <span>Stock</span>
                                <flux:icon name="{{ $sortField === 'stock' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Documentos
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('price_venta')">
                            <div class="flex items-center space-x-1">
                                <span>Precio Venta</span>
                                <flux:icon name="{{ $sortField === 'price_venta' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($productos as $producto)
                        <tr wire:key="producto-{{ $producto->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $producto->isActive ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        <flux:button wire:click="toggleProductStatus({{ $producto->id }})" size="xs" variant="outline" icon="{{ $producto->isActive ? 'eye-slash' : 'eye' }}" title="{{ $producto->isActive ? 'Desactivar' : 'Activar' }}" />
                                    </div>
                                    <div class="font-medium">{{ $producto->code ?? 'N/A' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $producto->code_fabrica ?? 'N/A' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $producto->code_peru ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="relative group">
                                    <img src="{{ $producto->image ? asset('storage/' . $producto->image) : 'https://placehold.co/600x400/e2e8f0/64748b?text=Sin+Imagen' }}"
                                        alt="Imagen del producto"
                                        class="w-16 h-16 rounded-lg object-cover border-2 border-zinc-200 dark:border-zinc-600 hover:border-blue-300 transition-colors">
                                    @if($producto->image)
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <flux:icon name="eye" class="w-6 h-6 text-white" />
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="max-w-xs">
                                    <div class="font-medium">{{ Str::limit($producto->description, 50) }}</div>
                                    @if($producto->garantia)
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            <flux:icon name="shield-check" class="w-3 h-3 inline mr-1" />
                                            {{ $producto->garantia }}
                                        </div>
                                    @endif
                                    @if($producto->dias_entrega > 0)
                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                            <flux:icon name="clock" class="w-3 h-3 inline mr-1" />
                                            {{ $producto->dias_entrega }} días
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1">
                                        <flux:icon name="tag" class="w-3 h-3 text-blue-500" />
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $producto->brand->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <flux:icon name="folder" class="w-3 h-3 text-green-500" />
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">{{ $producto->category->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <flux:icon name="cube" class="w-3 h-3 text-purple-500" />
                                        <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ $producto->line->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium {{ $producto->stock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $producto->stock }}
                                    </span>
                                    @if($producto->stock > 0)
                                        <flux:icon name="check-circle" class="w-4 h-4 text-green-500" />
                                    @else
                                        <flux:icon name="x-circle" class="w-4 h-4 text-red-500" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex gap-1">
                                    @if($producto->archivo)
                                        <a href="{{ asset('storage/' . $producto->archivo) }}" target="_blank"
                                           class="p-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                           title="Ver documento principal">
                                            <flux:icon name="document" class="w-5 h-5" />
                                        </a>
                                    @endif
                                    @if($producto->archivo2)
                                        <a href="{{ asset('storage/' . $producto->archivo2) }}" target="_blank"
                                           class="p-1 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition-colors"
                                           title="Ver documento secundario">
                                            <flux:icon name="document-text" class="w-5 h-5" />
                                        </a>
                                    @endif
                                    @if(!$producto->archivo && !$producto->archivo2)
                                        <span class="text-zinc-400 text-xs">Sin documentos</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="space-y-1">
                                    <div class="font-medium text-green-600 dark:text-green-400">
                                        S/ {{ number_format($producto->price_venta, 2) }}
                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        Compra: S/ {{ number_format($producto->price_compra, 2) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex items-center gap-1">
                                    <flux:button wire:click="editarProducto({{ $producto->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar producto"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarProducto({{ $producto->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar producto"
                                        class="hover:bg-red-600 transition-colors">
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center space-y-2">
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
        @if($productos->hasPages())
            <div class="px-4 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $productos->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Producto -->
    <x-modal wire:model="modal_form_producto" max-width="4xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header del modal -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                    {{ $producto_id ? 'Editar Producto' : 'Nuevo Producto' }}
                </h2>
            </div>

            <!-- Contenido del modal -->
            <div class="px-6 py-4">
                <form wire:submit="guardarProducto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Básica -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700 pb-2">
                                Información Básica
                            </h3>

                            <div>
                                <flux:label for="code">Código del Producto *</flux:label>
                                <flux:input wire:model="code" id="code" placeholder="Ingrese el código" />
                                <flux:error field="code" />
                            </div>

                            <div>
                                <flux:label for="code_fabrica">Código de Fábrica *</flux:label>
                                <flux:input wire:model="code_fabrica" id="code_fabrica" placeholder="Ingrese el código de fábrica" />
                                <flux:error field="code_fabrica" />
                            </div>

                            <div>
                                <flux:label for="code_peru">Código Perú *</flux:label>
                                <flux:input wire:model="code_peru" id="code_peru" placeholder="Ingrese el código Perú" />
                                <flux:error field="code_peru" />
                            </div>

                            <div>
                                <flux:label for="description">Descripción *</flux:label>
                                <flux:textarea wire:model="description" id="description" placeholder="Ingrese la descripción del producto" rows="3" />
                                <flux:error field="description" />
                            </div>
                        </div>

                        <!-- Categorización -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700 pb-2">
                                Categorización
                            </h3>

                            <div>
                                <flux:label for="brand_id">Marca *</flux:label>
                                <flux:select wire:model="brand_id" id="brand_id">
                                    <option value="">Seleccione una marca</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error field="brand_id" />
                            </div>

                            <div>
                                <flux:label for="category_id">Categoría *</flux:label>
                                <flux:select wire:model="category_id" id="category_id">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error field="category_id" />
                            </div>

                            <div>
                                <flux:label for="line_id">Línea *</flux:label>
                                <flux:select wire:model="line_id" id="line_id">
                                    <option value="">Seleccione una línea</option>
                                    @foreach ($lines as $line)
                                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error field="line_id" />
                            </div>
                        </div>

                        <!-- Precios y Stock -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700 pb-2">
                                Precios y Stock
                            </h3>

                            <div>
                                <flux:label for="price_compra">Precio de Compra *</flux:label>
                                <flux:input wire:model="price_compra" id="price_compra" type="number" step="0.01" min="0" placeholder="0.00" />
                                <flux:error field="price_compra" />
                            </div>

                            <div>
                                <flux:label for="price_venta">Precio de Venta *</flux:label>
                                <flux:input wire:model="price_venta" id="price_venta" type="number" step="0.01" min="0" placeholder="0.00" />
                                <flux:error field="price_venta" />
                            </div>

                            <div>
                                <flux:label for="stock">Stock *</flux:label>
                                <flux:input wire:model="stock" id="stock" type="number" min="0" placeholder="0" />
                                <flux:error field="stock" />
                            </div>

                            <div>
                                <flux:label for="dias_entrega">Días de Entrega</flux:label>
                                <flux:input wire:model="dias_entrega" id="dias_entrega" type="number" min="0" placeholder="0" />
                                <flux:error field="dias_entrega" />
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700 pb-2">
                                Información Adicional
                            </h3>

                            <div>
                                <flux:label for="garantia">Garantía</flux:label>
                                <flux:input wire:model="garantia" id="garantia" placeholder="Ej: 1 año" />
                                <flux:error field="garantia" />
                            </div>

                            <div>
                                <flux:label for="observaciones">Observaciones</flux:label>
                                <flux:textarea wire:model="observaciones" id="observaciones" placeholder="Observaciones adicionales" rows="3" />
                                <flux:error field="observaciones" />
                            </div>

                            <div>
                                <flux:label for="isActive">Estado</flux:label>
                                <flux:checkbox wire:model="isActive" id="isActive" label="Producto activo" />
                                <flux:error field="isActive" />
                            </div>
                        </div>
                    </div>

                    <!-- Archivos -->
                    <div class="mt-6 space-y-4">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700 pb-2">
                            Archivos
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Imagen -->
                            <div>
                                <flux:label>Imagen del Producto</flux:label>
                                <div class="mt-1">
                                    @if($imagePreview)
                                        <div class="relative inline-block">
                                            <img src="{{ $imagePreview }}" alt="Vista previa" class="w-32 h-32 rounded-lg object-cover border">
                                            <flux:button wire:click="removeImage" size="xs" variant="danger" icon="x-mark" class="absolute -top-2 -right-2" />
                                        </div>
                                    @endif
                                    <flux:input wire:model="tempImage" type="file" accept="image/*" />
                                </div>
                                <flux:error field="tempImage" />
                            </div>

                            <!-- Archivo 1 -->
                            <div>
                                <flux:label>Documento Principal</flux:label>
                                <div class="mt-1">
                                    @if($archivoPreview)
                                        <div class="flex items-center gap-2 mb-2">
                                            <flux:icon name="document" class="w-4 h-4" />
                                            <span class="text-sm">{{ $archivoPreview }}</span>
                                            <flux:button wire:click="removeArchivo" size="xs" variant="danger" icon="x-mark" />
                                        </div>
                                    @endif
                                    <flux:input wire:model="tempArchivo" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" />
                                </div>
                                <flux:error field="tempArchivo" />
                            </div>

                            <!-- Archivo 2 -->
                            <div>
                                <flux:label>Documento Secundario</flux:label>
                                <div class="mt-1">
                                    @if($archivo2Preview)
                                        <div class="flex items-center gap-2 mb-2">
                                            <flux:icon name="document-text" class="w-4 h-4" />
                                            <span class="text-sm">{{ $archivo2Preview }}</span>
                                            <flux:button wire:click="removeArchivo2" size="xs" variant="danger" icon="x-mark" />
                                        </div>
                                    @endif
                                    <flux:input wire:model="tempArchivo2" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" />
                                </div>
                                <flux:error field="tempArchivo2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <flux:button wire:click="$set('modal_form_producto', false)">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $producto_id ? 'Actualizar' : 'Crear' }} Producto
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>

    <!-- Modal Confirmar Eliminación -->
    <x-modal wire:model="modal_form_eliminar_producto" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header del modal -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                    Confirmar Eliminación
                </h2>
            </div>

            <!-- Contenido del modal -->
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <flux:icon name="exclamation-triangle" class="w-8 h-8 text-red-500" />
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white">
                                ¿Eliminar Producto?
                            </h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Esta acción no se puede deshacer. Se eliminarán todos los archivos asociados.
                            </p>
                        </div>
                    </div>

                    @if($producto)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                        <div class="flex items-center gap-2">
                            <flux:icon name="information-circle" class="w-5 h-5 text-red-500" />
                            <span class="text-sm font-medium text-red-800 dark:text-red-200">
                                Producto: {{ $producto->code }} - {{ $producto->description }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <flux:button wire:click="$set('modal_form_eliminar_producto', false)">
                        Cancelar
                    </flux:button>
                    <flux:button variant="danger" wire:click="confirmarEliminarProducto">
                        Eliminar Producto
                    </flux:button>
                </div>
            </div>
        </div>
    </x-modal>
</div>
