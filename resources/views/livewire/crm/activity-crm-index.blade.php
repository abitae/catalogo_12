<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Actividades</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra las actividades del CRM</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevaActivity" icon="plus">
                    Nueva Actividad
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
                    placeholder="Buscar actividades por asunto o descripción..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo</flux:label>
                    <flux:select wire:model.live="tipo_filter" class="w-full mt-1">
                        <option value="">Todos los tipos</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                    <flux:select wire:model.live="estado_filter" class="w-full mt-1">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Prioridad</flux:label>
                    <flux:select wire:model.live="prioridad_filter" class="w-full mt-1">
                        <option value="">Todas las prioridades</option>
                        @foreach ($prioridades as $prioridad)
                            <option value="{{ $prioridad }}">{{ ucfirst($prioridad) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Oportunidad</flux:label>
                    <flux:select wire:model.live="opportunity_filter" class="w-full mt-1">
                        <option value="">Todas las oportunidades</option>
                        @foreach ($opportunities as $opportunity)
                            <option value="{{ $opportunity->id }}">{{ $opportunity->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Contacto</flux:label>
                    <flux:select wire:model.live="contact_filter" class="w-full mt-1">
                        <option value="">Todos los contactos</option>
                        @foreach ($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->nombre }} {{ $contact->apellido }}</option>
                        @endforeach
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

    <!-- Tabla de Actividades -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Actividades</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $activities->count() }} actividades encontradas</span>
                    <flux:select wire:model.live="perPage" class="w-32">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('asunto')">
                            <div class="flex items-center space-x-2">
                                <span>Asunto</span>
                                @if ($sortField === 'asunto')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Prioridad
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Relacionado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-2">
                                <span>Fecha</span>
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
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $activity->asunto }}</div>
                                    @if ($activity->descripcion)
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400 truncate max-w-xs">{{ $activity->descripcion }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($activity->tipo === 'llamada') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($activity->tipo === 'reunion') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($activity->tipo === 'email') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @endif">
                                    {{ ucfirst($activity->tipo) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($activity->estado === 'pendiente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($activity->estado === 'completada') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ ucfirst($activity->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($activity->prioridad === 'baja') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @elseif($activity->prioridad === 'normal') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($activity->prioridad === 'alta') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ ucfirst($activity->prioridad) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                @if($activity->oportunidad)
                                    <div class="text-sm font-medium">{{ $activity->oportunidad->nombre }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Oportunidad</div>
                                @elseif($activity->contacto)
                                    <div class="text-sm font-medium">{{ $activity->contacto->nombre }} {{ $activity->contacto->apellido }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Contacto</div>
                                @else
                                    <span class="text-zinc-400">Sin relación</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $activity->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarActivity({{ $activity->id }})" size="sm" color="blue" icon="pencil">
                                        Editar
                                    </flux:button>
                                    <flux:button wire:click="eliminarActivity({{ $activity->id }})" size="sm" color="red" icon="trash">
                                        Eliminar
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="inbox" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay actividades</p>
                                    <p class="text-sm">Crea tu primera actividad para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $activities->links() }}
        </div>
    </div>

    <!-- Modal Form Actividad -->
    <flux:modal wire:model="modal_form_activity" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $activity_id ? 'Editar' : 'Nueva' }} Actividad
                </h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:label for="tipo" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo *</flux:label>
                        <flux:select wire:model="tipo" id="tipo" class="w-full mt-1">
                            <option value="">Seleccionar tipo</option>
                            @foreach ($tipos as $tipo_option)
                                <option value="{{ $tipo_option }}">{{ ucfirst($tipo_option) }}</option>
                            @endforeach
                        </flux:select>
                        @error('tipo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="asunto" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Asunto *</flux:label>
                        <flux:input wire:model="asunto" id="asunto" type="text" class="w-full mt-1" />
                        @error('asunto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="estado" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado *</flux:label>
                        <flux:select wire:model="estado" id="estado" class="w-full mt-1">
                            <option value="">Seleccionar estado</option>
                            @foreach ($estados as $estado_option)
                                <option value="{{ $estado_option }}">{{ ucfirst($estado_option) }}</option>
                            @endforeach
                        </flux:select>
                        @error('estado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="prioridad" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Prioridad *</flux:label>
                        <flux:select wire:model="prioridad" id="prioridad" class="w-full mt-1">
                            <option value="">Seleccionar prioridad</option>
                            @foreach ($prioridades as $prioridad_option)
                                <option value="{{ $prioridad_option }}">{{ ucfirst($prioridad_option) }}</option>
                            @endforeach
                        </flux:select>
                        @error('prioridad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="opportunity_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Oportunidad</flux:label>
                        <flux:select wire:model="opportunity_id" id="opportunity_id" class="w-full mt-1">
                            <option value="">Seleccionar oportunidad</option>
                            @foreach ($opportunities as $opportunity)
                                <option value="{{ $opportunity->id }}">{{ $opportunity->nombre }}</option>
                            @endforeach
                        </flux:select>
                        @error('opportunity_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="contact_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Contacto</flux:label>
                        <flux:select wire:model="contact_id" id="contact_id" class="w-full mt-1">
                            <option value="">Seleccionar contacto</option>
                            @foreach ($contacts as $contact)
                                <option value="{{ $contact->id }}">{{ $contact->nombre }} {{ $contact->apellido }}</option>
                            @endforeach
                        </flux:select>
                        @error('contact_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="descripcion" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción</flux:label>
                        <flux:textarea wire:model="descripcion" id="descripcion" rows="3" class="w-full mt-1" />
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Imagen</flux:label>
                        <flux:input wire:model="tempImage" type="file" accept="image/*" class="w-full mt-1" />
                        @error('tempImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @if($imagePreview)
                            <div class="mt-2">
                                <img src="{{ $imagePreview }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg">
                                <flux:button wire:click="removeImage" size="sm" color="red" class="mt-2">Remover</flux:button>
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Archivo</flux:label>
                        <flux:input wire:model="tempArchivo" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="w-full mt-1" />
                        @error('tempArchivo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_activity', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarActivity" variant="primary">
                        {{ $activity_id ? 'Actualizar' : 'Crear' }} Actividad
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_activity" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <p class="text-zinc-600 dark:text-zinc-400">
                    ¿Estás seguro de que quieres eliminar esta actividad? Esta acción no se puede deshacer.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_activity', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarActivity" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
