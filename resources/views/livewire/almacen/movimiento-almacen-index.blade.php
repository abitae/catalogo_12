<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Movimientos de Almacén</h1>
            <div class="flex items-center justify-end gap-4 w-full">
                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar..." wire:model.live="search" icon="magnifying-glass" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button wire:click="exportarMovimientos" icon="arrow-down-tray">
                        Exportar
                    </flux:button>
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="nuevoMovimiento" icon="plus">
                        Nuevo
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Entradas del Periodo</p>
                    <p class="text-2xl font-bold">{{ $movimientos->where('tipo_movimiento', 'entrada')->where('estado', 'completado')->sum('total') ? 'S/ ' . number_format($movimientos->where('tipo_movimiento', 'entrada')->where('estado', 'completado')->sum('total'), 2) : 'S/ 0.00' }}</p>
                </div>
                <flux:icon name="arrow-down" class="w-8 h-8 opacity-80" />
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Salidas del Periodo</p>
                    <p class="text-2xl font-bold">{{ $movimientos->where('tipo_movimiento', 'salida')->where('estado', 'completado')->sum('total') ? 'S/ ' . number_format($movimientos->where('tipo_movimiento', 'salida')->where('estado', 'completado')->sum('total'), 2) : 'S/ 0.00' }}</p>
                </div>
                <flux:icon name="arrow-up" class="w-8 h-8 opacity-80" />
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Pendientes</p>
                    <p class="text-2xl font-bold">{{ $movimientos->where('estado', 'pendiente')->count() }}</p>
                </div>
                <flux:icon name="clock" class="w-8 h-8 opacity-80" />
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Movimientos</p>
                    <p class="text-2xl font-bold">{{ $movimientos->count() }}</p>
                </div>
                <flux:icon name="document-text" class="w-8 h-8 opacity-80" />
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Almacén -->
            <div>
                <flux:label>Almacén</flux:label>
                <flux:select wire:model.live="almacen_filter" class="w-full">
                    <option value="">Todos los almacenes</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Tipo de Movimiento -->
            <div>
                <flux:label>Tipo de Movimiento</flux:label>
                <flux:select wire:model.live="tipo_movimiento_filter" class="w-full">
                    <option value="">Todos</option>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </flux:select>
            </div>

            <!-- Estado -->
            <div>
                <flux:label>Estado</flux:label>
                <flux:select wire:model.live="estado_filter" class="w-full">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
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

    <!-- Tabla de Movimientos -->
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
                            Tipo/Documento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Almacén
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Productos/Lotes
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Total
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
                    @forelse ($movimientos as $movimiento)
                        <tr wire:key="movimiento-{{ $movimiento->id }}" class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $movimiento->code }}</span>
                                    <span class="text-xs text-zinc-500">{{ $movimiento->user->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($movimiento->tipo_movimiento === 'entrada') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($movimiento->tipo_movimiento) }}
                                    </span>
                                    <span class="text-xs text-zinc-500 mt-1">
                                        {{ ucfirst($movimiento->tipo_documento) }}: {{ $movimiento->numero_documento }}
                                    </span>
                                    <span class="text-xs text-zinc-500">
                                        {{ ucfirst($movimiento->tipo_operacion) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $movimiento->almacen->nombre }}</span>
                                    <span class="text-xs text-zinc-500">{{ $movimiento->almacen->direccion ?? 'Sin dirección' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    @forelse ($movimiento->productos as $producto)
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
                                            <flux:icon name="cube" class="w-4 h-4 mx-auto mb-1" />
                                            No hay productos
                                        </div>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span class="font-medium">S/ {{ number_format($movimiento->total, 2) }}</span>
                                    <span class="text-xs text-zinc-500">
                                        Sub: S/ {{ number_format($movimiento->subtotal, 2) }}
                                    </span>
                                    @if($movimiento->descuento > 0)
                                        <span class="text-xs text-green-600">
                                            Desc: S/ {{ number_format($movimiento->descuento, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                <div class="flex flex-col">
                                    <span>{{ $movimiento->fecha_emision->format('d/m/Y H:i') }}</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mt-1
                                        @if($movimiento->estado === 'completado') bg-green-100 text-green-800
                                        @elseif($movimiento->estado === 'cancelado') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($movimiento->estado) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="verDetalleMovimiento({{ $movimiento->id }})" size="xs"
                                        icon="eye" title="Ver detalle del movimiento"
                                        class="hover:bg-blue-600 transition-colors">
                                    </flux:button>
                                    @if($movimiento->estado === 'pendiente')
                                        <flux:button wire:click="completarMovimiento({{ $movimiento->id }})" size="xs"
                                            icon="check" title="Completar movimiento"
                                            class="hover:bg-green-600 transition-colors">
                                        </flux:button>
                                        <flux:button wire:click="cancelarMovimiento({{ $movimiento->id }})" size="xs"
                                            variant="danger" icon="x-mark" title="Cancelar movimiento"
                                            class="hover:bg-red-600 transition-colors">
                                        </flux:button>
                                        <flux:button wire:click="editarMovimiento({{ $movimiento->id }})" size="xs"
                                            variant="primary" icon="pencil" title="Editar movimiento"
                                            class="hover:bg-blue-600 transition-colors">
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-zinc-500">
                                <div class="flex flex-col items-center py-8">
                                    <flux:icon name="document-text" class="w-12 h-12 text-zinc-400 mb-4" />
                                    <p class="text-lg font-medium text-zinc-600 dark:text-zinc-400">No hay movimientos disponibles</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-500">Intenta ajustar los filtros o crear un nuevo movimiento</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
            {{ $movimientos->links() }}
        </div>
    </div>

    <!-- Modal Form Movimiento -->
    <flux:modal wire:model="modal_form_movimiento" variant="flyout" class="w-2/3 max-w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $movimiento_id ? 'Editar Movimiento' : 'Nuevo Movimiento' }}</flux:heading>
                <flux:text class="mt-2">Complete los datos del movimiento de almacén.</flux:text>
            </div>

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="guardarMovimiento">
                <!-- Información básica -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select
                        label="Tipo de Movimiento"
                        wire:model.live="tipo_movimiento"
                        :error="$errors->first('tipo_movimiento')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    >
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </flux:select>

                    <flux:select
                        label="Almacén"
                        wire:model.live="almacen_id"
                        :error="$errors->first('almacen_id')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    >
                        <option value="">Seleccione un almacén</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input
                        label="Código"
                        wire:model="code"
                        placeholder="Ingrese el código"
                        :error="$errors->first('code')"
                        :disabled="true"
                    />
                </div>

                <!-- Información del documento -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <flux:select
                        label="Tipo de Documento"
                        wire:model="tipo_documento"
                        :error="$errors->first('tipo_documento')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    >
                        <option value="factura">Factura</option>
                        <option value="boleta">Boleta</option>
                        <option value="nota_credito">Nota de Crédito</option>
                        <option value="nota_debito">Nota de Débito</option>
                        <option value="guia_remision">Guía de Remisión</option>
                        <option value="nota_de_venta">Nota de Venta</option>
                        <option value="sin_documento">Sin Documento</option>
                    </flux:select>

                    <flux:input
                        label="Número de Documento"
                        wire:model="numero_documento"
                        placeholder="Ingrese el número"
                        :error="$errors->first('numero_documento')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    />

                    <flux:select
                        label="Tipo de Operación"
                        wire:model="tipo_operacion"
                        :error="$errors->first('tipo_operacion')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    >
                        <option value="compra">Compra</option>
                        <option value="venta">Venta</option>
                        <option value="ajuste">Ajuste</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="devolucion">Devolución</option>
                    </flux:select>

                    <flux:select
                        label="Tipo de Pago"
                        wire:model="tipo_pago"
                        :error="$errors->first('tipo_pago')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    >
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="cheque">Cheque</option>
                    </flux:select>
                </div>

                <!-- Información de fechas y pagos -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <flux:input
                        label="Fecha de Emisión"
                        type="date"
                        wire:model="fecha_emision"
                        :error="$errors->first('fecha_emision')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    />

                    <flux:select
                        label="Forma de Pago"
                        wire:model.live="forma_pago"
                        :error="$errors->first('forma_pago')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    >
                        <option value="contado">Contado</option>
                        <option value="credito">Crédito</option>
                    </flux:select>

                    <flux:input
                        label="Fecha de Vencimiento"
                        type="date"
                        wire:model="fecha_vencimiento"
                        :error="$errors->first('fecha_vencimiento')"
                        :disabled="$movimiento_id && $estado !== 'pendiente' || $forma_pago !== 'credito'"
                        :class="$forma_pago !== 'credito' ? 'opacity-50' : ''"
                    />

                    <flux:select
                        label="Tipo de Moneda"
                        wire:model="tipo_moneda"
                        :error="$errors->first('tipo_moneda')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    >
                        <option value="PEN">PEN (Soles)</option>
                        <option value="USD">USD (Dólares)</option>
                        <option value="EUR">EUR (Euros)</option>
                    </flux:select>
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
                                :disabled="!$almacen_id || ($movimiento_id && $estado !== 'pendiente')"
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

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="md:col-span-2">
                                <flux:select
                                    label="Producto"
                                    wire:model.live="producto_seleccionado"
                                    :disabled="!$almacen_id || ($movimiento_id && $estado !== 'pendiente')"
                                    :error="$errors->first('producto_seleccionado')"
                                >
                                    <option value="">Seleccione un producto</option>
                                    @foreach ($productos_disponibles as $producto)
                                        <option value="{{ $producto->id }}">
                                            {{ $producto->code }} - {{ $producto->nombre }}
                                            @if($tipo_movimiento === 'salida')
                                                (Stock: {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }})
                                            @endif
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
                                    wire:model.live="cantidad_producto"
                                    min="1"
                                    step="1"
                                    placeholder="1"
                                    :disabled="!$producto_seleccionado || ($movimiento_id && $estado !== 'pendiente')"
                                    :error="$errors->first('cantidad_producto')"
                                />
                            </div>
                            <div>
                                <flux:input
                                    label="Lote"
                                    wire:model.live="lote_producto"
                                    placeholder="Número de lote (opcional)"
                                    :disabled="!$producto_seleccionado || ($movimiento_id && $estado !== 'pendiente')"
                                    :error="$errors->first('lote_producto')"
                                />
                            </div>
                            <div>
                                <flux:input
                                    type="number"
                                    label="Precio"
                                    wire:model.live="precio_producto"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    :disabled="!$producto_seleccionado || ($movimiento_id && $estado !== 'pendiente')"
                                    :error="$errors->first('precio_producto')"
                                />
                            </div>
                            <div class="flex items-end">
                                <flux:button
                                    icon="plus"
                                    wire:click="agregarProducto"
                                    :disabled="!$producto_seleccionado || !$cantidad_producto || !$precio_producto || ($movimiento_id && $estado !== 'pendiente')"
                                    class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200"
                                    title="Agregar producto al movimiento"
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
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-green-500 dark:text-green-300">{{ $productoInfo->nombre }}</span>
                                            <span class="text-blue-600 dark:text-blue-400">({{ $productoInfo->code }})</span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-blue-600 dark:text-blue-400">
                                                Stock actual: <strong>{{ number_format($productoInfo->stock_actual, 2) }} {{ $productoInfo->unidad_medida }}</strong>
                                            </span>
                                            @if($productoInfo->necesitaReposicion())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                    Stock Bajo
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-4">
                                            @if($tipo_movimiento === 'salida')
                                                <span class="text-orange-600 dark:text-orange-400">
                                                    Cantidad máxima: <strong>{{ number_format($productoInfo->stock_actual, 2) }} {{ $productoInfo->unidad_medida }}</strong>
                                                </span>
                                            @else
                                                <span class="text-green-600 dark:text-green-400">
                                                    Cantidad disponible: <strong>Sin límite</strong>
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
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Precio</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Subtotal</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Unidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @forelse ($productos_seleccionados as $index => $productoId)
                                        @php
                                            $producto = null;
                                            $cantidad = $cantidades[$productoId] ?? 1;
                                            $precio = $precios[$productoId] ?? 0;

                                            if ($productos_disponibles && $productos_disponibles->count()) {
                                                $producto = $productos_disponibles->first(function($p) use ($productoId) {
                                                    return $p->id == $productoId;
                                                });
                                            }
                                        @endphp
                                        @if($producto)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 {{ isset($producto->estado) && !$producto->estado ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-300">
                                                    <div class="flex flex-col">
                                                        <span class="font-medium text-blue-600 dark:text-blue-400">{{ $producto->code }}</span>
                                                        <span class="text-zinc-600 dark:text-zinc-400">{{ $producto->nombre }}</span>
                                                        @if(isset($producto->estado) && !$producto->estado)
                                                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">No disponible en este almacén</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-300">
                                                    <div class="flex items-center gap-2">
                                                        <flux:input
                                                            type="number"
                                                            wire:model.live="cantidades.{{ $productoId }}"
                                                            min="0.01"
                                                            step="0.01"
                                                            @if($tipo_movimiento === 'salida' && isset($producto->estado) && $producto->estado)
                                                                max="{{ $producto->stock_actual }}"
                                                            @endif
                                                            class="w-20"
                                                            :disabled="$movimiento_id && $estado !== 'pendiente' || (isset($producto->estado) && !$producto->estado)"
                                                            :error="$errors->first('cantidades.' . $productoId)"
                                                            placeholder="0.00"
                                                        />
                                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                            {{ $producto->unidad_medida }}
                                                        </span>
                                                    </div>
                                                    @error('cantidades.' . $productoId)
                                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-300">
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-zinc-500 dark:text-zinc-400 text-xs">Lote:</span>
                                                        <flux:input
                                                            type="text"
                                                            wire:model.live="lotes.{{ $productoId }}"
                                                            placeholder="Número de lote (opcional)"
                                                            class="w-24"
                                                            :disabled="$movimiento_id && $estado !== 'pendiente' || (isset($producto->estado) && !$producto->estado)"
                                                            :error="$errors->first('lotes.' . $productoId)"
                                                        />
                                                    </div>
                                                    @error('lotes.' . $productoId)
                                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-300">
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-zinc-500 dark:text-zinc-400 text-xs">S/</span>
                                                        <flux:input
                                                            type="number"
                                                            wire:model.live="precios.{{ $productoId }}"
                                                            min="0"
                                                            step="0.01"
                                                            class="w-24"
                                                            :disabled="$movimiento_id && $estado !== 'pendiente' || (isset($producto->estado) && !$producto->estado)"
                                                            :error="$errors->first('precios.' . $productoId)"
                                                            placeholder="0.00"
                                                        />
                                                    </div>
                                                    @error('precios.' . $productoId)
                                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-300">
                                                    <div class="font-medium text-green-600 dark:text-green-400">
                                                        S/ {{ number_format($cantidad * $precio, 2) }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-300">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                        {{ $producto->unidad_medida }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-300">
                                                    @if(!$movimiento_id || $estado === 'pendiente')
                                                        <flux:button
                                                            wire:click="quitarProducto({{ $productoId }})"
                                                            variant="danger"
                                                            size="xs"
                                                            icon="trash"
                                                            class="hover:bg-red-600"
                                                            title="Eliminar producto"
                                                        />
                                                    @else
                                                        <span class="text-zinc-400 text-xs">No editable</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center">
                                                <div class="flex flex-col items-center gap-2 text-zinc-500 dark:text-zinc-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                    </svg>
                                                    <span class="text-sm">No hay productos seleccionados</span>
                                                    <span class="text-xs">Agrega productos usando el formulario superior</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Totales -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <flux:input
                        label="Subtotal"
                        wire:model="subtotal"
                        :disabled="true"
                        :error="$errors->first('subtotal')"
                    />

                    <flux:input
                        label="Descuento"
                        wire:model.live="descuento"
                        type="number"
                        min="0"
                        step="0.01"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                        :error="$errors->first('descuento')"
                    />

                    <flux:input
                        label="Impuesto (IGV 18%)"
                        wire:model="impuesto"
                        :disabled="true"
                        :error="$errors->first('impuesto')"
                    />

                    <flux:input
                        label="Total"
                        wire:model="total"
                        :disabled="true"
                        :error="$errors->first('total')"
                    />
                </div>

                <div class="mt-6">
                    <flux:input
                        label="Observaciones"
                        wire:model="observaciones"
                        placeholder="Ingrese las observaciones"
                        :error="$errors->first('observaciones')"
                        :disabled="$movimiento_id && $estado !== 'pendiente'"
                    />
                </div>

                <div class="mt-6">
                    <flux:select
                        label="Estado"
                        wire:model="estado"
                        :error="$errors->first('estado')"
                        :disabled="true"
                    >
                        <option value="pendiente">Pendiente</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </flux:select>
                    <div class="text-xs text-zinc-500 mt-1">
                        El estado solo se puede cambiar usando los botones de "Completar" o "Cancelar" en la lista de movimientos.
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button
                        type="button"
                        wire:click="$set('modal_form_movimiento', false)"
                        class="mr-2"
                    >
                        Cancelar
                    </flux:button>
                    @if(!$movimiento_id || $estado === 'pendiente')
                        <flux:button
                            type="submit"
                            variant="primary"
                            :disabled="empty($productos_seleccionados)"
                        >
                            {{ $movimiento_id ? 'Actualizar' : 'Guardar' }}
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Detalle de Movimiento -->
    <flux:modal wire:model="modal_detalle_movimiento" class="max-w-5xl">
        <div class="p-6 space-y-4">
            @if($movimiento_detalle)
                <!-- Encabezado mejorado -->
                <div class="flex justify-between items-start border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Detalle del Movimiento</h2>
                            <p class="text-zinc-600 dark:text-zinc-400 mt-1 flex items-center gap-2">
                                <flux:icon name="hashtag" class="w-4 h-4" />
                                {{ $movimiento_detalle->code }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        @php
                            $estadoIcon = match($movimiento_detalle->estado) {
                                'completado' => 'check-circle',
                                'cancelado' => 'x-circle',
                                default => 'clock'
                            };
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full shadow-sm
                            @if($movimiento_detalle->estado === 'completado') bg-green-100 text-green-800 border border-green-200
                            @elseif($movimiento_detalle->estado === 'cancelado') bg-red-100 text-red-800 border border-red-200
                            @else bg-yellow-100 text-yellow-800 border border-yellow-200 @endif">
                            <flux:icon name="{{ $estadoIcon }}" class="w-4 h-4 mr-1" />
                            {{ ucfirst($movimiento_detalle->estado) }}
                        </span>
                    </div>
                </div>

                <!-- Información del movimiento -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Información básica -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                                <flux:icon name="information" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                Información General
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-blue-100 dark:border-blue-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Tipo de Movimiento:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">
                                        @php
                                            $tipoIcon = $movimiento_detalle->tipo_movimiento === 'entrada' ? 'arrow-down' : 'arrow-up';
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full shadow-sm
                                            @if($movimiento_detalle->tipo_movimiento === 'entrada') bg-green-100 text-green-800 border border-green-200
                                            @else bg-red-100 text-red-800 border border-red-200 @endif">
                                            <flux:icon name="{{ $tipoIcon }}" class="w-3 h-3 mr-1" />
                                            {{ ucfirst($movimiento_detalle->tipo_movimiento) }}
                                        </span>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-blue-100 dark:border-blue-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Almacén:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white flex items-center gap-2">
                                        <flux:icon name="building-office" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        {{ $movimiento_detalle->almacen->nombre }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-blue-100 dark:border-blue-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Tipo de Operación:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ ucfirst($movimiento_detalle->tipo_operacion) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-blue-100 dark:border-blue-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Usuario:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white flex items-center gap-2">
                                        <flux:icon name="user" class="w-4 h-4" />
                                        {{ $movimiento_detalle->user->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del documento -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                                <flux:icon name="document" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                Documento
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Tipo de Documento:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ ucfirst($movimiento_detalle->tipo_documento) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Número:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white font-mono">{{ $movimiento_detalle->numero_documento }}</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Fecha de Emisión:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white flex items-center gap-2">
                                        <flux:icon name="calendar" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                        {{ $movimiento_detalle->fecha_emision }}
                                    </span>
                                </div>
                                @if($movimiento_detalle->fecha_vencimiento)
                                    <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Fecha de Vencimiento:</span>
                                        <span class="font-medium text-zinc-900 dark:text-white flex items-center gap-2">
                                            <flux:icon name="calendar" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                            {{ $movimiento_detalle->fecha_vencimiento }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Forma de Pago:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ ucfirst($movimiento_detalle->forma_pago) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-purple-100 dark:border-purple-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Moneda:</span>
                                    <span class="font-medium text-zinc-900 dark:text-white font-mono">{{ $movimiento_detalle->tipo_moneda }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                        <flux:icon name="cube" class="w-4 h-4 text-green-600 dark:text-green-400" />
                        Productos
                        <span class="ml-auto text-sm font-normal text-zinc-500 dark:text-zinc-400">
                            {{ count($movimiento_detalle->productos) }} productos
                        </span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white dark:bg-zinc-800 rounded-lg">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Producto</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Lote</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Precio Unitario</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Subtotal</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Unidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-green-200 dark:divide-green-800">
                                @forelse ($movimiento_detalle->productos as $producto)
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
                                            <span class="font-semibold text-purple-600 dark:text-purple-400">{{ $producto['lote'] }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                            <span class="font-semibold">S/ {{ number_format($producto['precio'], 2) }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                                S/ {{ number_format($producto['cantidad'] * $producto['precio'], 2) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-300">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                {{ $producto['unidad_medida'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                            <div class="flex flex-col items-center gap-2">
                                                <flux:icon name="cube" class="w-6 h-6 text-zinc-400" />
                                                <p>No hay productos en este movimiento</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen financiero integrado -->
                    <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-800">
                        <div class="flex justify-end">
                            <div class="w-72 space-y-2">
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-green-100 dark:border-green-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Subtotal:</span>
                                    <span class="font-semibold text-zinc-900 dark:text-white">S/ {{ number_format($movimiento_detalle->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-green-100 dark:border-green-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Descuento:</span>
                                    <span class="font-semibold text-zinc-900 dark:text-white">S/ {{ number_format($movimiento_detalle->descuento, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-white dark:bg-zinc-800 rounded-lg border border-green-100 dark:border-green-900/50">
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Impuesto (IGV 18%):</span>
                                    <span class="font-semibold text-zinc-900 dark:text-white">S/ {{ number_format($movimiento_detalle->impuesto, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg border border-green-300 dark:border-green-700">
                                    <span class="text-base font-bold">Total:</span>
                                    <span class="text-lg font-bold">S/ {{ number_format($movimiento_detalle->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                @if($movimiento_detalle->observaciones)
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-4 border border-indigo-200 dark:border-indigo-800">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <flux:icon name="chat-bubble-left" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                            Observaciones
                        </h3>
                        <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg border border-indigo-100 dark:border-indigo-900/50">
                            <p class="text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                {{ $movimiento_detalle->observaciones }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Fecha de creación -->
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <div class="text-sm text-zinc-500 dark:text-zinc-400 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-2">
                                <flux:icon name="calendar" class="w-4 h-4" />
                                Creado el {{ $movimiento_detalle->created_at->format('d/m/Y h:i A') }}
                            </span>
                            @if($movimiento_detalle->updated_at != $movimiento_detalle->created_at)
                                <span class="flex items-center gap-2">
                                    <flux:icon name="clock" class="w-4 h-4" />
                                    Última actualización: {{ $movimiento_detalle->updated_at->format('d/m/Y h:i A') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:icon name="user" class="w-4 h-4" />
                            <span>{{ $movimiento_detalle->user->name }}</span>
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
                        <p>No se pudo cargar el detalle del movimiento</p>
                    </div>
                </div>
            @endif

            <!-- Botón cerrar -->
            <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button
                    wire:click="$set('modal_detalle_movimiento', false)"
                    class="px-4 py-2"
                >
                    Cerrar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
