<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Tipos de Cliente</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra los tipos de cliente del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevoTipoCustomer" icon="plus">
                    Nuevo Tipo de Cliente
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
                    placeholder="Buscar tipos de cliente por nombre o descripción..."
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

    <!-- Tabla de Tipos de Cliente -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Tipos de Cliente</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $tipos_customer->count() }} tipos encontrados</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('nombre')">
                            <div class="flex items-center space-x-2">
                                <span>Nombre</span>
                                @if ($sortField === 'nombre')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Clientes
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-2">
                                <span>Fecha Creación</span>
                                @if ($sortField === 'created_at')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($tipos_customer as $tipo)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $tipo->nombre }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                {{ $tipo->descripcion ?: 'Sin descripción' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $tipo->clientes_count ?? $tipo->clientes->count() }} clientes
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $tipo->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarTipoCustomer({{ $tipo->id }})" size="sm" color="blue" icon="pencil">

                                    </flux:button>
                                    <flux:button wire:click="eliminarTipoCustomer({{ $tipo->id }})" size="sm" color="red" icon="trash">
                                        
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="users" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay tipos de cliente</p>
                                    <p class="text-sm">Crea tu primer tipo de cliente para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $tipos_customer->links() }}
        </div>
    </div>

    <!-- Modal Form Tipo de Cliente -->
    <flux:modal wire:model="modal_form_tipo_customer" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $tipo_customer_id ? 'Editar' : 'Nuevo' }} Tipo de Cliente
                </h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:label for="nombre" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre *</flux:label>
                        <flux:input wire:model="nombre" id="nombre" type="text" placeholder="Ingrese el nombre" class="w-full mt-1" />
                        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="descripcion" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción</flux:label>
                        <flux:textarea wire:model="descripcion" id="descripcion" rows="3" placeholder="Ingrese la descripción" class="w-full mt-1" />
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_tipo_customer', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarTipoCustomer" variant="primary">
                        {{ $tipo_customer_id ? 'Actualizar' : 'Crear' }} Tipo de Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_tipo_customer" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <p class="text-zinc-600 dark:text-zinc-400">
                    ¿Estás seguro de que quieres eliminar este tipo de cliente? Esta acción no se puede deshacer.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_tipo_customer', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarTipoCustomer" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
