<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex-1 max-w-sm">
            <input wire:model.live="search" type="search" placeholder="Buscar línea..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div>
            <button wire:click="$dispatch('openModal', { component: 'catalogo.line-catalogo-form' })" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Nueva Línea
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('code')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                        Código
                        @if($sortField === 'code')
                            @if($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                        Nombre
                        @if($sortField === 'name')
                            @if($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Logo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fondo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lines as $line)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $line->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $line->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($line->logo)
                                <img src="{{ Storage::url($line->logo) }}" alt="{{ $line->name }}" class="h-10 w-10 object-cover rounded-full">
                            @else
                                <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-500 text-xs">Sin logo</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($line->fondo)
                                <img src="{{ Storage::url($line->fondo) }}" alt="Fondo" class="h-10 w-10 object-cover rounded">
                            @else
                                <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-gray-500 text-xs">Sin fondo</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $line->isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $line->isActive ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="$dispatch('openModal', { component: 'catalogo.line-catalogo-form', arguments: { line: {{ $line->id }} }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Editar
                            </button>
                            <button wire:click="delete({{ $line->id }})" class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No se encontraron líneas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $lines->links() }}
    </div>

    <flux:modal wire:model="isOpen">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100">
                {{ $isEditing ? 'Editar Línea' : 'Crear Línea' }}
            </h3>
        </div>

        <div class="px-6 py-4">
            <form wire:submit="save" class="space-y-6">
                <div>
                    <flux:label for="name">Nombre de la Línea</flux:label>
                    <flux:input
                        wire:model="line.name"
                        id="name"
                        type="text"
                        placeholder="Ingrese el nombre de la línea"
                    />
                    @error('line.name')
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
                    @if($line->logo)
                        <div class="mt-2">
                            <img src="{{ Storage::url($line->logo) }}" alt="Logo actual" class="h-20 w-20 object-contain">
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
                    @if($line->archivo)
                        <div class="mt-2">
                            <a href="{{ Storage::url($line->archivo) }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                                Ver archivo actual
                            </a>
                        </div>
                    @endif
                </div>

                <div class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="line.isActive"
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
