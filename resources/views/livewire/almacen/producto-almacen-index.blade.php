<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Productos de Almacén</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarProductos" icon="arrow-down-tray">
                    Exportar
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
                <flux:select wire:model.live="stock_status" class="w-full">
                    <option value="">Todos</option>
                    <option value="in_stock">En Stock</option>
                    <option value="out_of_stock">Sin Stock</option>
                    <option value="low_stock">Stock Bajo</option>
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
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
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
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm mb-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-1">
                                <span>Código</span>
                                @if ($sortField === 'code')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Cód.Salida
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Imagen
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('nombre')">
                            <div class="flex items-center space-x-1">
                                <span>Nombre</span>
                                @if ($sortField === 'nombre')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Almacén/Categoría
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Stock
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Precio/Estado
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($productos as $producto)
                        <tr wire:key="producto-{{ $producto->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $producto->code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @foreach ($producto->codes_exit as $code)
                                    <span
                                        class="px-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $code }}
                                    </span>
                                    <br>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if ($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                                        class="w-20 h-20 rounded-full">
                                @else
                                    <div
                                        class="w-20 h-20 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <flux:icon name="photo" class="w-10 h-10 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $producto->nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $producto->almacen->nombre ?? 'N/A' }} <br> {{ $producto->categoria }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span>Actual: {{ $producto->stock_actual }}</span>
                                    <span>Mínimo: {{ $producto->stock_minimo }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                S/ {{ number_format($producto->precio_unitario, 2) }} <br>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $producto->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select label="Almacén" wire:model="almacen_id">
                        <option value="">Seleccione un almacén</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input label="Código" wire:model="code" placeholder="Ingrese el código" />
                    <flux:input label="Nombre" wire:model="nombre" placeholder="Ingrese el nombre" />
                    <flux:input label="Descripción" wire:model="descripcion" placeholder="Ingrese la descripción" />
                    <flux:input label="Categoría" wire:model="categoria" placeholder="Ingrese la categoría" />
                    <flux:input label="Unidad de Medida" wire:model="unidad_medida"
                        placeholder="Ingrese la unidad de medida" />
                    <flux:input label="Stock Mínimo" type="number" wire:model="stock_minimo"
                        placeholder="Ingrese el stock mínimo" />
                    <flux:input label="Stock Actual" type="number" wire:model="stock_actual"
                        placeholder="Ingrese el stock actual" />
                    <flux:input label="Precio Unitario" type="number" step="0.01" wire:model="precio_unitario"
                        placeholder="Ingrese el precio unitario" />
                    <flux:input label="Código de Barras" wire:model="codigo_barras"
                        placeholder="Ingrese el código de barras" />
                    <flux:input label="Marca" wire:model="marca" placeholder="Ingrese la marca" />
                    <flux:input label="Modelo" wire:model="modelo" placeholder="Ingrese el modelo" />
                </div>

                <!-- Sección de Códigos de Salida -->
                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <flux:heading size="md">Códigos de Salida</flux:heading>
                    <div class="mt-4">
                        <div class="flex gap-2">
                            <flux:input wire:model="nuevo_codigo_salida" placeholder="Ingrese nuevo código de salida"
                                class="flex-1" />
                            <flux:button wire:click="agregarCodigoSalida" variant="primary" icon="plus">
                                Agregar
                            </flux:button>
                        </div>

                        <!-- Lista de códigos existentes -->
                        <div class="mt-4">
                            <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                                Código de Salida
                                            </th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                        @foreach ($codes_exit as $code)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                                    {{ $code }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-right">
                                                    <flux:button
                                                        wire:click="eliminarCodigoSalida('{{ $code }}')"
                                                        size="xs" variant="danger" icon="trash"
                                                        title="Eliminar código"
                                                        class="hover:bg-red-600 transition-colors">
                                                    </flux:button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Imagen del Producto</label>
                    <div class="mt-1 flex items-center">
                        <div class="flex-1">
                            <input type="file" wire:model.live="tempImage" class="hidden" id="image-upload"
                                accept="image/*">
                            <label for="image-upload"
                                class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Seleccionar Imagen
                            </label>
                        </div>
                        @if ($imagePreview)
                            <div class="ml-4 relative group">
                                <img src="{{ $imagePreview }}" alt="Vista previa"
                                    class="h-20 w-20 object-cover rounded shadow-sm">
                                <button type="button" wire:click="removeImage"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                                    <flux:icon name="x-mark" class="h-4 w-4" />
                                </button>
                            </div>
                        @endif
                    </div>
                    @error('tempImage')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-4">
                    <flux:checkbox label="Activo" wire:model="estado" />
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
    @if ($producto_id)
        <flux:modal wire:model="modal_form_eliminar_producto" class="w-2/3 max-w-2xl">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Eliminar Producto</flux:heading>
                    <flux:text class="mt-2">¿Está seguro de querer eliminar este producto?</flux:text>
                </div>
                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_eliminar_producto', false)"
                        class="mr-2">
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
