<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-3 bg-zinc-50 dark:bg-zinc-800 rounded-xl p-3 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
            <div>
                <flux:heading size="sm" class="text-zinc-900 dark:text-white">Gestión de Oportunidades</flux:heading>
                <flux:text class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Administra las oportunidades de venta del CRM</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <flux:button variant="primary" wire:click="nuevaOpportunity" icon="plus" size="xs">
                    Nueva Oportunidad
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="mb-3 bg-zinc-50 dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="p-3">
            <!-- Búsqueda -->
            <div class="mb-3">
                <flux:input type="search" placeholder="Buscar oportunidades..."
                    wire:model.live="search" icon="magnifying-glass" class="w-full" size="sm" />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-2">
                <div>
                    <flux:label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Estado</flux:label>
                    <flux:select wire:model.live="estado_filter" class="w-full mt-1" size="sm">
                        <option value="">Todos</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado }}">{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Tipo</flux:label>
                    <flux:select wire:model.live="tipo_negocio_filter" class="w-full mt-1" size="sm">
                        <option value="">Todos</option>
                        @foreach ($tipos_negocio as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Marca</flux:label>
                    <flux:select wire:model.live="marca_filter" class="w-full mt-1" size="sm">
                        <option value="">Todas</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Cliente</flux:label>
                    <x-mary-choices-offline wire:model.live="customer_filter" id="customer_filter" class="w-full mt-1"
                        :options="$customers" single clearable option-label="rznSocial" searchable
                        placeholder="Todos" size="sm" />
                </div>

                <div>
                    <flux:label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Etapa</flux:label>
                    <flux:select wire:model.live="etapa_filter" class="w-full mt-1" size="sm">
                        <option value="">Todas</option>
                        @foreach ($etapas as $etapa)
                            <option value="{{ $etapa }}">{{ ucfirst($etapa) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="flex items-end">
                    <flux:button wire:click="clearFilters" color="red" icon="trash" class="w-full" size="sm">
                        Limpiar
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Oportunidades -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Oportunidades</h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $opportunities->count() }} oportunidades</span>
                    <flux:select wire:model.live="perPage" class="w-24 text-xs">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                    <tr>
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Código
                        </th>
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('nombre')">
                            <div class="flex items-center space-x-1">
                                <span>Oportunidad</span>
                                <flux:icon
                                    name="{{ $sortField === 'nombre' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-3 h-3" />
                            </div>
                        </th>
                        
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Encargado
                        </th>
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Etapa
                        </th>
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Valor
                        </th>
                        
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('fecha_cierre_esperada')">
                            <div class="flex items-center space-x-1">
                                <span>Cierre</span>
                                <flux:icon
                                    name="{{ $sortField === 'fecha_cierre_esperada' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : 'arrows-up-down' }}"
                                    class="w-3 h-3" />
                            </div>
                        </th>
                        <th class="px-2 py-1.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($opportunities as $opportunity)
                        <tr wire:key="opportunity-{{ $opportunity->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-2 py-1.5 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                @if ($opportunity->codigo_oportunidad)
                                    <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">
                                        {{ $opportunity->codigo_oportunidad }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-1.5 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $opportunity->nombre }}</div>
                                    @if ($opportunity->descripcion)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate max-w-40">
                                            {{ $opportunity->descripcion }}</div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-2 py-1.5 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                @if ($opportunity->cliente)
                                    <div class="text-sm font-medium">{{ $opportunity->cliente->rznSocial }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $opportunity->cliente->nombreComercial }}</div>
                                @else
                                    <span class="text-zinc-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-1.5 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                @if ($opportunity->usuario)
                                    <span class="text-sm font-medium truncate max-w-20">{{ $opportunity->usuario->name }}</span>
                                @else
                                    <span class="text-zinc-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-1.5 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($opportunity->etapa === 'aceptada') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($opportunity->etapa === 'entregada') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($opportunity->etapa === 'pagada') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                    {{ ucfirst($opportunity->etapa) }}
                                </span>
                            </td>
                            <td class="px-2 py-1.5 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                <span class="font-medium text-sm">S/ {{ number_format($opportunity->valor, 0) }}</span>
                            </td>
                            
                            <td class="px-2 py-1.5 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $opportunity->fecha_cierre_esperada ? $opportunity->fecha_cierre_esperada->format('d/m') : '-' }}
                            </td>
                            <td class="px-2 py-1.5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-1">
                                    <flux:button wire:click="verActividades({{ $opportunity->id }})" size="xs"
                                        color="blue" icon="eye" class="!px-1.5 !py-0.5">
                                    </flux:button>
                                    <flux:button wire:click="editarOpportunity({{ $opportunity->id }})"
                                        size="xs" color="blue" icon="pencil" class="!px-1.5 !py-0.5">
                                    </flux:button>
                                    <flux:button wire:click="eliminarOpportunity({{ $opportunity->id }})"
                                        size="xs" color="red" icon="trash" class="!px-1.5 !py-0.5">
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-12 h-12 mb-4 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
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
        <div class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700">
            {{ $opportunities->links() }}
        </div>
    </div>

    <!-- Modal Form Oportunidad Optimizado -->
    <flux:modal wire:model="modal_form_opportunity" variant="flyout" class="w-full max-w-6xl">
        <form wire:submit.prevent="guardarOpportunity">
            <!-- Header Compacto -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 rounded-t-lg text-white mb-4">
                <div class="flex items-center gap-3">
                    <flux:icon name="chart-bar" class="w-6 h-6" />
                    <div>
                        <h2 class="text-lg font-bold">
                            {{ $opportunity_id ? 'Editar Oportunidad' : 'Nueva Oportunidad' }}
                        </h2>
                        <p class="text-blue-100 text-sm">Complete los datos de la oportunidad</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-4 h-full">
                <!-- Columna Izquierda - Información Principal -->
                <div class="lg:w-3/5 space-y-3">
                    <!-- Información Básica Compacta -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="document" class="w-4 h-4 text-blue-600" />
                            <h3 class="text-sm font-semibold text-gray-800">Información Básica</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <flux:label class="text-xs font-medium">Nombre de la Oportunidad *</flux:label>
                                <flux:input wire:model="nombre" type="text"
                                    placeholder="Ej: Implementación CRM Enterprise" size="sm" />
                                @error('nombre')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Código de Oportunidad</flux:label>
                                <flux:input wire:model="codigo_oportunidad" type="text"
                                    placeholder="Ej: OPP-2024-001" size="sm" />
                                @error('codigo_oportunidad')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Valor Estimado *</flux:label>
                                    <div class="relative">
                                        <flux:input wire:model="valor" type="number" step="0.01"
                                            placeholder="0.00" size="sm" class="pl-8" />
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm">S/</span>
                                        </div>
                                    </div>
                                    @error('valor')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Probabilidad (%)</flux:label>
                                    <div class="relative">
                                        <flux:input wire:model="probabilidad" type="number" min="0"
                                            max="100" placeholder="0" size="sm" class="pr-8" />
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm">%</span>
                                        </div>
                                    </div>
                                    @error('probabilidad')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Etapa *</flux:label>
                                    <flux:select wire:model="etapa" size="sm">
                                        <option value="">Seleccionar etapa</option>
                                        @foreach ($etapas as $etapa_option)
                                            <option value="{{ $etapa_option }}">{{ ucfirst($etapa_option) }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('etapa')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Fuente</flux:label>
                                    <flux:select wire:model="fuente" size="sm">
                                        <option value="">Seleccionar fuente</option>
                                        @foreach ($fuentes as $fuente_option)
                                            <option value="{{ $fuente_option }}">{{ ucfirst($fuente_option) }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                    @error('fuente')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Fecha Cierre</flux:label>
                                    <flux:input wire:model="fecha_cierre_esperada" type="date" size="sm" />
                                    @error('fecha_cierre_esperada')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Encargado *</flux:label>
                                    <x-mary-choices-offline wire:model="user_id" :options="$users" single clearable
                                        option-label="name" searchable placeholder="Seleccionar encargado"
                                        size="sm" />
                                    @error('user_id')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cliente y Contacto Compacto -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="user" class="w-4 h-4 text-green-600" />
                            <h3 class="text-sm font-semibold text-gray-800">Cliente y Contacto</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <div class="flex items-center justify-between">
                                    <flux:label class="text-xs font-medium">Cliente *</flux:label>
                                    <flux:button type="button" size="xs" color="primary" icon="plus"
                                        class="!px-2 !py-1" wire:click="nuevoCliente">Nuevo</flux:button>
                                </div>
                                <x-mary-choices-offline wire:model.live="customer_id" :options="$customers" single
                                    clearable option-label="rznSocial" searchable placeholder="Seleccionar cliente"
                                    size="sm" />
                                @error('customer_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <div class="flex items-center justify-between">
                                    <flux:label class="text-xs font-medium">Contacto *</flux:label>
                                    @if ($customer_id)
                                        <flux:button type="button" size="xs" color="primary" icon="plus"
                                            class="!px-2 !py-1" wire:click="nuevoContacto">Nuevo</flux:button>
                                    @endif
                                </div>
                                @if (!$customer_id)
                                    <div
                                        class="p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
                                        <flux:icon name="exclamation-triangle" class="w-3 h-3 inline mr-1" />
                                        Primero selecciona un cliente
                                    </div>
                                @else
                                    <x-mary-choices-offline wire:model="contact_id" :options="$customer_id
                                        ? \App\Models\Crm\ContactCrm::where('customer_id', $customer_id)->get()
                                        : []" single clearable
                                        option-label="nombre" searchable placeholder="Seleccionar contacto"
                                        size="sm" />
                                @endif
                                @error('contact_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Tipo de Negocio</flux:label>
                                <flux:select wire:model="tipo_negocio_id" size="sm">
                                    <option value="">Seleccionar tipo</option>
                                    @foreach ($tipos_negocio as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </flux:select>
                                @error('tipo_negocio_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Marca *</flux:label>
                                <x-mary-choices-offline wire:model="marca_id" :options="$marcas" single clearable
                                    option-label="nombre" searchable placeholder="Seleccionar marca"
                                    size="sm" />
                                @error('marca_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Descripción y Notas Compactas -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="chat-bubble-left-ellipsis" class="w-4 h-4 text-purple-600" />
                            <h3 class="text-sm font-semibold text-gray-800">Descripción y Notas</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <flux:label class="text-xs font-medium">Descripción</flux:label>
                                <flux:textarea wire:model="descripcion" rows="3"
                                    placeholder="Describe los detalles de la oportunidad..." size="sm" />
                                @error('descripcion')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Notas Adicionales</flux:label>
                                <flux:textarea wire:model="notas" rows="3"
                                    placeholder="Información adicional, observaciones..." size="sm" />
                                @error('notas')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Archivos y Acciones -->
                <div class="lg:w-2/5">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <flux:icon name="document" class="w-5 h-5 text-orange-600" />
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">Archivos Adjuntos</h3>
                                    <p class="text-xs text-gray-500">Agregue imágenes y documentos relacionados</p>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen de la Oportunidad -->
                        <div class="mb-4">
                            <flux:label class="text-xs font-medium">Imagen de la Oportunidad</flux:label>
                            <div
                                class="mt-1 p-4 border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-orange-400 transition-colors">
                                <flux:input wire:model="tempImage" type="file" accept="image/*" class="w-full" />
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF hasta 20MB</p>
                            </div>
                            @error('tempImage')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                            @if ($imagePreview)
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <img src="{{ $imagePreview }}" alt="Preview"
                                        class="w-24 h-24 object-cover rounded-lg mx-auto">
                                    <flux:button wire:click="removeImage" size="sm" color="red"
                                        class="mt-2 w-full">
                                        <flux:icon name="trash" class="w-3 h-3 mr-1" />
                                        Remover Imagen
                                    </flux:button>
                                </div>
                            @endif
                        </div>

                        <!-- Documento Adjunto -->
                        <div class="mb-4">
                            <flux:label class="text-xs font-medium">Documento Adjunto</flux:label>
                            <div
                                class="mt-1 p-4 border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-orange-400 transition-colors">
                                <flux:input wire:model="tempArchivo" type="file"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="w-full" />
                                <p class="text-xs text-gray-500 mt-2">PDF, DOC, XLS, PPT hasta 10MB</p>
                            </div>
                            @error('tempArchivo')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Información Adicional -->
                        <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <flux:icon name="information-circle" class="w-4 h-4 text-blue-600" />
                                <h4 class="text-sm font-medium text-blue-800">Información de la Oportunidad</h4>
                            </div>
                            <div class="text-xs text-blue-700 space-y-1">
                                <p>• Los campos marcados con * son obligatorios</p>
                                <p>• La imagen debe ser clara y representativa</p>
                                <p>• Los documentos deben estar en formato legible</p>
                                <p>• La fecha de cierre ayuda a priorizar las oportunidades</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Compacto -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <flux:icon name="information-circle" class="w-4 h-4 inline mr-1" />
                        Complete todos los campos obligatorios para continuar
                    </div>
                    <div class="flex gap-3">
                        <flux:button type="button" wire:click="cerrarModalOpportunity" variant="outline"
                            size="sm">

                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary" size="sm">

                            {{ $opportunity_id ? 'Actualizar' : 'Crear' }} Oportunidad
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Actividades -->
    <flux:modal wire:model="modal_actividades" variant="flyout" class="w-3/4 max-w-7xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl h-screen flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <flux:icon name="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                Gestión de Actividades
                            </h3>
                            @if ($selected_opportunity)
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
                    <div
                        class="mx-6 mt-4 p-3 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-r-lg flex items-center gap-2">
                        <flux:icon name="check-circle" class="w-4 h-4" />
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div
                        class="mx-6 mt-4 p-3 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-lg flex items-center gap-2">
                        <flux:icon name="exclamation-circle" class="w-4 h-4" />
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-0 h-full">
                    <!-- Lista de Actividades -->
                    <div class="xl:col-span-2 p-6 overflow-y-auto border-r border-zinc-200 dark:border-zinc-700">
                        @if (!$selected_opportunity)
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center">
                                    <flux:icon name="exclamation-triangle"
                                        class="w-8 h-8 text-red-500 dark:text-red-400" />
                                </div>
                                <h4 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Selecciona una
                                    Oportunidad</h4>
                                <p class="text-zinc-500 dark:text-zinc-400">Para gestionar actividades, primero
                                    selecciona una oportunidad</p>
                            </div>
                        @else
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                        Actividades
                                    </h4>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
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

                            @if ($selected_opportunity->actividades && $selected_opportunity->actividades->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($selected_opportunity->actividades->sortByDesc('created_at') as $activity)
                                        <div class="bg-white dark:bg-zinc-700/50 rounded-lg p-4 border border-zinc-200 dark:border-zinc-600 hover:shadow-md transition-shadow cursor-pointer"
                                            wire:click="editarActivity({{ $activity->id }})">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-8 h-8 rounded-full flex items-center justify-center
                                                        @if ($activity->tipo === 'llamada') bg-blue-100 dark:bg-blue-900
                                                        @elseif($activity->tipo === 'email') bg-green-100 dark:bg-green-900
                                                        @elseif($activity->tipo === 'reunion') bg-purple-100 dark:bg-purple-900
                                                        @elseif($activity->tipo === 'tarea') bg-orange-100 dark:bg-orange-900
                                                        @else bg-gray-100 dark:bg-gray-700 @endif">
                                                        <span
                                                            class="text-xs font-medium
                                                            @if ($activity->tipo === 'llamada') text-blue-600 dark:text-blue-400
                                                            @elseif($activity->tipo === 'email') text-green-600 dark:text-green-400
                                                            @elseif($activity->tipo === 'reunion') text-purple-600 dark:text-purple-400
                                                            @elseif($activity->tipo === 'tarea') text-orange-600 dark:text-orange-400
                                                            @else text-gray-600 dark:text-gray-400 @endif">
                                                            {{ strtoupper(substr($activity->tipo, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <h5 class="font-medium text-zinc-900 dark:text-white text-sm">
                                                            {{ $activity->asunto }}</h5>
                                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                            {{ ucfirst($activity->tipo) }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                        @if ($activity->estado === 'completada') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @elseif($activity->estado === 'en_proceso') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                        @elseif($activity->estado === 'pendiente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $activity->estado)) }}
                                                    </span>
                                                    <flux:button
                                                        wire:click.stop="eliminarActivity({{ $activity->id }})"
                                                        size="xs" color="red" icon="trash"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                    </flux:button>
                                                </div>
                                            </div>

                                            @if ($activity->descripcion)
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3 line-clamp-2">
                                                    {{ $activity->descripcion }}</p>
                                            @endif

                                            <div
                                                class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                                                <div class="flex items-center gap-3">
                                                    @if ($activity->contacto)
                                                        <span class="flex items-center gap-1">
                                                            {{ $activity->contacto->nombre }}
                                                            {{ $activity->contacto->apellido }}
                                                        </span>
                                                    @endif
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if ($activity->prioridad === 'urgente') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                        @elseif($activity->prioridad === 'alta') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                        @elseif($activity->prioridad === 'normal') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                                        {{ ucfirst($activity->prioridad) }}
                                                    </span>
                                                </div>
                                                <span>{{ $activity->created_at->format('d/m/Y H:i') }}</span>
                                            </div>

                                            @if ($activity->image)
                                                <div class="mt-2 flex items-center gap-2">
                                                    <a href="{{ asset('storage/' . $activity->image) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                                                        title="Ver imagen">
                                                        Imagen
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($activity->archivo)
                                                <div class="mt-2 flex items-center gap-2">
                                                    <a href="{{ asset('storage/' . $activity->archivo) }}"
                                                        target="_blank"
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
                                    <div
                                        class="w-12 h-12 mx-auto mb-4 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-zinc-500 dark:text-zinc-400">No hay actividades registradas</p>
                                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Crea la primera actividad para
                                        comenzar</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Formulario de Actividad -->
                    <div class="p-6 border-l border-zinc-200 dark:border-zinc-700 overflow-y-auto">
                        @if (!$selected_opportunity)
                            <div class="text-center py-8">
                                <div
                                    class="w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-500 dark:text-red-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-red-500 dark:text-red-400">Selecciona una oportunidad para crear
                                    actividades</p>
                            </div>
                        @else
                            <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">
                                {{ $activity_id ? 'Editar' : 'Nueva' }} Actividad
                            </h4>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <flux:label for="tipo_activity"
                                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo *
                                        </flux:label>
                                        <flux:select wire:model="tipo_activity" id="tipo_activity"
                                            class="w-full mt-1">
                                            <option value="">Seleccionar tipo</option>
                                            @foreach ($tipos_activity as $tipo)
                                                <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('tipo_activity')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <flux:label for="asunto_activity"
                                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Asunto *
                                        </flux:label>
                                        <flux:input wire:model="asunto_activity" id="asunto_activity" type="text"
                                            placeholder="Ingrese el asunto" class="w-full mt-1" />
                                        @error('asunto_activity')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <flux:label for="estado_activity"
                                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado *
                                        </flux:label>
                                        <flux:select wire:model="estado_activity" id="estado_activity"
                                            class="w-full mt-1">
                                            <option value="">Seleccionar estado</option>
                                            @foreach ($estados_activity as $estado)
                                                <option value="{{ $estado }}">
                                                    {{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                                            @endforeach
                                        </flux:select>
                                        @error('estado_activity')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <flux:label for="prioridad_activity"
                                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Prioridad *
                                        </flux:label>
                                        <flux:select wire:model="prioridad_activity" id="prioridad_activity"
                                            class="w-full mt-1">
                                            <option value="">Seleccionar prioridad</option>
                                            @foreach ($prioridades_activity as $prioridad)
                                                <option value="{{ $prioridad }}">{{ ucfirst($prioridad) }}
                                                </option>
                                            @endforeach
                                        </flux:select>
                                        @error('prioridad_activity')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <flux:label for="contact_id_activity"
                                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Contacto
                                        </flux:label>
                                        <flux:select wire:model="contact_id_activity" id="contact_id_activity"
                                            class="w-full mt-1">
                                            <option value="">Seleccionar contacto</option>
                                            @if ($selected_opportunity && $selected_opportunity->cliente && $selected_opportunity->cliente->contactos)
                                                @foreach ($selected_opportunity->cliente->contactos as $contact)
                                                    <option value="{{ $contact->id }}">{{ $contact->nombre }}
                                                        {{ $contact->apellido }}</option>
                                                @endforeach
                                            @endif
                                        </flux:select>
                                        @error('contact_id_activity')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <flux:label for="descripcion_activity"
                                        class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción
                                    </flux:label>
                                    <flux:textarea wire:model="descripcion_activity" id="descripcion_activity"
                                        rows="3" placeholder="Ingrese la descripción" class="w-full mt-1" />
                                    @error('descripcion_activity')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Imagen
                                    </flux:label>
                                    <flux:input wire:model="tempImageActivity" type="file" accept="image/*"
                                        class="w-full mt-1" />
                                    @error('tempImageActivity')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    @if ($imagePreviewActivity)
                                        <div class="mt-2">
                                            <img src="{{ $imagePreviewActivity }}" alt="Preview"
                                                class="w-32 h-32 object-cover rounded-lg">
                                            <flux:button wire:click="removeImageActivity" size="sm"
                                                color="red" class="mt-2">Remover</flux:button>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Archivo
                                    </flux:label>
                                    <flux:input wire:model="tempArchivoActivity" type="file"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="w-full mt-1" />
                                    @error('tempArchivoActivity')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
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
            <div
                class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
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

    <!-- Modal Form Cliente -->
    <flux:modal wire:model="modal_form_customer" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $customer_id ? 'Editar' : 'Nuevo' }} Cliente
                </h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información Básica -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Información Básica</h4>
                    </div>

                    <div>
                        <flux:label for="tipoDoc" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo
                            de Documento *</flux:label>
                        <flux:select wire:model="tipoDoc" id="tipoDoc" class="w-full mt-1">
                            <option value="">Seleccione tipo</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">CE</option>
                            <option value="PAS">PAS</option>
                        </flux:select>
                        @error('tipoDoc')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="numDoc" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Número
                            de Documento *</flux:label>
                        <flux:input wire:model="numDoc" id="numDoc" type="text"
                            placeholder="Ingrese el número" class="w-full mt-1" />
                        @error('numDoc')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="rznSocial" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Razón
                            Social *</flux:label>
                        <flux:input wire:model="rznSocial" id="rznSocial" type="text"
                            placeholder="Ingrese la razón social" class="w-full mt-1" />
                        @error('rznSocial')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="nombreComercial"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre Comercial</flux:label>
                        <flux:input wire:model="nombreComercial" id="nombreComercial" type="text"
                            placeholder="Ingrese el nombre comercial" class="w-full mt-1" />
                        @error('nombreComercial')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="tipo_customer_id"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Cliente</flux:label>
                        <flux:select wire:model="tipo_customer_id" id="tipo_customer_id" class="w-full mt-1">
                            <option value="">Seleccione tipo</option>
                            @foreach ($tipos_customer as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </flux:select>
                        @error('tipo_customer_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Información de Contacto -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Información de Contacto
                        </h4>
                    </div>

                    <div>
                        <flux:label for="email" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Email
                        </flux:label>
                        <flux:input wire:model="email" id="email" type="email" placeholder="Ingrese el email"
                            class="w-full mt-1" />
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="telefono" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Teléfono</flux:label>
                        <flux:input wire:model="telefono" id="telefono" type="text"
                            placeholder="Ingrese el teléfono" class="w-full mt-1" />
                        @error('telefono')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="direccion" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Dirección</flux:label>
                        <flux:input wire:model="direccion" id="direccion" type="text"
                            placeholder="Ingrese la dirección" class="w-full mt-1" />
                        @error('direccion')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="codigoPostal" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Código Postal</flux:label>
                        <flux:input wire:model="codigoPostal" id="codigoPostal" type="text"
                            placeholder="Ingrese el código postal" class="w-full mt-1" />
                        @error('codigoPostal')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Archivos -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Archivos</h4>
                    </div>

                    <div>
                        <flux:label for="tempImageCustomer"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Imagen</flux:label>
                        <flux:input wire:model="tempImageCustomer" id="tempImageCustomer" type="file"
                            accept="image/*" class="w-full mt-1" />
                        @error('tempImageCustomer')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror

                        @if ($imagePreviewCustomer)
                            <div class="mt-2">
                                <img src="{{ $imagePreviewCustomer }}" alt="Preview"
                                    class="w-20 h-20 object-cover rounded-lg">
                                <flux:button wire:click="removeImageCustomer" size="sm" color="red"
                                    class="mt-2">Eliminar</flux:button>
                            </div>
                        @endif
                    </div>

                    <div>
                        <flux:label for="tempArchivoCustomer"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Archivo</flux:label>
                        <flux:input wire:model="tempArchivoCustomer" id="tempArchivoCustomer" type="file"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="w-full mt-1" />
                        @error('tempArchivoCustomer')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notas -->
                    <div class="md:col-span-2">
                        <flux:label for="notas_cliente" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Notas</flux:label>
                        <flux:textarea wire:model="notas_cliente" id="notas_cliente" rows="3"
                            placeholder="Ingrese notas adicionales" class="w-full mt-1" />
                        @error('notas_cliente')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cerrarModalCustomer" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarCustomer" variant="primary">
                        {{ $customer_id_form ? 'Actualizar' : 'Crear' }} Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Form Contacto -->
    <flux:modal wire:model="modal_form_contacto" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $contact_id_form ? 'Editar' : 'Nuevo' }} Contacto
                </h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:label for="nombre_contacto"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre *</flux:label>
                        <flux:input wire:model="nombre_contacto" id="nombre_contacto" type="text"
                            placeholder="Ingrese el nombre" class="w-full mt-1" />
                        @error('nombre_contacto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="apellido_contacto"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Apellido *</flux:label>
                        <flux:input wire:model="apellido_contacto" id="apellido_contacto" type="text"
                            placeholder="Ingrese el apellido" class="w-full mt-1" />
                        @error('apellido_contacto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="correo_contacto"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Correo *</flux:label>
                        <flux:input wire:model="correo_contacto" id="correo_contacto" type="email"
                            placeholder="correo@ejemplo.com" class="w-full mt-1" />
                        @error('correo_contacto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="telefono_contacto"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Teléfono</flux:label>
                        <flux:input wire:model="telefono_contacto" id="telefono_contacto" type="text"
                            placeholder="Ingrese el teléfono" class="w-full mt-1" />
                        @error('telefono_contacto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="cargo_contacto" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Cargo</flux:label>
                        <flux:input wire:model="cargo_contacto" id="cargo_contacto" type="text"
                            placeholder="Ingrese el cargo" class="w-full mt-1" />
                        @error('cargo_contacto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label for="empresa_contacto"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Empresa</flux:label>
                        <flux:input wire:model="empresa_contacto" id="empresa_contacto" type="text"
                            placeholder="Ingrese la empresa" class="w-full mt-1" />
                        @error('empresa_contacto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <flux:checkbox wire:model="es_principal_contacto" id="es_principal_contacto" />
                        <flux:label for="es_principal_contacto"
                            class="ml-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">Contacto principal
                        </flux:label>
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="notas_contacto" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Notas</flux:label>
                        <flux:textarea wire:model="notas_contacto" id="notas_contacto" rows="3"
                            placeholder="Ingrese notas adicionales" class="w-full mt-1" />
                        @error('notas_contacto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cerrarModalContacto" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarContacto" variant="primary">
                        {{ $contact_id_form ? 'Actualizar' : 'Crear' }} Contacto
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
