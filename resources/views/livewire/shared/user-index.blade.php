<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Administración de Usuarios</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra los usuarios y roles del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevoUser" icon="plus">
                    Nuevo Usuario
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
                    placeholder="Buscar usuarios por nombre o email..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Rol</flux:label>
                    <flux:select wire:model.live="role_filter" class="w-full mt-1">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                    <flux:select wire:model.live="status_filter" class="w-full mt-1">
                        <option value="">Todos los estados</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </flux:select>
                </div>

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

    <!-- Tabla de Usuarios -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Usuarios</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $users->count() }} usuarios encontrados</span>
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
                                <span>Usuario</span>
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('email')">
                            <div class="flex items-center space-x-2">
                                <span>Email</span>
                                @if ($sortField === 'email')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Rol
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->profile_image)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->name }}">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center">
                                                <flux:icon name="user" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                        @if($user->notes)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($user->notes, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    <div class="flex items-center">
                                        <flux:icon name="envelope" class="w-4 h-4 mr-1 text-zinc-400" />
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @if($user->roles->first())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $user->roles->first()->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            Sin rol
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarUser({{ $user->id }})" size="sm" color="blue" icon="pencil">
                                    </flux:button>

                                    @if($user->id !== Auth::id())
                                        <flux:button
                                            wire:click="toggleUserStatus({{ $user->id }})"
                                            size="sm"
                                            :color="$user->is_active ? 'orange' : 'green'"
                                            :icon="$user->is_active ? 'eye-slash' : 'eye'"
                                            :title="$user->is_active ? 'Desactivar' : 'Activar'">
                                        </flux:button>

                                        <flux:button wire:click="eliminarUser({{ $user->id }})" size="sm" color="red" icon="trash">
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="users" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay usuarios</p>
                                    <p class="text-sm">Crea tu primer usuario para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal Form Usuario -->
    <flux:modal wire:model="modal_form_user" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $user_id ? 'Editar' : 'Nuevo' }} Usuario
                </h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información Básica -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Información Básica</h4>
                    </div>

                    <div>
                        <flux:label for="name" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre *</flux:label>
                        <flux:input wire:model="name" id="name" type="text" placeholder="Ingrese el nombre completo" class="w-full mt-1" />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="email" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Email *</flux:label>
                        <flux:input wire:model="email" id="email" type="email" placeholder="usuario@ejemplo.com" class="w-full mt-1" />
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <flux:icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-2" />
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Gestión de Contraseñas</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                        Las contraseñas se gestionan desde el panel de configuración del usuario o mediante el sistema de recuperación de contraseñas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                        <div class="mt-2">
                            <flux:checkbox wire:model="is_active" id="is_active" label="Usuario Activo" />
                        </div>
                    </div>

                    <!-- Rol -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Asignación de Rol</h4>
                    </div>

                    <div>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Rol</flux:label>
                        <flux:select wire:model="role_name" class="w-full mt-1">
                            <option value="">Sin rol asignado</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('role_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Los permisos se asignan a los roles, no directamente a los usuarios
                        </p>
                    </div>

                    <!-- Imagen de Perfil -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Imagen de Perfil</h4>
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="tempImage" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Imagen</flux:label>
                        <flux:input wire:model="tempImage" id="tempImage" type="file" accept="image/*" class="w-full mt-1" />
                        @error('tempImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        @if($imagePreview)
                            <div class="mt-2">
                                <img src="{{ $imagePreview }}" alt="Preview" class="w-20 h-20 object-cover rounded-lg">
                                <flux:button wire:click="removeImage" size="sm" color="red" class="mt-2">Eliminar</flux:button>
                            </div>
                        @endif
                    </div>

                    <!-- Notas -->
                    <div class="md:col-span-2">
                        <flux:label for="notes" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Notas</flux:label>
                        <flux:textarea wire:model="notes" id="notes" rows="3" placeholder="Ingrese notas adicionales" class="w-full mt-1" />
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_user', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarUser" variant="primary">
                        {{ $user_id ? 'Actualizar' : 'Crear' }} Usuario
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_user" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                @if($user)
                    <div class="text-center">
                        <flux:icon name="exclamation-triangle" class="w-16 h-16 text-red-500 mx-auto mb-4" />
                        <h3 class="text-lg font-semibold mb-2">¿Estás seguro?</h3>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                            Estás a punto de eliminar al usuario <strong>{{ $user->name }}</strong> ({{ $user->email }}).
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_user', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarUser" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
