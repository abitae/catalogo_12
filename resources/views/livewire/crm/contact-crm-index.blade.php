<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Contactos</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button wire:click="exportarContactos" icon="arrow-down-tray">
                    Exportar
                </flux:button>
            </div>
            <div class="flex items-end">
                <flux:button wire:click="importar" icon="arrow-up-tray">
                    Importar
                </flux:button>
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevoContacto" icon="plus">
                    Nuevo
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Lead -->
            <div>
                <flux:label>Lead</flux:label>
                <flux:select wire:model.live="lead_filter" class="w-full">
                    <option value="">Todos los Leads</option>
                    @foreach($leads as $lead)
                        <option value="{{ $lead->id }}">{{ $lead->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Empresa -->
            <div>
                <flux:label>Empresa</flux:label>
                <flux:input wire:model.live="empresa_filter" placeholder="Filtrar por empresa" />
            </div>

            <!-- Tipo de Contacto -->
            <div>
                <flux:label>Tipo de Contacto</flux:label>
                <flux:select wire:model.live="es_principal_filter" class="w-full">
                    <option value="">Todos los contactos</option>
                    <option value="1">Contactos principales</option>
                    <option value="0">Contactos secundarios</option>
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

    <!-- Tabla de Contactos -->
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
                            wire:click="sortBy('cargo')">
                            <div class="flex items-center space-x-1">
                                <span>Cargo</span>
                                @if ($sortField === 'cargo')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Principal
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($contactos as $contacto)
                        <tr wire:key="contacto-{{ $contacto->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($contacto->es_principal) bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $contacto->es_principal ? 'Principal' : 'Secundario' }}
                                    </span>
                                    <span>{{ $contacto->nombre }} {{ $contacto->apellido }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $contacto->correo }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $contacto->empresa }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $contacto->cargo }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($contacto->es_principal) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $contacto->es_principal ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarContacto({{ $contacto->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar contacto"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarContacto({{ $contacto->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar contacto"
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
        {{ $contactos->links() }}
    </div>

    <!-- Modal Form Contacto -->
    <flux:modal wire:model="modal_form_contacto" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $contacto_id ? 'Editar Contacto' : 'Nuevo Contacto' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos del contacto.</flux:text>
            </div>
            <form wire:submit.prevent="guardarContacto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input label="Nombre" wire:model="nombre" placeholder="Ingrese el nombre" />
                    <flux:input label="Apellido" wire:model="apellido" placeholder="Ingrese el apellido" />
                    <flux:input label="Correo" type="email" wire:model="correo" placeholder="correo@ejemplo.com" />

                    <flux:input label="Teléfono" wire:model="telefono" placeholder="Ingrese el teléfono" />
                    <flux:input label="Cargo" wire:model="cargo" placeholder="Ingrese el cargo" />
                    <flux:input label="Empresa" wire:model="empresa" placeholder="Ingrese la empresa" />

                    <flux:select label="Lead" wire:model="lead_id">
                        <option value="">Seleccione un lead</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input label="Última fecha de contacto" type="date" wire:model="ultima_fecha_contacto" />
                    <div class="flex items-end">
                        <flux:checkbox label="Contacto principal" wire:model="es_principal" />
                    </div>
                </div>

                <div class="mt-4">
                    <flux:textarea label="Notas" wire:model="notas" placeholder="Ingrese notas adicionales" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_contacto', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $contacto_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Eliminar Contacto -->
    @if($contacto_id)
    <flux:modal wire:model="modal_form_eliminar_contacto" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Contacto</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar este contacto?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_contacto', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarContacto">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
