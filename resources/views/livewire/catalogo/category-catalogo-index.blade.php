<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Catálogo de Categorías</h1>
            <div class="w-full md:w-96">
                <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
            </div>
            <div class="flex items-end gap-2">
                <flux:button variant="primary" wire:click="nuevoCategoria" icon="plus">
                    Nueva Categoría
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Tabla de Categorías -->
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
                            Fondo
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
                    @foreach ($categories as $category)
                        <tr class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($category->logo)
                                    <img src="{{ asset('storage/' . $category->logo) }}" alt="Logo de la categoría" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <flux:icon name="photo" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($category->fondo)
                                    <img src="{{ asset('storage/' . $category->fondo) }}" alt="Fondo de la categoría" class="w-10 h-10 rounded object-cover">
                                @else
                                    <div class="w-10 h-10 rounded bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <flux:icon name="photo" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($category->archivo)
                                    <a href="{{ asset('storage/' . $category->archivo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <flux:icon name="document" class="w-6 h-6" />
                                    </a>
                                @else
                                    <div class="w-10 h-10 rounded bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <flux:icon name="document" class="w-6 h-6 text-zinc-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $category->isActive ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarCategoria({{ $category->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar categoría"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    <flux:button wire:click="eliminarCategoria({{ $category->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar categoría"
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
        {{ $categories->links() }}
    </div>

    <!-- Modal Form Categoría -->
    <flux:modal wire:model="modal_form_categoria" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $categoria_id ? 'Editar Categoría' : 'Nueva Categoría' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos de la categoría.</flux:text>
            </div>
            <form wire:submit.prevent="guardarCategoria">
                <div class="grid grid-cols-1 gap-4">
                    <flux:input label="Nombre" wire:model="name" placeholder="Ingrese el nombre" />
                </div>

                <div class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <!-- Logo -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Logo de la Categoría</label>
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

                        <!-- Fondo -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Fondo de la Categoría</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-1">
                                    <input type="file" wire:model.live="tempFondo" class="hidden" id="fondo-upload" accept="image/*" wire:loading.attr="disabled">
                                    <label for="fondo-upload" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Seleccionar Fondo
                                    </label>
                                </div>

                                <div class="ml-4 relative group">
                                    @if($fondoPreview)
                                        <div class="relative">
                                            <img src="{{ $fondoPreview }}" alt="Vista previa del fondo" class="h-20 w-20 object-cover rounded shadow-sm" loading="lazy">
                                            <button type="button" wire:click="removeFondo" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600" title="Eliminar fondo">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('tempFondo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Archivo -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Archivo Adjunto 1</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-1">
                                    <input type="file" wire:model.live="tempArchivo" class="hidden" id="archivo-upload" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                    <label for="archivo-upload" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Seleccionar Archivo
                                    </label>
                                </div>
                                @if($archivoPreview)
                                    <div class="ml-4 flex items-center group">
                                        <span class="text-sm text-gray-500">{{ $archivoPreview }}</span>
                                        <button type="button" wire:click="removeArchivo" class="ml-2 text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @error('tempArchivo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="tempArchivo" class="mt-2">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Subiendo archivo...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <flux:checkbox label="Activo" wire:model="isActive" />
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button type="button" wire:click="$set('modal_form_categoria', false)" class="mr-2">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $categoria_id ? 'Actualizar' : 'Guardar' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Form Eliminar Categoría -->
    @if($categoria_id)
    <flux:modal wire:model="modal_form_eliminar_categoria" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Categoría</flux:heading>
                <flux:text class="mt-2">¿Está seguro de querer eliminar esta categoría?</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:button type="button" wire:click="$set('modal_form_eliminar_categoria', false)" class="mr-2">
                    Cancelar
                </flux:button>
                <flux:button variant="danger" wire:click="confirmarEliminarCategoria">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
