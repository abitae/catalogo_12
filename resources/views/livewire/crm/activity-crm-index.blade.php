<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Actividades</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarActividades" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="importar" icon="arrow-up-tray">
                    Importar
                </flux:button>
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevaActividad" icon="plus">
                    Nueva
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tipo -->
            <div>
                <flux:label>Tipo</flux:label>
                <flux:select wire:model.live="tipo_filter" class="w-full">
                    <option value="">Todos los tipos</option>
                    <option value="llamada">Llamada</option>
                    <option value="reunion">Reunión</option>
                    <option value="email">Email</option>
                    <option value="tarea">Tarea</option>
                </flux:select>
            </div>

            <!-- Estado -->
            <div>
                <flux:label>Estado</flux:label>
                <flux:select wire:model.live="estado_filter" class="w-full">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                </flux:select>
            </div>

            <!-- Prioridad -->
            <div>
                <flux:label>Prioridad</flux:label>
                <flux:select wire:model.live="prioridad_filter" class="w-full">
                    <option value="">Todas las prioridades</option>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
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

    <!-- Tabla de Actividades -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('titulo')">
                            <div class="flex items-center space-x-1">
                                <span>Título</span>
                                @if ($sortField === 'titulo')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('tipo')">
                            <div class="flex items-center space-x-1">
                                <span>Tipo</span>
                                @if ($sortField === 'tipo')
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
                            wire:click="sortBy('prioridad')">
                            <div class="flex items-center space-x-1">
                                <span>Prioridad</span>
                                @if ($sortField === 'prioridad')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('fecha_inicio')">
                            <div class="flex items-center space-x-1">
                                <span>Fecha Inicio</span>
                                @if ($sortField === 'fecha_inicio')
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
                    @foreach($activities as $activity)
                        <tr wire:key="activity-{{ $activity->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($activity->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                        @elseif($activity->estado === 'en_proceso') bg-blue-100 text-blue-800
                                        @elseif($activity->estado === 'completada') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $activity->estado)) }}
                                    </span>
                                    <span>{{ $activity->titulo }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ ucfirst($activity->tipo) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($activity->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                    @elseif($activity->estado === 'en_proceso') bg-blue-100 text-blue-800
                                    @elseif($activity->estado === 'completada') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $activity->estado)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($activity->prioridad === 'baja') bg-green-100 text-green-800
                                    @elseif($activity->prioridad === 'media') bg-yellow-100 text-yellow-800
                                    @elseif($activity->prioridad === 'alta') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($activity->prioridad) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $activity->fecha_inicio }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarActividad({{ $activity->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar actividad"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarActividad({{ $activity->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar actividad"
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
        {{ $activities->links() }}
    </div>

    <!-- Modal Form Actividad -->
    <flux:modal wire:model="modal_form_activity" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $activity_id ? 'Editar Actividad' : 'Nueva Actividad' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos de la actividad.</flux:text>
            </div>
            <form wire:submit.prevent="guardarActividad">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input label="Título" wire:model="titulo" placeholder="Ingrese el título" />
                    <flux:select label="Tipo" wire:model="tipo">
                        <option value="llamada">Llamada</option>
                        <option value="reunion">Reunión</option>
                        <option value="email">Email</option>
                        <option value="tarea">Tarea</option>
                    </flux:select>
                    <flux:select label="Estado" wire:model="estado">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                    <flux:select label="Prioridad" wire:model="prioridad">
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </flux:select>
                    <flux:input label="Fecha Inicio" type="datetime-local" wire:model="fecha_inicio" />
                    <flux:input label="Fecha Fin" type="datetime-local" wire:model="fecha_fin" />
                    <flux:input label="Asignado a" type="number" wire:model="asignado_a" />
                    <flux:select label="Lead" wire:model="lead_id">
                        <option value="">Seleccione un lead</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->nombre }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select label="Oportunidad" wire:model="opportunity_id">
                        <option value="">Seleccione una oportunidad</option>
                        @foreach($opportunities as $opportunity)
                            <option value="{{ $opportunity->id }}">{{ $opportunity->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="mt-4">
                    <flux:textarea label="Descripción" wire:model="descripcion" placeholder="Ingrese la descripción" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_activity', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $activity_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Eliminar Actividad -->
    @if($activity_id)
    <flux:modal wire:model="modal_form_eliminar_activity" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Actividad</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar esta actividad?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_activity', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarActividad">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
