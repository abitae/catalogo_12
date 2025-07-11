<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Permisos</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra los permisos del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevoPermission" icon="plus">
                    Nuevo Permiso
                </flux:button>
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
                    placeholder="Buscar permisos por nombre o descripción..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Registros por página</flux:label>
                    <flux:select wire:model.live="perPage" class="w-full mt-1">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
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

    <!-- Tabla de Permisos -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Permisos</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $permissions->count() }} permisos encontrados</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-2">
                                <span>Permiso</span>
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Guard
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Roles
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($permissions as $permission)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                            <flux:icon name="key" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $permission->name }}</div>
                                        @if($permission->description)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($permission->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    {{ $permission->description ?? 'Sin descripción' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $permission->guard_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $permission->roles_count }} roles
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarPermission({{ $permission->id }})" size="sm" color="blue" icon="pencil">
                                    </flux:button>

                                    @if($permission->roles_count == 0)
                                        <flux:button wire:click="eliminarPermission({{ $permission->id }})" size="sm" color="red" icon="trash">
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="key" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay permisos</p>
                                    <p class="text-sm">Crea tu primer permiso para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $permissions->links() }}
        </div>
    </div>

    <!-- Modal Form Permiso -->
    <flux:modal wire:model="modal_form_permission" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $permission_id ? 'Editar' : 'Nuevo' }} Permiso
                </h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Información Básica -->
                    <div>
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Información Básica</h4>
                    </div>

                    <div>
                        <flux:label for="name" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre del Permiso *</flux:label>
                        <flux:input wire:model="name" id="name" type="text" placeholder="Ej: view users" class="w-full mt-1" />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="description" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción</flux:label>
                        <flux:textarea wire:model="description" id="description" rows="3" placeholder="Descripción del permiso..." class="w-full mt-1" />
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="guard_name" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Guard *</flux:label>
                        <flux:select wire:model="guard_name" id="guard_name" class="w-full mt-1">
                            <option value="web">Web</option>
                            <option value="api">API</option>
                        </flux:select>
                        @error('guard_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_permission', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarPermission" variant="primary">
                        {{ $permission_id ? 'Actualizar' : 'Crear' }} Permiso
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_permission" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                @if($permission)
                    <div class="text-center">
                        <flux:icon name="exclamation-triangle" class="w-16 h-16 text-red-500 mx-auto mb-4" />
                        <h3 class="text-lg font-semibold mb-2">¿Estás seguro?</h3>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                            Estás a punto de eliminar el permiso <strong>{{ $permission->name }}</strong>.
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_permission', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarPermission" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
