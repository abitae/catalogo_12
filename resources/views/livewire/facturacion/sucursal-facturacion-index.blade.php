<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <flux:heading size="lg">Gestión de Sucursales</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">Administra y consulta las sucursales de
                    facturación.</flux:text>
            </div>
            <div class="flex items-center justify-end gap-4 w-full md:w-auto">
                <!-- Filtro por compañía -->
                <div class="w-full md:w-64">
                    <flux:select wire:model.live="company_filter">
                        <option value="">Todas las compañías</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->razonSocial }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar sucursales..." wire:model.live="search"
                        icon="magnifying-glass" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="crearSucursal" icon="plus">
                        Nueva Sucursal
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
    <!-- Tabla de Sucursales agrupada por compañía -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Logo</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Nombre</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            RUC</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Compañía</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Email</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Teléfono</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @php
                        $lastCompany = null;
                    @endphp
                    @forelse ($sucursales as $sucursal)
                        @if ($lastCompany !== $sucursal->company_id)
                            <tr>
                                <td colspan="8"
                                    class="bg-zinc-100 dark:bg-zinc-700 font-bold px-6 py-2 text-zinc-700 dark:text-zinc-200">
                                    {{ $sucursal->company->razonSocial ?? '-' }}
                                </td>
                            </tr>
                            @php $lastCompany = $sucursal->company_id; @endphp
                        @endif
                        <tr wire:key="sucursal-{{ $sucursal->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300 font-medium">
                                @if($sucursal->logo_path)
                                    <img src="{{ Storage::url($sucursal->logo_path) }}" alt="Logo de la sucursal"
                                         class="w-10 h-10 object-cover rounded-full border border-zinc-200 dark:border-zinc-700">
                                @else
                                    <flux:icon name="photo" class="w-10 h-10 text-zinc-500 dark:text-zinc-400" />
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300 font-medium">
                                {{ $sucursal->name }}</td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300 font-medium">
                                {{ $sucursal->ruc }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $sucursal->company->razonSocial ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $sucursal->email }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $sucursal->telephone }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if($sucursal->isActive)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <flux:icon name="check-circle" class="w-3 h-3 mr-1" />
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <flux:icon name="x-circle" class="w-3 h-3 mr-1" />
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarSucursal({{ $sucursal->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar sucursal"></flux:button>
                                    <flux:button wire:click="eliminarSucursal({{ $sucursal->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar sucursal"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="inbox" class="w-12 h-12 text-zinc-300" />
                                    <span class="text-lg font-medium">No se encontraron sucursales</span>
                                    <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($sucursales->hasPages())
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $sucursales->links() }}
            </div>
        @endif
    </div>
    <!-- Modal Form Sucursal -->
    <flux:modal wire:model="modal_sucursal" variant="flyout" class="w-full max-w-2xl">
        <form wire:submit.prevent="guardarSucursal">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 rounded-t-lg text-white mb-4">
                <div class="flex items-center gap-3">
                    <flux:icon name="building-storefront" class="w-6 h-6" />
                    <div>
                        <h2 class="text-lg font-bold">
                            {{ $editingSucursal ? 'Editar Sucursal' : 'Nueva Sucursal' }}
                        </h2>
                        <p class="text-blue-100 text-sm">Complete los datos de la sucursal</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:select wire:model.live="company_id" label="Compañía *" size="sm">
                            <option value="">Seleccionar compañía</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->razonSocial }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="name" size="sm" label="Nombre de Sucursal *" />
                    </div>
                </div>
                @if ($company_id)
                    <div
                        class="mb-2 px-2 py-2 bg-zinc-50 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-700 dark:text-zinc-300">
                        <div><span class="font-semibold">RUC:</span> {{ $ruc }}</div>
                        <div><span class="font-semibold">Razón Social:</span> {{ $razonSocial }}</div>
                        <div><span class="font-semibold">Nombre Comercial:</span> {{ $nombreComercial }}</div>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="email" wire:model.live="email" size="sm" label="Email" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="telephone" size="sm" label="Teléfono" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="series_suffix" size="sm" label="Sufijo de Serie (01-99)"
                            placeholder="01" maxlength="2" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="codigoSunat" size="sm" label="Código SUNAT"
                            placeholder="Código de sucursal en SUNAT" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Logo de la Sucursal
                        </label>
                        <div class="space-y-3">
                            <!-- Previsualización de imagen actual -->
                            @if($logo_path && !$logo_temp)
                                <div class="relative inline-block">
                                    <img src="{{ Storage::url($logo_path) }}" alt="Logo actual"
                                         class="w-24 h-24 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                                    <flux:button wire:click="eliminarImagen" size="xs" variant="danger"
                                        class="absolute -top-2 -right-2" icon="x-mark" title="Eliminar logo actual">
                                    </flux:button>
                                </div>
                            @endif

                            <!-- Previsualización de imagen temporal -->
                            @if($logo_temp)
                                <div class="relative inline-block">
                                    <img src="{{ $logo_temp->temporaryUrl() }}" alt="Nueva imagen"
                                         class="w-24 h-24 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                                    <flux:button wire:click="eliminarImagen" size="xs" variant="danger"
                                        class="absolute -top-2 -right-2" icon="x-mark" title="Eliminar imagen">
                                    </flux:button>
                                </div>
                            @endif

                            <!-- Input de archivo -->
                            @if(!$logo_temp && !$logo_path)
                                <div class="flex items-center justify-center w-full">
                                    <label for="logo_temp" class="flex flex-col items-center justify-center w-full h-24 border-2 border-zinc-300 dark:border-zinc-600 border-dashed rounded-lg cursor-pointer bg-zinc-50 dark:bg-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-600">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <flux:icon name="photo" class="w-8 h-8 mb-2 text-zinc-500 dark:text-zinc-400" />
                                            <p class="mb-2 text-sm text-zinc-500 dark:text-zinc-400">
                                                <span class="font-semibold">Haz clic para subir</span> o arrastra y suelta
                                            </p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">PNG, JPG, GIF hasta 1MB</p>
                                        </div>
                                        <input id="logo_temp" wire:model="logo_temp" type="file" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-full">
                                    <label for="logo_temp" class="flex flex-col items-center justify-center w-full h-16 border-2 border-zinc-300 dark:border-zinc-600 border-dashed rounded-lg cursor-pointer bg-zinc-50 dark:bg-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-600">
                                        <div class="flex flex-col items-center justify-center">
                                            <flux:icon name="arrow-up-tray" class="w-6 h-6 mb-1 text-zinc-500 dark:text-zinc-400" />
                                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                                <span class="font-semibold">Cambiar imagen</span>
                                            </p>
                                        </div>
                                        <input id="logo_temp" wire:model="logo_temp" type="file" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                            @endif
                        </div>
                        @error('logo_temp')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <flux:checkbox wire:model.live="isActive" label="Sucursal Activa" />
                </div>
                <div class="mt-4 border-t pt-4">
                    <h3 class="text-base font-semibold mb-2">Dirección</h3>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <flux:input type="text" wire:model.live="direccion" size="sm"
                                label="Dirección *" />
                        </div>
                        <div>
                            <flux:input type="text" wire:model.live="departamento" size="sm"
                                label="Departamento" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <flux:input type="text" wire:model.live="provincia" size="sm"
                                label="Provincia" />
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
            </div>
            <div class="flex justify-end gap-2 mt-6 border-t pt-3">
                <flux:button wire:click="$set('modal_sucursal', false)" variant="outline" size="sm">Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                    {{ $editingSucursal ? 'Actualizar' : 'Crear' }} Sucursal
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
