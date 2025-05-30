<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Oportunidades</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarOportunidades" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="importar" icon="arrow-up-tray">
                    Importar
                </flux:button>
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevaOpportunity" icon="plus">
                    Nueva
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
                    <option value="nueva">Nueva</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="ganada">Ganada</option>
                    <option value="perdida">Perdida</option>
                </flux:select>
            </div>

            <!-- Tipo de Negocio -->
            <div>
                <flux:label>Tipo de Negocio</flux:label>
                <flux:select wire:model.live="tipo_negocio_filter" class="w-full">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos_negocio as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Marca -->
            <div>
                <flux:label>Marca</flux:label>
                <flux:select wire:model.live="marca_filter" class="w-full">
                    <option value="">Todas las marcas</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Lead -->
            <div>
                <flux:label>Lead</flux:label>
                <flux:select wire:model.live="lead_filter" class="w-full">
                    <option value="">Todos los leads</option>
                    @foreach($leads as $lead)
                        <option value="{{ $lead->id }}">{{ $lead->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Etapa -->
            <div>
                <flux:label>Etapa</flux:label>
                <flux:select wire:model.live="etapa_filter" class="w-full">
                    <option value="">Todas las etapas</option>
                    <option value="inicial">Inicial</option>
                    <option value="negociacion">Negociación</option>
                    <option value="propuesta">Propuesta</option>
                    <option value="cierre">Cierre</option>
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

    <!-- Tabla de Oportunidades -->
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
                            wire:click="sortBy('valor')">
                            <div class="flex items-center space-x-1">
                                <span>Valor</span>
                                @if ($sortField === 'valor')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('etapa')">
                            <div class="flex items-center space-x-1">
                                <span>Etapa</span>
                                @if ($sortField === 'etapa')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('probabilidad')">
                            <div class="flex items-center space-x-1">
                                <span>Probabilidad</span>
                                @if ($sortField === 'probabilidad')
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($opportunities as $opportunity)
                        <tr wire:key="opportunity-{{ $opportunity->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($opportunity->estado === 'nueva') bg-blue-100 text-blue-800
                                        @elseif($opportunity->estado === 'en_proceso') bg-yellow-100 text-yellow-800
                                        @elseif($opportunity->estado === 'ganada') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $opportunity->estado)) }}
                                    </span>
                                    <span>{{ $opportunity->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                ${{ number_format($opportunity->valor, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($opportunity->etapa === 'inicial') bg-blue-100 text-blue-800
                                    @elseif($opportunity->etapa === 'negociacion') bg-yellow-100 text-yellow-800
                                    @elseif($opportunity->etapa === 'propuesta') bg-purple-100 text-purple-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($opportunity->etapa) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $opportunity->probabilidad }}%
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $opportunity->lead->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarOpportunity({{ $opportunity->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar oportunidad"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarOpportunity({{ $opportunity->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar oportunidad"
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
        {{ $opportunities->links() }}
    </div>

    <!-- Modal Form Oportunidad -->
    <flux:modal wire:model="modal_form_opportunity" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $opportunity_id ? 'Editar Oportunidad' : 'Nueva Oportunidad' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos de la oportunidad.</flux:text>
            </div>
            <form wire:submit.prevent="guardarOpportunity">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input label="Nombre" wire:model="nombre" placeholder="Ingrese el nombre" />
                    <flux:input label="Valor" type="number" step="0.01" wire:model="valor" placeholder="0.00" />
                    <flux:select label="Estado" wire:model="estado">
                        <option value="nueva">Nueva</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="ganada">Ganada</option>
                        <option value="perdida">Perdida</option>
                    </flux:select>

                    <flux:select label="Tipo de Negocio" wire:model="tipo_negocio_id">
                        <option value="">Seleccione un tipo</option>
                        @foreach($tipos_negocio as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Marca" wire:model="marca_id">
                        <option value="">Seleccione una marca</option>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Lead" wire:model="lead_id">
                        <option value="">Seleccione un lead</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Etapa" wire:model="etapa">
                        <option value="inicial">Inicial</option>
                        <option value="negociacion">Negociación</option>
                        <option value="propuesta">Propuesta</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>

                    <flux:input label="Probabilidad (%)" type="number" min="0" max="100" wire:model="probabilidad" />
                    <flux:input label="Fecha de Cierre Esperada" type="date" wire:model="fecha_cierre_esperada" />
                    <flux:input label="Asignado a" type="number" wire:model="asignado_a" />
                    <flux:input label="Última Fecha de Actividad" type="date" wire:model="ultima_fecha_actividad" />
                </div>

                <div class="mt-4">
                    <flux:textarea label="Descripción" wire:model="descripcion" placeholder="Ingrese la descripción" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_opportunity', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $opportunity_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Eliminar Oportunidad -->
    @if($opportunity_id)
    <flux:modal wire:model="modal_form_eliminar_opportunity" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Oportunidad</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar esta oportunidad?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_opportunity', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarOpportunity">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
