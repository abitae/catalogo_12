<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado con Estadísticas -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Almacenes</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra y controla todos los almacenes del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button wire:click="exportarAlmacenes" icon="arrow-down-tray">
                    Exportar
                </flux:button>
                <flux:button variant="primary" wire:click="nuevoAlmacen" icon="plus">
                    Nuevo Almacén
                </flux:button>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Almacenes</p>
                        <p class="text-3xl font-bold">{{ $almacenes->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-400/20 rounded-full">
                        <flux:icon name="building-office" class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Almacenes Activos</p>
                        <p class="text-3xl font-bold">{{ $almacenes->where('estado', true)->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-400/20 rounded-full">
                        <flux:icon name="check-circle" class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Almacenes Inactivos</p>
                        <p class="text-3xl font-bold">{{ $almacenes->where('estado', false)->count() }}</p>
                    </div>
                    <div class="p-3 bg-orange-400/20 rounded-full">
                        <flux:icon name="pause-circle" class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Capacidad Total</p>
                        <p class="text-3xl font-bold">{{ number_format($almacenes->sum('capacidad')) }}</p>
                    </div>
                    <div class="p-3 bg-purple-400/20 rounded-full">
                        <flux:icon name="cube" class="w-8 h-8" />
                    </div>
                </div>
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
                    placeholder="Buscar almacenes por código, nombre, dirección o responsable..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                    <flux:select wire:model.live="isActive_filter" class="w-full mt-1">
                        <option value="">Todos los estados</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Registros por página</flux:label>
                    <flux:select wire:model.live="perPage" class="w-full mt-1">
                        <option value="10">10 registros</option>
                        <option value="25">25 registros</option>
                        <option value="50">50 registros</option>
                        <option value="100">100 registros</option>
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

    <!-- Tabla de Almacenes -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Lista de Almacenes</h3>
                <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $almacenes->count() }} almacenes encontrados</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-2">
                                <span>Código</span>
                                @if ($sortField === 'code')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
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
                            Ubicación
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Responsable
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Capacidad
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($almacenes as $almacen)
                        <tr wire:key="almacen-{{ $almacen->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <flux:icon name="building-office" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $almacen->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $almacen->nombre }}</div>
                                @if($almacen->telefono)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $almacen->telefono }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-900 dark:text-white">{{ $almacen->direccion }}</div>
                                @if($almacen->email)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $almacen->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                        <flux:icon name="user" class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $almacen->responsable }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-900 dark:text-white">{{ number_format($almacen->capacidad) }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">unidades</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $almacen->estado ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    <flux:icon name="{{ $almacen->estado ? 'check-circle' : 'x-circle' }}" class="w-3 h-3 mr-1" />
                                    {{ $almacen->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <flux:button
                                        wire:click="editarAlmacen({{ $almacen->id }})"
                                        size="xs"
                                        variant="primary"
                                        icon="pencil"
                                        title="Editar almacén"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button
                                        wire:click="eliminarAlmacen({{ $almacen->id }})"
                                        size="xs"
                                        variant="danger"
                                        icon="trash"
                                        title="Eliminar almacén"
                                        class="hover:bg-red-600 transition-colors">
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                        <flux:icon name="building-office" class="w-8 h-8 text-zinc-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">No hay almacenes</h3>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-4">Comienza creando tu primer almacén</p>
                                    <flux:button variant="primary" wire:click="nuevoAlmacen" icon="plus">
                                        Crear Almacén
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if($almacenes->hasPages())
        <div class="mt-6">
            {{ $almacenes->links() }}
        </div>
    @endif

    <!-- Modal Form Almacén -->
    <flux:modal wire:model="modal_form_almacen" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="space-y-6">
            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <flux:heading size="lg" class="flex items-center gap-2">
                    <flux:icon name="{{ $almacen_id ? 'pencil' : 'plus' }}" class="w-6 h-6" />
                    {{ $almacen_id ? 'Editar Almacén' : 'Nuevo Almacén' }}
                </flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">Complete los datos del almacén para continuar.</flux:text>
            </div>

            <form wire:submit.prevent="guardarAlmacen">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <flux:input label="Código" wire:model="code" placeholder="Ej: ALM-001" required />
                        <flux:input label="Nombre" wire:model="nombre" placeholder="Ej: Almacén Principal" required />
                        <flux:input label="Dirección" wire:model="direccion" placeholder="Ej: Av. Principal 123" required />
                        <flux:input label="Teléfono" wire:model="telefono" placeholder="Ej: +51 123 456 789" />
                    </div>
                    <div class="space-y-4">
                        <flux:input label="Email" wire:model="email" placeholder="Ej: almacen@empresa.com" type="email" />
                        <flux:input label="Capacidad" type="number" wire:model="capacidad" placeholder="Ej: 10000" required />
                        <flux:input label="Responsable" wire:model="responsable" placeholder="Ej: Juan Pérez" required />
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:checkbox label="Almacén activo" wire:model="estado" />
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2">Los almacenes inactivos no pueden recibir productos</p>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="button" wire:click="$set('modal_form_almacen', false)">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="{{ $almacen_id ? 'check' : 'plus' }}">
                        {{ $almacen_id ? 'Actualizar Almacén' : 'Crear Almacén' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminación -->
    @if($almacen_id)
    <flux:modal wire:model="modal_form_eliminar_almacen" class="w-2/3 max-w-md">
        <div class="space-y-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <flux:icon name="exclamation-triangle" class="w-8 h-8 text-red-600 dark:text-red-400" />
                </div>
                <flux:heading size="lg">Eliminar Almacén</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                    ¿Está seguro de querer eliminar este almacén? Esta acción no se puede deshacer.
                </flux:text>
            </div>
            <div class="flex justify-end gap-3">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_almacen', false)">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarAlmacen" icon="trash">
                    Eliminar Almacén
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
