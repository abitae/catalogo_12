<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Catálogo de Productos</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Search..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarProductos" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="importar" icon="arrow-up-tray">
                    Importar
                </flux:button>
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevoProducto" icon="plus">
                    Nuevo
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
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

            <!-- Registros por página -->
            <div>
                <flux:label>Registros por página</flux:label>
                <flux:select wire:model.live="perPage" class="w-full">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
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
                                <span>Códigos</span>
                                @if ($sortField === 'code')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            <div class="flex items-center space-x-1">
                                Imagen
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Descripción</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Marca / Categoria / Línea</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Stock</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Precio</th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($productos as $producto)
                        <tr class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $producto->isActive ? 'Activo' : 'Inactivo' }}
                                    </span>
                                    <span>{{ $producto->code ?? '' }}</span>
                                    <span>{{ $producto->code_fabrica ?? '' }}</span>
                                    <span>{{ $producto->code_peru ?? '' }}</span>

                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <img src="{{ $producto->image ? asset('storage/' . $producto->image) : 'https://placehold.co/600x400' }}"
                                    alt="Imagen del producto" class="w-10 h-10 rounded-full">
                                <flux:button wire:click="descargarArchivo({{ $producto->id }})" size="xs"
                                    variant="ghost" icon="arrow-down-tray" title="Descargar archivo">
                                </flux:button>
                                <flux:button wire:click="descargarArchivo2({{ $producto->id }})" size="xs"
                                    variant="ghost" icon="arrow-down-tray" title="Descargar archivo 2">
                                </flux:button>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $producto->description }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $producto->brand->name ?? 'N/A' }} /
                                {{ $producto->category->name ?? 'N/A' }} /
                                {{ $producto->line->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $producto->stock }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">S/
                                {{ number_format($producto->price_venta, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $productos->links() }}
    </div>

    <!-- Modal Form Producto -->
    <flux:modal wire:model="modal_form_producto" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $producto_id ? 'Editar Producto' : 'Nuevo Producto' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos del producto.</flux:text>
            </div>
            <form wire:submit.prevent="guardarProducto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select label="Marca" wire:model="brand_id">
                        <option value="">Seleccione una marca</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Categoría" wire:model="category_id">
                        <option value="">Seleccione una categoría</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Línea" wire:model="line_id">
                        <option value="">Seleccione una línea</option>
                        @foreach ($lines as $line)
                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input label="Código" wire:model="code" placeholder="Ingrese el código" />
                    <flux:input label="Código Fábrica" wire:model="code_fabrica"
                        placeholder="Ingrese el código de fábrica" />
                    <flux:input label="Código Perú" wire:model="code_peru" placeholder="Ingrese el código Perú" />
                    <flux:input label="Precio Compra" type="number" step="0.01" wire:model="price_compra"
                        placeholder="0.00" />
                    <flux:input label="Precio Venta" type="number" step="0.01" wire:model="price_venta"
                        placeholder="0.00" />
                    <flux:input label="Stock" type="number" wire:model="stock" placeholder="0" />
                    <flux:input label="Días Entrega" type="number" wire:model="dias_entrega" placeholder="0" />
                    <flux:input label="Garantía" wire:model="garantia" placeholder="Ingrese la garantía" />
                </div>

                <div class="mt-4">
                    <flux:textarea label="Descripción" wire:model="description"
                        placeholder="Ingrese la descripción" />
                    <flux:textarea label="Observaciones" wire:model="observaciones"
                        placeholder="Ingrese observaciones" />
                </div>

                <div class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <!-- Imagen -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Imagen del Producto</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-1">
                                    <input
                                        type="file"
                                        wire:model.live="tempImage"
                                        class="hidden"
                                        id="image-upload"
                                        accept="image/*"
                                        wire:loading.attr="disabled"
                                    >
                                    <label
                                        for="image-upload"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Seleccionar Imagen
                                    </label>
                                </div>

                                <div class="ml-4 relative group">
                                    @if($imagePreview)
                                        <div class="relative">
                                            <img
                                                src="{{ $imagePreview }}"
                                                alt="Vista previa"
                                                class="h-20 w-20 object-cover rounded shadow-sm"
                                                loading="lazy"
                                            >
                                            <button
                                                type="button"
                                                wire:click="removeImage"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600"
                                                title="Eliminar imagen"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('tempImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="tempImage" class="mt-2">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Subiendo imagen...
                                </div>
                            </div>
                        </div>

                        <!-- Archivo 1 -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Archivo Adjunto 1</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-1">
                                    <input type="file" wire:model.live="tempArchivo" class="hidden" id="archivo-upload" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                    <label for="archivo-upload" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Seleccionar Archivo
                                    </label>
                                </div>
                                @if($archivoPreview)
                                    <div class="ml-4 flex items-center group">
                                        <span class="text-sm text-gray-500">{{ $archivoPreview }}</span>
                                        <button type="button" wire:click="removeArchivo" class="ml-2 text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @error('tempArchivo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="tempArchivo" class="mt-2">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Subiendo archivo...
                                </div>
                            </div>
                        </div>

                        <!-- Archivo 2 -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Archivo Adjunto 2</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-1">
                                    <input type="file" wire:model.live="tempArchivo2" class="hidden" id="archivo2-upload" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                    <label for="archivo2-upload" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Seleccionar Archivo
                                    </label>
                                </div>
                                @if($archivo2Preview)
                                    <div class="ml-4 flex items-center group">
                                        <span class="text-sm text-gray-500">{{ $archivo2Preview }}</span>
                                        <button type="button" wire:click="removeArchivo2" class="ml-2 text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @error('tempArchivo2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="tempArchivo2" class="mt-2">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Subiendo archivo...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <flux:checkbox label="Activo" wire:model="isActive" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_producto', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $producto_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    <!-- Modal Form Eliminar Producto -->
    @if($producto_id)
    <flux:modal wire:model="modal_form_eliminar_producto" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Producto</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar este producto?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_producto', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarProducto">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
