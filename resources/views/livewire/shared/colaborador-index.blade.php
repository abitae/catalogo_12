<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Colaboradores</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra los colaboradores del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevoColaborador" icon="plus">
                    Nuevo Colaborador
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
                    placeholder="Buscar colaboradores por nombre o email..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

    <!-- Tabla de Colaboradores -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Colaboradores</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $colaboradores->count() }} colaboradores encontrados</span>
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
                                <span>Colaborador</span>
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
                            Estado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($colaboradores as $colaborador)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($colaborador->profile_image)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($colaborador->profile_image) }}" alt="{{ $colaborador->name }}">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center">
                                                <flux:icon name="user" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $colaborador->name }}</div>
                                        @if($colaborador->notes)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($colaborador->notes, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    <div class="flex items-center">
                                        <flux:icon name="envelope" class="w-4 h-4 mr-1 text-zinc-400" />
                                        {{ $colaborador->email }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($colaborador->is_active)
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
                                    <flux:button wire:click="editarColaborador({{ $colaborador->id }})" size="sm" color="blue" icon="pencil">
                                    </flux:button>

                                    @if($colaborador->id !== Auth::id())
                                        <flux:button
                                            wire:click="toggleColaboradorStatus({{ $colaborador->id }})"
                                            size="sm"
                                            :color="$colaborador->is_active ? 'orange' : 'green'"
                                            :icon="$colaborador->is_active ? 'eye-slash' : 'eye'"
                                            :title="$colaborador->is_active ? 'Desactivar' : 'Activar'">
                                        </flux:button>

                                        <flux:button wire:click="eliminarColaborador({{ $colaborador->id }})" size="sm" color="red" icon="trash">
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="users" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay colaboradores</p>
                                    <p class="text-sm">Crea tu primer colaborador para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $colaboradores->links() }}
        </div>
    </div>

    <!-- Modal Form Colaborador -->
    <flux:modal wire:model="modal_form_colaborador" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $colaborador_id ? 'Editar' : 'Nuevo' }} Colaborador
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
                        <flux:input wire:model="email" id="email" type="email" placeholder="colaborador@ejemplo.com" class="w-full mt-1" />
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
                            <flux:checkbox wire:model="is_active" id="is_active" label="Colaborador Activo" />
                        </div>
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
                    <flux:button wire:click="$set('modal_form_colaborador', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarColaborador" variant="primary">
                        {{ $colaborador_id ? 'Actualizar' : 'Crear' }} Colaborador
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_colaborador" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                @if($colaborador)
                    <div class="text-center">
                        <flux:icon name="exclamation-triangle" class="w-16 h-16 text-red-500 mx-auto mb-4" />
                        <h3 class="text-lg font-semibold mb-2">¿Estás seguro?</h3>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                            Estás a punto de eliminar al colaborador <strong>{{ $colaborador->name }}</strong> ({{ $colaborador->email }}).
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_colaborador', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarColaborador" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
