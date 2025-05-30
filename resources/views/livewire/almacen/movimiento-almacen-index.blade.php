<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Movimientos de Almacén</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarMovimientos" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevoMovimiento" icon="plus">
                    Nuevo
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Almacén -->
            <div>
                <flux:label>Almacén</flux:label>
                <flux:select wire:model.live="almacen_filter" class="w-full">
                    <option value="">Todos los almacenes</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Producto -->
            <div>
                <flux:label>Producto</flux:label>
                <flux:select wire:model.live="producto_filter" class="w-full">
                    <option value="">Todos los productos</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Tipo -->
            <div>
                <flux:label>Tipo</flux:label>
                <flux:select wire:model.live="tipo_filter" class="w-full">
                    <option value="">Todos</option>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </flux:select>
            </div>

            <!-- Fecha Inicio -->
            <div>
                <flux:label>Fecha Inicio</flux:label>
                <flux:input type="date" wire:model.live="fecha_inicio" />
            </div>

            <!-- Fecha Fin -->
            <div>
                <flux:label>Fecha Fin</flux:label>
                <flux:input type="date" wire:model.live="fecha_fin" />
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

    <!-- Tabla de Movimientos -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Almacén
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Producto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Cantidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($movimientos as $movimiento)
                        <tr wire:key="movimiento-{{ $movimiento->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $movimiento->code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $movimiento->almacen->nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $movimiento->producto->nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($movimiento->tipo === 'entrada') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($movimiento->tipo_movimiento) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $movimiento->cantidad }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $movimiento->fecha_movimiento }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarMovimiento({{ $movimiento->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar movimiento"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarMovimiento({{ $movimiento->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar movimiento"
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
        {{ $movimientos->links() }}
    </div>

    <!-- Modal Form Movimiento -->
    <flux:modal wire:model="modal_form_movimiento" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $movimiento_id ? 'Editar Movimiento' : 'Nuevo Movimiento' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos del movimiento.</flux:text>
            </div>
            <form wire:submit.prevent="guardarMovimiento">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select label="Almacén" wire:model="almacen_id">
                        <option value="">Seleccione un almacén</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Producto" wire:model="producto_id">
                        <option value="">Seleccione un producto</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input label="Código" wire:model="code" placeholder="Ingrese el código" />
                    <flux:input label="Fecha" type="datetime-local" wire:model="fecha" />
                    <flux:input label="Cantidad" type="number" wire:model="cantidad" placeholder="Ingrese la cantidad" />
                    <flux:input label="Observaciones" wire:model="observaciones" placeholder="Ingrese las observaciones" />
                </div>

                <div class="mt-4">
                    <flux:select label="Tipo" wire:model="tipo">
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </flux:select>
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_movimiento', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $movimiento_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Form Eliminar Movimiento -->
    @if($movimiento_id)
    <flux:modal wire:model="modal_form_eliminar_movimiento" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Movimiento</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar este movimiento?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_movimiento', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarMovimiento">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
