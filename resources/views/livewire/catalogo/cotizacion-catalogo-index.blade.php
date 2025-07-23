<div class="p-6 bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Encabezado y Búsqueda -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <flux:heading size="lg">Gestión de Cotizaciones</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">Administra y consulta las cotizaciones de
                    productos catálogo.</flux:text>
            </div>
            <div class="flex items-center justify-end gap-4 w-full md:w-auto">
                <div class="w-full md:w-96">
                    <flux:input type="search" placeholder="Buscar cotizaciones..." wire:model.live="search"
                        icon="magnifying-glass" />
                </div>
                <div class="flex items-end gap-2">
                    <flux:button variant="primary" wire:click="crearCotizacion" icon="plus">
                        Nueva Cotización
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="mb-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <flux:label>Cliente</flux:label>
                <flux:select wire:model.live="customer_filter" class="w-full">
                    <option value="">Todos los clientes</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->rznSocial }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:label>Estado</flux:label>
                <flux:select wire:model.live="estado" class="w-full">
                    <option value="">Todos</option>
                    <option value="borrador">Borrador</option>
                    <option value="enviada">Enviada</option>
                    <option value="aprobada">Aprobada</option>
                    <option value="rechazada">Rechazada</option>
                </flux:select>
            </div>
            <div>
                <flux:label>Fecha desde</flux:label>
                <flux:input type="date" wire:model.live="fecha_desde" class="w-full" />
            </div>
            <div>
                <flux:label>Fecha hasta</flux:label>
                <flux:input type="date" wire:model.live="fecha_hasta" class="w-full" />
            </div>
            <div class="flex items-end">
                <flux:button color="red" icon="trash" class="w-full" wire:click="limpiarFiltros">
                    Limpiar Filtros
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Tabla de Cotizaciones -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Código</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Cliente</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Fecha</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Línea</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Total</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Estado</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($cotizaciones as $cotizacion)
                        <tr wire:key="cotizacion-{{ $cotizacion->id }}"
                            class="hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200 ease-in-out">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-300 font-medium">
                                {{ $cotizacion->codigo_cotizacion }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $cotizacion->cliente_nombre }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300">
                                {{ $cotizacion->line?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-300 font-semibold">S/
                                {{ number_format($cotizacion->total, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $cotizacion->estado === 'borrador'
                                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                                        : ($cotizacion->estado === 'enviada'
                                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                            : ($cotizacion->estado === 'aprobada'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200')) }}">
                                    {{ ucfirst($cotizacion->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <flux:button wire:click="visualizarCotizacion({{ $cotizacion->id }})"
                                        size="xs" icon="eye" title="Visualizar cotización aprobada">
                                    </flux:button>
                                    @if ($cotizacion->estado !== 'aprobada')
                                        <flux:button wire:click="editarCotizacion({{ $cotizacion->id }})"
                                            size="xs" variant="primary" icon="pencil" title="Editar cotización">
                                        </flux:button>
                                    @endif
                                    @if ($cotizacion->estado === 'borrador')
                                        <flux:button wire:click="eliminarCotizacion({{ $cotizacion->id }})"
                                            size="xs" variant="danger" icon="trash" title="Eliminar cotización">
                                        </flux:button>
                                    @else
                                        <flux:button size="xs" variant="outline" icon="lock-closed"
                                            title="No se puede eliminar cotizaciones que no estén en borrador"
                                            class="opacity-50 cursor-not-allowed" disabled></flux:button>
                                    @endif
                                    @if ($cotizacion->estado !== 'aprobada')
                                        <flux:dropdown>
                                            <flux:button icon="ellipsis-vertical" size="xs" variant="outline">
                                            </flux:button>
                                            <flux:menu>
                                                <flux:menu.item
                                                    wire:click="cambiarEstado({{ $cotizacion->id }}, 'borrador')">
                                                    Marcar
                                                    como Borrador</flux:menu.item>
                                                <flux:menu.item
                                                    wire:click="cambiarEstado({{ $cotizacion->id }}, 'enviada')">Marcar
                                                    como Enviada</flux:menu.item>
                                                <flux:menu.item
                                                    wire:click="cambiarEstado({{ $cotizacion->id }}, 'aprobada')">
                                                    Marcar
                                                    como Aprobada</flux:menu.item>
                                                <flux:menu.item
                                                    wire:click="cambiarEstado({{ $cotizacion->id }}, 'rechazada')">
                                                    Marcar
                                                    como Rechazada</flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    @else
                                        <flux:button size="xs" variant="outline" icon="lock-closed"
                                            title="Cotización aprobada - No se puede modificar"
                                            class="opacity-50 cursor-not-allowed" disabled></flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="inbox" class="w-12 h-12 text-zinc-300" />
                                    <span class="text-lg font-medium">No se encontraron cotizaciones</span>
                                    <span class="text-sm">Intenta ajustar los filtros de búsqueda</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($cotizaciones->hasPages())
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-700 border-t border-zinc-200 dark:border-zinc-600">
                {{ $cotizaciones->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Cotización Optimizado -->
    <flux:modal wire:model="modal_cotizacion" variant="flyout" class="w-full max-w-7xl">
        <form wire:submit.prevent="guardarCotizacion">
            <!-- Header Compacto -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 rounded-t-lg text-white mb-4">
                <div class="flex items-center gap-3">
                    <flux:icon name="document-text" class="w-6 h-6" />
                    <div>
                        <h2 class="text-lg font-bold">
                            {{ $editingCotizacion ? 'Editar Cotización' : 'Nueva Cotización' }}
                        </h2>
                        <p class="text-blue-100 text-sm">Complete los datos de la cotización</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-4 h-full">
                <!-- Columna Izquierda - Información Principal -->
                <div class="lg:w-2/5 space-y-3">

                    <!-- Información Básica y Fechas Compactas -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="document" class="w-4 h-4 text-blue-600" />
                            <h3 class="text-sm font-semibold text-gray-800">Información Básica</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <flux:label class="text-xs font-medium">Código</flux:label>
                                <flux:input type="text" wire:model.live="codigo_cotizacion" readonly
                                    size="sm" class="bg-gray-50 font-mono" />
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Estado</flux:label>
                                <flux:select wire:model.live="estado_cotizacion" size="sm">
                                    <option value="borrador">Borrador</option>
                                    <option value="enviada">Enviada</option>
                                    <option value="aprobada">Aprobada</option>
                                    <option value="rechazada">Rechazada</option>
                                </flux:select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <flux:label class="text-xs font-medium">Vendedor</flux:label>
                                <flux:select wire:model.live="user_id" size="sm">
                                    <option value="">Seleccionar vendedor</option>
                                    @forelse ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @empty
                                        <option value="">No hay vendedores disponibles</option>
                                    @endforelse
                                </flux:select>
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Línea</flux:label>
                                <flux:select wire:model.live="line_id" size="sm">
                                    <option value="">Seleccionar línea</option>
                                    @forelse ($lines as $line)
                                        <option value="{{ $line->id }}">{{ $line->name }}</option>
                                    @empty
                                        <option value="">No hay líneas disponibles</option>
                                    @endforelse
                                </flux:select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <flux:label class="text-xs font-medium">Fecha Cotización</flux:label>
                                <flux:input type="date" wire:model.live="fecha_cotizacion"
                                    wire:change="calcularFechaVencimiento" size="sm" />
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Validez (días)</flux:label>
                                <flux:input type="number" wire:model.live="validez_dias"
                                    wire:change="calcularFechaVencimiento" min="1" max="30"
                                    size="sm" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <flux:label class="text-xs font-medium">Fecha Vencimiento</flux:label>
                                <flux:input type="date" wire:model.live="fecha_vencimiento"
                                    wire:change="calcularValidezDias" size="sm" />
                            </div>
                        </div>
                    </div>

                    <!-- Cliente Compacto -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="user" class="w-4 h-4 text-green-600" />
                            <h3 class="text-sm font-semibold text-gray-800">Datos del Cliente</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <flux:label class="text-xs font-medium">Seleccionar Cliente</flux:label>
                                <flux:select wire:model.live="customer_id" wire:change="cargarDatosCliente"
                                    size="sm">
                                    <option value="">Seleccionar cliente</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->rznSocial }}</option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Nombre</flux:label>
                                    <flux:input type="text" wire:model.live="cliente_nombre"
                                        placeholder="Nombre del cliente" size="sm" />
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Email</flux:label>
                                    <flux:input type="email" wire:model.live="cliente_email"
                                        placeholder="email@ejemplo.com" size="sm" />
                                </div>
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Teléfono</flux:label>
                                <flux:input type="text" wire:model.live="cliente_telefono"
                                    placeholder="+51 999 999 999" size="sm" />
                            </div>
                        </div>
                    </div>

                    <!-- Condiciones y Observaciones Compactas -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="credit-card" class="w-4 h-4 text-purple-600" />
                            <h3 class="text-sm font-semibold text-gray-800">Condiciones y Observaciones</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <flux:label class="text-xs font-medium">Condiciones de Pago</flux:label>
                                <flux:textarea wire:model.live="condiciones_pago" rows="2"
                                    placeholder="Ej: Pago al contado, 30 días, etc." size="sm" />
                            </div>
                            <div>
                                <flux:label class="text-xs font-medium">Condiciones de Entrega</flux:label>
                                <flux:textarea wire:model.live="condiciones_entrega" rows="2"
                                    placeholder="Ej: Entrega en 5 días hábiles" size="sm" />
                            </div>
                        </div>
                        <div>
                            <flux:label class="text-xs font-medium">Observaciones Generales</flux:label>
                            <flux:textarea wire:model.live="observaciones_general" rows="2"
                                placeholder="Observaciones adicionales sobre la cotización..." size="sm" />
                        </div>
                    </div>

                    <!-- Resumen de Totales Compacto -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="calculator" class="w-4 h-4 text-emerald-600" />
                            <h3 class="text-sm font-semibold text-gray-800">Resumen de Totales</h3>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal (sin IGV):</span>
                                <span class="font-medium">S/
                                    {{ number_format($this->calcularSubtotalSinIgv(), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">IGV (18%):</span>
                                <span class="font-medium">S/ {{ number_format($this->calcularIgv(), 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="font-semibold text-gray-800">Total (con IGV):</span>
                                <span class="font-bold text-lg text-emerald-600">S/
                                    {{ number_format($this->calcularTotal(), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Productos Optimizada -->
                <div class="lg:w-3/5">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <flux:icon name="cube" class="w-5 h-5 text-orange-600" />
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">Productos Cotizados</h3>
                                    <p class="text-xs text-gray-500">Agregue los productos a cotizar</p>
                                </div>
                            </div>
                            <flux:button type="button" icon="plus" size="sm" wire:click="mostrarProductos"
                                class="bg-orange-500 hover:bg-orange-600 text-white">
                                Agregar Producto
                            </flux:button>
                        </div>

                        <!-- Lista de Productos Disponibles -->
                        @if ($showProductosList)
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg border">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="magnifying-glass" class="w-4 h-4 text-gray-500" />
                                        <h4 class="text-sm font-medium text-gray-700">Seleccionar Producto</h4>
                                    </div>
                                    <flux:button type="button" icon="x-mark" size="xs"
                                        wire:click="ocultarProductos" class="text-gray-500 hover:text-red-500" />
                                </div>

                                <flux:input placeholder="Buscar producto..."
                                    wire:model.live.debounce.300ms="searchProducto" class="mb-3" size="sm" />

                                <div class="max-h-48 overflow-y-auto space-y-2">
                                    @foreach ($productos as $producto)
                                        <div class="border rounded p-2 hover:bg-orange-50 cursor-pointer transition-colors"
                                            wire:click="agregarProducto({{ $producto->id }})"
                                            wire:key="producto-{{ $producto->id }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="font-medium text-xs text-gray-900">
                                                        {{ $producto->code }} -
                                                        {{ \Illuminate\Support\Str::limit($producto->description, 40, '') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $producto->brand->name ?? '' }} /
                                                        {{ $producto->category->name ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="text-right ml-3">
                                                    <div class="font-semibold text-xs text-green-600">S/
                                                        {{ number_format($producto->price_venta, 2) }}</div>
                                                    <div class="text-xs text-gray-500">Incluye IGV</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Productos Seleccionados Compactos -->
                        <div class="space-y-2 max-h-80 overflow-y-auto">
                            @if (count($selectedProductos) > 0)
                                @foreach ($selectedProductos as $productoId)
                                    @php $producto = $productos->firstWhere('id', $productoId); @endphp
                                    @if ($producto)
                                        <div class="border rounded p-3 bg-gray-50"
                                            wire:key="selected-producto-{{ $productoId }}">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <div class="font-medium text-xs text-gray-900">
                                                        {{ $producto->code }}</div>
                                                    <div class="text-xs text-gray-600">
                                                        {{ \Illuminate\Support\Str::limit($producto->description, 35, '') }}
                                                    </div>
                                                </div>
                                                <flux:button icon="trash" size="xs"
                                                    class="text-red-500 hover:text-red-700"
                                                    wire:click="removerProducto({{ $productoId }})" />
                                            </div>

                                            <div class="grid grid-cols-3 gap-2 mb-2">
                                                <div>
                                                    <flux:label class="text-xs font-medium">Cantidad</flux:label>
                                                    <flux:input type="number" min="1"
                                                        wire:model.live="cantidades.{{ $productoId }}"
                                                        size="sm" />
                                                </div>
                                                <div>
                                                    <flux:label class="text-xs font-medium">Precio Unit.</flux:label>
                                                    <flux:input type="number" step="0.01" min="0"
                                                        wire:model.live="precios.{{ $productoId }}"
                                                        size="sm" />
                                                </div>
                                                <div>
                                                    <flux:label class="text-xs font-medium">Subtotal</flux:label>
                                                    <flux:input readonly
                                                        value="S/ {{ number_format(($cantidades[$productoId] ?? 0) * ($precios[$productoId] ?? 0), 2) }}"
                                                        size="sm" class="bg-gray-100" />
                                                </div>
                                            </div>

                                            <flux:textarea wire:model.live="observaciones.{{ $productoId }}"
                                                rows="1" size="sm" placeholder="Observaciones..." />
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <flux:icon name="shopping-cart" class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                    <p class="text-xs font-medium">No hay productos agregados</p>
                                    <p class="text-xs text-gray-400 mt-1">Haga clic en "Agregar Producto" para comenzar
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción compactos -->
            <div
                class="flex justify-between items-center mt-4 border-t pt-3 bg-white dark:bg-zinc-900 sticky bottom-0 z-10">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <flux:icon name="information-circle" class="w-3 h-3" />
                    <span>Los campos marcados con * son obligatorios</span>
                </div>
                <div class="flex gap-2">
                    <flux:button wire:click="$set('modal_cotizacion', false)" variant="outline" size="sm">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                        {{ $editingCotizacion ? 'Actualizar' : 'Crear' }} Cotización
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Modal de Visualización de Cotización - Formato A4 -->
    <flux:modal wire:model="modal_visualizacion" class="w-full max-w-5xl">
        @if ($pdfUrl)
            <div class="flex flex-col items-center justify-center h-[80vh]">
                <iframe src="{{ $pdfUrl }}" class="w-full h-full min-h-[70vh] rounded shadow"
                    frameborder="0"></iframe>
                <div class="mt-4 flex justify-end w-full">
                    <flux:button wire:click="cerrarModalVisualizacion" variant="outline">
                        Cerrar
                    </flux:button>
                </div>
            </div>
        @else
            <div class="flex items-center justify-center h-64">
                <div class="text-center">
                    <flux:icon name="printer" class="w-16 h-16 mx-auto mb-4 text-yellow-500" />
                    <p class="text-lg font-medium text-gray-700">Generando PDF de la cotización...</p>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
