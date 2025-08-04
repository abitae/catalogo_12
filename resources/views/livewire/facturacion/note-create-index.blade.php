<div class="min-h-screen bg-zinc-100 dark:bg-zinc-900 p-4">
    <!-- Contenedor A4 -->
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado de la página -->
        <div class="mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex justify-between items-center">
                <div>
                    <flux:heading size="lg" class="text-zinc-900 dark:text-white">
                        {{ $tipoDoc === '07' ? 'Crear Nota de Crédito' : 'Crear Nota de Débito' }}
                    </flux:heading>
                    <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                        Complete los datos del documento de {{ $tipoDoc === '07' ? 'nota de crédito' : 'nota de débito' }}
                    </flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:button icon="arrow-left" variant="outline" size="sm">
                        Volver
                    </flux:button>
                    <flux:button icon="check" type="submit" variant="primary" size="sm" form="nota-form"
                        :disabled="count($productos) == 0">
                        Crear {{ $tipoDoc === '07' ? 'Nota de Crédito' : 'Nota de Débito' }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Notificaciones -->
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Documento A4 -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <form wire:submit.prevent="crearNota" id="nota-form">
                <!-- Encabezado del Documento -->
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold mb-2">
                                {{ $tipoDoc === '07' ? 'NOTA DE CRÉDITO ELECTRÓNICA' : 'NOTA DE DÉBITO ELECTRÓNICA' }}
                            </h1>
                            <p class="text-purple-100 text-sm">Sistema de Facturación Electrónica</p>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                                <p class="text-xs text-purple-100">R.U.C.</p>
                                <p class="font-mono text-lg font-bold">{{ $ruc ?? '00000000000' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de la Empresa y Sucursal -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Empresa -->
                        <div>
                            <flux:select label="Empresa Emisora" wire:model.live="company_id"
                                placeholder="Seleccionar empresa" class="mb-3">
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->razonSocial }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Sucursal -->
                        <div>
                            <flux:select label="Sucursal" wire:model.live="sucursal_id"
                                placeholder="Seleccionar sucursal" :disabled="!$company_id">
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <!-- Información de la empresa seleccionada -->
                    @if ($company_id)
                        <div class="mt-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">Razón Social:</span>
                                    <p class="text-zinc-900 dark:text-zinc-100">{{ $razonSocial }}</p>
                                </div>
                                <div>
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">Nombre Comercial:</span>
                                    <p class="text-zinc-900 dark:text-zinc-100">{{ $nombreComercial }}</p>
                                </div>
                                <div>
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">RUC:</span>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-mono">{{ $ruc }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Datos del Documento -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Datos del Documento</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Tipo de Documento -->
                        <flux:select label="Tipo de Documento" wire:model.live="tipoDoc">
                            <option value="07">07 - Nota de Crédito</option>
                            <option value="08">08 - Nota de Débito</option>
                        </flux:select>

                        <!-- Tipo de Operación -->
                        <flux:select label="Tipo de Operación" wire:model="tipoOperacion">
                            <option value="0101">0101 - Venta Interna</option>
                            <option value="0102">0102 - Venta al por menor</option>
                            <option value="0103">0103 - Venta al por mayor</option>
                        </flux:select>

                        <!-- Serie -->
                        <flux:input label="Serie" wire:model="serie" readonly />

                        <!-- Correlativo -->
                        <flux:input label="Correlativo" wire:model="correlativo" readonly />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Fecha de Emisión -->
                        <flux:input type="date" label="Fecha de Emisión" wire:model="fechaEmision" />

                        <!-- Forma de Pago -->
                        <flux:select label="Forma de Pago" wire:model="formaPago_tipo">
                            <option value="01">01 - Efectivo</option>
                            <option value="02">02 - Tarjeta</option>
                            <option value="03">03 - Transferencia</option>
                        </flux:select>
                    </div>
                </div>

                <!-- Documento que Modifica -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Documento que Modifica</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Tipo de Documento -->
                        <flux:select label="Tipo de Documento" wire:model="tipoDocModifica">
                            <option value="01">01 - Factura</option>
                            <option value="03">03 - Boleta</option>
                        </flux:select>

                        <!-- Serie -->
                        <flux:input label="Serie" wire:model="serieModifica" placeholder="Ej: F001" />

                        <!-- Correlativo -->
                        <flux:input label="Correlativo" wire:model="correlativoModifica" placeholder="Ej: 1" />

                        <!-- Fecha de Emisión -->
                        <flux:input type="date" label="Fecha de Emisión" wire:model="fechaEmisionModifica" />
                    </div>
                </div>

                <!-- Motivo de la Nota -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Motivo de la Nota</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Código de Motivo -->
                        <flux:select label="Código de Motivo" wire:model="codMotivo">
                            @if ($tipoDoc === '07')
                                <!-- Catálogo 09 - Motivos de Nota de Crédito -->
                                <option value="01">01 - Anulación de la operación</option>
                                <option value="02">02 - Anulación por error en descuento</option>
                                <option value="03">03 - Corrección por error en la cantidad</option>
                                <option value="04">04 - Descuento global</option>
                                <option value="05">05 - Descuento por ítem</option>
                                <option value="06">06 - Descuento por ítem por anticipo</option>
                                <option value="07">07 - Por anticipo</option>
                                <option value="08">08 - Por diferencias de cambio</option>
                                <option value="09">09 - Por devolución parcial</option>
                                <option value="10">10 - Por devolución por anulación de la operación</option>
                                <option value="11">11 - Otros conceptos</option>
                                <option value="12">12 - Ajustes de operaciones de exportación</option>
                                <option value="13">13 - Ajustes afectos al IVAP</option>
                            @else
                                <!-- Catálogo 10 - Motivos de Nota de Débito -->
                                <option value="01">01 - Intereses por mora</option>
                                <option value="02">02 - Aumento en el valor</option>
                                <option value="03">03 - Penalidades/ otros conceptos</option>
                                <option value="10">10 - Ajustes de operaciones de exportación</option>
                                <option value="11">11 - Ajustes afectos al IVAP</option>
                            @endif
                        </flux:select>

                        <!-- Descripción del Motivo -->
                        <flux:textarea label="Descripción del Motivo" wire:model="desMotivo" rows="3" />
                    </div>
                </div>

                <!-- Datos del Cliente -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Datos del Cliente</flux:heading>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Columna 1: Búsqueda por Número de Documento y datos principales -->
                        <div class="flex flex-col gap-4">
                            <flux:input.group>
                                <flux:select class="min-w-[90px]" wire:model="typeCodeCliente">
                                    <flux:select.option value="DNI" selected>DNI</flux:select.option>
                                    <flux:select.option value="RUC">RUC</flux:select.option>
                                    <flux:select.option value="CE">CE</flux:select.option>
                                    <flux:select.option value="PASAPORTE">PASAPORTE</flux:select.option>
                                </flux:select>
                                <flux:input class="w-full" wire:model="numeroDocumentoCliente" placeholder="Documento" />
                                <flux:button wire:click="searchClient" icon="magnifying-glass" class="">Buscar</flux:button>
                            </flux:input.group>

                            <flux:input placeholder="Dirección" wire:model.live="addressCliente" />
                            <flux:input placeholder="Ubigeo" wire:model.live="textoUbigeoCliente" />
                        </div>

                        <!-- Columna 2: Contacto -->
                        <div class="flex flex-col gap-4">
                            <flux:input placeholder="Razón Social o Nombre Remitente" wire:model.live="nameCliente" />
                            <flux:input placeholder="Teléfono" wire:model.live="phoneCliente" />
                            <flux:input placeholder="Email" wire:model.live="emailCliente" />
                        </div>
                    </div>

                    <!-- Información del cliente seleccionado -->
                    @if ($client_id)
                        @php
                            $clienteSeleccionado = $clients->firstWhere('id', $client_id);
                        @endphp
                        @if ($clienteSeleccionado)
                            <div class="mt-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                                <flux:heading size="sm" class="mb-2">Información del Cliente</flux:heading>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">Razón Social:</span>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $clienteSeleccionado->rznSocial }}</p>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">Número de Documento:</span>
                                        <p class="text-zinc-900 dark:text-zinc-100 font-mono">{{ $clienteSeleccionado->numDoc }}</p>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">Tipo de Documento:</span>
                                        <p class="text-zinc-900 dark:text-zinc-100">{{ $clienteSeleccionado->tipoDoc }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Productos -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex justify-between items-center mb-4">
                        <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">Productos y Servicios</flux:heading>
                        <flux:button icon="plus" wire:click="abrirModalProductos" variant="primary" size="sm">
                            Agregar Producto
                        </flux:button>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Código</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Descripción</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Cant.</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Unidad</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        P.Unit.</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Total</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($productos as $index => $producto)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                        <td class="px-2 py-2 text-[10px] text-zinc-900 dark:text-zinc-100 font-mono">
                                            {{ $producto['codigo'] }}</td>
                                        <td class="px-2 py-2 text-[10px] text-zinc-900 dark:text-zinc-100">
                                            {{ $producto['descripcion'] }}</td>
                                        <td class="px-2 py-2 text-[10px] text-zinc-900 dark:text-zinc-100 text-right">
                                            {{ number_format($producto['cantidad'], 2) }}</td>
                                        <td class="px-2 py-2 text-[10px] text-zinc-900 dark:text-zinc-100 text-center">
                                            {{ $producto['unidad'] }}</td>
                                        <td class="px-2 py-2 text-[10px] text-zinc-900 dark:text-zinc-100 text-right">
                                            {{ number_format($producto['precio_unitario'], 2) }}</td>
                                        <td class="px-2 py-2 text-[10px] text-right">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-green-600 dark:text-green-400">{{ number_format($producto['total'], 2) }}</span>
                                                <span class="text-[8px] text-zinc-500 dark:text-zinc-400">Total</span>
                                            </div>
                                        </td>
                                        <td class="px-2 py-2 text-[10px]">
                                            <div class="flex gap-1">
                                                <flux:button icon="pencil" wire:click="editarProducto({{ $index }})" variant="outline" size="xs" title="Editar" />
                                                <flux:button icon="trash" wire:click="eliminarProducto({{ $index }})" variant="danger" size="xs" title="Eliminar" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-2 py-4 text-center text-[10px] text-zinc-500 dark:text-zinc-400">
                                            <div class="flex flex-col items-center gap-1">
                                                <flux:icon name="shopping-cart" class="w-6 h-6 text-zinc-300" />
                                                <span>No hay productos agregados</span>
                                                <flux:button icon="plus" wire:click="abrirModalProductos" variant="outline" size="xs">
                                                    Agregar primer producto
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totales -->
                @if (count($productos) > 0)
                    <div class="p-6 bg-zinc-50 dark:bg-zinc-700">
                        <div class="flex justify-end">
                            <div class="w-80 space-y-3">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-zinc-600 dark:text-zinc-400">Subtotal:</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">S/ {{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-zinc-600 dark:text-zinc-400">IGV (18%):</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">S/ {{ number_format($igv, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-zinc-300 dark:border-zinc-600">
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">Total:</flux:heading>
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">S/ {{ number_format($total, 2) }}</flux:heading>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Observaciones -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:textarea label="Observaciones" wire:model="observacion" placeholder="Observaciones adicionales..." rows="3" />
                        <flux:input label="Referencia" wire:model="note_reference" placeholder="Referencia de la nota..." />
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Agregar/Editar Producto -->
    <flux:modal wire:model="modal_productos" class="w-full max-w-3xl">
        <div class="p-6">
            <flux:heading size="lg" class="mb-6">
                {{ $editando_producto ? 'Editar Producto' : 'Agregar Producto' }}
            </flux:heading>

            <form wire:submit.prevent="agregarProducto" class="space-y-6">
                <!-- Búsqueda y Selección de Producto -->
                <div class="space-y-4">
                    <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Buscar y Seleccionar Producto</flux:heading>

                    <!-- Campo de Búsqueda -->
                    <flux:input wire:model.live="busquedaProducto" placeholder="Buscar por código o nombre del producto..." icon="magnifying-glass" />

                    <!-- Lista de Productos Filtrados -->
                    @if ($busquedaProducto && count($productosFiltrados) > 0)
                        <div class="max-h-48 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            @foreach ($productosFiltrados as $producto)
                                <div wire:click="seleccionarProducto({{ $producto->id }})"
                                    class="p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer border-b border-zinc-100 dark:border-zinc-600 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $producto->code }}</div>
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $producto->description }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-green-600 dark:text-green-400">S/ {{ number_format($producto->price_venta, 2) }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Stock: {{ $producto->stock }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($busquedaProducto && count($productosFiltrados) == 0)
                        <div class="text-center py-4 text-zinc-500 dark:text-zinc-400">
                            <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                            <p>No se encontraron productos</p>
                        </div>
                    @endif

                    <!-- Producto Seleccionado -->
                    @if ($producto_id)
                        @php
                            $productoSeleccionado = collect($productos)->firstWhere('id', $producto_id);
                        @endphp
                        @if ($productoSeleccionado)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-3 mb-3">
                                    <flux:icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    <flux:heading size="sm" class="text-blue-800 dark:text-blue-200">Producto Seleccionado</flux:heading>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-blue-700 dark:text-blue-300">Código:</span>
                                        <p class="text-blue-900 dark:text-blue-100 font-mono">{{ $productoSeleccionado->code }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-blue-700 dark:text-blue-300">Descripción:</span>
                                        <p class="text-blue-900 dark:text-blue-100">{{ $productoSeleccionado->description }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-blue-700 dark:text-blue-300">Precio:</span>
                                        <p class="text-blue-900 dark:text-blue-100 font-mono">S/ {{ number_format($productoSeleccionado->price_venta, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Detalles del Producto -->
                <div class="space-y-4">
                    <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Detalles de la Venta</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Cantidad -->
                        <flux:input type="number" label="Cantidad" wire:model.live="cantidad" step="0.01" min="0.01" placeholder="1.00" />

                        <!-- Unidad -->
                        <flux:select label="Unidad" wire:model="unidad">
                            @foreach ($unidades as $codigo => $descripcion)
                                <option value="{{ $codigo }}">{{ $descripcion }}</option>
                            @endforeach
                        </flux:select>

                        <!-- Precio Unitario -->
                        <flux:input type="number" label="Precio Unitario" wire:model.live="precio_unitario" step="0.01" min="0" placeholder="0.00" />
                    </div>

                    <!-- Descripción Editable -->
                    <flux:textarea label="Descripción del Producto" wire:model="descripcion_producto" placeholder="Descripción del producto (opcional)" rows="2" />

                    <!-- Resumen de Cálculos -->
                    @if ($cantidad && $precio_unitario)
                        @php
                            $valor_venta = $cantidad * $precio_unitario;
                            // Los precios ya incluyen IGV, calculamos el valor sin IGV
                            $valor_sin_igv = $valor_venta / 1.18;
                            $igv_producto = $valor_venta - $valor_sin_igv;
                            $total_producto = $valor_venta; // Ya incluye IGV
                        @endphp

                        <!-- Resumen Completo de Cálculos -->
                        <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                            <flux:heading size="sm" class="mb-3">Resumen de Cálculos (Precios incluyen IGV)</flux:heading>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400">Valor Venta:</span>
                                    <p class="font-mono">S/ {{ number_format($valor_venta, 2) }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400">IGV (18%):</span>
                                    <p class="font-mono">S/ {{ number_format($igv_producto, 2) }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400">Total:</span>
                                    <p class="font-mono font-bold text-green-600">S/ {{ number_format($total_producto, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Botones del modal -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <flux:button wire:click="cerrarModalProductos" variant="outline">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary" icon="{{ $editando_producto ? 'check' : 'plus' }}">
                        {{ $editando_producto ? 'Actualizar Producto' : 'Agregar Producto' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
