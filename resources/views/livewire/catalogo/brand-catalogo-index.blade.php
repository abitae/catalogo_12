<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <flux:heading size="lg">Catálogo de Marcas</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">Administra y consulta las marcas registradas en
                    el sistema.</flux:text>
            </div>
            <div class="flex items-center justify-end gap-4 w-full md:w-auto">
                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar marcas..." wire:model.live="search"
                        icon="magnifying-glass" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="nuevoMarca" icon="plus">Nueva Marca</flux:button>
                </div>
            </div>
        </div>
    </div>
    <!-- Estadísticas Rápidas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Marcas</p>
                    <p class="text-2xl font-bold">{{ $brands->total() }}</p>
                </div>
                <flux:icon name="cube" class="w-8 h-8 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Activas</p>
                    <p class="text-2xl font-bold">{{ $brands->where('isActive', 1)->count() }}</p>
                </div>
                <flux:icon name="check-circle" class="w-8 h-8 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Inactivas</p>
                    <p class="text-2xl font-bold">{{ $brands->where('isActive', 0)->count() }}</p>
                </div>
                <flux:icon name="x-circle" class="w-8 h-8 opacity-80" />
            </div>
        </div>
    </div>
    <!-- Filtros Avanzados -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <flux:label>Estado</flux:label>
                <flux:select wire:model.live="isActive" class="w-full">
                    <option value="">Todos</option>
                    <option value="1">Activa</option>
                    <option value="0">Inactiva</option>
                </flux:select>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="$set('isActive', '')" color="red" icon="trash" class="w-full">Limpiar
                    Filtros</flux:button>
            </div>
        </div>
    </div>
    <!-- Tabla de Marcas -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Nombre</span>
                                <flux:icon name="arrows-up-down" class="w-4 h-4" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Logo</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($brands as $brand)
                        <tr wire:key="brand-{{ $brand->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $brand->name }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if ($brand->logo)
                                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="Logo"
                                        class="w-12 h-12 rounded-full object-cover border" />
                                @else
                                    <span class="text-zinc-400 text-xs">Sin logo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $brand->isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $brand->isActive ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarMarca({{ $brand->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar marca"
                                        class="hover:bg-blue-600 transition-colors"></flux:button>
                                    <flux:button wire:click="eliminarMarca({{ $brand->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar marca"
                                        class="hover:bg-red-600 transition-colors"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="inbox" class="w-12 h-12 text-zinc-300" />
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
        @if ($brands->hasPages())
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $brands->links() }}
            </div>
        @endif
    </div>
    <!-- Modal Form Marca -->
    <flux:modal wire:model="modal_form_marca" variant="flyout" class="w-2/3 max-w-2xl">
        <form wire:submit.prevent="guardarMarca">
            <div class="space-y-6">
                <div class="border-b pb-4 mb-2 flex items-center gap-3">
                    <flux:icon name="tag" class="w-8 h-8 text-blue-500" />
                    <div>
                        <flux:heading size="lg">{{ $marca_id ? 'Editar Marca' : 'Nueva Marca' }}</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">Complete los datos de la marca.</flux:text>
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="information-circle" class="w-5 h-5 text-blue-400" />
                        <flux:heading size="md">Información Básica</flux:heading>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:label>Nombre</flux:label>
                            <flux:input type="text" wire:model.live="name" placeholder="Ej: Marca X" />
                        </div>
                        <div class="flex items-center mt-6">
                            <flux:checkbox wire:model.live="isActive" label="Marca activa" />
                        </div>
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="photo" class="w-5 h-5 text-blue-400" />
                        <flux:heading size="md">Logo</flux:heading>
                        <span class="text-xs text-zinc-400 ml-2">Solo formatos JPG, PNG. Tamaño recomendado:
                            200x200px.</span>
                    </div>
                    <div>
                        <flux:label>Logo de la marca</flux:label>
                        <div class="mt-1">
                            @if ($logoPreview)
                                <div class="relative inline-block group">
                                    <img src="{{ $logoPreview }}" alt="Vista previa"
                                        class="w-24 h-24 rounded-lg object-cover border shadow" />
                                    <flux:button wire:click="removeLogo" size="xs" variant="danger"
                                        icon="x-mark" class="absolute -top-2 -right-2" />
                                </div>
                            @endif
                            <flux:input wire:model="tempLogo" type="file" accept="image/*" />
                        </div>
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="paper-clip" class="w-5 h-5 text-pink-400" />
                        <flux:heading size="md">Documento</flux:heading>
                        <span class="text-xs text-zinc-400 ml-2">Formatos permitidos: PDF, DOC, XLS, PPT.</span>
                    </div>
                    <div>
                        <flux:label>Archivo adjunto</flux:label>
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
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-8 border-t pt-4 bg-white dark:bg-zinc-900 sticky bottom-0 z-10">
                <flux:button wire:click="$set('modal_form_marca', false)">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">{{ $marca_id ? 'Actualizar' : 'Crear' }} Marca
                </flux:button>
            </div>
        </form>
    </flux:modal>
    <!-- Modal Confirmar Eliminación -->
    <flux:modal wire:model="modal_form_eliminar_marca" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Marca</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar esta marca?</flux:text>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <flux:button wire:click="$set('modal_form_eliminar_marca', false)">Cancelar</flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarMarca">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
