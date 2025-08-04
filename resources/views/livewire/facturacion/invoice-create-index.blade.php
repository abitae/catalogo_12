<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-900 dark:to-slate-800">
    <!-- Header con navegación -->
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <flux:icon name="receipt" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Facturación Electrónica</h1>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Sistema GREENTER</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <flux:button icon="arrow-left" variant="outline" size="sm" onclick="history.back()" class="hidden sm:flex">
                        Volver
                    </flux:button>
                    <flux:button icon="check" type="submit" variant="primary" size="sm" form="factura-form" :disabled="count($productos) == 0" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">
                        <span class="hidden sm:inline">Crear Factura</span>
                        <span class="sm:hidden">Crear</span>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <flux:icon name="home" class="w-4 h-4 text-slate-400" />
                    <span class="ml-2 text-sm text-slate-500 dark:text-slate-400">Inicio</span>
                </li>
                <li>
                    <div class="flex items-center">
                        <flux:icon name="chevron-right" class="w-4 h-4 text-slate-400" />
                        <span class="ml-2 text-sm text-slate-500 dark:text-slate-400">Facturación</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <flux:icon name="chevron-right" class="w-4 h-4 text-slate-400" />
                        <span class="ml-2 text-sm font-medium text-slate-900 dark:text-white">Nueva Factura</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Notificaciones mejoradas -->
        @if (session()->has('message'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <flux:icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Se encontraron errores</h3>
                        <ul class="mt-2 text-sm text-red-700 dark:text-red-300 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center space-x-2">
                                    <flux:icon name="x-circle" class="w-4 h-4 flex-shrink-0" />
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Documento principal con diseño mejorado -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form wire:submit.prevent="crearFactura" id="factura-form">
                <!-- Encabezado del Documento con diseño profesional -->
                <div class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white p-8 overflow-hidden">
                    <!-- Patrón de fondo -->
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>

                    <div class="relative flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <flux:icon name="receipt" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold mb-1">
                                        {{ $tipoDoc == '01' ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA' }}
                                    </h1>
                                    <p class="text-blue-100 text-sm font-medium">Sistema de Facturación Electrónica - GREENTER</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <flux:icon name="calendar" class="w-4 h-4 text-blue-200" />
                                    <span class="text-blue-100">Fecha: {{ date('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <flux:icon name="clock" class="w-4 h-4 text-blue-200" />
                                    <span class="text-blue-100">Hora: {{ date('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 rounded-2xl p-6 backdrop-blur-sm border border-white/30">
                                <div class="flex items-center space-x-2 mb-2">
                                    <flux:icon name="building" class="w-4 h-4 text-blue-200" />
                                    <p class="text-xs text-blue-200 font-medium">R.U.C.</p>
                                </div>
                                <p class="font-mono text-2xl font-bold tracking-wider">{{ $ruc ?? '00000000000' }}</p>
                                <div class="mt-3 pt-3 border-t border-white/20">
                                    <p class="text-xs text-blue-200">Serie: {{ $serie ?? 'F001' }}</p>
                                    <p class="text-xs text-blue-200">Correlativo: {{ $correlativo ?? '1' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de la Empresa y Sucursal -->
                <div class="p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="building" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Información del Emisor</flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Seleccione la empresa y sucursal emisora</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Empresa -->
                        <div class="space-y-2">
                            <flux:select label="Empresa Emisora" wire:model.live="company_id" placeholder="Seleccionar empresa" size="xs" class="w-full">
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->razonSocial }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Sucursal -->
                        <div class="space-y-2">
                            <flux:select label="Sucursal" wire:model.live="sucursal_id" placeholder="Seleccionar sucursal" :disabled="!$company_id" size="xs" class="w-full">
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <!-- Información de la empresa seleccionada -->
                    @if ($company_id)
                        <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center space-x-3 mb-4">
                                <flux:icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Empresa Seleccionada</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="building" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Razón Social</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $razonSocial }}</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="tag" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Nombre Comercial</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $nombreComercial }}</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="identification" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">RUC</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-mono font-medium">{{ $ruc }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Datos del Documento -->
                <div class="p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="document-text" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Datos del Documento</flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Configure los datos básicos del documento</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                        <!-- Tipo de Documento -->
                        <div class="space-y-2">
                            <flux:select label="Tipo de Documento" wire:model.live="tipoDoc" size="xs" class="w-full">
                                <option value="01">Factura</option>
                                <option value="03">Boleta</option>
                            </flux:select>
                        </div>

                        <!-- Tipo de Operación -->
                        <div class="space-y-2">
                            <flux:select label="Tipo de Operación" wire:model="tipoOperacion" size="xs" class="w-full">
                                @foreach ($tiposOperacion as $tipo)
                                    <option value="{{ $tipo->codigo }}">{{ $tipo->codigo }} - {{ $tipo->descripcion }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Serie -->
                        <div class="space-y-2">
                            <flux:input label="Serie" wire:model="serie" placeholder="F001" readonly size="xs" class="w-full" />
                        </div>

                        <!-- Correlativo -->
                        <div class="space-y-2">
                            <flux:input label="Correlativo" wire:model="correlativo" placeholder="1" readonly size="xs" class="w-full" />
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="space-y-2">
                            <flux:input label="Fecha de Emisión" type="date" wire:model="fechaEmision" size="xs" class="w-full" />
                        </div>
                    </div>

                    <!-- Forma de Pago -->
                    <div class="mt-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <flux:icon name="credit-card" class="w-5 h-5 text-slate-600 dark:text-slate-400" />
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Forma de Pago</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <flux:select label="Forma de Pago" wire:model="formaPago_tipo" size="xs" class="w-full">
                                    @foreach ($mediosPago as $medio)
                                        <option value="{{ $medio->codigo }}">{{ $medio->codigo }} - {{ $medio->descripcion }}</option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div class="space-y-2">
                                <flux:select label="Moneda" wire:model="formaPago_moneda" size="xs" class="w-full">
                                    <option value="PEN">PEN - Soles</option>
                                    <option value="USD">USD - Dólares</option>
                                </flux:select>
                            </div>
                            <div class="space-y-2">
                                <flux:select label="Tipo de Moneda" wire:model="tipoMoneda" size="xs" class="w-full">
                                    <option value="PEN">PEN - Soles</option>
                                    <option value="USD">USD - Dólares</option>
                                </flux:select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos del Cliente -->
                <div class="p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="user" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Datos del Cliente</flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Busque y seleccione el cliente receptor</p>
                        </div>
                    </div>

                    <!-- Búsqueda de Cliente -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div class="space-y-2">
                            <flux:input label="Buscar por Número de Documento" wire:model.live="numeroDocumentoCliente" placeholder="Ingrese DNI/RUC" size="xs" class="w-full" />
                        </div>
                        <div class="flex items-end">
                            <flux:button icon="search" variant="outline" wire:click="searchClient" :disabled="!$numeroDocumentoCliente" class="w-full">
                                <span class="hidden sm:inline">Buscar Cliente</span>
                                <span class="sm:hidden">Buscar</span>
                            </flux:button>
                        </div>
                        <div class="space-y-2">
                            <flux:select label="Cliente" wire:model="client_id" placeholder="Seleccionar cliente" size="xs" class="w-full">
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->razonSocial }} - {{ $client->numeroDocumento }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <!-- Información del cliente seleccionado -->
                    @if ($client_id)
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-2xl p-6 border border-orange-200 dark:border-orange-800">
                            <div class="flex items-center space-x-3 mb-4">
                                <flux:icon name="check-circle" class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                <h3 class="text-lg font-semibold text-orange-900 dark:text-orange-100">Cliente Seleccionado</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="user" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                        <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Razón Social</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $nameCliente }}</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="identification" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                        <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Tipo Doc</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $typeCodeCliente }}</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="document" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                        <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Número Doc</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-mono font-medium">{{ $numeroDocumentoCliente }}</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="map-pin" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                        <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Dirección</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $addressCliente }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Productos y Servicios -->
                <div class="p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <flux:icon name="shopping-cart" class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <flux:heading size="lg" class="text-slate-900 dark:text-white">Productos y Servicios</flux:heading>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Gestione los productos del documento</p>
                            </div>
                        </div>
                        <flux:button icon="plus" variant="primary" size="sm" wire:click="abrirModalProductos" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700">
                            <span class="hidden sm:inline">Agregar Producto</span>
                            <span class="sm:hidden">Agregar</span>
                        </flux:button>
                    </div>

                    <!-- Tabla de Productos mejorada -->
                    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Código</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Descripción</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Unidad</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Precio Unit.</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Valor Venta</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">IGV</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                    @forelse ($productos as $index => $producto)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <flux:icon name="cube" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                                    </div>
                                                    <span class="text-sm font-medium text-slate-900 dark:text-slate-100 font-mono">{{ $producto['codigo'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-slate-900 dark:text-slate-100 max-w-xs truncate" title="{{ $producto['descripcion'] }}">
                                                    {{ $producto['descripcion'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                    {{ $producto['cantidad'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                                {{ $producto['unidad'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-100 font-medium">
                                                S/ {{ number_format($producto['precio_unitario'], 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-100">
                                                S/ {{ number_format($producto['valor_venta'] / 1.18, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400 font-medium">
                                                S/ {{ number_format($producto['valor_venta'] - ($producto['valor_venta'] / 1.18), 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                    S/ {{ number_format($producto['valor_venta'], 2) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <flux:button icon="edit" variant="outline" size="xs" wire:click="editarProducto({{ $index }})" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300" />
                                                    <flux:button icon="trash" variant="outline" size="xs" wire:click="eliminarProducto({{ $index }})" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" />
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center space-y-4">
                                                    <div class="w-16 h-16 bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center">
                                                        <flux:icon name="shopping-cart" class="w-8 h-8 text-slate-400 dark:text-slate-500" />
                                                    </div>
                                                    <div class="text-center">
                                                        <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100">No hay productos agregados</h3>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400">Comience agregando productos al documento</p>
                                                    </div>
                                                    <flux:button icon="plus" variant="outline" size="sm" wire:click="abrirModalProductos" class="mt-2">
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
                </div>

                <!-- Campos Adicionales GREENTER -->
                <div class="p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="cog" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Campos Adicionales GREENTER</flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Configuraciones avanzadas según estándares SUNAT</p>
                        </div>
                    </div>

                    <!-- Detracción -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <flux:icon name="calculator" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Detracción</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <flux:select label="Bien/Servicio Detracción" wire:model.live="codBienDetraccion" size="xs" class="w-full">
                                    <option value="">Sin detracción</option>
                                    @foreach ($bienesDetraccion as $bien)
                                        <option value="{{ $bien->codigo }}">{{ $bien->codigo }} - {{ $bien->descripcion }}</option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div class="space-y-2">
                                <flux:select label="Medio de Pago Detracción" wire:model="codMedioPago" :disabled="!$codBienDetraccion" size="xs" class="w-full">
                                    @foreach ($mediosPago as $medio)
                                        <option value="{{ $medio->codigo }}">{{ $medio->codigo }} - {{ $medio->descripcion }}</option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div class="space-y-2">
                                <flux:input label="Cuenta Bancaria" wire:model="ctaBanco" placeholder="Número de cuenta" :disabled="!$codBienDetraccion" size="xs" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <flux:input label="Porcentaje Detracción" wire:model="setPercent" type="number" step="0.01" readonly size="xs" class="w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Percepción -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <flux:icon name="percent" class="w-5 h-5 text-green-600 dark:text-green-400" />
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Percepción</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <flux:input label="Base Percepción" wire:model.live="perception_mtoBase" type="number" step="0.01" placeholder="0.00" size="xs" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <flux:input label="Monto Percepción" wire:model.live="perception_mto" type="number" step="0.01" placeholder="0.00" size="xs" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <flux:input label="Total Percepción" wire:model="perception_mtoTotal" type="number" step="0.01" readonly size="xs" class="w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Descuentos y Cargos Globales -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <flux:icon name="adjustments" class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Descuentos y Cargos Globales</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <flux:input label="Base Descuentos" wire:model.live="descuentos_mtoBase" type="number" step="0.01" placeholder="0.00" size="xs" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <flux:input label="Monto Descuentos" wire:model.live="descuentos_mto" type="number" step="0.01" placeholder="0.00" size="xs" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <flux:input label="Base Cargos" wire:model.live="cargos_mtoBase" type="number" step="0.01" placeholder="0.00" size="xs" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <flux:input label="Monto Cargos" wire:model.live="cargos_mto" type="number" step="0.01" placeholder="0.00" size="xs" class="w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Leyendas -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <flux:icon name="document-text" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Leyendas</h3>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($leyendas as $leyenda)
                                    <flux:checkbox
                                        label="{{ $leyenda->codigo }} - {{ $leyenda->descripcion }}"
                                        wire:model="leyendasSeleccionadas"
                                        value="{{ $leyenda->codigo }}"
                                        class="text-sm"
                                    />
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Guías de Remisión -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center space-x-3">
                                <flux:icon name="truck" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Guías de Remisión Relacionadas</h3>
                            </div>
                            <flux:button icon="plus" variant="outline" size="sm" wire:click="agregarGuia" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white border-0">
                                Agregar Guía
                            </flux:button>
                        </div>
                        @foreach ($guias as $index => $guia)
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                <flux:input wire:model="guias.{{ $index }}.serie" placeholder="Serie" size="xs" class="w-full" />
                                <flux:input wire:model="guias.{{ $index }}.correlativo" placeholder="Correlativo" size="xs" class="w-full" />
                                <div class="flex items-end">
                                    <flux:button icon="trash" variant="outline" size="sm" wire:click="eliminarGuia({{ $index }})" class="w-full text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        Eliminar
                                    </flux:button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Documentos Relacionados -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center space-x-3">
                                <flux:icon name="link" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Documentos Relacionados</h3>
                            </div>
                            <flux:button icon="plus" variant="outline" size="sm" wire:click="agregarDocumentoRelacionado" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white border-0">
                                Agregar Documento
                            </flux:button>
                        </div>
                        @foreach ($relDocs as $index => $doc)
                            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                <flux:select wire:model="relDocs.{{ $index }}.tipoDoc" size="xs" class="w-full">
                                    <option value="01">Factura</option>
                                    <option value="03">Boleta</option>
                                    <option value="07">Nota Crédito</option>
                                    <option value="08">Nota Débito</option>
                                </flux:select>
                                <flux:input wire:model="relDocs.{{ $index }}.serie" placeholder="Serie" size="xs" class="w-full" />
                                <flux:input wire:model="relDocs.{{ $index }}.correlativo" placeholder="Correlativo" size="xs" class="w-full" />
                                <flux:input wire:model="relDocs.{{ $index }}.fechaEmision" type="date" size="xs" class="w-full" />
                                <div class="flex items-end">
                                    <flux:button icon="trash" variant="outline" size="sm" wire:click="eliminarDocumentoRelacionado({{ $index }})" class="w-full text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        Eliminar
                                    </flux:button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Totales -->
                <div class="p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="calculator" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Resumen de Totales</flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Desglose completo de montos y cálculos</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 border border-slate-200 dark:border-slate-600">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Desglose de Montos</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                    <span class="text-slate-600 dark:text-slate-400">Subtotal:</span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">S/ {{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                    <span class="text-slate-600 dark:text-slate-400">IGV (18%):</span>
                                    <span class="font-semibold text-green-600 dark:text-green-400">S/ {{ number_format($igv, 2) }}</span>
                                </div>
                                @if($descuentos_mto > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                        <span class="text-slate-600 dark:text-slate-400">Descuentos:</span>
                                        <span class="font-semibold text-red-600 dark:text-red-400">- S/ {{ number_format($descuentos_mto, 2) }}</span>
                                    </div>
                                @endif
                                @if($cargos_mto > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                        <span class="text-slate-600 dark:text-slate-400">Cargos:</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">+ S/ {{ number_format($cargos_mto, 2) }}</span>
                                    </div>
                                @endif
                                @if($setMount > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                        <span class="text-slate-600 dark:text-slate-400">Detracción:</span>
                                        <span class="font-semibold text-red-600 dark:text-red-400">- S/ {{ number_format($setMount, 2) }}</span>
                                    </div>
                                @endif
                                @if($perception_mtoTotal > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-600">
                                        <span class="text-slate-600 dark:text-slate-400">Percepción:</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">+ S/ {{ number_format($perception_mtoTotal, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center py-3 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg px-4">
                                    <span class="text-lg font-bold text-slate-900 dark:text-slate-100">TOTAL:</span>
                                    <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-800">
                            <div class="flex items-center space-x-3 mb-4">
                                <flux:icon name="document-text" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                <h3 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">Monto en Letras</h3>
                            </div>
                            <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-emerald-200 dark:border-emerald-700">
                                <p class="text-sm text-emerald-800 dark:text-emerald-200 font-medium leading-relaxed">
                                    {{ $numeroALetras($total) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="chat-bubble-left-right" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Información Adicional</flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Observaciones y referencias del documento</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:textarea label="Observaciones" wire:model="observacion" placeholder="Observaciones adicionales del documento..." rows="4" size="xs" class="w-full" />
                        </div>
                        <div class="space-y-2">
                            <flux:input label="Referencia" wire:model="note_reference" placeholder="Referencia del documento" size="xs" class="w-full" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Productos Mejorado -->
    <flux:modal wire:model="modal_productos" max-width="5xl">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <flux:icon name="shopping-cart" class="w-4 h-4" />
                </div>
                <flux:heading size="lg" class="text-white">{{ $editando_producto ? 'Editar' : 'Agregar' }} Producto</flux:heading>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-6">
                <!-- Búsqueda de Productos -->
                <div class="bg-gradient-to-r from-slate-50 to-blue-50 dark:from-slate-800 dark:to-blue-900/20 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-3">
                        <flux:icon name="magnifying-glass" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Buscar Producto</h3>
                    </div>
                    <flux:input label="Buscar por código o nombre" wire:model.live="busquedaProducto" placeholder="Escriba para buscar productos..." size="xs" class="w-full" />
                </div>

                <!-- Lista de Productos Filtrados -->
                @if (!empty($productosFiltrados))
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Productos Disponibles</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @foreach ($productosFiltrados as $producto)
                                <div class="p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-purple-900/20 dark:hover:to-pink-900/20 cursor-pointer transition-all duration-200"
                                    wire:click="seleccionarProducto({{ $producto->id }})">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-lg flex items-center justify-center">
                                                <flux:icon name="cube" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $producto->codigo }}</div>
                                                <div class="text-sm text-slate-600 dark:text-slate-400">{{ $producto->nombre }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold text-slate-900 dark:text-slate-100">S/ {{ number_format($producto->precio, 2) }}</div>
                                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $producto->unidad }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Campos del Producto -->
                <div class="bg-gradient-to-r from-slate-50 to-emerald-50 dark:from-slate-800 dark:to-emerald-900/20 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-4">
                        <flux:icon name="adjustments" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Detalles del Producto</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div class="space-y-2">
                            <flux:input label="Cantidad" wire:model.live="cantidad" type="number" step="0.01" min="0.01" size="xs" class="w-full" />
                        </div>
                        <div class="space-y-2">
                            <flux:input label="Precio Unitario" wire:model.live="precio_unitario" type="number" step="0.01" min="0" size="xs" class="w-full" />
                        </div>
                        <div class="space-y-2">
                            <flux:select label="Unidad de Medida" wire:model="unidad" size="xs" class="w-full">
                                @foreach ($unidades as $unidad)
                                    <option value="{{ $unidad->codigo }}">{{ $unidad->codigo }} - {{ $unidad->descripcion }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <flux:textarea label="Descripción" wire:model="descripcion_producto" placeholder="Descripción del producto..." rows="3" size="xs" class="w-full" />
                        </div>

                        <div class="space-y-2">
                            <flux:select label="Tipo de Afectación IGV" wire:model="tipAfeIgv" size="xs" class="w-full">
                                @foreach ($tiposAfectacionIgv as $tipo)
                                    <option value="{{ $tipo->codigo }}">{{ $tipo->codigo }} - {{ $tipo->descripcion }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                </div>

                <!-- Resumen del Producto -->
                @if ($producto_id && $cantidad > 0 && $precio_unitario > 0)
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-6 border border-emerald-200 dark:border-emerald-800">
                        <div class="flex items-center space-x-3 mb-4">
                            <flux:icon name="calculator" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            <h3 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">Resumen del Producto</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-emerald-200 dark:border-emerald-700">
                                <div class="text-sm font-medium text-emerald-700 dark:text-emerald-300 mb-1">Valor Venta</div>
                                <div class="text-lg font-bold text-emerald-900 dark:text-emerald-100">S/ {{ number_format(($cantidad * $precio_unitario) / 1.18, 2) }}</div>
                            </div>
                            <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-emerald-200 dark:border-emerald-700">
                                <div class="text-sm font-medium text-emerald-700 dark:text-emerald-300 mb-1">IGV</div>
                                <div class="text-lg font-bold text-emerald-900 dark:text-emerald-100">S/ {{ number_format(($cantidad * $precio_unitario) - (($cantidad * $precio_unitario) / 1.18), 2) }}</div>
                            </div>
                            <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-emerald-200 dark:border-emerald-700">
                                <div class="text-sm font-medium text-emerald-700 dark:text-emerald-300 mb-1">Total</div>
                                <div class="text-lg font-bold text-emerald-900 dark:text-emerald-100">S/ {{ number_format($cantidad * $precio_unitario, 2) }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 p-6">
            <div class="flex justify-between items-center w-full">
                <flux:button variant="outline" wire:click="cerrarModalProductos" class="border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                    Cancelar
                </flux:button>
                <flux:button variant="primary" wire:click="agregarProducto" :disabled="!$producto_id || $cantidad <= 0 || $precio_unitario <= 0" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white border-0">
                    <flux:icon name="{{ $editando_producto ? 'check' : 'plus' }}" class="w-4 h-4 mr-2" />
                    {{ $editando_producto ? 'Actualizar' : 'Agregar' }} Producto
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
edit
