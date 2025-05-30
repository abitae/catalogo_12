<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Almacenes</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarAlmacenes" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevoAlmacen" icon="plus">
                    Nuevo
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Estado -->
            <div>
                <flux:label>Estado</flux:label>
                <flux:select wire:model.live="isActive_filter" class="w-full">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </flux:select>
            </div>

            <!-- Registros por página -->
            <div>
                <flux:label>Registros por página</flux:label>
                <flux:select wire:model.live="perPage" class="w-full">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
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

    <!-- Tabla de Almacenes -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-1">
                                <span>Código</span>
                                @if ($sortField === 'code')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('nombre')">
                            <div class="flex items-center space-x-1">
                                <span>Nombre</span>
                                @if ($sortField === 'nombre')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Dirección
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Responsable
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Capacidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($almacenes as $almacen)
                        <tr wire:key="almacen-{{ $almacen->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $almacen->code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $almacen->nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $almacen->direccion }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $almacen->responsable }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $almacen->capacidad }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $almacen->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $almacen->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarAlmacen({{ $almacen->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar almacén"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarAlmacen({{ $almacen->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar almacén"
                                        class="hover:bg-red-600 transition-colors">
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $almacenes->links() }}
    </div>

    <!-- Modal Form Almacén -->
    <flux:modal wire:model="modal_form_almacen" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $almacen_id ? 'Editar Almacén' : 'Nuevo Almacén' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos del almacén.</flux:text>
            </div>
            <form wire:submit.prevent="guardarAlmacen">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input label="Código" wire:model="code" placeholder="Ingrese el código" />
                    <flux:input label="Nombre" wire:model="nombre" placeholder="Ingrese el nombre" />
                    <flux:input label="Dirección" wire:model="direccion" placeholder="Ingrese la dirección" />
                    <flux:input label="Teléfono" wire:model="telefono" placeholder="Ingrese el teléfono" />
                    <flux:input label="Email" wire:model="email" placeholder="Ingrese el email" />
                    <flux:input label="Capacidad" type="number" wire:model="capacidad" placeholder="Ingrese la capacidad" />
                    <flux:input label="Responsable" wire:model="responsable" placeholder="Ingrese el responsable" />
                </div>

                <div class="mt-4">
                    <flux:checkbox label="Activo" wire:model="estado" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_almacen', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $almacen_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Form Eliminar Almacén -->
    @if($almacen_id)
    <flux:modal wire:model="modal_form_eliminar_almacen" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Almacén</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar este almacén?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_almacen', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarAlmacen">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
