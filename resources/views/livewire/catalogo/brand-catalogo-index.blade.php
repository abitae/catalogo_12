<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Gestión de Marcas</h1>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    {{ $brands->total() }} marcas
                </span>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <div class="w-full sm:w-80">
                    <flux:input type="search" placeholder="Buscar marcas..." wire:model.live="search" icon="magnifying-glass" />
                </div>

                <div class="flex gap-2">
                    <flux:button wire:click="toggleFilters" variant="outline" icon="funnel" class="whitespace-nowrap">
                        {{ $showFilters ? 'Ocultar' : 'Mostrar' }} Filtros
                    </flux:button>
                    <flux:button variant="primary" wire:click="nuevoMarca" icon="plus" class="whitespace-nowrap">
                        Nueva Marca
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    @if($showFilters)
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Registros por página -->
            <div>
                <flux:label>Registros por página</flux:label>
                <flux:select wire:model.live="perPage" class="w-full">
                    @foreach([10, 25, 50, 100, 200, 500] as $option)
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

    <!-- Tabla de Marcas -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Nombre</span>
                                <flux:icon name="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Logo
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Documentos
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-1">
                                <span>Fecha Creación</span>
                                <flux:icon name="{{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($brands as $brand)
                        <tr wire:key="brand-{{ $brand->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($brand->logo)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                                <flux:icon name="tag" class="w-5 h-5 text-zinc-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-300">
                                            {{ $brand->name }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            ID: {{ $brand->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($brand->logo)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $brand->logo) }}"
                                             alt="Logo de {{ $brand->name }}"
                                             class="w-16 h-16 rounded-lg object-cover border-2 border-zinc-200 dark:border-zinc-600 hover:border-blue-300 transition-colors">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <flux:icon name="eye" class="w-6 h-6 text-white" />
                                        </div>
                                    </div>
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                                        <flux:icon name="photo" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($brand->archivo)
                                    <a href="{{ asset('storage/' . $brand->archivo) }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs hover:bg-blue-200 transition-colors"
                                       title="Ver documento">
                                        <flux:icon name="document" class="w-4 h-4" />
                                        <span>Ver documento</span>
                                    </a>
                                @else
                                    <span class="text-zinc-400 text-xs">Sin documentos</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="text-sm">{{ $brand->created_at->format('d/m/Y') }}</span>
                                    <span class="text-xs text-zinc-500">{{ $brand->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $brand->isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $brand->isActive ? 'Activa' : 'Inactiva' }}
                                    </span>
                                    <flux:button wire:click="toggleMarcaStatus({{ $brand->id }})" size="xs" variant="outline" icon="{{ $brand->isActive ? 'eye-slash' : 'eye' }}" title="{{ $brand->isActive ? 'Desactivar' : 'Activar' }}" />
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex items-center gap-1">
                                    <flux:button wire:click="editarMarca({{ $brand->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar marca"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarMarca({{ $brand->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar marca"
                                        class="hover:bg-red-600 transition-colors">
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center space-y-2">
                                    <flux:icon name="tag" class="w-12 h-12 text-zinc-300" />
                                    <span class="text-lg font-medium">No se encontraron marcas</span>
                                    <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($brands->hasPages())
            <div class="px-4 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $brands->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Marca -->
    <x-modal wire:model="modal_form_marca" max-width="2xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header del modal -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                    {{ $marca_id ? 'Editar Marca' : 'Nueva Marca' }}
                </h2>
            </div>

            <!-- Contenido del modal -->
            <div class="px-6 py-4">
                <form wire:submit="guardarMarca">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Básica -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700 pb-2">
                                Información Básica
                            </h3>

                            <div>
                                <flux:label for="name">Nombre de la Marca *</flux:label>
                                <flux:input wire:model="name" id="name" placeholder="Ingrese el nombre de la marca" />
                                <flux:error field="name" />
                            </div>

                            <div>
                                <flux:label for="isActive">Estado</flux:label>
                                <flux:checkbox wire:model="isActive" id="isActive" label="Marca activa" />
                                <flux:error field="isActive" />
                            </div>
                        </div>

                        <!-- Archivos -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700 pb-2">
                                Archivos
                            </h3>

                            <div>
                                <flux:label>Logo de la Marca</flux:label>
                                <div class="mt-1">
                                    @if($logoPreview)
                                        <div class="relative inline-block mb-2">
                                            <img src="{{ $logoPreview }}" alt="Vista previa del logo" class="w-32 h-32 rounded-lg object-cover border">
                                            <flux:button wire:click="removeLogo" size="xs" variant="danger" icon="x-mark" class="absolute -top-2 -right-2" />
                                        </div>
                                    @endif
                                    <flux:input wire:model="tempLogo" type="file" accept="image/*" />
                                </div>
                                <flux:error field="tempLogo" />
                            </div>

                            <div>
                                <flux:label>Documento</flux:label>
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
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <flux:button variant="light" wire:click="$set('modal_form_marca', false)">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $marca_id ? 'Actualizar' : 'Crear' }} Marca
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>

    <!-- Modal Confirmar Eliminación -->
    <x-modal wire:model="modal_form_eliminar_marca" max-width="md">
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
                                ¿Eliminar Marca?
                            </h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Esta acción no se puede deshacer. Se eliminarán todos los archivos asociados.
                            </p>
                        </div>
                    </div>

                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                        <div class="flex items-center gap-2">
                            <flux:icon name="information-circle" class="w-5 h-5 text-red-500" />
                            <span class="text-sm font-medium text-red-800 dark:text-red-200">
                                Se eliminarán todos los productos asociados a esta marca
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <flux:button variant="light" wire:click="$set('modal_form_eliminar_marca', false)">
                        Cancelar
                    </flux:button>
                    <flux:button variant="danger" wire:click="confirmarEliminarMarca">
                        Eliminar Marca
                    </flux:button>
                </div>
            </div>
        </div>
    </x-modal>
</div>
