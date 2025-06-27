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

    <!-- Estadísticas Rápidas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Transferencias del Periodo</p>
                    <p class="text-2xl font-bold">{{ $transferencias->where('estado', 'completada')->count() }}</p>
                </div>
                <flux:icon name="arrow-path" class="w-8 h-8 opacity-80" />
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Pendientes</p>
                    <p class="text-2xl font-bold">{{ $transferencias->where('estado', 'pendiente')->count() }}</p>
                </div>
                <flux:icon name="clock" class="w-8 h-8 opacity-80" />
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Canceladas</p>
                    <p class="text-2xl font-bold">{{ $transferencias->where('estado', 'cancelada')->count() }}</p>
                </div>
                <flux:icon name="x-mark" class="w-8 h-8 opacity-80" />
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Transferencias</p>
                    <p class="text-2xl font-bold">{{ $transferencias->count() }}</p>
                </div>
                <flux:icon name="document-text" class="w-8 h-8 opacity-80" />
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
                            Origen → Destino
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Productos/Lotes
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Fecha/Estado
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
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $transferencia->code }}</span>
                                    <span class="text-xs text-zinc-500">{{ $transferencia->usuario->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1">
                                            <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                                {{ $transferencia->almacenOrigen->nombre }}
                                            </div>
                                            <div class="text-xs text-zinc-500">
                                                {{ $transferencia->almacenOrigen->direccion ?? 'Sin dirección' }}
                                            </div>
                                        </div>
                                        <flux:icon name="arrow-right" class="w-4 h-4 text-zinc-400" />
                                        <div class="flex-1">
                                            <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                                {{ $transferencia->almacenDestino->nombre }}
                                            </div>
                                            <div class="text-xs text-zinc-500">
                                                {{ $transferencia->almacenDestino->direccion ?? 'Sin dirección' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    @forelse ($transferencia->productos as $producto)
                                        <div class="flex items-center justify-between p-2 bg-zinc-50 dark:bg-zinc-700 rounded-md border border-zinc-200 dark:border-zinc-600">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-zinc-800 dark:text-zinc-200 truncate">
                                                    {{ $producto['code'] }}
                                                </div>
                                                <div class="text-xs text-zinc-600 dark:text-zinc-400 truncate">
                                                    {{ Str::limit($producto['nombre'], 25) }}
                                                </div>
                                                @if(isset($producto['lote']) && !empty($producto['lote']))
                                                    <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                                        Lote: {{ $producto['lote'] }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-2 ml-2">
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
                                            <flux:icon name="cube" class="w-6 h-6 text-zinc-400" />
                                            No hay productos
                                        </div>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span>{{ $transferencia->fecha_transferencia->format('d/m/Y H:i') }}</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mt-1
                                        @if($transferencia->estado === 'completada') bg-green-100 text-green-800
                                        @elseif($transferencia->estado === 'cancelada') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($transferencia->estado) }}
                                    </span>
                                    @if(!empty($transferencia->observaciones))
                                        <span class="text-xs text-zinc-500 mt-1 truncate" title="{{ $transferencia->observaciones }}">
                                            {{ Str::limit($transferencia->observaciones, 30) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="verDetalleTransferencia({{ $transferencia->id }})" size="xs"
                                         icon="eye" title="Ver detalle de transferencia"
                                        class="hover:bg-gray-600 transition-colors">
                                    </flux:button>
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
                            <td colspan="5" class="px-6 py-4 text-center text-zinc-500">
                                <div class="flex flex-col items-center py-8">
                                    <flux:icon name="arrow-path" class="w-12 h-12 text-zinc-400 mb-4" />
                                    <p class="text-lg font-medium text-zinc-600 dark:text-zinc-400">No hay transferencias disponibles</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-500">Intenta ajustar los filtros o crear una nueva transferencia</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
            {{ $transferencias->links() }}
        </div>
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
                        <!-- Selector de lotes -->
                        <div class="mb-4">
                            <flux:select
                                label="Filtrar por Lote (Opcional)"
                                wire:model.live="lote_producto"
                                :disabled="!$almacen_origen_id || ($transferencia_id && $estado !== 'pendiente')"
                                class="w-full md:w-1/3"
                            >
                                <option value="">Todos los lotes</option>
                                @foreach ($this->getLotesDisponibles() as $lote)
                                    <option value="{{ $lote }}">
                                        Lote: {{ $lote }}
                                        @php
                                            $productosEnLote = $this->getProductosEnLote($lote);
                                            $totalStock = $productosEnLote->sum('stock_actual');
                                        @endphp
                                        ({{ $productosEnLote->count() }} productos, {{ number_format($totalStock, 2) }} unidades)
                                    </option>
                                @endforeach
                            </flux:select>
                        </div>

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
                                            {{ $producto->code }} - {{ $producto->nombre }}
                                            (Stock: {{ number_format($producto->stock_disponible ?? $producto->stock_actual, 2) }} {{ $producto->unidad_medida }})
                                            @if($producto->lote)
                                                - Lote: {{ $producto->lote }}
                                            @endif
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
                            <div>
                                <flux:input
                                    label="Lote"
                                    wire:model="lote_producto"
                                    placeholder="Número de lote (opcional)"
                                    :disabled="!$producto_seleccionado || ($transferencia_id && $estado !== 'pendiente')"
                                    :error="$errors->first('lote_producto')"
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
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Lote</th>
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
                                                <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-300">{{ $producto->lote }}</td>
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

    <!-- Modal Detalle Transferencia -->
    <flux:modal wire:model="modal_detalle_transferencia" class="max-w-5xl">
        <div class="p-6 space-y-4">
            @if($transferencia_detalle)
                <!-- Encabezado mejorado -->
                <div class="flex justify-between items-start border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                            <flux:icon name="arrow-path" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Detalle de la Transferencia</h2>
                            <p class="text-zinc-600 dark:text-zinc-400 mt-1 flex items-center gap-2">
                                <flux:icon name="hashtag" class="w-4 h-4" />
                                {{ $transferencia_detalle->code }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        @php
                            $estadoIcon = match($transferencia_detalle->estado) {
                                'completada' => 'check-circle',
                                'cancelada' => 'x-circle',
                                default => 'clock'
                            };
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full shadow-sm
                            @if($transferencia_detalle->estado === 'completada') bg-green-100 text-green-800 border border-green-200
                            @elseif($transferencia_detalle->estado === 'cancelada') bg-red-100 text-red-800 border border-red-200
                            @else bg-yellow-100 text-yellow-800 border border-yellow-200 @endif">
                            <flux:icon name="{{ $estadoIcon }}" class="w-4 h-4 mr-1" />
                            {{ ucfirst($transferencia_detalle->estado) }}
                        </span>
                    </div>
                </div>

                <!-- Información de la transferencia -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Información básica -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                                <flux:icon name="document-text" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                Información General
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-blue-100 dark:border-blue-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Fecha de Transferencia:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white flex items-center gap-2">
                                        <flux:icon name="calendar" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        {{ $transferencia_detalle->fecha_transferencia->format('d/m/Y h:i A') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-blue-100 dark:border-blue-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Usuario:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white flex items-center gap-2">
                                        <flux:icon name="user" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        {{ $transferencia_detalle->usuario->name ?? 'Usuario no disponible' }}
                                    </span>
                                </div>
                                @if($transferencia_detalle->observaciones)
                                    <div class="flex justify-between items-start p-2 bg-white dark:bg-zinc-800 rounded-lg border border-blue-100 dark:border-blue-900/50">
                                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Observaciones:</span>
                                        <span class="font-medium text-zinc-900 dark:text-white text-right max-w-xs">{{ $transferencia_detalle->observaciones }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información de almacenes -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                                <flux:icon name="building-office" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                Almacenes
                            </h3>
                            <div class="space-y-3">
                                <div class="p-3 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                    <div class="text-center mb-2">
                                        <span class="text-zinc-600 dark:text-zinc-400 text-sm font-medium">Almacén Origen</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-semibold text-zinc-900 dark:text-white text-base">{{ $transferencia_detalle->almacenOrigen->nombre }}</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $transferencia_detalle->almacenOrigen->direccion ?? 'Sin dirección' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center">
                                    <div class="p-1 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                                        <flux:icon name="arrow-down" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    </div>
                                </div>
                                <div class="p-3 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                    <div class="text-center mb-2">
                                        <span class="text-zinc-600 dark:text-zinc-400 text-sm font-medium">Almacén Destino</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-semibold text-zinc-900 dark:text-white text-base">{{ $transferencia_detalle->almacenDestino->nombre }}</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $transferencia_detalle->almacenDestino->direccion ?? 'Sin dirección' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                        <flux:icon name="cube" class="w-4 h-4 text-green-600 dark:text-green-400" />
                        Productos Transferidos
                        <span class="ml-auto text-sm font-normal text-zinc-500 dark:text-zinc-400">
                            {{ count($transferencia_detalle->productos) }} productos
                        </span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white dark:bg-zinc-800 rounded-lg">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Producto</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Unidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-green-200 dark:divide-green-800">
                                @forelse ($transferencia_detalle->productos as $producto)
                                    <tr class="hover:bg-white/50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                            <div class="flex flex-col">
                                                <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $producto['code'] }}</span>
                                                <span class="text-zinc-600 dark:text-zinc-400">{{ $producto['nombre'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                            <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($producto['cantidad'], 2) }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                {{ $producto['unidad_medida'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                            <div class="flex flex-col items-center gap-2">
                                                <flux:icon name="cube" class="w-6 h-6 text-zinc-400" />
                                                <p>No hay productos en esta transferencia</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen de transferencia integrado -->
                    @if(count($transferencia_detalle->productos) > 0)
                        <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-800">
                            <div class="flex justify-end">
                                <div class="w-72 space-y-2">
                                    <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-green-100 dark:border-green-900/50">
                                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Total de productos:</span>
                                        <span class="font-semibold text-zinc-900 dark:text-white">{{ count($transferencia_detalle->productos) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg border border-green-300 dark:border-green-700">
                                        <span class="text-base font-bold">Cantidad total:</span>
                                        <span class="text-lg font-bold">{{ number_format(collect($transferencia_detalle->productos)->sum('cantidad'), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Fecha de creación -->
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <div class="text-sm text-zinc-500 dark:text-zinc-400 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-2">
                                <flux:icon name="calendar" class="w-4 h-4" />
                                Creado el {{ $transferencia_detalle->created_at->format('d/m/Y h:i A') }}
                            </span>
                            @if($transferencia_detalle->updated_at != $transferencia_detalle->created_at)
                                <span class="flex items-center gap-2">
                                    <flux:icon name="clock" class="w-4 h-4" />
                                    Última actualización: {{ $transferencia_detalle->updated_at->format('d/m/Y h:i A') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:icon name="user" class="w-4 h-4" />
                            <span>{{ $transferencia_detalle->usuario->name ?? 'Usuario no disponible' }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-zinc-500 dark:text-zinc-400">
                        <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                            <flux:icon name="exclamation-triangle" class="w-8 h-8 text-red-500 dark:text-red-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Error al cargar</h3>
                        <p>No se pudo cargar el detalle de la transferencia</p>
                    </div>
                </div>
            @endif

            <!-- Botón cerrar -->
            <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button
                    wire:click="$set('modal_detalle_transferencia', false)"
                    class="px-4 py-2"
                >
                    Cerrar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
