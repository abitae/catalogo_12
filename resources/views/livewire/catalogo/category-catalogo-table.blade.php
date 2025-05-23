<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div class="flex-1 max-w-sm">
            <flux:input
                wire:model.live="search"
                type="search"
                placeholder="Buscar categoría..."
                icon="magnifying-glass"
            />
        </div>
        <div>
            <flux:button
                wire:click="$dispatch('openModal', { component: 'catalogo.category-catalogo-form' })"
                variant="primary"
                icon="plus"
            >
                Nueva Categoría
            </flux:button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
            <thead class="bg-gray-50 dark:bg-zinc-700">
                <tr>
                    <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer dark:text-zinc-300">
                        Nombre
                        @if($sortField === 'name')
                            @if($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-zinc-300">
                        Logo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-zinc-300">
                        Fondo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-zinc-300">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-zinc-300">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-zinc-800 dark:divide-zinc-700">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $category->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->logo)
                                <img src="{{ Storage::url($category->logo) }}" alt="{{ $category->name }}" class="h-10 w-10 object-cover rounded-full">
                            @else
                                <div class="h-10 w-10 bg-gray-200 dark:bg-zinc-600 rounded-full flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-zinc-300 text-xs">Sin logo</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->fondo)
                                <img src="{{ Storage::url($category->fondo) }}" alt="Fondo" class="h-10 w-10 object-cover rounded">
                            @else
                                <div class="h-10 w-10 bg-gray-200 dark:bg-zinc-600 rounded flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-zinc-300 text-xs">Sin fondo</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $category->isActive ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <flux:button
                                    wire:click="$dispatch('openModal', { component: 'catalogo.category-catalogo-form', arguments: { category: {{ $category->id }} }})"
                                    icon="pencil"
                                    size="sm"
                                >
                                    Editar
                                </flux:button>
                                <flux:button
                                    wire:click="delete({{ $category->id }})"
                                    variant="danger"
                                    icon="trash"
                                    size="sm"
                                >
                                    Eliminar
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-zinc-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <svg class="h-12 w-12 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-zinc-100">No se encontraron categorías</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Comienza agregando una nueva categoría</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    <flux:modal wire:model="isOpen">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100">
                {{ $isEditing ? 'Editar Categoría' : 'Crear Categoría' }}
            </h3>
        </div>

        <div class="px-6 py-4">
            <form wire:submit="save" class="space-y-6">
                <div>
                    <flux:label for="name">Nombre de la Categoría</flux:label>
                    <flux:input
                        wire:model="category.name"
                        id="name"
                        type="text"
                        placeholder="Ingrese el nombre de la categoría"
                    />
                    @error('category.name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:label for="logo">Logo</flux:label>
                    <div class="mt-1">
                        <input
                            type="file"
                            wire:model="logo"
                            id="logo"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 file:text-primary-700
                                dark:file:bg-zinc-700 dark:file:text-zinc-200
                                hover:file:bg-primary-100 dark:hover:file:bg-zinc-600"
                        />
                    </div>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if($category->logo)
                        <div class="mt-2">
                            <img src="{{ Storage::url($category->logo) }}" alt="Logo actual" class="h-20 w-20 object-contain">
                        </div>
                    @endif
                </div>

                <div>
                    <flux:label for="fondo">Fondo</flux:label>
                    <div class="mt-1">
                        <input
                            type="file"
                            wire:model="fondo"
                            id="fondo"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 file:text-primary-700
                                dark:file:bg-zinc-700 dark:file:text-zinc-200
                                hover:file:bg-primary-100 dark:hover:file:bg-zinc-600"
                        />
                    </div>
                    @error('fondo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if($category->fondo)
                        <div class="mt-2">
                            <img src="{{ Storage::url($category->fondo) }}" alt="Fondo actual" class="h-20 w-20 object-cover rounded">
                        </div>
                    @endif
                </div>

                <div>
                    <flux:label for="archivo">Archivo</flux:label>
                    <div class="mt-1">
                        <input
                            type="file"
                            wire:model="archivo"
                            id="archivo"
                            class="block w-full text-sm text-gray-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 file:text-primary-700
                                dark:file:bg-zinc-700 dark:file:text-zinc-200
                                hover:file:bg-primary-100 dark:hover:file:bg-zinc-600"
                        />
                    </div>
                    @error('archivo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @if($category->archivo)
                        <div class="mt-2">
                            <a href="{{ Storage::url($category->archivo) }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                                Ver archivo actual
                            </a>
                        </div>
                    @endif
                </div>

                <div class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="category.isActive"
                        id="isActive"
                        class="h-4 w-4 text-primary-600 dark:text-primary-500 focus:ring-primary-500 dark:focus:ring-zinc-600 border-gray-300 dark:border-zinc-600 rounded"
                    >
                    <label for="isActive" class="ml-2 block text-sm text-gray-900 dark:text-zinc-100">
                        Activo
                    </label>
                </div>
            </form>
        </div>

        <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 flex justify-end space-x-3">
            <flux:button
                type="button"
                wire:click="$set('isOpen', false)"
            >
                Cancelar
            </flux:button>
            <flux:button
                type="submit"
                variant="primary"
                wire:click="save"
                :loading="$isSubmitting"
            >
                {{ $isEditing ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>
</div>
