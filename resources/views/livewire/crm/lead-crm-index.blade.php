<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Leads</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarLeads" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="importar" icon="arrow-up-tray">
                    Importar
                </flux:button>
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevoLead" icon="plus">
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
                <flux:select wire:model.live="estado_filter" class="w-full">
                    <option value="">Todos los estados</option>
                    <option value="nuevo">Nuevo</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="calificado">Calificado</option>
                    <option value="perdido">Perdido</option>
                </flux:select>
            </div>

            <!-- Origen -->
            <div>
                <flux:label>Origen</flux:label>
                <flux:select wire:model.live="origen_filter" class="w-full">
                    <option value="">Todos los orígenes</option>
                    <option value="web">Web</option>
                    <option value="referido">Referido</option>
                    <option value="evento">Evento</option>
                    <option value="redes_sociales">Redes Sociales</option>
                </flux:select>
            </div>

            <!-- Empresa -->
            <div>
                <flux:label>Empresa</flux:label>
                <flux:input wire:model.live="empresa_filter" placeholder="Filtrar por empresa" />
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

    <!-- Tabla de Leads -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('correo')">
                            <div class="flex items-center space-x-1">
                                <span>Correo</span>
                                @if ($sortField === 'correo')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('empresa')">
                            <div class="flex items-center space-x-1">
                                <span>Empresa</span>
                                @if ($sortField === 'empresa')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('estado')">
                            <div class="flex items-center space-x-1">
                                <span>Estado</span>
                                @if ($sortField === 'estado')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('origen')">
                            <div class="flex items-center space-x-1">
                                <span>Origen</span>
                                @if ($sortField === 'origen')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($leads as $lead)
                        <tr wire:key="lead-{{ $lead->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($lead->estado === 'nuevo') bg-blue-100 text-blue-800
                                        @elseif($lead->estado === 'en_proceso') bg-yellow-100 text-yellow-800
                                        @elseif($lead->estado === 'calificado') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $lead->estado)) }}
                                    </span>
                                    <span>{{ $lead->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $lead->correo }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $lead->empresa }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($lead->estado === 'nuevo') bg-blue-100 text-blue-800
                                    @elseif($lead->estado === 'en_proceso') bg-yellow-100 text-yellow-800
                                    @elseif($lead->estado === 'calificado') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $lead->estado)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ ucfirst(str_replace('_', ' ', $lead->origen)) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarLead({{ $lead->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar lead"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarLead({{ $lead->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar lead"
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
        {{ $leads->links() }}
    </div>

    <!-- Modal Form Lead -->
    <flux:modal wire:model="modal_form_lead" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $lead_id ? 'Editar Lead' : 'Nuevo Lead' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos del lead.</flux:text>
            </div>
            <form wire:submit.prevent="guardarLead">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input label="Nombre" wire:model="nombre" placeholder="Ingrese el nombre" />
                    <flux:input label="Correo" type="email" wire:model="correo" placeholder="Ingrese el correo" />
                    <flux:input label="Teléfono" wire:model="telefono" placeholder="Ingrese el teléfono" />
                    <flux:input label="Empresa" wire:model="empresa" placeholder="Ingrese la empresa" />
                    <flux:select label="Estado" wire:model="estado">
                        <option value="nuevo">Nuevo</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="calificado">Calificado</option>
                        <option value="perdido">Perdido</option>
                    </flux:select>
                    <flux:select label="Origen" wire:model="origen">
                        <option value="">Seleccione un origen</option>
                        <option value="web">Web</option>
                        <option value="referido">Referido</option>
                        <option value="evento">Evento</option>
                        <option value="redes_sociales">Redes Sociales</option>
                    </flux:select>
                    <flux:input label="Asignado a" type="number" wire:model="asignado_a" />
                    <flux:input label="Última fecha de contacto" type="date" wire:model="ultima_fecha_contacto" />
                </div>

                <div class="mt-4">
                    <flux:textarea label="Notas" wire:model="notas" placeholder="Ingrese notas" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_lead', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $lead_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Eliminar Lead -->
    @if($lead_id)
    <flux:modal wire:model="modal_form_eliminar_lead" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Lead</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar este lead?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_lead', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarLead">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
