<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Oportunidades</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra las oportunidades de venta del CRM</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevaOpportunity" icon="plus">
                    Nueva Oportunidad
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
                    placeholder="Buscar oportunidades por nombre o descripción..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                    <flux:select wire:model.live="estado_filter" class="w-full mt-1">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado }}">{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Negocio</flux:label>
                    <flux:select wire:model.live="tipo_negocio_filter" class="w-full mt-1">
                        <option value="">Todos los tipos</option>
                        @foreach ($tipos_negocio as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Marca</flux:label>
                    <flux:select wire:model.live="marca_filter" class="w-full mt-1">
                        <option value="">Todas las marcas</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Cliente</flux:label>
                    <flux:select wire:model.live="customer_filter" class="w-full mt-1">
                        <option value="">Todos los clientes</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->rznSocial }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Etapa</flux:label>
                    <flux:select wire:model.live="etapa_filter" class="w-full mt-1">
                        <option value="">Todas las etapas</option>
                        @foreach ($etapas as $etapa)
                            <option value="{{ $etapa }}">{{ ucfirst($etapa) }}</option>
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

    <!-- Tabla de Oportunidades -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Oportunidades</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $opportunities->count() }} oportunidades encontradas</span>
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
                            wire:click="sortBy('nombre')">
                            <div class="flex items-center space-x-2">
                                <span>Oportunidad</span>
                                @if ($sortField === 'nombre')
                                    @if ($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Etapa
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Valor
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Probabilidad
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('fecha_cierre_esperada')">
                            <div class="flex items-center space-x-2">
                                <span>Cierre Esperado</span>
                                @if ($sortField === 'fecha_cierre_esperada')
                                    @if ($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($opportunities as $opportunity)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $opportunity->nombre }}</div>
                                    @if ($opportunity->descripcion)
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400 truncate max-w-xs">{{ $opportunity->descripcion }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                @if($opportunity->cliente)
                                    <div class="text-sm font-medium">{{ $opportunity->cliente->rznSocial }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $opportunity->cliente->nombreComercial }}</div>
                                @else
                                    <span class="text-zinc-400">Sin cliente</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($opportunity->estado === 'nueva') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($opportunity->estado === 'en_proceso') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($opportunity->estado === 'ganada') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $opportunity->estado)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($opportunity->etapa === 'inicial') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @elseif($opportunity->etapa === 'negociacion') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($opportunity->etapa === 'propuesta') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @endif">
                                    {{ ucfirst($opportunity->etapa) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                <span class="font-medium">S/ {{ number_format($opportunity->valor, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $opportunity->probabilidad }}%"></div>
                                    </div>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $opportunity->probabilidad }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $opportunity->fecha_cierre_esperada ? $opportunity->fecha_cierre_esperada->format('d/m/Y') : 'Sin fecha' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="verActividades({{ $opportunity->id }})" size="xs" color="blue" icon="eye">
                                    </flux:button>
                                    <flux:button wire:click="editarOpportunity({{ $opportunity->id }})" size="xs" color="blue" icon="pencil">
                                    </flux:button>
                                    <flux:button wire:click="eliminarOpportunity({{ $opportunity->id }})" size="xs" color="red" icon="trash">
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 mb-4 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-medium">No hay oportunidades</p>
                                    <p class="text-sm">Crea tu primera oportunidad para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $opportunities->links() }}
        </div>
    </div>

    <!-- Modal Form Oportunidad -->
    <flux:modal wire:model="modal_form_opportunity" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $opportunity_id ? 'Editar' : 'Nueva' }} Oportunidad
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

                    <div>
                        <flux:label for="valor" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Valor *</flux:label>
                        <flux:input wire:model="valor" id="valor" type="number" step="0.01" placeholder="0.00" class="w-full mt-1" />
                        @error('valor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="etapa" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Etapa *</flux:label>
                        <flux:select wire:model="etapa" id="etapa" class="w-full mt-1">
                            <option value="">Seleccionar etapa</option>
                            @foreach ($etapas as $etapa_option)
                                <option value="{{ $etapa_option }}">{{ ucfirst($etapa_option) }}</option>
                            @endforeach
                        </flux:select>
                        @error('etapa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="probabilidad" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Probabilidad (%)</flux:label>
                        <flux:input wire:model="probabilidad" id="probabilidad" type="number" min="0" max="100" placeholder="0" class="w-full mt-1" />
                        @error('probabilidad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="customer_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Cliente *</flux:label>
                        <flux:select wire:model="customer_id" id="customer_id" class="w-full mt-1">
                            <option value="">Seleccionar cliente</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->rznSocial }}</option>
                            @endforeach
                        </flux:select>
                        @error('customer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="contact_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Contacto</flux:label>
                        <flux:select wire:model="contact_id" id="contact_id" class="w-full mt-1">
                            <option value="">Seleccionar contacto</option>
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}">{{ $contact->nombre }} {{ $contact->apellido }}</option>
                            @endforeach
                        </flux:select>
                        @error('contact_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="tipo_negocio_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Negocio</flux:label>
                        <flux:select wire:model="tipo_negocio_id" id="tipo_negocio_id" class="w-full mt-1">
                            <option value="">Seleccionar tipo</option>
                            @foreach($tipos_negocio as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </flux:select>
                        @error('tipo_negocio_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="marca_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Marca</flux:label>
                        <flux:select wire:model="marca_id" id="marca_id" class="w-full mt-1">
                            <option value="">Seleccionar marca</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                            @endforeach
                        </flux:select>
                        @error('marca_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="fecha_cierre_esperada" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Fecha de Cierre Esperada</flux:label>
                        <flux:input wire:model="fecha_cierre_esperada" id="fecha_cierre_esperada" type="date" class="w-full mt-1" />
                        @error('fecha_cierre_esperada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="fuente" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Fuente</flux:label>
                        <flux:select wire:model="fuente" id="fuente" class="w-full mt-1">
                            <option value="">Seleccionar fuente</option>
                            @foreach ($fuentes as $fuente_option)
                                <option value="{{ $fuente_option }}">{{ ucfirst($fuente_option) }}</option>
                            @endforeach
                        </flux:select>
                        @error('fuente') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="descripcion" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción</flux:label>
                        <flux:textarea wire:model="descripcion" id="descripcion" rows="3" placeholder="Ingrese la descripción" class="w-full mt-1" />
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="notas" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Notas</flux:label>
                        <flux:textarea wire:model="notas" id="notas" rows="3" placeholder="Ingrese notas adicionales" class="w-full mt-1" />
                        @error('notas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                    <flux:button wire:click="$set('modal_form_opportunity', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarOpportunity" variant="primary">
                        {{ $opportunity_id ? 'Actualizar' : 'Crear' }} Oportunidad
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Actividades -->
    <flux:modal wire:model="modal_actividades" variant="flyout" class="w-3/4 max-w-7xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl h-screen flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <flux:icon name="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                Gestión de Actividades
                            </h3>
                            @if($selected_opportunity)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                    <flux:icon name="briefcase" class="w-3 h-3" />
                                    {{ $selected_opportunity->nombre }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button wire:click="nuevaActivity" size="sm" variant="primary" icon="plus">
                            Nueva Actividad
                        </flux:button>
                        <flux:button wire:click="cerrarModalActividades" size="sm" color="gray">
                            Cerrar
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-hidden">
                <!-- Mensajes de notificación -->
                @if (session()->has('success'))
                    <div class="mx-6 mt-4 p-3 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-r-lg flex items-center gap-2">
                        <flux:icon name="check-circle" class="w-4 h-4" />
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mx-6 mt-4 p-3 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-lg flex items-center gap-2">
                        <flux:icon name="exclamation-circle" class="w-4 h-4" />
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-0 h-full">
                    <!-- Lista de Actividades -->
                    <div class="xl:col-span-2 p-6 overflow-y-auto border-r border-zinc-200 dark:border-zinc-700">
                        @if(!$selected_opportunity)
                            <div class="text-center py-12">
                                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center">
                                    <flux:icon name="exclamation-triangle" class="w-8 h-8 text-red-500 dark:text-red-400" />
                                </div>
                                <h4 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Selecciona una Oportunidad</h4>
                                <p class="text-zinc-500 dark:text-zinc-400">Para gestionar actividades, primero selecciona una oportunidad</p>
                            </div>
                        @else
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                        Actividades
                                    </h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $selected_opportunity->actividades ? $selected_opportunity->actividades->count() : 0 }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:button size="xs" color="gray" class="text-xs">
                                        Filtrar
                                    </flux:button>
                                    <flux:button size="xs" color="gray" class="text-xs">
                                        Ordenar
                                    </flux:button>
                                </div>
                            </div>

                            @if($selected_opportunity->actividades && $selected_opportunity->actividades->count() > 0)
                                <div class="space-y-3">
                                    @foreach($selected_opportunity->actividades as $activity)
                                        <div class="bg-white dark:bg-zinc-700/50 rounded-lg p-4 border border-zinc-200 dark:border-zinc-600 hover:shadow-md transition-shadow cursor-pointer"
                                             wire:click="editarActivity({{ $activity->id }})">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                        @if($activity->tipo === 'llamada') bg-blue-100 dark:bg-blue-900
                                                        @elseif($activity->tipo === 'email') bg-green-100 dark:bg-green-900
                                                        @elseif($activity->tipo === 'reunion') bg-purple-100 dark:bg-purple-900
                                                        @elseif($activity->tipo === 'tarea') bg-orange-100 dark:bg-orange-900
                                                        @else bg-gray-100 dark:bg-gray-700
                                                        @endif">
                                                        <span class="text-xs font-medium
                                                            @if($activity->tipo === 'llamada') text-blue-600 dark:text-blue-400
                                                            @elseif($activity->tipo === 'email') text-green-600 dark:text-green-400
                                                            @elseif($activity->tipo === 'reunion') text-purple-600 dark:text-purple-400
                                                            @elseif($activity->tipo === 'tarea') text-orange-600 dark:text-orange-400
                                                            @else text-gray-600 dark:text-gray-400
                                                            @endif">
                                                            {{ strtoupper(substr($activity->tipo, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <h5 class="font-medium text-zinc-900 dark:text-white text-sm">{{ $activity->asunto }}</h5>
                                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($activity->tipo) }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                        @if($activity->estado === 'completada') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @elseif($activity->estado === 'en_proceso') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                        @elseif($activity->estado === 'pendiente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $activity->estado)) }}
                                                    </span>
                                                    <flux:button wire:click.stop="eliminarActivity({{ $activity->id }})" size="xs" color="red" icon="trash" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                    </flux:button>
                                                </div>
                                            </div>

                                            @if($activity->descripcion)
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3 line-clamp-2">{{ $activity->descripcion }}</p>
                                            @endif

                                            <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                                                <div class="flex items-center gap-3">
                                                    @if($activity->contacto)
                                                        <span class="flex items-center gap-1">
                                                            {{ $activity->contacto->nombre }} {{ $activity->contacto->apellido }}
                                                        </span>
                                                    @endif
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if($activity->prioridad === 'urgente') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                        @elseif($activity->prioridad === 'alta') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                        @elseif($activity->prioridad === 'normal') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                        @endif">
                                                        {{ ucfirst($activity->prioridad) }}
                                                    </span>
                                                </div>
                                                <span>{{ $activity->created_at->format('d/m/Y H:i') }}</span>
                                            </div>

                                            @if($activity->image)
                                                <div class="mt-2 flex items-center gap-2">
                                                    <a href="{{ asset('storage/' . $activity->image) }}" target="_blank"
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                                                        title="Ver imagen">
                                                        Imagen
                                                    </a>
                                                </div>
                                            @endif
                                            @if($activity->archivo)
                                                <div class="mt-2 flex items-center gap-2">
                                                    <a href="{{ asset('storage/' . $activity->archivo) }}" target="_blank"
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800 transition-colors"
                                                        title="Ver archivo">
                                                        Archivo
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 mx-auto mb-4 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-zinc-500 dark:text-zinc-400">No hay actividades registradas</p>
                                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Crea la primera actividad para comenzar</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Formulario de Actividad -->
                    <div class="p-6 border-l border-zinc-200 dark:border-zinc-700 overflow-y-auto">
                        @if(!$selected_opportunity)
                            <div class="text-center py-8">
                                <div class="w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <p class="text-red-500 dark:text-red-400">Selecciona una oportunidad para crear actividades</p>
                            </div>
                        @else
                            <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">
                                {{ $activity_id ? 'Editar' : 'Nueva' }} Actividad
                            </h4>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <flux:label for="tipo_activity" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo *</flux:label>
                                        <flux:select wire:model="tipo_activity" id="tipo_activity" class="w-full mt-1">
                                            <option value="">Seleccionar tipo</option>
                                            @foreach($tipos_activity as $tipo)
                                                <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('tipo_activity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <flux:label for="asunto_activity" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Asunto *</flux:label>
                                        <flux:input wire:model="asunto_activity" id="asunto_activity" type="text" placeholder="Ingrese el asunto" class="w-full mt-1" />
                                        @error('asunto_activity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <flux:label for="estado_activity" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado *</flux:label>
                                        <flux:select wire:model="estado_activity" id="estado_activity" class="w-full mt-1">
                                            <option value="">Seleccionar estado</option>
                                            @foreach($estados_activity as $estado)
                                                <option value="{{ $estado }}">{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('estado_activity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <flux:label for="prioridad_activity" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Prioridad *</flux:label>
                                        <flux:select wire:model="prioridad_activity" id="prioridad_activity" class="w-full mt-1">
                                            <option value="">Seleccionar prioridad</option>
                                            @foreach($prioridades_activity as $prioridad)
                                                <option value="{{ $prioridad }}">{{ ucfirst($prioridad) }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('prioridad_activity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <flux:label for="contact_id_activity" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Contacto</flux:label>
                                        <flux:select wire:model="contact_id_activity" id="contact_id_activity" class="w-full mt-1">
                                            <option value="">Seleccionar contacto</option>
                                            @if($selected_opportunity && $selected_opportunity->cliente && $selected_opportunity->cliente->contactos)
                                                @foreach($selected_opportunity->cliente->contactos as $contact)
                                                    <option value="{{ $contact->id }}">{{ $contact->nombre }} {{ $contact->apellido }}</option>
                                                @endforeach
                                            @endif
                                        </flux:select>
                                        @error('contact_id_activity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <flux:label for="descripcion_activity" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción</flux:label>
                                    <flux:textarea wire:model="descripcion_activity" id="descripcion_activity" rows="3" placeholder="Ingrese la descripción" class="w-full mt-1" />
                                    @error('descripcion_activity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Imagen</flux:label>
                                    <flux:input wire:model="tempImageActivity" type="file" accept="image/*" class="w-full mt-1" />
                                    @error('tempImageActivity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    @if($imagePreviewActivity)
                                        <div class="mt-2">
                                            <img src="{{ $imagePreviewActivity }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg">
                                            <flux:button wire:click="removeImageActivity" size="sm" color="red" class="mt-2">Remover</flux:button>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Archivo</flux:label>
                                    <flux:input wire:model="tempArchivoActivity" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="w-full mt-1" />
                                    @error('tempArchivoActivity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <flux:button wire:click="resetActivityForm" color="gray" size="sm">
                                        Limpiar
                                    </flux:button>
                                    <flux:button wire:click="guardarActivity" variant="primary" size="sm">
                                        {{ $activity_id ? 'Actualizar' : 'Crear' }} Actividad
                                    </flux:button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_opportunity" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <p class="text-zinc-600 dark:text-zinc-400">
                    ¿Estás seguro de que quieres eliminar esta oportunidad? Esta acción no se puede deshacer.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_opportunity', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarOpportunity" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
