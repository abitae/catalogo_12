<div class="min-h-screen bg-zinc-100 dark:bg-zinc-900 p-4">
    <!-- Contenedor A4 -->
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado de la página -->
        <div class="mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex justify-between items-center">
                <div>
                    <flux:heading size="lg" class="text-zinc-900 dark:text-white">
                        Crear Guía de Remisión
                    </flux:heading>
                    <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                        Complete los datos del documento de traslado de mercancías
                    </flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:button icon="arrow-left" variant="outline" size="sm">
                        Volver
                    </flux:button>
                    <flux:button icon="check" type="submit" variant="primary" size="sm" form="despatch-form"
                        :disabled="count($productos) == 0">
                        Crear Guía de Remisión
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
            <form wire:submit.prevent="crearGuiaRemision" id="despatch-form">
                <!-- Encabezado del Documento -->
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold mb-2">
                                GUÍA DE REMISIÓN REMITENTE
                            </h1>
                            <p class="text-green-100 text-sm">Sistema de Facturación Electrónica</p>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                                <p class="text-xs text-green-100">R.U.C.</p>
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
                        <flux:input label="Tipo de Documento" value="09 - Guía de Remisión Remitente" readonly />

                        <!-- Serie -->
                        <flux:input label="Serie" wire:model="serie" readonly />

                        <!-- Correlativo -->
                        <flux:input label="Correlativo" wire:model="correlativo" readonly />

                        <!-- Fecha de Emisión -->
                        <flux:input type="date" label="Fecha de Emisión" wire:model="fechaEmision" />
                    </div>
                </div>

                <!-- Destinatario -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Datos del Destinatario</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Tipo de Documento -->
                        <flux:select label="Tipo de Documento" wire:model="tipoDocDestinatario">
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">CE</option>
                            <option value="PAS">PAS</option>
                        </flux:select>

                        <!-- Número de Documento -->
                        <flux:input label="Número de Documento" wire:model="numDocDestinatario" placeholder="Ingrese el número" />

                        <!-- Razón Social -->
                        <flux:input label="Razón Social" wire:model="rznSocialDestinatario" placeholder="Razón social del destinatario" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Dirección -->
                        <flux:input label="Dirección" wire:model="direccionDestinatario" placeholder="Dirección del destinatario" />

                        <!-- Ubigeo -->
                        <flux:input label="Ubigeo" wire:model="ubigeoDestinatario" placeholder="Código de ubigeo" />
                    </div>
                </div>

                <!-- Transportista -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Datos del Transportista (Opcional)</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Tipo de Documento -->
                        <flux:select label="Tipo de Documento" wire:model="tipoDocTransportista">
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">CE</option>
                            <option value="PAS">PAS</option>
                        </flux:select>

                        <!-- Número de Documento -->
                        <flux:input label="Número de Documento" wire:model="numDocTransportista" placeholder="Número de documento" />

                        <!-- Razón Social -->
                        <flux:input label="Razón Social" wire:model="rznSocialTransportista" placeholder="Razón social del transportista" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Placa del Vehículo -->
                        <flux:input label="Placa del Vehículo" wire:model="placaVehiculo" placeholder="Ej: ABC-123" />

                        <!-- Código Establecimiento Destino -->
                        <flux:input label="Código Establecimiento Destino" wire:model="codEstabDestino" placeholder="Código del establecimiento" />
                    </div>
                </div>

                <!-- Direcciones -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Direcciones de Traslado</flux:heading>

                    <!-- Dirección de Partida -->
                    <div class="mb-6">
                        <flux:heading size="sm" class="mb-3 text-zinc-700 dark:text-zinc-300">Dirección de Partida</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:input label="Dirección" wire:model="direccionPartida" placeholder="Dirección de partida" />
                            <flux:input label="Ubigeo" wire:model="ubigeoPartida" placeholder="Código de ubigeo" />
                        </div>
                    </div>

                    <!-- Dirección de Llegada -->
                    <div>
                        <flux:heading size="sm" class="mb-3 text-zinc-700 dark:text-zinc-300">Dirección de Llegada</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:input label="Dirección" wire:model="direccionLlegada" placeholder="Dirección de llegada" />
                            <flux:input label="Ubigeo" wire:model="ubigeoLlegada" placeholder="Código de ubigeo" />
                        </div>
                    </div>
                </div>

                <!-- Fechas y Motivo de Traslado -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Fechas y Motivo de Traslado</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Fecha Inicio Traslado -->
                        <flux:input type="date" label="Fecha Inicio Traslado" wire:model="fechaInicioTraslado" />

                        <!-- Fecha Fin Traslado -->
                        <flux:input type="date" label="Fecha Fin Traslado" wire:model="fechaFinTraslado" />

                        <!-- Modalidad de Traslado -->
                        <flux:select label="Modalidad de Traslado" wire:model="modalidadTraslado">
                            <option value="01">01 - Público</option>
                            <option value="02">02 - Privado</option>
                        </flux:select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Motivo de Traslado -->
                        <flux:select label="Motivo de Traslado" wire:model.live="codMotivoTraslado">
                            <option value="01">01 - Venta</option>
                            <option value="02">02 - Compra</option>
                            <option value="03">03 - Consignación</option>
                            <option value="04">04 - Traslado entre establecimientos</option>
                            <option value="05">05 - Exportación</option>
                            <option value="06">06 - Importación</option>
                            <option value="07">07 - Otros</option>
                        </flux:select>

                        <!-- Descripción del Motivo -->
                        <flux:textarea label="Descripción del Motivo" wire:model="desMotivoTraslado" rows="3" readonly />
                    </div>
                </div>

                <!-- Indicadores y Totales -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="md" class="mb-4 text-zinc-900 dark:text-zinc-100">Indicadores y Totales</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Indicador de Transbordo -->
                        <div class="flex items-center">
                            <flux:checkbox label="Indicador de Transbordo" wire:model="indicadorTransbordo" />
                        </div>

                        <!-- Peso Bruto Total -->
                        <flux:input type="number" label="Peso Bruto Total" wire:model="pesoBrutoTotal" step="0.01" min="0" placeholder="0.00" />

                        <!-- Número de Bultos -->
                        <flux:input type="number" label="Número de Bultos" wire:model="numeroBultos" min="0" placeholder="0" />
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
                        <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">Productos a Trasladar</flux:heading>
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
                                        Peso Bruto</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Peso Neto</th>
                                    <th class="px-2 py-2 text-left text-[10px] font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Lote</th>
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
                                            {{ $producto['pesoBruto'] ? number_format($producto['pesoBruto'], 2) : '-' }}</td>
                                        <td class="px-2 py-2 text-[10px] text-zinc-900 dark:text-zinc-100 text-right">
                                            {{ $producto['pesoNeto'] ? number_format($producto['pesoNeto'], 2) : '-' }}</td>
                                        <td class="px-2 py-2 text-[10px] text-zinc-900 dark:text-zinc-100 text-center">
                                            {{ $producto['codLote'] ?: '-' }}</td>
                                        <td class="px-2 py-2 text-[10px]">
                                            <div class="flex gap-1">
                                                <flux:button icon="pencil" wire:click="editarProducto({{ $index }})" variant="outline" size="xs" title="Editar" />
                                                <flux:button icon="trash" wire:click="eliminarProducto({{ $index }})" variant="danger" size="xs" title="Eliminar" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-2 py-4 text-center text-[10px] text-zinc-500 dark:text-zinc-400">
                                            <div class="flex flex-col items-center gap-1">
                                                <flux:icon name="cube" class="w-6 h-6 text-zinc-300" />
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

                <!-- Documentos Relacionados -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex justify-between items-center mb-4">
                        <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">Documentos Relacionados</flux:heading>
                        <flux:button type="button" wire:click="agregarDocumentoRelacionado" variant="outline" icon="plus" size="sm">
                            Agregar Documento
                        </flux:button>
                    </div>

                    @forelse($documentosRelacionados as $index => $documento)
                        <div class="flex gap-4 items-end mb-4 p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <flux:select label="Tipo de Documento" wire:model="documentosRelacionados.{{ $index }}.tipoDoc">
                                <option value="01">01 - Factura</option>
                                <option value="03">03 - Boleta</option>
                                <option value="07">07 - Nota de Crédito</option>
                                <option value="08">08 - Nota de Débito</option>
                            </flux:select>

                            <flux:input label="Serie" wire:model="documentosRelacionados.{{ $index }}.serie" placeholder="Ej: F001" />
                            <flux:input label="Correlativo" wire:model="documentosRelacionados.{{ $index }}.correlativo" placeholder="Ej: 1" />

                            <flux:button type="button" wire:click="eliminarDocumentoRelacionado({{ $index }})" variant="outline" icon="trash" />
                        </div>
                    @empty
                        <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            <flux:icon name="document-text" class="w-12 h-12 mx-auto mb-2 text-zinc-300" />
                            <p class="text-sm">No hay documentos relacionados</p>
                            <flux:button type="button" wire:click="agregarDocumentoRelacionado" variant="outline" size="sm" class="mt-2">
                                Agregar primer documento
                            </flux:button>
                        </div>
                    @endforelse
                </div>

                <!-- Observaciones -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:textarea label="Observaciones" wire:model="observacion" placeholder="Observaciones adicionales sobre el traslado..." rows="3" />
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
                    <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300">Detalles del Producto</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Cantidad -->
                        <flux:input type="number" label="Cantidad" wire:model.live="cantidad" step="0.01" min="0.01" placeholder="1.00" />

                        <!-- Unidad -->
                        <flux:select label="Unidad" wire:model="unidad">
                            @foreach ($unidades as $codigo => $descripcion)
                                <option value="{{ $codigo }}">{{ $descripcion }}</option>
                            @endforeach
                        </flux:select>

                        <!-- Descripción Editable -->
                        <flux:input label="Descripción" wire:model="descripcion_producto" placeholder="Descripción del producto" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Peso Bruto -->
                        <flux:input type="number" label="Peso Bruto (kg)" wire:model="pesoBruto" step="0.01" min="0" placeholder="0.00" />

                        <!-- Peso Neto -->
                        <flux:input type="number" label="Peso Neto (kg)" wire:model="pesoNeto" step="0.01" min="0" placeholder="0.00" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Código de Lote -->
                        <flux:input label="Código de Lote" wire:model="codLote" placeholder="Código del lote (opcional)" />

                        <!-- Fecha de Vencimiento -->
                        <flux:input type="date" label="Fecha de Vencimiento" wire:model="fechaVencimiento" />
                    </div>
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
