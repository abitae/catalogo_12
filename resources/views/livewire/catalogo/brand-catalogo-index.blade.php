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
                    <p class="text-2xl font-bold">{{ $marcas->total() }}</p>
                </div>
                <flux:icon name="tag" class="w-8 h-8 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Activas</p>
                    <p class="text-2xl font-bold">{{ $marcas->where('isActive', 1)->count() }}</p>
                </div>
                <flux:icon name="check-circle" class="w-8 h-8 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Inactivas</p>
                    <p class="text-2xl font-bold">{{ $marcas->where('isActive', 0)->count() }}</p>
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
                <flux:select wire:model.live="isActiveFilter" class="w-full">
                    <option value="">Todos</option>
                    <option value="1">Activa</option>
                    <option value="0">Inactiva</option>
                </flux:select>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="$set('isActiveFilter', '')" color="red" icon="trash" class="w-full">Limpiar
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
                            Archivo</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($marcas as $marca)
                        <tr wire:key="marca-{{ $marca->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $marca->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($marca->logo)
                                    <div class="flex items-center gap-2">
                                        <img src="{{ asset('storage/' . $marca->logo) }}"
                                             alt="{{ $marca->name }}"
                                             class="w-12 h-12 rounded-lg object-cover border shadow-sm" />
                                    </div>
                                @else
                                    <div class="flex items-center justify-center w-12 h-12 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                                        <flux:icon name="photo" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($marca->archivo)
                                    <div class="flex items-center justify-center">
                                        <a href="{{ asset('storage/' . $marca->archivo) }}"
                                           download="{{ $marca->name }}_archivo.{{ pathinfo($marca->archivo, PATHINFO_EXTENSION) }}"
                                           class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-green-600 bg-green-100 border border-transparent rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                            <flux:icon name="document-arrow-down" class="w-4 h-4 mr-2" />
                                            Descargar Archivo
                                        </a>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center w-full h-12 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                                        <flux:icon name="document" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $marca->isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $marca->isActive ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarMarca({{ $marca->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar marca"
                                        class="hover:bg-blue-600 transition-colors"></flux:button>
                                    <flux:button wire:click="eliminarMarca({{ $marca->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar marca"
                                        class="hover:bg-red-600 transition-colors"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
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
        @if ($marcas->hasPages())
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $marcas->links() }}
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
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <flux:label>Nombre</flux:label>
                            <flux:input type="text" wire:model.live="name" placeholder="Ej: Marca X" />
                            @error('name')
                                <flux:text class="text-xs text-red-500 mt-1">{{ $message }}</flux:text>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- Logo -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="photo" class="w-5 h-5 text-blue-400" />
                        <flux:heading size="md">Logo</flux:heading>
                        <span class="text-xs text-zinc-400 ml-2">Solo formatos JPG, PNG. Tamaño recomendado: 200x200px.</span>
                    </div>
                    <div>
                        <flux:label>Logo de la marca</flux:label>
                        <div class="mt-1 space-y-3">
                            <!-- Vista previa de logo -->
                            @if ($logoPreview)
                                <div class="relative inline-block group">
                                    <div class="relative">
                                        <img src="{{ $logoPreview }}" alt="Vista previa del logo"
                                            class="w-24 h-24 rounded-lg object-cover border-2 border-blue-200 shadow-lg" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                                        <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                            <flux:button wire:click="removeLogo" size="xs" variant="danger"
                                                icon="x-mark" class="hover:bg-red-600 transition-colors shadow-lg" />
                                        </div>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <flux:text class="text-xs text-blue-600 font-medium">Vista previa del logo</flux:text>
                                    </div>
                                </div>
                            @endif

                            <!-- Información del logo actual (solo en edición) -->
                            @if($marca_id && !$logoPreview)
                                <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                    <div class="flex-shrink-0">
                                        <flux:icon name="information-circle" class="w-6 h-6 text-blue-500" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-blue-900 dark:text-blue-100">Logo actual</p>
                                        <p class="text-sm text-blue-700 dark:text-blue-300">El logo actual se mantendrá si no subes uno nuevo</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <flux:icon name="check-circle" class="w-5 h-5 text-blue-500" />
                                    </div>
                                </div>
                            @endif

                            <!-- Input de archivo -->
                            <div class="flex items-center gap-3">
                                <flux:input wire:model="tempLogo" type="file" accept="image/*" class="flex-1" />
                                @if($logoPreview)
                                    <flux:button wire:click="removeLogo" size="sm" icon="trash">
                                        Eliminar
                                    </flux:button>
                                @endif
                            </div>

                            <!-- Información de ayuda -->
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                <p>• Formatos soportados: JPG, PNG, GIF</p>
                                <p>• Tamaño máximo: 2MB</p>
                                <p>• Tamaño recomendado: 200x200px para mejor calidad</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Archivo -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="document" class="w-5 h-5 text-purple-400" />
                        <flux:heading size="md">Archivo</flux:heading>
                        <span class="text-xs text-zinc-400 ml-2">Cualquier tipo de archivo. Tamaño máximo: 10MB.</span>
                    </div>
                    <div>
                        <flux:label>Archivo de la marca</flux:label>
                        <div class="mt-1 space-y-3">
                            <!-- Información del archivo actual (solo en edición) -->
                            @if($marca_id)
                                <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-800">
                                    <div class="flex-shrink-0">
                                        <flux:icon name="information-circle" class="w-6 h-6 text-purple-500" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-purple-900 dark:text-purple-100">Archivo actual</p>
                                        <p class="text-sm text-purple-700 dark:text-purple-300">El archivo actual se mantendrá si no subes uno nuevo</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <flux:icon name="check-circle" class="w-5 h-5 text-purple-500" />
                                    </div>
                                </div>
                            @endif

                            <!-- Input de archivo -->
                            <div class="flex items-center gap-3">
                                <flux:input wire:model="tempArchivo" type="file" class="flex-1" />
                            </div>

                            <!-- Información de ayuda -->
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                <p>• Cualquier tipo de archivo</p>
                                <p>• Tamaño máximo: 10MB</p>
                                <p>• Se reemplazará el archivo actual si existe</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center mt-2">
                    <flux:checkbox wire:model.live="isActive" label="Marca activa" />
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
    <flux:modal wire:model="modal_form_eliminar_marca">
        <div class="space-y-6">
            <div>
                <flux:icon name="exclamation-triangle" class="w-12 h-12 text-red-500 mx-auto mb-4" />
                <flux:heading size="lg" class="text-center">¿Eliminar marca?</flux:heading>
                <flux:text class="text-center text-zinc-600 dark:text-zinc-400">
                    Esta acción no se puede deshacer. Se eliminarán todos los datos asociados a esta marca.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('modal_form_eliminar_marca', false)">Cancelar</flux:button>
                <flux:button wire:click="confirmarEliminarMarca" variant="danger">Eliminar Marca</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
