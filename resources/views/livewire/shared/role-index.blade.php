<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Roles</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra los roles y permisos del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevoRole" icon="plus">
                    Nuevo Rol
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
                    placeholder="Buscar roles por nombre o descripción..."
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

    <!-- Tabla de Roles -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Roles</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $roles->count() }} roles encontrados</span>
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
                                <span>Rol</span>
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Permisos
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Usuarios
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($roles as $role)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                            <flux:icon name="shield-check" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $role->name }}</div>
                                        @if($role->description)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($role->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    {{ $role->description ?? 'Sin descripción' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $role->permissions_count }} permisos
                                    </span>
                                    @if($role->permissions_count > 0)
                                        <flux:button
                                            wire:click="$set('modal_ver_permisos', true)"
                                            size="xs"
                                            color="blue"
                                            icon="eye"
                                            title="Ver permisos">
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $role->users_count }} usuarios
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarRole({{ $role->id }})" size="sm" color="blue" icon="pencil">
                                    </flux:button>

                                    @if(!in_array($role->name, ['Super Admin', 'Administrador']))
                                        <flux:button wire:click="eliminarRole({{ $role->id }})" size="sm" color="red" icon="trash">
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="shield-check" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay roles</p>
                                    <p class="text-sm">Crea tu primer rol para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $roles->links() }}
        </div>
    </div>

    <!-- Modal Form Rol -->
    <flux:modal wire:model="modal_form_role" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $role_id ? 'Editar' : 'Nuevo' }} Rol
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
                        <flux:label for="name" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre del Rol *</flux:label>
                        <flux:input wire:model="name" id="name" type="text" placeholder="Ej: Vendedor" class="w-full mt-1" />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="description" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción</flux:label>
                        <flux:textarea wire:model="description" id="description" rows="3" placeholder="Descripción del rol..." class="w-full mt-1" />
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Permisos -->
                    <div>
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Permisos</h4>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Seleccionar Permisos</flux:label>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded">
                                    <input type="checkbox"
                                           wire:model="permissions"
                                           value="{{ $permission->id }}"
                                           class="rounded border-zinc-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('permissions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_role', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarRole" variant="primary">
                        {{ $role_id ? 'Actualizar' : 'Crear' }} Rol
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_role" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                @if($role)
                    <div class="text-center">
                        <flux:icon name="exclamation-triangle" class="w-16 h-16 text-red-500 mx-auto mb-4" />
                        <h3 class="text-lg font-semibold mb-2">¿Estás seguro?</h3>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                            Estás a punto de eliminar el rol <strong>{{ $role->name }}</strong>.
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_role', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarRole" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
