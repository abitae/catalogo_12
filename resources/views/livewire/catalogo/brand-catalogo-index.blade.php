<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Catálogo de Marcas</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevoMarca" icon="plus">
                    Nueva Marca
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Tabla de Marcas -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Nombre</span>
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Logo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Archivo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($brands as $brand)
                        <tr class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $brand->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($brand->logo)
                                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="Logo de la marca" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <flux:icon name="photo" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($brand->archivo)
                                    <a href="{{ asset('storage/' . $brand->archivo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <flux:icon name="document" class="w-6 h-6" />
                                    </a>
                                @else
                                    <div class="w-10 h-10 rounded bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <flux:icon name="document" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $brand->isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $brand->isActive ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarMarca({{ $brand->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar marca"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarMarca({{ $brand->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar marca"
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
        {{ $brands->links() }}
    </div>

    <!-- Modal Form Marca -->
    <flux:modal wire:model="modal_form_marca" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $marca_id ? 'Editar Marca' : 'Nueva Marca' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos de la marca.</flux:text>
            </div>
            <form wire:submit.prevent="guardarMarca">
                <div class="grid grid-cols-1 gap-4">
                    <flux:input label="Nombre" wire:model="name" placeholder="Ingrese el nombre" />
                </div>

                <div class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <!-- Logo -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Logo de la Marca</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-1">
                                    <input type="file" wire:model.live="tempLogo" class="hidden" id="logo-upload" accept="image/*" wire:loading.attr="disabled">
                                    <label for="logo-upload" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Seleccionar Logo
                                    </label>
                                </div>

                                <div class="ml-4 relative group">
                                    @if($logoPreview)
                                        <div class="relative">
                                            <img src="{{ $logoPreview }}" alt="Vista previa del logo" class="h-20 w-20 object-cover rounded-full shadow-sm" loading="lazy">
                                            <button type="button" wire:click="removeLogo" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600" title="Eliminar logo">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('tempLogo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Archivo -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Archivo de la Marca</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-1">
                                    <input type="file" wire:model.live="tempArchivo" class="hidden" id="archivo-upload" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" wire:loading.attr="disabled">
                                    <label for="archivo-upload" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Seleccionar Archivo
                                    </label>
                                </div>

                                <div class="ml-4 relative group">
                                    @if($archivoPreview)
                                        <div class="relative">
                                            <div class="flex items-center space-x-2 bg-zinc-50 dark:bg-zinc-700 p-2 rounded-lg">
                                                <flux:icon name="document" class="w-8 h-8 text-blue-600" />
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                        {{ $archivoPreview }}
                                                    </span>
                                                </div>
                                            </div>
                                            <button type="button" wire:click="removeArchivo" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600" title="Eliminar archivo">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('tempArchivo')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (máx. 20MB)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <flux:checkbox label="Activo" wire:model="isActive" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_marca', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $marca_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Form Eliminar Marca -->
    @if($marca_id)
    <flux:modal wire:model="modal_form_eliminar_marca" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Marca</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar esta marca?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_marca', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarMarca">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
