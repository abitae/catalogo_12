<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">Administrador de Acuerdos Marco</flux:heading>
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">Gestiona los acuerdos marco registrados en el sistema.</flux:text>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full lg:w-auto">
                <div class="w-full sm:w-80">
                    <flux:input type="search" placeholder="Buscar acuerdos..." wire:model.live="search" icon="magnifying-glass" />
                </div>
                <div class="flex items-center gap-3">
                    <flux:button variant="primary" wire:click="nuevoAcuerdo" icon="plus">Nuevo Acuerdo</flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Total Acuerdos</p>
                    <p class="text-3xl font-bold">{{ $acuerdos->total() }}</p>
                </div>
                <flux:icon.document-text class="w-10 h-10 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Activos</p>
                    <p class="text-3xl font-bold">{{ $acuerdos->where('isActive', 1)->count() }}</p>
                </div>
                <flux:icon.check-circle class="w-10 h-10 opacity-80" />
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 font-medium">Inactivos</p>
                    <p class="text-3xl font-bold">{{ $acuerdos->where('isActive', 0)->count() }}</p>
                </div>
                <flux:icon.x-circle class="w-10 h-10 opacity-80" />
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center gap-3 mb-4">
            <flux:icon.funnel class="w-5 h-5 text-zinc-500" />
            <flux:heading size="md" class="text-zinc-700 dark:text-zinc-300">Filtros Avanzados</flux:heading>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                <flux:select wire:model.live="isActiveFilter" class="w-full mt-1">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </flux:select>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="$set('isActiveFilter', '')" color="red" icon="trash" class="w-full">
                    Limpiar Filtros
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Tabla de Acuerdos Marco -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Acuerdos Marco</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $acuerdos->count() }} acuerdos encontrados</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-2">
                                <span>Código</span>
                                <flux:icon name="{{ $sortField === 'code' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-2">
                                <span>Nombre</span>
                                <flux:icon name="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}" class="w-4 h-4" />
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
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($acuerdos as $acuerdo)
                        <tr wire:key="acuerdo-{{ $acuerdo->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex items-center gap-2">
                                    <flux:icon.document-text class="w-5 h-5 text-blue-500" />
                                    <span class="font-medium">{{ $acuerdo->code }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $acuerdo->name }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $acuerdo->isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $acuerdo->isActive ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarAcuerdo({{ $acuerdo->id }})" size="xs" variant="primary" icon="pencil" title="Editar acuerdo" class="hover:bg-blue-600 transition-colors"></flux:button>
                                    <flux:button wire:click="eliminarAcuerdo({{ $acuerdo->id }})" size="xs" variant="danger" icon="trash" title="Eliminar acuerdo" class="hover:bg-red-600 transition-colors"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-3">
                                    <flux:icon.inbox class="w-16 h-16 text-zinc-300" />
                                    <span class="text-lg font-medium">No se encontraron acuerdos marco</span>
                                    <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        @if ($acuerdos->hasPages())
            <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $acuerdos->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Acuerdo Marco -->
    <flux:modal wire:model="modal_form_acuerdo" variant="flyout" class="w-2/3 max-w-2xl">
        <form wire:submit.prevent="guardarAcuerdo">
            <div class="space-y-6">
                <div class="border-b pb-4 mb-2 flex items-center gap-3">
                    <flux:icon.document-text class="w-8 h-8 text-blue-500" />
                    <div>
                        <flux:heading size="lg">{{ $acuerdo_id ? 'Editar Acuerdo Marco' : 'Nuevo Acuerdo Marco' }}</flux:heading>
                        <flux:text class="mt-1 text-zinc-500">Complete los datos del acuerdo marco.</flux:text>
                    </div>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon.information-circle class="w-5 h-5 text-blue-400" />
                        <flux:heading size="md">Información del Acuerdo</flux:heading>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:label>Código</flux:label>
                            <flux:input type="text" wire:model.live="code" placeholder="Ej: AM-001" />
                            @error('code')
                                <flux:text class="text-xs text-red-500 mt-1">{{ $message }}</flux:text>
                            @enderror
                        </div>
                        <div>
                            <flux:label>Nombre</flux:label>
                            <flux:input type="text" wire:model.live="name" placeholder="Ej: Acuerdo Marco 2024" />
                            @error('name')
                                <flux:text class="text-xs text-red-500 mt-1">{{ $message }}</flux:text>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm border">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon.cog class="w-5 h-5 text-blue-400" />
                        <flux:heading size="md">Configuración</flux:heading>
                    </div>
                    <div>
                        <flux:label class="flex items-center gap-2">
                            <flux:checkbox wire:model.live="isActive" />
                            <span>Acuerdo activo</span>
                        </flux:label>
                        <flux:text class="text-xs text-zinc-500 mt-1">Los acuerdos inactivos no aparecerán en las listas de selección</flux:text>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <flux:button type="button" wire:click="$set('modal_form_acuerdo', false)" variant="outline">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        {{ $acuerdo_id ? 'Actualizar' : 'Crear' }} Acuerdo
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Confirmar Eliminación -->
    <flux:modal wire:model="modal_form_eliminar_acuerdo" variant="flyout" class="w-1/3 max-w-md">
        <div class="space-y-6">
            <div class="border-b pb-4 mb-2 flex items-center gap-3">
                <flux:icon.exclamation-triangle class="w-8 h-8 text-red-500" />
                <div>
                    <flux:heading size="lg">Confirmar Eliminación</flux:heading>
                    <flux:text class="mt-1 text-zinc-500">Esta acción no se puede deshacer.</flux:text>
                </div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <flux:icon.exclamation-circle class="w-6 h-6 text-red-500 mt-0.5" />
                    <div>
                        <flux:heading size="md" class="text-red-800 dark:text-red-200">¿Estás seguro?</flux:heading>
                        <flux:text class="text-sm text-red-700 dark:text-red-300 mt-1">
                            Al eliminar este acuerdo marco, se perderán todos los datos asociados y no se podrán recuperar.
                        </flux:text>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_acuerdo', false)" variant="outline">
                    Cancelar
                </flux:button>
                <flux:button type="button" wire:click="confirmarEliminarAcuerdo" variant="danger" icon="trash">
                    Eliminar Acuerdo
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
