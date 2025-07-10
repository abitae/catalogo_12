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
                                      @if($cotizacion->estado !== 'aprobada')
                                          <flux:button wire:click="editarCotizacion({{ $cotizacion->id }})" size="xs"
                                              variant="primary" icon="pencil" title="Editar cotización"></flux:button>
                                      @else
                                          <flux:button wire:click="visualizarCotizacion({{ $cotizacion->id }})" size="xs"
                                              variant="info" icon="eye" title="Visualizar cotización aprobada"></flux:button>
                                      @endif
                                      @if($cotizacion->estado === 'borrador')
                                          <flux:button wire:click="eliminarCotizacion({{ $cotizacion->id }})" size="xs"
                                              variant="danger" icon="trash" title="Eliminar cotización"></flux:button>
                                      @else
                                          <flux:button size="xs" variant="outline" icon="lock-closed"
                                              title="No se puede eliminar cotizaciones que no estén en borrador"
                                              class="opacity-50 cursor-not-allowed" disabled></flux:button>
                                      @endif
                                                                          @if($cotizacion->estado !== 'aprobada')
                                          <flux:dropdown>
                                              <flux:button icon="ellipsis-vertical" size="xs" variant="outline">
                                              </flux:button>
                                              <flux:menu>
                                                  <flux:menu.item
                                                      wire:click="cambiarEstado({{ $cotizacion->id }}, 'borrador')">Marcar
                                                      como Borrador</flux:menu.item>
                                                  <flux:menu.item
                                                      wire:click="cambiarEstado({{ $cotizacion->id }}, 'enviada')">Marcar
                                                      como Enviada</flux:menu.item>
                                                  <flux:menu.item
                                                      wire:click="cambiarEstado({{ $cotizacion->id }}, 'aprobada')">Marcar
                                                      como Aprobada</flux:menu.item>
                                                  <flux:menu.item
                                                      wire:click="cambiarEstado({{ $cotizacion->id }}, 'rechazada')">Marcar
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

    <!-- Modal Form Cotización -->
    <flux:modal wire:model="modal_cotizacion" variant="flyout" class="w-full max-w-none">
        <form wire:submit.prevent="guardarCotizacion">
            <div class="flex gap-6 h-full">
                <!-- Columna Izquierda - Información Principal -->
                <div class="w-2/5 space-y-4">
                    <!-- Cabecera -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white">
                        <div class="flex items-center gap-3 mb-2">
                            <flux:icon name="document-text" class="w-8 h-8" />
                            <div>
                                <flux:heading size="lg" class="text-white">
                                    @if($editingCotizacion && $editingCotizacion->estado === 'aprobada')
                                        Visualizar Cotización Aprobada
                                    @else
                                        {{ $editingCotizacion ? 'Editar Cotización' : 'Nueva Cotización' }}
                                    @endif
                                </flux:heading>
                                <flux:text class="text-blue-100">Complete los datos de la cotización</flux:text>
                            </div>
                        </div>

                    </div>

                    <!-- Primera Fila - 3 Columnas -->
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Información Básica -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-2 mb-3">
                                <flux:icon name="document" class="w-4 h-4 text-blue-600" />
                                <flux:heading size="sm">Información Básica</flux:heading>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Código</flux:label>
                                    <flux:input type="text" wire:model.live="codigo_cotizacion" readonly
                                        class="bg-gray-50 font-mono text-xs" />
                                    @error('codigo_cotizacion')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Estado</flux:label>
                                    <flux:select wire:model.live="estado_cotizacion" class="text-xs" {{ $modoVisualizacion ? 'disabled' : '' }}>
                                        <option value="borrador">
                                            <flux:icon.pencil-square class="inline w-4 h-4 mr-1 text-zinc-500" />
                                            Borrador
                                        </option>
                                        <option value="enviada">
                                            <flux:icon.paper-airplane class="inline w-4 h-4 mr-1 text-blue-500" />
                                            Enviada
                                        </option>
                                        <option value="aprobada">
                                            <flux:icon.check-circle class="inline w-4 h-4 mr-1 text-emerald-500" />
                                            Aprobada
                                        </option>
                                        <option value="rechazada">
                                            <flux:icon.x-circle class="inline w-4 h-4 mr-1 text-red-500" /> Rechazada
                                        </option>
                                    </flux:select>
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Vendedor</flux:label>
                                    <flux:select wire:model.live="user_id" class="text-xs">
                                        <option value="">Seleccionar</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('user_id')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div
                            class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
                            <div class="flex items-center gap-2 mb-3">
                                <flux:icon name="calendar" class="w-4 h-4 text-emerald-600" />
                                <flux:heading size="sm">Fechas</flux:heading>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Fecha Cotización</flux:label>
                                    <flux:input type="date" wire:model.live="fecha_cotizacion"
                                        wire:change="calcularFechaVencimiento" class="text-xs" />
                                    @error('fecha_cotizacion')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Fecha Vencimiento</flux:label>
                                    <flux:input type="date" wire:model.live="fecha_vencimiento"
                                        wire:change="calcularValidezDias" class="text-xs" />
                                    @error('fecha_vencimiento')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Validez (días)</flux:label>
                                    <flux:input type="number" wire:model.live="validez_dias"
                                        wire:change="calcularFechaVencimiento" min="1" max="30"
                                        class="text-xs" />
                                    @error('validez_dias')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                    <flux:text class="text-xs text-gray-500 mt-1">Mínimo 1, máximo 30 días</flux:text>
                                </div>
                            </div>
                        </div>

                        <!-- Cliente -->
                        <div
                            class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                            <div class="flex items-center gap-2 mb-3">
                                <flux:icon name="user" class="w-4 h-4 text-green-600" />
                                <flux:heading size="sm">Cliente</flux:heading>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Seleccionar</flux:label>
                                    <flux:select wire:model.live="customer_id" wire:change="cargarDatosCliente"
                                        class="text-xs">
                                        <option value="">Seleccionar</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->rznSocial }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('customer_id')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Nombre</flux:label>
                                    <flux:input type="text" wire:model.live="cliente_nombre" placeholder="Nombre"
                                        class="text-xs" />
                                    @error('cliente_nombre')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Email</flux:label>
                                    <flux:input type="email" wire:model.live="cliente_email" placeholder="Email"
                                        class="text-xs" />
                                    @error('cliente_email')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Segunda Fila - 2 Columnas -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Información Adicional del Cliente -->
                        <div
                            class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl p-4 border border-cyan-200 dark:border-cyan-800">
                            <div class="flex items-center gap-2 mb-3">
                                <flux:icon name="phone" class="w-4 h-4 text-cyan-600" />
                                <flux:heading size="sm">Contacto</flux:heading>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Teléfono</flux:label>
                                    <flux:input type="text" wire:model.live="cliente_telefono"
                                        placeholder="+51 999 999 999" class="text-xs" />
                                    @error('cliente_telefono')
                                        <div class="text-red-600 text-xs mt-1 flex items-center gap-1">
                                            <flux:icon name="exclamation-circle" class="w-3 h-3" />
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Observaciones</flux:label>
                                    <flux:textarea wire:model.live="observaciones_general" rows="3"
                                        placeholder="Observaciones..." class="text-xs" />
                                </div>
                            </div>
                        </div>

                        <!-- Condiciones Comerciales -->
                        <div
                            class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center gap-2 mb-3">
                                <flux:icon name="credit-card" class="w-4 h-4 text-purple-600" />
                                <flux:heading size="sm">Condiciones</flux:heading>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <flux:label class="text-xs font-medium">Condiciones de Pago</flux:label>
                                    <flux:textarea wire:model.live="condiciones_pago" rows="2"
                                        placeholder="Condiciones..." class="text-xs" />
                                </div>
                                <div>
                                    <flux:label class="text-xs font-medium">Condiciones de Entrega</flux:label>
                                    <flux:textarea wire:model.live="condiciones_entrega" rows="2"
                                        placeholder="Entrega..." class="text-xs" />
                                </div>
                            </div>
                        </div>
                    </div>

                                        <!-- Tercera Fila - 1 Columna Completa -->
                    <div
                        class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="clipboard-document-list" class="w-4 h-4 text-amber-600" />
                            <flux:heading size="sm">Observaciones Generales</flux:heading>
                        </div>

                        <div>
                            <flux:label class="text-xs font-medium">Observaciones Detalladas</flux:label>
                            <flux:textarea wire:model.live="observaciones_general" rows="3"
                                placeholder="Observaciones adicionales sobre la cotización, requisitos especiales, notas importantes..."
                                class="text-xs" />
                        </div>
                    </div>

                    <!-- Cuarta Fila - Totales -->
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="calculator" class="w-4 h-4 text-emerald-600" />
                            <flux:heading size="sm">Resumen de Totales</flux:heading>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal (sin IGV):</span>
                                <span class="font-medium">S/ {{ number_format($this->calcularSubtotalSinIgv(), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">IGV (18%):</span>
                                <span class="font-medium">S/ {{ number_format($this->calcularIgv(), 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="font-semibold text-gray-800">Total (con IGV):</span>
                                <span class="font-bold text-lg text-emerald-600">S/ {{ number_format($this->calcularTotal(), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Productos -->
                <div class="w-3/5 space-y-6">
                    <!-- Productos Cotizados -->
                    <div
                        class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-xl p-6 border border-orange-200 dark:border-orange-800 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <flux:icon name="cube" class="w-6 h-6 text-orange-600" />
                                <div>
                                    <flux:heading size="md">Productos Cotizados</flux:heading>
                                    <flux:text class="text-sm text-zinc-500">Agregue los productos a cotizar
                                    </flux:text>
                                </div>
                            </div>
                            @if(!$modoVisualizacion)
                                <flux:button type="button" icon="plus" size="sm" wire:click="mostrarProductos"
                                    class="bg-orange-500 hover:bg-orange-600">
                                    Agregar Producto
                                </flux:button>
                            @endif
                        </div>

                        <!-- Lista de Productos Disponibles -->
                        @if ($showProductosList)
                            <div class="mb-6 p-4 bg-white dark:bg-zinc-700 rounded-lg border shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="magnifying-glass" class="w-4 h-4 text-gray-500" />
                                        <flux:heading size="sm">Seleccionar Producto</flux:heading>
                                    </div>
                                    <flux:button type="button" icon="x-mark" size="xs"
                                        wire:click="ocultarProductos" class="btn-error" />
                                </div>

                                <flux:input label="Buscar Producto" placeholder="Código o descripción..."
                                    wire:model.live.debounce.300ms="searchProducto" class="mb-4" />

                                <div class="max-h-64 overflow-y-auto space-y-2">
                                    @foreach ($productos as $producto)
                                        <div class="border rounded-lg p-3 hover:bg-orange-50 cursor-pointer transition-colors"
                                            wire:click="agregarProducto({{ $producto->id }})">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="font-semibold text-sm text-gray-900">
                                                        {{ $producto->code }} - {{ \Illuminate\Support\Str::limit($producto->description, 60, '') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $producto->brand->name ?? '' }} / {{ $producto->category->name ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <div class="font-semibold text-sm text-green-600">S/ {{ number_format($producto->price_venta, 2) }}</div>
                                                    <div class="text-xs text-gray-500">Incluye IGV</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Productos Seleccionados -->
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @if (count($selectedProductos) > 0)
                                @foreach ($selectedProductos as $productoId)
                                    @php $producto = $productos->firstWhere('id', $productoId); @endphp
                                    @if ($producto)
                                        <div class="border rounded-lg p-4 bg-white dark:bg-zinc-700 shadow-sm">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <div class="font-semibold text-sm text-gray-900">
                                                        {{ $producto->code }}</div>
                                                    <div class="text-xs text-gray-600">
                                                        {{ \Illuminate\Support\Str::limit($producto->description, 50, '') }}
                                                    </div>
                                                </div>
                                                @if(!$modoVisualizacion)
                                                    <flux:button icon="trash" class="btn-xs btn-error"
                                                        wire:click="removerProducto({{ $productoId }})" />
                                                @endif
                                            </div>

                                                                                          <div class="grid grid-cols-3 gap-3 mb-3">
                                                  <div>
                                                      <flux:label class="text-xs font-medium">Cantidad</flux:label>
                                                      <flux:input type="number" min="1"
                                                          wire:model.live="cantidades.{{ $productoId }}"
                                                          size="xs" {{ $modoVisualizacion ? 'disabled' : '' }} />
                                                  </div>
                                                  <div>
                                                      <flux:label class="text-xs font-medium">Precio Unit. (Inc. IGV)</flux:label>
                                                      <flux:input type="number" step="0.01" min="0"
                                                          wire:model.live="precios.{{ $productoId }}"
                                                          size="xs" {{ $modoVisualizacion ? 'disabled' : '' }} />
                                                  </div>
                                                  <div>
                                                      <flux:label class="text-xs font-medium">Subtotal</flux:label>
                                                      <flux:input readonly
                                                          value="S/ {{ number_format(($cantidades[$productoId] ?? 0) * ($precios[$productoId] ?? 0), 2) }}"
                                                          size="xs" class="bg-gray-50" />
                                                  </div>
                                              </div>

                                            <flux:textarea label="Observaciones"
                                                wire:model.live="observaciones.{{ $productoId }}" rows="1"
                                                size="xs" placeholder="Observaciones específicas..." {{ $modoVisualizacion ? 'disabled' : '' }} />
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="text-center py-12 text-gray-500">
                                    <flux:icon name="shopping-cart" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                                    <p class="text-sm font-medium">No hay productos agregados</p>
                                    <p class="text-xs text-gray-400 mt-1">Haga clic en "Agregar Producto" para comenzar
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div
                class="flex justify-between items-center mt-8 border-t pt-4 bg-white dark:bg-zinc-900 sticky bottom-0 z-10">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <flux:icon name="information-circle" class="w-4 h-4" />
                    <span>Los campos marcados con * son obligatorios</span>
                </div>
                <div class="flex gap-2">
                    @if($modoVisualizacion)
                        <flux:button wire:click="$set('modal_cotizacion', false)" variant="primary" class="bg-blue-600 hover:bg-blue-700">
                            <flux:icon name="x-mark" class="w-4 h-4 mr-2" />
                            Cerrar
                        </flux:button>
                    @else
                        <flux:button wire:click="$set('modal_cotizacion', false)" class="btn-outline">Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary" class="bg-blue-600 hover:bg-blue-700">
                            <flux:icon name="check" class="w-4 h-4 mr-2" />
                            {{ $editingCotizacion ? 'Actualizar' : 'Crear' }} Cotización
                        </flux:button>
                    @endif
                </div>
            </div>
        </form>
    </flux:modal>
</div>

