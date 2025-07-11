<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Gestión de Clientes</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Administra los clientes del sistema</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="primary" wire:click="nuevoCustomer" icon="plus">
                    Nuevo Cliente
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
                    placeholder="Buscar clientes por razón social, nombre comercial, documento o email..."
                    wire:model.live="search"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Cliente</flux:label>
                    <flux:select wire:model.live="tipo_customer_filter" class="w-full mt-1">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos_customer as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Documento</flux:label>
                    <flux:select wire:model.live="tipo_doc_filter" class="w-full mt-1">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos_doc as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </flux:select>
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

    <!-- Tabla de Clientes -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Clientes</h3>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $customers->count() }} clientes encontrados</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('tipoDoc')">
                            <div class="flex items-center space-x-2">
                                <span>Documento</span>
                                @if ($sortField === 'tipoDoc')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Contacto
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-600 transition-colors"
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-2">
                                <span>Fecha Creación</span>
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
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($customer->image)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($customer->image) }}" alt="{{ $customer->rznSocial }}">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center">
                                                <flux:icon name="user" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $customer->rznSocial }}</div>
                                        @if($customer->nombreComercial)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $customer->nombreComercial }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    <span class="font-medium">{{ $customer->tipoDoc }}</span>
                                    <span class="ml-1">{{ $customer->numDoc }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    @if($customer->email)
                                        <div class="flex items-center">
                                            <flux:icon name="envelope" class="w-4 h-4 mr-1 text-zinc-400" />
                                            {{ $customer->email }}
                                        </div>
                                    @endif
                                    @if($customer->telefono)
                                        <div class="flex items-center mt-1">
                                            <flux:icon name="phone" class="w-4 h-4 mr-1 text-zinc-400" />
                                            {{ $customer->telefono }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($customer->tipoCustomer)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $customer->tipoCustomer->nombre }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 text-sm">Sin tipo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $customer->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarCustomer({{ $customer->id }})" size="sm" color="blue" icon="pencil">

                                    </flux:button>
                                    <flux:button wire:click="eliminarCustomer({{ $customer->id }})" size="sm" color="red" icon="trash">

                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="users" class="w-12 h-12 mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium">No hay clientes</p>
                                    <p class="text-sm">Crea tu primer cliente para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $customers->links() }}
        </div>
    </div>

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
                        <flux:label for="tipoDoc" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Documento *</flux:label>
                        <flux:select wire:model="tipoDoc" id="tipoDoc" class="w-full mt-1">
                            <option value="">Seleccione tipo</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">CE</option>
                            <option value="PAS">PAS</option>
                        </flux:select>
                        @error('tipoDoc') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="numDoc" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Número de Documento *</flux:label>
                        <flux:input wire:model="numDoc" id="numDoc" type="text" placeholder="Ingrese el número" class="w-full mt-1" />
                        @error('numDoc') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="rznSocial" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Razón Social *</flux:label>
                        <flux:input wire:model="rznSocial" id="rznSocial" type="text" placeholder="Ingrese la razón social" class="w-full mt-1" />
                        @error('rznSocial') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="nombreComercial" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre Comercial</flux:label>
                        <flux:input wire:model="nombreComercial" id="nombreComercial" type="text" placeholder="Ingrese el nombre comercial" class="w-full mt-1" />
                        @error('nombreComercial') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="tipo_customer_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de Cliente</flux:label>
                        <flux:select wire:model="tipo_customer_id" id="tipo_customer_id" class="w-full mt-1">
                            <option value="">Seleccione tipo</option>
                            @foreach($tipos_customer as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </flux:select>
                        @error('tipo_customer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Información de Contacto -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Información de Contacto</h4>
                    </div>

                    <div>
                        <flux:label for="email" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Email</flux:label>
                        <flux:input wire:model="email" id="email" type="email" placeholder="Ingrese el email" class="w-full mt-1" />
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="telefono" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Teléfono</flux:label>
                        <flux:input wire:model="telefono" id="telefono" type="text" placeholder="Ingrese el teléfono" class="w-full mt-1" />
                        @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="direccion" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Dirección</flux:label>
                        <flux:input wire:model="direccion" id="direccion" type="text" placeholder="Ingrese la dirección" class="w-full mt-1" />
                        @error('direccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <flux:label for="codigoPostal" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Código Postal</flux:label>
                        <flux:input wire:model="codigoPostal" id="codigoPostal" type="text" placeholder="Ingrese el código postal" class="w-full mt-1" />
                        @error('codigoPostal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Archivos -->
                    <div class="md:col-span-2">
                        <h4 class="text-md font-semibold text-zinc-900 dark:text-white mb-4">Archivos</h4>
                    </div>

                    <div>
                        <flux:label for="tempImage" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Imagen</flux:label>
                        <flux:input wire:model="tempImage" id="tempImage" type="file" accept="image/*" class="w-full mt-1" />
                        @error('tempImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        @if($imagePreview)
                            <div class="mt-2">
                                <img src="{{ $imagePreview }}" alt="Preview" class="w-20 h-20 object-cover rounded-lg">
                                <flux:button wire:click="removeImage" size="sm" color="red" class="mt-2">Eliminar</flux:button>
                            </div>
                        @endif
                    </div>

                    <div>
                        <flux:label for="tempArchivo" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Archivo</flux:label>
                        <flux:input wire:model="tempArchivo" id="tempArchivo" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="w-full mt-1" />
                        @error('tempArchivo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notas -->
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
                    <flux:button wire:click="$set('modal_form_customer', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="guardarCustomer" variant="primary">
                        {{ $customer_id ? 'Actualizar' : 'Crear' }} Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Confirmar Eliminar -->
    <flux:modal wire:model="modal_form_eliminar_customer" max-width="md">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar Eliminación</h3>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <p class="text-zinc-600 dark:text-zinc-400">
                    ¿Estás seguro de que quieres eliminar este cliente? Esta acción no se puede deshacer.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700/50 rounded-b-lg">
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('modal_form_eliminar_customer', false)" color="gray">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="confirmarEliminarCustomer" color="red">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
