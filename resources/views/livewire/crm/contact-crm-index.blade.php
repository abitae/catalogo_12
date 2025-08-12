<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Contactos</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra los contactos del CRM</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button wire:click="exportarContactos" icon="arrow-down-tray" color="gray">
                    Exportar
                </flux:button>
                <flux:button wire:click="importar" icon="arrow-up-tray" color="gray">
                    Importar
                </flux:button>
                <flux:button variant="primary" wire:click="nuevoContacto" icon="plus">
                    Nuevo Contacto
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
                    placeholder="Buscar contactos por nombre, correo o empresa..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Cliente</flux:label>
                    <flux:select wire:model.live="customer_filter" class="w-full mt-1">
                        <option value="">Todos los clientes</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Empresa</flux:label>
                    <flux:input wire:model.live="empresa_filter" placeholder="Filtrar por empresa" class="w-full mt-1" />
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Contacto</flux:label>
                    <flux:select wire:model.live="es_principal_filter" class="w-full mt-1">
                        <option value="">Todos los contactos</option>
                        <option value="1">Contactos principales</option>
                        <option value="0">Contactos secundarios</option>
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Cargo</flux:label>
                    <flux:input wire:model.live="cargo_filter" placeholder="Filtrar por cargo" class="w-full mt-1" />
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Registros por página</flux:label>
                    <flux:select wire:model.live="perPage" class="w-full mt-1">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
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

    <!-- Tabla de Contactos -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Contactos</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $contactos->count() }} contactos encontrados</span>
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
                                <span>Nombre</span>
                                @if ($sortField === 'nombre')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('correo')">
                            <div class="flex items-center space-x-2">
                                <span>Correo</span>
                                @if ($sortField === 'correo')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('empresa')">
                            <div class="flex items-center space-x-2">
                                <span>Empresa</span>
                                @if ($sortField === 'empresa')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('cargo')">
                            <div class="flex items-center space-x-2">
                                <span>Cargo</span>
                                @if ($sortField === 'cargo')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Teléfono
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Principal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($contactos as $contacto)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $contacto->nombre }} {{ $contacto->apellido }}</div>
                                    @if ($contacto->correo)
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $contacto->correo }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $contacto->correo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $contacto->empresa }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $contacto->cargo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $contacto->telefono }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($contacto->es_principal) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    {{ $contacto->es_principal ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarContacto({{ $contacto->id }})" size="sm" color="blue" icon="pencil">
                                        Editar
                                    </flux:button>
                                    <flux:button wire:click="eliminarContacto({{ $contacto->id }})" size="sm" color="red" icon="trash">
                                        Eliminar
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="users" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay contactos</p>
                                    <p class="text-sm">Crea tu primer contacto para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $contactos->links() }}
        </div>
    </div>

    <!-- Modal Form Contacto -->
    <flux:modal wire:model="modal_form_contacto" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $contacto_id ? 'Editar' : 'Nuevo' }} Contacto
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
                        <flux:label for="apellido" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Apellido *</flux:label>
                        <flux:input wire:model="apellido" id="apellido" type="text" placeholder="Ingrese el apellido" class="w-full mt-1" />
                        @error('apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="correo" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Correo *</flux:label>
                        <flux:input wire:model="correo" id="correo" type="email" placeholder="correo@ejemplo.com" class="w-full mt-1" />
                        @error('correo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="telefono" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Teléfono</flux:label>
                        <flux:input wire:model="telefono" id="telefono" type="text" placeholder="Ingrese el teléfono" class="w-full mt-1" />
                        @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="cargo" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Cargo</flux:label>
                        <flux:input wire:model="cargo" id="cargo" type="text" placeholder="Ingrese el cargo" class="w-full mt-1" />
                        @error('cargo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="empresa" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Empresa</flux:label>
                        <flux:input wire:model="empresa" id="empresa" type="text" placeholder="Ingrese la empresa" class="w-full mt-1" />
                        @error('empresa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="customer_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Cliente *</flux:label>
                        <flux:select wire:model="customer_id" id="customer_id" class="w-full mt-1">
                            <option value="">Seleccione un cliente</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->rznSocial }}</option>
                            @endforeach
                        </flux:select>
                        @error('customer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <flux:checkbox wire:model="es_principal" id="es_principal" />
                        <flux:label for="es_principal" class="ml-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">Contacto principal</flux:label>
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="notas" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Notas</flux:label>
                        <flux:textarea wire:model="notas" id="notas" rows="3" placeholder="Ingrese notas adicionales" class="w-full mt-1" />
                        @error('notas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_contacto', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarContacto" variant="primary">
                        {{ $contacto_id ? 'Actualizar' : 'Crear' }} Contacto
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_contacto" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <p class="text-zinc-600 dark:text-zinc-400">
                    ¿Estás seguro de que quieres eliminar este contacto? Esta acción no se puede deshacer.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_contacto', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarContacto" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
