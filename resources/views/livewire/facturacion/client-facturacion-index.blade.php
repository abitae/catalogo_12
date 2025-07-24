<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <flux:heading size="lg">Gestión de Clientes</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">Administra y consulta los clientes de
                    facturación.</flux:text>
            </div>
            <div class="flex items-center justify-end gap-4 w-full md:w-auto">
                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar clientes..." wire:model.live="search"
                        icon="magnifying-glass" size="sm" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="crearClient" icon="plus">
                        Nuevo Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
    <!-- Tabla de Clientes -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Tipo Doc</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            N° Doc</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Razón Social</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Dirección</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Email</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Teléfono</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($clients as $client)
                        <tr wire:key="client-{{ $client->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $client->tipoDoc }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $client->numDoc }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $client->rznSocial }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ collect([
                                    $client->address->direccion ?? null,
                                    $client->address->distrito ?? null,
                                    $client->address->provincia ?? null,
                                    $client->address->departamento ?? null,
                                    $client->address->codigoPais ?? null,
                                ])->filter()->implode(', ') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $client->email }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $client->telephone }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarClient({{ $client->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar cliente"></flux:button>
                                    <flux:button wire:click="eliminarClient({{ $client->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar cliente"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="inbox" class="w-12 h-12 text-zinc-300" />
                                    <span class="text-lg font-medium">No se encontraron clientes</span>
                                    <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($clients->hasPages())
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
    <!-- Modal Form Cliente -->
    <flux:modal wire:model="modal_client" variant="flyout" class="w-full max-w-2xl">
        <form wire:submit.prevent="guardarClient">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 rounded-t-lg text-white mb-4">
                <div class="flex items-center gap-3">
                    <flux:icon name="user" class="w-6 h-6" />
                    <div>
                        <h2 class="text-lg font-bold">
                            {{ $editingClient ? 'Editar Cliente' : 'Nuevo Cliente' }}
                        </h2>
                        <p class="text-blue-100 text-sm">Complete los datos del cliente</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="tipoDoc" size="sm" label="Tipo Doc *" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="numDoc" size="sm" label="N° Doc *" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="rznSocial" size="sm" label="Razón Social *" />
                    </div>
                </div>
                <div class="mt-4 border-t pt-4">
                    <h3 class="text-base font-semibold mb-2">Dirección</h3>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <flux:input type="text" wire:model.live="direccion" size="sm" label="Dirección *" />
                        </div>
                        <div>
                            <flux:input type="text" wire:model.live="departamento" size="sm"
                                label="Departamento" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <flux:input type="text" wire:model.live="provincia" size="sm" label="Provincia" />
                        </div>
                        <div>
                            <flux:input type="text" wire:model.live="distrito" size="sm" label="Distrito" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <flux:input type="text" wire:model.live="urbanizacion" size="sm"
                                label="Urbanización" />
                        </div>
                        <div>
                            <flux:input type="text" wire:model.live="codLocal" size="sm"
                                label="Código Local" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <flux:input type="text" wire:model.live="ubigueo" size="sm" label="Ubigueo" />
                        </div>
                        <div>
                            <flux:input type="text" wire:model.live="codigoPais" size="sm"
                                label="Código País" />
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="email" wire:model.live="email" size="sm" label="Email" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="telephone" size="sm" label="Teléfono" />
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 border-t pt-3">
                <flux:button wire:click="$set('modal_client', false)" variant="outline" size="sm">Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                    {{ $editingClient ? 'Actualizar' : 'Crear' }} Cliente
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
