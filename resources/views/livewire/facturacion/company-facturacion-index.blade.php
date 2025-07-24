<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <flux:heading size="lg">Gestión de Compañías</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">Administra y consulta las compañías de
                    facturación.</flux:text>
            </div>
            <div class="flex items-center justify-end gap-4 w-full md:w-auto">

                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar compañías..." wire:model.live="search"
                        icon="magnifying-glass" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="crearCompany" icon="plus">
                        Nueva Compañía
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados Mejorados -->
    <div class="mb-6">
        <div class="flex flex-wrap gap-4 items-end bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="w-full md:w-56">
                <flux:select wire:model.live="company_active_filter" size="sm" label="Estado de actividad">
                    <option value="">Todos los estados</option>
                    <option value="1">Solo Activos</option>
                    <option value="0">Solo Inactivos</option>
                </flux:select>
            </div>
            <div class="w-full md:w-56">
                <flux:select wire:model.live="company_production_filter" size="sm" label="Ambiente">
                    <option value="">Todos los ambientes</option>
                    <option value="1">Producción</option>
                    <option value="0">Beta / Pruebas</option>
                </flux:select>
            </div>
            <div class="flex-1 flex gap-2 justify-end">
                <flux:button size="sm" icon="arrow-path"
                    wire:click="$set('company_active_filter', ''); $set('company_production_filter', '')"
                    class="mt-4 md:mt-0">
                    Limpiar Filtros
                </flux:button>
            </div>
        </div>
    </div>
    <!-- Fin Filtros Avanzados Mejorados -->

    <!-- Tabla de Compañías -->
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
                            RUC</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Razón Social</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Teléfono</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Dirección</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Fechas</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($companies as $company)
                        <tr wire:key="company-{{ $company->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                @if ($company->logo_path)
                                    <img src="{{ Storage::url($company->logo_path) }}" alt="Logo"
                                        class="h-10 rounded shadow border bg-white" />
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300 font-medium">
                                {{ $company->ruc }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $company->razonSocial }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">{{ $company->telephone }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $company->address->direccion ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col gap-1">
                                    {{-- Estado Activo/Inactivo --}}
                                    <span
                                        class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $company->isActive
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-300 dark:border-green-700'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-300 dark:border-red-700' }}">
                                        <flux:icon name="{{ $company->isActive ? 'check-circle' : 'x-circle' }}"
                                            class="w-4 h-4 {{ $company->isActive ? 'text-green-500' : 'text-red-500' }}" />
                                        <span>
                                            {{ $company->isActive ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </span>
                                    {{-- Estado Producción/Beta --}}
                                    <span
                                        class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $company->isProduction
                                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-300 dark:border-blue-700'
                                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 border border-yellow-300 dark:border-yellow-700' }}">
                                        <flux:icon name="{{ $company->isProduction ? 'server' : 'beaker' }}"
                                            class="w-4 h-4 {{ $company->isProduction ? 'text-blue-500' : 'text-yellow-500' }}" />
                                        <span>
                                            {{ $company->isProduction ? 'Producción' : 'Beta' }}
                                        </span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="text-xs space-y-1">
                                    <div>
                                        <span class="font-semibold">Suscripción:</span>
                                        @if ($company->inicio_suscripcion || $company->fin_suscripcion)
                                            <span class="inline-flex items-center gap-1">
                                                @if ($company->inicio_suscripcion)
                                                    <span
                                                        class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700">
                                                        {{ \Carbon\Carbon::parse($company->inicio_suscripcion)->isoFormat('D MMM YYYY') }}
                                                    </span>
                                                @else
                                                    <span class="text-zinc-400">-</span>
                                                @endif
                                                <span class="text-zinc-400">→</span>
                                                @if ($company->fin_suscripcion)
                                                    <span
                                                        class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700">
                                                        {{ \Carbon\Carbon::parse($company->fin_suscripcion)->isoFormat('D MMM YYYY') }}
                                                    </span>
                                                @else
                                                    <span class="text-zinc-400">-</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-zinc-400">Sin fechas</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-semibold">Producción:</span>
                                        @if ($company->inicio_produccion || $company->fin_produccion)
                                            <span class="inline-flex items-center gap-1">
                                                @if ($company->inicio_produccion)
                                                    <span
                                                        class="px-2 py-0.5 rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200 dark:border-green-700">
                                                        {{ \Carbon\Carbon::parse($company->inicio_produccion)->isoFormat('D MMM YYYY') }}
                                                    </span>
                                                @else
                                                    <span class="text-zinc-400">-</span>
                                                @endif
                                                <span class="text-zinc-400">→</span>
                                                @if ($company->fin_produccion)
                                                    <span
                                                        class="px-2 py-0.5 rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200 dark:border-green-700">
                                                        {{ \Carbon\Carbon::parse($company->fin_produccion)->isoFormat('D MMM YYYY') }}
                                                    </span>
                                                @else
                                                    <span class="text-zinc-400">-</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-zinc-400">Sin fechas</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="editarCompany({{ $company->id }})" size="xs"
                                        variant="primary" icon="pencil" title="Editar compañía"></flux:button>
                                    <flux:button wire:click="eliminarCompany({{ $company->id }})" size="xs"
                                        variant="danger" icon="trash" title="Eliminar compañía"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="inbox" class="w-12 h-12 text-zinc-300" />
                                    <span class="text-lg font-medium">No se encontraron compañías</span>
                                    <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($companies->hasPages())
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
    <!-- Modal Form Compañía -->
    <flux:modal wire:model="modal_company" variant="flyout" class="w-full max-w-2xl">
        <form wire:submit.prevent="guardarCompany">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 rounded-t-lg text-white mb-4">
                <div class="flex items-center gap-3">
                    <flux:icon name="building-office" class="w-6 h-6" />
                    <div>
                        <h2 class="text-lg font-bold">
                            {{ $editingCompany ? 'Editar Compañía' : 'Nueva Compañía' }}
                        </h2>
                        <p class="text-blue-100 text-sm">Complete los datos de la compañía</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="ruc" size="sm" label="RUC *" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="razonSocial" size="sm"
                            label="Razón Social *" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="nombreComercial" size="sm"
                            label="Nombre Comercial" />
                    </div>
                    <div>
                        <flux:input type="email" wire:model.live="email" size="sm" label="Email" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" mask="999999999" wire:model.live="telephone" size="sm"
                            label="Teléfono" />
                    </div>
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="ctaBanco" size="sm"
                            label="Cuenta Bancaria" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="nroMtc" size="sm" label="N° MTC" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="file" wire:model.live="logoFile" size="sm" label="Logo (imagen)"
                            accept="image/*" />
                        @if ($logo_path)
                            <div class="mt-2">
                                <img src="{{ Storage::url($logo_path) }}" alt="Logo"
                                    class="h-16 rounded shadow border" />
                            </div>
                        @endif
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="logo_path" size="sm"
                            label="Logo (URL o ruta)" readonly />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="file" wire:model.live="certFile" size="sm"
                            label="Certificado (archivo)"
                            accept=".pfx,.cer,.pem,.crt,.key,.der,.zip,.rar,.7z,.tar,.gz" />
                        @if ($cert_path)
                            <div class="mt-2">
                                <a href="{{ Storage::url($cert_path) }}" target="_blank"
                                    class="text-blue-600 underline text-xs">Descargar certificado actual</a>
                            </div>
                        @endif
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="cert_path" size="sm"
                            label="Certificado (ruta)" readonly />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="sol_user" size="sm" label="Usuario SOL" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="sol_pass" size="sm"
                            label="Contraseña SOL" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="text" wire:model.live="client_id" size="sm" label="Client ID" />
                    </div>
                    <div>
                        <flux:input type="text" wire:model.live="client_secret" size="sm"
                            label="Client Secret" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:switch wire:model.live="isProduction" label="¿Producción?" />
                    </div>
                    <div>
                        <flux:switch wire:model.live="isActive" label="¿Activo?" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="date" wire:model.live="inicio_suscripcion" size="sm"
                            label="Inicio Suscripción" />
                    </div>
                    <div>
                        <flux:input type="date" wire:model.live="fin_suscripcion" size="sm"
                            label="Fin Suscripción" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input type="date" wire:model.live="inicio_produccion" size="sm"
                            label="Inicio Producción" />
                    </div>
                    <div>
                        <flux:input type="date" wire:model.live="fin_produccion" size="sm"
                            label="Fin Producción" />
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 border-t pt-3">
                <flux:button wire:click="$set('modal_company', false)" variant="outline" size="sm">Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                    {{ $editingCompany ? 'Actualizar' : 'Crear' }} Compañía
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
