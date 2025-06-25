<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Transferencias de Almacén</h1>
            <div class="flex items-center justify-end gap-4 w-full">
                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button wire:click="exportarTransferencias" icon="arrow-down-tray">
                        Exportar
                    </flux:button>
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="nuevaTransferencia" icon="plus">
                        Nueva
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Almacén Origen -->
            <div>
                <flux:label>Almacén Origen</flux:label>
                <flux:select wire:model.live="almacen_origen_filter" class="w-full">
                    <option value="">Todos los almacenes</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Almacén Destino -->
            <div>
                <flux:label>Almacén Destino</flux:label>
                <flux:select wire:model.live="almacen_destino_filter" class="w-full">
                    <option value="">Todos los almacenes</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Estado -->
            <div>
                <flux:label>Estado</flux:label>
                <flux:select wire:model.live="estado_filter" class="w-full">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
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
                    <option value="200">200</option>
                    <option value="500">500</option>
                </flux:select>
            </div>
            <!-- Fecha de inicio -->
            <div>
                <flux:label>Fecha de inicio</flux:label>
                <flux:input type="date" wire:model.live="fecha_inicio" class="w-full" />
            </div>
            <!-- Fecha de fin -->
            <div>
                <flux:label>Fecha de fin</flux:label>
                <flux:input type="date" wire:model.live="fecha_fin" class="w-full" />
            </div>
            <!-- Registros por página -->
            <div>
                <flux:label>Registros por página</flux:label>
                <flux:select wire:model.live="perPage" class="w-full">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
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

    <!-- Tabla de Transferencias -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-blue-500 transition-colors"
                            wire:click="sortBy('code')">
                            <div class="flex items-center space-x-1">
                                <span>Código</span>
                                @if ($sortField === 'code')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                        class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Origen/Destino
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Productos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($transferencias as $transferencia)
                        <tr wire:key="transferencia-{{ $transferencia->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $transferencia->code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $transferencia->almacenOrigen->nombre }} <br> {{ $transferencia->almacenDestino->nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="space-y-2">
                                    @forelse ($transferencia->productos as $producto)
                                        <div class="flex items-center justify-between p-2 bg-zinc-50 dark:bg-zinc-700 rounded-md border border-zinc-200 dark:border-zinc-600">
                                            <div class="flex-1">
                                                <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                                    {{ $producto['code'] }}
                                                </div>
                                                <div class="text-xs text-zinc-600 dark:text-zinc-400">
                                                    {{ Str::limit($producto['nombre'], 20) }}
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">
                                                    {{ $producto['cantidad'] }}
                                                </span>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                                                    {{ $producto['unidad_medida'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-3 text-zinc-500 dark:text-zinc-400 italic">
                                            <flux:icon name="package" class="w-4 h-4 mx-auto mb-1" />
                                            No hay productos
                                        </div>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $transferencia->fecha_transferencia->format('d/m/Y h:i A') }}
                                <br>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($transferencia->estado === 'completada') bg-green-100 text-green-800
                                    @elseif($transferencia->estado === 'cancelada') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($transferencia->estado) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    @if($transferencia->estado === 'pendiente')
                                        <flux:button wire:click="completarTransferencia({{ $transferencia->id }})" size="xs"
                                            icon="check" title="Completar transferencia"
                                            class="hover:bg-green-600 transition-colors">
                                        </flux:button>
                                        <flux:button wire:click="cancelarTransferencia({{ $transferencia->id }})" size="xs"
                                            variant="danger" icon="x-mark" title="Cancelar transferencia"
                                            class="hover:bg-red-600 transition-colors">
                                        </flux:button>
                                        <flux:button wire:click="editarTransferencia({{ $transferencia->id }})" size="xs"
                                            variant="primary" icon="pencil" title="Editar transferencia"
                                            class="hover:bg-blue-600 transition-colors">
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-zinc-500">
                                No hay transferencias disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $transferencias->links() }}
    </div>

    <!-- Modal Form Transferencia -->
    <flux:modal wire:model="modal_form_transferencia" variant="flyout" class="w-2/3 max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $transferencia_id ? 'Editar Transferencia' : 'Nueva Transferencia' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos de la transferencia.</flux:text>
            </div>

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="guardarTransferencia">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select
                        label="Almacén Origen"
                        wire:model.live="almacen_origen_id"
                        :error="$errors->first('almacen_origen_id')"
                        :disabled="$transferencia_id && $estado !== 'pendiente'"
                    >
                        <option value="">Seleccione un almacén origen</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select
                        label="Almacén Destino"
                        wire:model="almacen_destino_id"
                        :error="$errors->first('almacen_destino_id')"
                        :disabled="!$almacen_origen_id || ($transferencia_id && $estado !== 'pendiente')"
                    >
                        <option value="">Seleccione un almacén destino</option>
                        @foreach ($almacenes as $almacen)
                            @if($almacen->id !== $almacen_origen_id)
                                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                            @endif
                        @endforeach
                    </flux:select>

                    <flux:input
                        label="Código"
                        wire:model="code"
                        placeholder="Ingrese el código"
                        :error="$errors->first('code')"
                        :disabled="true"
                    />

                    <flux:input
                        label="Fecha"
                        type="datetime-local"
                        wire:model="fecha"
                        :error="$errors->first('fecha')"
                        :disabled="$transferencia_id && $estado !== 'pendiente'"
                    />
                </div>

                <!-- Productos -->
                <div class="mt-6">
                    <flux:heading size="sm">Productos</flux:heading>

                    <!-- Formulario para agregar productos -->
                    <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <flux:select
                                    label="Producto"
                                    wire:model.live="producto_seleccionado"
                                    :disabled="!$almacen_origen_id || ($transferencia_id && $estado !== 'pendiente')"
                                    :error="$errors->first('producto_seleccionado')"
                                >
                                    <option value="">Seleccione un producto</option>
                                    @foreach ($productos_disponibles as $producto)
                                        <option value="{{ $producto->id }}">
                                            <div class="flex justify-between items-center">
                                                <span>{{ $producto->code }} - {{ $producto->nombre }}</span>
                                                <span class="text-sm text-zinc-500">
                                                    Stock: {{ number_format($producto->stock_disponible ?? $producto->stock_actual, 2) }} {{ $producto->unidad_medida }}
                                                    @if($producto->necesitaReposicion())
                                                        <span class="text-red-500 ml-1">⚠️</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div>
                                <flux:input
                                    type="number"
                                    label="Cantidad"
                                    wire:model="cantidad_producto"
                                    min="1"
                                    step="1"
                                    placeholder="1"
                                    :disabled="!$producto_seleccionado || ($transferencia_id && $estado !== 'pendiente')"
                                    :error="$errors->first('cantidad_producto')"
                                />
                            </div>
                            <div class="flex items-end">
                                <flux:button
                                    icon="plus"
                                    wire:click="agregarProducto"
                                    :disabled="!$producto_seleccionado || !$cantidad_producto || ($transferencia_id && $estado !== 'pendiente')"
                                    class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200"
                                    title="Agregar producto a la transferencia"
                                >

                                </flux:button>
                            </div>
                        </div>

                        <!-- Información del producto seleccionado -->
                        @if($producto_seleccionado && $productos_disponibles && $productos_disponibles->count())
                            @php
                                $productoInfo = $productos_disponibles->first(function($p) use ($producto_seleccionado) {
                                    return $p->id == $producto_seleccionado;
                                });
                            @endphp
                            @if($productoInfo)
                                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-green-500 dark:text-green-300">{{ $productoInfo->nombre }}</span>
                                            <span class="text-blue-600 dark:text-blue-400">({{ $productoInfo->code }})</span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-blue-600 dark:text-blue-400">
                                                Stock disponible: <strong>{{ number_format($productoInfo->stock_disponible ?? $productoInfo->stock_actual, 2) }} {{ $productoInfo->unidad_medida }}</strong>
                                            </span>
                                            @if($productoInfo->necesitaReposicion())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                    Stock Bajo
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Lista de productos seleccionados -->
                    <div class="mt-4">
                        @error('productos_seleccionados')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-zinc-50 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Producto</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Unidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @forelse ($productos_seleccionados as $productoId)
                                        @php
                                            $producto = null;
                                            if ($productos_disponibles && $productos_disponibles->count()) {
                                                $producto = $productos_disponibles->first(function($p) use ($productoId) {
                                                    return $p->id == $productoId;
                                                });
                                            }
                                        @endphp
                                        @if($producto)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-300">{{ $producto->code }} <br> {{ $producto->nombre }}</td>
                                                <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                                    <div class="flex items-center gap-2">
                                                        <flux:input
                                                            type="number"
                                                            wire:model="cantidades.{{ $productoId }}"
                                                            min="1"
                                                            max="{{ $producto->stock_disponible ?? $producto->stock_actual }}"
                                                            class="w-12"
                                                            :disabled="$transferencia_id && $estado !== 'pendiente'"
                                                            :error="$errors->first('cantidades.' . $productoId)"
                                                        />
                                                        <span class="text-sm text-zinc-500">
                                                            (Stock: {{ number_format($producto->stock_disponible ?? $producto->stock_actual, 2) }})
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-300">{{ $producto->unidad_medida }}</td>
                                                <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                                    @if(!$transferencia_id || $estado === 'pendiente')
                                                        <flux:button
                                                            wire:click="quitarProducto({{ $productoId }})"
                                                            variant="danger"
                                                            size="xs"
                                                            icon="trash"
                                                        />
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-2 text-center text-zinc-500">
                                                No hay productos seleccionados
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <flux:input
                        label="Observaciones"
                        wire:model="observaciones"
                        placeholder="Ingrese las observaciones"
                        :error="$errors->first('observaciones')"
                        :disabled="$transferencia_id && $estado !== 'pendiente'"
                    />
                </div>

                <div class="mt-6">
                    <flux:select
                        label="Estado"
                        wire:model="estado"
                        :error="$errors->first('estado')"
                        :disabled="!$transferencia_id || $estado !== 'pendiente'"
                    >
                        <option value="pendiente">Pendiente</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button
                        type="button"
                        wire:click="$set('modal_form_transferencia', false)"
                        class="mr-2"
                    >
                        Cancelar
                    </flux:button>
                    @if(!$transferencia_id || $estado === 'pendiente')
                        <flux:button
                            type="submit"
                            variant="primary"
                            :disabled="empty($productos_seleccionados)"
                        >
                            {{ $transferencia_id ? 'Actualizar' : 'Guardar' }}
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>
    </flux:modal>
</div>
