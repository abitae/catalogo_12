<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-900 dark:to-slate-800">
    <!-- Header con navegación -->
    <div
        class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <flux:icon name="document-text" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Facturación Electrónica</h1>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Sistema GREENTER</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <flux:button icon="arrow-left" variant="outline" size="sm" onclick="history.back()"
                        class="hidden sm:flex">
                        Volver
                    </flux:button>
                    <flux:button icon="check" type="submit" variant="primary" size="sm" form="factura-form"
                        :disabled="count($productos) == 0"
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">
                        <span class="hidden sm:inline">Crear Factura</span>
                        <span class="sm:hidden">Crear</span>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6 py-6">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
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
            <div
                class="mb-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl p-3 shadow-sm">
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
            <div
                class="mb-3 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 shadow-sm">
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
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form wire:submit.prevent="crearFactura" id="factura-form">
                <!-- Encabezado del Documento con diseño profesional -->
                <div
                    class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white p-6 overflow-hidden">
                    <!-- Patrón de fondo -->
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12">
                    </div>

                    <div class="relative flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-4">
                                <div
                                    class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <flux:icon name="document-text" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold mb-1">
                                        {{ $tipoDoc == '01' ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA' }}
                                    </h1>
                                    <p class="text-blue-100 text-sm font-medium">Sistema de Facturación Electrónica</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <flux:icon name="calendar" class="w-4 h-4 text-blue-200" />
                                    <span class="text-blue-100">Fecha: {{ date('d/m/Y') }}</span>
                                </div>

                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 rounded-2xl p-6 backdrop-blur-sm border border-white/30">
                                <div class="flex items-center space-x-2 mb-2">
                                    <flux:icon name="building-office" class="w-4 h-4 text-blue-200" />
                                    <p class="text-xs text-blue-200 font-medium">R.U.C.</p>
                                </div>
                                <p class="font-mono text-2xl font-bold tracking-wider">{{ $ruc ?? '00000000000' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de la Empresa y Sucursal -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="building-office" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Información del Emisor
                            </flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Seleccione la empresa y sucursal
                                emisora</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Empresa -->
                        <div class="space-y-2">
                            <flux:select label="Empresa Emisora" wire:model.live="company_id"
                                placeholder="Seleccionar empresa" size="xs" class="w-full">
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->razonSocial }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Sucursal -->
                        <div class="space-y-2">
                            <flux:select label="Sucursal" wire:model.live="sucursal_id"
                                placeholder="Seleccionar sucursal" :disabled="!$company_id" size="xs"
                                class="w-full">
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <!-- Información de la empresa seleccionada -->
                    @if ($company_id)
                        <div
                            class="mt-3 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-3 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center space-x-3 mb-2">
                                <flux:icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Empresa Seleccionada
                                </h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="building-office"
                                            class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Razón
                                            Social</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $razonSocial }}</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="tag" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Nombre
                                            Comercial</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-medium">{{ $nombreComercial }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <flux:icon name="identification"
                                            class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">RUC</span>
                                    </div>
                                    <p class="text-slate-900 dark:text-slate-100 font-mono font-medium">
                                        {{ $ruc }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Datos del Documento -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="document-text" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Datos del Documento
                            </flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Configure los datos básicos del
                                documento</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                        <!-- Tipo de Documento -->
                        <div class="space-y-2">
                            <flux:select label="Tipo de Documento" wire:model.live="tipoDoc" size="xs"
                                class="w-full">
                                <option value="01">01 - Factura</option>
                                <option value="03">03 - Boleta</option>
                            </flux:select>
                        </div>

                        <!-- Tipo de Operación -->
                        <div class="space-y-2">
                            <flux:select label="Tipo de Operación" wire:model.live="tipoOperacion" size="xs"
                                class="w-full">
                                @foreach ($tiposOperacion as $tipo)
                                    <option value="{{ $tipo->codigo }}">{{ $tipo->codigo }} -
                                        {{ $tipo->descripcion }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <!-- Tipo de Operación -->
                        @if (in_array($tipoOperacion, ['1001', '1002', '1003', '1004']))
                            <div class="space-y-2">
                                <flux:select label="Tipo de Detracción" wire:model="codBienDetraccion" size="xs"
                                    class="w-full">
                                    @foreach ($bienesDetraccion as $tipo)
                                        <option value="{{ $tipo->codigo }}">{{ $tipo->codigo }} -
                                            {{ $tipo->descripcion }}</option>
                                    @endforeach
                                </flux:select>
                            </div>
                        @endif

                        @if ($tipoOperacion == '2001')
                            <div class="space-y-2">
                                <flux:select label="Régimen de Percepción" wire:model.live="codReg" size="xs"
                                    class="w-full">
                                    <option value="">Seleccionar régimen</option>
                                    <option value="01">01 - Régimen General</option>
                                    <option value="02">02 - Régimen Especial</option>
                                    <option value="03">03 - Régimen MYPE</option>
                                </flux:select>
                            </div>
                        @endif

                        @if (in_array($tipoOperacion, ['2002', '2003', '2004']))
                            <div class="space-y-2">
                                <flux:select label="Régimen de Retención" wire:model.live="codRegRet" size="xs"
                                    class="w-full">
                                    <option value="">Seleccionar régimen</option>
                                    <option value="01">01 - Retención IGV</option>
                                    <option value="02">02 - Retención Renta</option>
                                    <option value="03">03 - Retención IGV + Renta</option>
                                </flux:select>
                            </div>
                        @endif

                        <!-- Fecha de Emisión -->
                        <div class="space-y-2">
                            <flux:input label="Fecha de Emisión" type="date" wire:model="fechaEmision"
                                size="xs" class="w-full" />
                        </div>

                        <!-- Fecha de Vencimiento -->
                        <div class="space-y-2">
                            <flux:input label="Fecha de Vencimiento" type="date" wire:model="fechaVencimiento"
                                size="xs" class="w-full" />
                        </div>


                    </div>

                    <!-- Forma de Pago -->
                    <div class="mt-4">
                        <div class="flex items-center space-x-3 mb-2">
                            <flux:icon name="credit-card" class="w-5 h-5 text-slate-600 dark:text-slate-400" />
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Forma de Pago</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <flux:select label="Forma de Pago" wire:model="formaPago_tipo" size="xs"
                                    class="w-full">
                                    @foreach ($mediosPago as $medio)
                                        <option value="{{ $medio->codigo }}">{{ $medio->codigo }} -
                                            {{ $medio->descripcion }}</option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div class="space-y-2">
                                <flux:select label="Moneda" wire:model="formaPago_moneda" size="xs"
                                    class="w-full">
                                    <option value="PEN">PEN - Soles</option>
                                    <option value="USD">USD - Dólares</option>
                                </flux:select>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Datos del Cliente -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                            <flux:icon name="user" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-slate-900 dark:text-white">Datos del Cliente
                            </flux:heading>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Busque y seleccione el cliente
                                receptor</p>
                        </div>
                    </div>

                    <!-- Búsqueda de Cliente -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                        <div class="space-y-2">
                            <flux:input.group>
                                <flux:select wire:model="typeCodeCliente" size="xs" class="max-w-24">
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                </flux:select>
                                <flux:input wire:model.live="numeroDocumentoCliente" placeholder="Ingrese DNI/RUC"
                                    size="xs" class="w-full" />
                                <flux:button size="xs" icon="magnifying-glass" variant="outline"
                                    wire:click="searchClient"
                                    :disabled="!$numeroDocumentoCliente || !$typeCodeCliente">
                                </flux:button>
                            </flux:input.group>
                        </div>
                        <div class="space-y-2">
                            <flux:input wire:model.live="nameCliente" placeholder="Ingrese Nombre" size="xs"
                                class="w-full" />
                        </div>
                        <div class="space-y-2">
                            <flux:input wire:model.live="addressCliente" placeholder="Ingrese Dirección"
                                size="xs" class="w-full" />
                        </div>
                        <div class="space-y-2">
                            <flux:input wire:model.live="textoUbigeoCliente" placeholder="Ingrese Ubigeo"
                                size="xs" class="w-full" />
                        </div>
                    </div>

                    <!-- Información del cliente seleccionado -->
                    @if ($client_id)
                        <div
                            class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-3 border border-orange-200 dark:border-orange-800">
                            <div class="flex items-center space-x-2 mb-2">
                                <flux:icon name="check-circle" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                <h3 class="text-base font-semibold text-orange-900 dark:text-orange-100">Cliente
                                    Seleccionado</h3>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <div class="flex items-center space-x-1">
                                        <flux:icon name="user"
                                            class="w-3 h-3 text-orange-600 dark:text-orange-400" />
                                        <span class="text-xs font-medium text-orange-700 dark:text-orange-300">Razón
                                            Social</span>
                                    </div>
                                    <p class="text-xs text-slate-900 dark:text-slate-100 font-medium truncate">
                                        {{ $nameCliente }}</p>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-1">
                                        <flux:icon name="identification"
                                            class="w-3 h-3 text-orange-600 dark:text-orange-400" />
                                        <span class="text-xs font-medium text-orange-700 dark:text-orange-300">Tipo
                                            Doc</span>
                                    </div>
                                    <p class="text-xs text-slate-900 dark:text-slate-100 font-medium">
                                        {{ $typeCodeCliente }}</p>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-1">
                                        <flux:icon name="document"
                                            class="w-3 h-3 text-orange-600 dark:text-orange-400" />
                                        <span class="text-xs font-medium text-orange-700 dark:text-orange-300">N°
                                            Doc</span>
                                    </div>
                                    <p class="text-xs text-slate-900 dark:text-slate-100 font-mono font-medium">
                                        {{ $numeroDocumentoCliente }}</p>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-1">
                                        <flux:icon name="map-pin"
                                            class="w-3 h-3 text-orange-600 dark:text-orange-400" />
                                        <span
                                            class="text-xs font-medium text-orange-700 dark:text-orange-300">Dirección</span>
                                    </div>
                                    <p class="text-xs text-slate-900 dark:text-slate-100 font-medium truncate">
                                        {{ $addressCliente }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Productos y Servicios -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <flux:icon name="shopping-cart" class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <flux:heading size="lg" class="text-slate-900 dark:text-white">Productos y
                                    Servicios</flux:heading>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Gestione los productos del
                                    documento</p>
                            </div>
                        </div>
                        <flux:button icon="plus" variant="primary" size="sm"
                            wire:click="abrirModalProductos"
                            class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700">
                            <span class="hidden sm:inline">Agregar Producto</span>
                            <span class="sm:hidden">Agregar</span>
                        </flux:button>
                    </div>

                    <!-- Tabla de Productos mejorada -->
                    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead
                                    class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Código</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Descripción</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Cantidad</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Unidad</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Precio Unit.</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Valor Venta</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            IGV</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Total</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                    @forelse ($productos as $index => $producto)
                                        <tr
                                            class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <flux:icon name="cube"
                                                            class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium text-slate-900 dark:text-slate-100 font-mono">{{ $producto['codigo'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-slate-900 dark:text-slate-100 max-w-xs truncate"
                                                    title="{{ $producto['descripcion'] }}">
                                                    {{ $producto['descripcion'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                    {{ $producto['cantidad'] }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                                {{ $producto['unidad'] }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-100 font-medium">
                                                S/ {{ number_format($producto['precio_unitario'], 2) }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-100">
                                                S/ {{ number_format($producto['valor_venta'] / 1.18, 2) }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400 font-medium">
                                                S/
                                                {{ number_format($producto['valor_venta'] - $producto['valor_venta'] / 1.18, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                    S/ {{ number_format($producto['valor_venta'], 2) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <flux:button icon="pencil" variant="outline" size="xs"
                                                        wire:click="editarProducto({{ $index }})"
                                                        class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300" />
                                                    <flux:button icon="trash" variant="outline" size="xs"
                                                        wire:click="eliminarProducto({{ $index }})"
                                                        class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" />
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center space-y-4">
                                                    <div
                                                        class="w-16 h-16 bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center">
                                                        <flux:icon name="shopping-cart"
                                                            class="w-8 h-8 text-slate-400 dark:text-slate-500" />
                                                    </div>
                                                    <div class="text-center">
                                                        <h3
                                                            class="text-lg font-medium text-slate-900 dark:text-slate-100">
                                                            No hay productos agregados</h3>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400">Comience
                                                            agregando productos al documento</p>
                                                    </div>
                                                    <flux:button icon="plus" variant="outline" size="sm"
                                                        wire:click="abrirModalProductos" class="mt-2">
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
                    <p class="text-xs text-emerald-800 dark:text-emerald-200 my-2">{{ $this->montoEnLetras }}</p>
                </div>

                <!-- Totales -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Monto en Letras -->
                        <div
                            class="rounded-lg p-4 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 space-y-4">

                            <!-- Descuentos Globales Minimalista -->
                            <div
                                class="rounded-md p-2 border border-yellow-100 dark:border-yellow-800 flex items-center space-x-2">
                                <flux:checkbox label="Descuentos" wire:model.live="aplicarDescuentos"
                                    class="text-yellow-600 dark:text-yellow-400" />
                                <flux:icon name="minus-circle" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                @if ($aplicarDescuentos)
                                    <flux:input label="" wire:model.live="descuentos_mto" type="number"
                                        step="0.01" placeholder="Descuento" size="xs" class="max-w-24 ml-2" />
                                    <span class="text-xs text-yellow-700 dark:text-yellow-300 ml-2">al total</span>
                                @endif
                            </div>

                            <!-- Cargos Globales Minimalista -->
                            <div
                                class="rounded-md p-2 border border-blue-100 dark:border-blue-800 flex items-center space-x-2">
                                <flux:checkbox label="Cargos" wire:model.live="aplicarCargos"
                                    class="text-blue-600 dark:text-blue-400" />
                                <flux:icon name="plus-circle" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                @if ($aplicarCargos)
                                    <flux:input label="" wire:model.live="cargos_mto" type="number"
                                        step="0.01" placeholder="Cargo" size="xs" class="max-w-24 ml-2" />
                                    <span class="text-xs text-blue-700 dark:text-blue-300 ml-2">al total</span>
                                @endif
                            </div>

                            <!-- Retenciones Globales Minimalista -->
                            <div
                                class="rounded-md p-2 border border-red-100 dark:border-red-800 flex items-center space-x-2">
                                <flux:checkbox label="Retenciones (3%)" wire:model.live="aplicarRetencion"
                                    class="text-red-600 dark:text-red-400" />
                                <flux:icon name="shield-check" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                @if ($aplicarRetencion)
                                    <span class="text-xs text-red-700 dark:text-red-300 ml-2">Automático si > S/ 700</span>
                                @endif
                            </div>

                            <!-- Tipo de Venta Minimalista -->
                            <div class="flex items-center gap-2">
                                <flux:icon name="credit-card" class="w-4 h-4 text-green-500 dark:text-green-400" />
                                <flux:select wire:model.live="tipoVenta" size="xs" class="w-28 border-slate-200 dark:border-slate-700">
                                    <option value="contado">Contado</option>
                                    <option value="credito">Crédito</option>
                                </flux:select>
                            </div>

                            <!-- Información de Regímenes Tributarios -->
                            @if ($aplicarDetraccion)
                                <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800">
                                    <div class="flex items-center gap-2 text-xs">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                        <span class="text-yellow-800 dark:text-yellow-200 font-medium">Detracción SPOT</span>
                                    </div>
                                    <div class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                        <div>Porcentaje: {{ $setPercent }}%</div>
                                        <div>Monto: S/ {{ number_format($setMount, 2) }}</div>
                                        <div class="text-xs mt-1">*Depositar en Banco de la Nación</div>
                                    </div>
                                </div>
                            @endif

                            @if ($aplicarPercepcion)
                                <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center gap-2 text-xs">
                                        <flux:icon name="document-text" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-blue-800 dark:text-blue-200 font-medium">Percepción</span>
                                    </div>
                                    <div class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                        <div>Porcentaje: {{ $porcentajePer }}%</div>
                                        <div>Monto: S/ {{ number_format($mtoTotalPer, 2) }}</div>
                                        <div class="text-xs mt-1">*Cobrar al cliente</div>
                                    </div>
                                </div>
                            @endif

                            @if ($aplicarRetencion && !in_array($tipoOperacion, ['2002', '2003', '2004']))
                                <div class="mt-2 p-2 bg-red-50 dark:bg-red-900/20 rounded border border-red-200 dark:border-red-800">
                                    <div class="flex items-center gap-2 text-xs">
                                        <flux:icon name="shield-check" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                        <span class="text-red-800 dark:text-red-200 font-medium">Retención Automática</span>
                                    </div>
                                    <div class="text-xs text-red-700 dark:text-red-300 mt-1">
                                        <div>Porcentaje: 3% del total</div>
                                        <div>Monto: S/ {{ number_format($mtoRet, 2) }}</div>
                                        <div class="text-xs mt-1">*Se aplica si total > S/ 700</div>
                                    </div>
                                </div>
                            @endif
                            <!-- Cuotas Minimalista (solo si es crédito) -->
                            @if ($tipoVenta === 'credito')
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-600 dark:text-slate-300 flex items-center gap-1">
                                            <flux:icon name="calendar" class="w-4 h-4 text-indigo-500 dark:text-indigo-400" />
                                            Cuotas
                                        </span>
                                        <flux:button icon="plus" variant="outline" size="xs"
                                            wire:click="agregarCuota"
                                            class="border-indigo-200 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400">
                                        </flux:button>
                                    </div>
                                    @forelse ($cuotas as $index => $cuota)
                                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800 rounded px-2 py-1 border border-slate-100 dark:border-slate-700">
                                            <flux:input label="" wire:model.live="cuotas.{{ $index }}.monto" type="number"
                                                step="0.01" placeholder="Monto" size="xs" class="w-20" />
                                            <flux:input label="" wire:model.live="cuotas.{{ $index }}.fecha_pago"
                                                type="date" size="xs" class="w-28" />
                                            <flux:button icon="trash" variant="ghost" size="xs"
                                                wire:click="eliminarCuota({{ $index }})"
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 ml-1">
                                            </flux:button>
                                        </div>
                                    @empty
                                        <div class="text-xs text-center text-slate-400 py-2">
                                            <span>No hay cuotas. </span>
                                            <span class="underline cursor-pointer" wire:click="agregarCuota">Agregar</span>
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                        <!-- Resumen Minimalista de Montos -->
                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 w-full mx-auto shadow-sm">
                            <ul class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                                <li class="flex justify-between py-1">
                                    <span class="text-slate-500 dark:text-slate-400">Subtotal</span>
                                    <span class="font-medium text-slate-900 dark:text-slate-100">S/ {{ number_format($subtotal, 2) }}</span>
                                </li>
                                <li class="flex justify-between py-1">
                                    <span class="text-slate-500 dark:text-slate-400">IGV 18%</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">S/ {{ number_format($igv, 2) }}</span>
                                </li>
                                @if ($aplicarDescuentos && $descuentos_mto > 0)
                                    <li class="flex justify-between py-1">
                                        <span class="text-slate-500 dark:text-slate-400">Descuentos</span>
                                        <span class="font-medium text-red-600 dark:text-red-400">- S/ {{ number_format($descuentos_mto, 2) }}</span>
                                    </li>
                                @endif
                                @if ($aplicarCargos && $cargos_mto > 0)
                                    <li class="flex justify-between py-1">
                                        <span class="text-slate-500 dark:text-slate-400">Cargos</span>
                                        <span class="font-medium text-green-600 dark:text-green-400">+ S/ {{ number_format($cargos_mto, 2) }}</span>
                                    </li>
                                @endif
                                @if ($setMount > 0)
                                    <li class="flex justify-between py-1">
                                        <span class="text-slate-500 dark:text-slate-400">Detracción ({{ $setPercent }}%)</span>
                                        <span class="font-medium text-red-600 dark:text-red-400">- S/ {{ number_format($setMount, 2) }}</span>
                                    </li>
                                @endif
                                @if ($mtoTotalPer > 0)
                                    <li class="flex justify-between py-1">
                                        <span class="text-slate-500 dark:text-slate-400">Percepción ({{ $porcentajePer }}%)</span>
                                        <span class="font-medium text-green-600 dark:text-green-400">+ S/ {{ number_format($mtoTotalPer, 2) }}</span>
                                    </li>
                                @endif
                                @if ($mtoRet > 0 && in_array($tipoOperacion, ['2002', '2003', '2004']))
                                    <li class="flex justify-between py-1">
                                        <span class="text-slate-500 dark:text-slate-400">Retención ({{ number_format($factorRet * 100, 0) }}%)</span>
                                        <span class="font-medium text-red-600 dark:text-red-400">- S/ {{ number_format($mtoRet, 2) }}</span>
                                    </li>
                                @endif
                                @if ($mtoRet > 0 && $aplicarRetencion && !in_array($tipoOperacion, ['2002', '2003', '2004']))
                                    <li class="flex justify-between py-1">
                                        <span class="text-slate-500 dark:text-slate-400">Retención (3%)</span>
                                        <span class="font-medium text-red-600 dark:text-red-400">- S/ {{ number_format($mtoRet, 2) }}</span>
                                    </li>
                                @endif
                            </ul>
                            <div class="flex justify-between items-center mt-3 pt-2 border-t border-slate-100 dark:border-slate-700">
                                <span class="text-base font-semibold text-slate-900 dark:text-slate-100">TOTAL</span>
                                <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Observaciones -->
                <div class="p-6">

                    <div class="space-y-2">
                        <flux:textarea label="Observaciones" wire:model.live="observacion"
                            placeholder="Observaciones adicionales del documento..." rows="4" size="xs"
                            class="w-full" />
                    </div>
                </div>

                <!-- Documentos Relacionados -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-3">
                        <div
                            class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <flux:icon name="document" class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <flux:heading size="md" class="text-slate-900 dark:text-white">Documentos
                                Relacionados</flux:heading>
                            <p class="text-xs text-slate-600 dark:text-slate-400">Guías de remisión y documentos
                                relacionados</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Guías de Remisión -->
                        <div
                            class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-3 border border-purple-200 dark:border-purple-800">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center space-x-2">
                                    <flux:checkbox label="Guías de Remisión" wire:model.live="aplicarGuias"
                                        class="text-purple-600 dark:text-purple-400" />
                                    <flux:icon name="truck" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                    <span class="text-sm font-medium text-purple-900 dark:text-purple-100">Guías de
                                        Remisión</span>
                                </div>
                                @if ($aplicarGuias)
                                    <flux:button icon="plus" variant="outline" size="sm"
                                        wire:click="agregarGuia"
                                        class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white border-0">
                                        Agregar
                                    </flux:button>
                                @endif
                            </div>
                            @if ($aplicarGuias)
                                @if (count($guias) > 0)
                                    <div class="space-y-2">
                                        @foreach ($guias as $index => $guia)
                                            <div
                                                class="flex items-center space-x-2 p-2 bg-white dark:bg-slate-800 rounded-md border border-purple-200 dark:border-purple-700 shadow-sm">
                                                <flux:input label=""
                                                    wire:model.live="guias.{{ $index }}.serie"
                                                    placeholder="Serie" size="xs" class="w-20" />
                                                <flux:input label=""
                                                    wire:model.live="guias.{{ $index }}.correlativo"
                                                    placeholder="Correlativo" size="xs" class="w-24" />
                                                <flux:button icon="trash" variant="outline" size="sm"
                                                    wire:click="eliminarGuia({{ $index }})"
                                                    class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                </flux:button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-2 text-purple-500 dark:text-purple-400">
                                        <flux:icon name="truck" class="w-4 h-4 mx-auto mb-1" />
                                        <p class="text-xs">Sin guías agregadas</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-2 text-purple-500 dark:text-purple-400">
                                    <flux:icon name="truck" class="w-4 h-4 mx-auto mb-1" />
                                    <p class="text-xs">Deshabilitado</p>
                                </div>
                            @endif
                        </div>

                        <!-- Documentos Relacionados -->
                        <div
                            class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg p-3 border border-emerald-200 dark:border-emerald-800">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center space-x-2">
                                    <flux:checkbox label="Documentos Relacionados" wire:model.live="aplicarDocumentos"
                                        class="text-emerald-600 dark:text-emerald-400" />
                                    <flux:icon name="link"
                                        class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    <span class="text-sm font-medium text-emerald-900 dark:text-emerald-100">Documentos
                                        Relacionados</span>
                                </div>
                                @if ($aplicarDocumentos)
                                    <flux:button icon="plus" variant="outline" size="sm"
                                        wire:click="agregarDocumentoRelacionado"
                                        class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white border-0">
                                        Agregar
                                    </flux:button>
                                @endif
                            </div>
                            @if ($aplicarDocumentos)
                                @if (count($relDocs) > 0)
                                    <div class="space-y-2">
                                        @foreach ($relDocs as $index => $doc)
                                            <div
                                                class="space-y-2 p-2 bg-white dark:bg-slate-800 rounded-md border border-emerald-200 dark:border-emerald-700 shadow-sm">
                                                <div class="flex items-center space-x-2">
                                                    <flux:select label=""
                                                        wire:model.live="relDocs.{{ $index }}.tipoDoc"
                                                        size="xs" class="w-24">
                                                        <option value="01">01 - Factura</option>
                                                        <option value="03">03 - Boleta</option>
                                                        <option value="07">07 - Nota Crédito</option>
                                                        <option value="08">08 - Nota Débito</option>
                                                    </flux:select>
                                                    <flux:input label=""
                                                        wire:model.live="relDocs.{{ $index }}.serie"
                                                        placeholder="Serie" size="xs" class="w-20" />
                                                    <flux:input label=""
                                                        wire:model.live="relDocs.{{ $index }}.correlativo"
                                                        placeholder="Correlativo" size="xs" class="w-24" />
                                                    <flux:button icon="trash" variant="outline" size="sm"
                                                        wire:click="eliminarDocumentoRelacionado({{ $index }})"
                                                        class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                    </flux:button>
                                                </div>
                                                <flux:input label=""
                                                    wire:model.live="relDocs.{{ $index }}.fechaEmision"
                                                    type="date" size="xs" class="w-full" />
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-2 text-emerald-500 dark:text-emerald-400">
                                        <flux:icon name="link" class="w-4 h-4 mx-auto mb-1" />
                                        <p class="text-xs">Sin documentos agregados</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-2 text-emerald-500 dark:text-emerald-400">
                                    <flux:icon name="link" class="w-4 h-4 mx-auto mb-1" />
                                    <p class="text-xs">Deshabilitado</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Productos - Formulario Directo -->
    <flux:modal wire:model="modal_productos" max-width="4xl">
        <!-- Header Minimalista -->
        <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                    {{ $editando_producto ? 'Editar' : 'Agregar' }} Producto
                </h2>
                <flux:button variant="ghost" size="xs" wire:click="cerrarModalProductos" class="text-slate-400 hover:text-slate-600">
                    <flux:icon name="x-mark" class="w-4 h-4" />
                </flux:button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Botón para buscar producto del catálogo -->
            <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center space-x-3">
                    <flux:icon name="magnifying-glass" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    <div>
                        <h3 class="font-medium text-blue-900 dark:text-blue-100">Buscar en Catálogo</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Selecciona un producto existente</p>
                    </div>
                </div>
                <flux:button
                    variant="outline"
                    size="sm"
                    wire:click="abrirModalEscogerProducto"
                    class="border-blue-300 text-blue-700 hover:bg-blue-100 dark:border-blue-600 dark:text-blue-300 dark:hover:bg-blue-900/30">
                    <flux:icon name="magnifying-glass" class="w-4 h-4 mr-2" />
                    Buscar Producto
                </flux:button>
            </div>

            <!-- Información del producto seleccionado -->
            @if($producto_seleccionado)
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <div class="font-medium text-emerald-900 dark:text-emerald-100">
                                    {{ $producto_seleccionado->code }}
                                </div>
                                <div class="text-sm text-emerald-700 dark:text-emerald-300">
                                    {{ $producto_seleccionado->description }}
                                </div>
                            </div>
                        </div>
                        <flux:button
                            variant="ghost"
                            size="xs"
                            wire:click="limpiarProductoSeleccionado"
                            class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200">
                            <flux:icon name="x-mark" class="w-4 h-4" />
                        </flux:button>
                    </div>
                </div>
            @endif

            <!-- Formulario Compacto Ordenado Profesionalmente -->
            <div class="space-y-4">
                <!-- Primera fila: Unidad, Cantidad, Precio Unitario -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select
                        label="Unidad"
                        wire:model="unidad"
                        size="sm">
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->codigo }}">{{ $unidad->codigo }}</option>
                        @endforeach
                    </flux:select>
                    <flux:input
                        label="Cantidad"
                        wire:model.live="cantidad"
                        type="number"
                        step="0.01"
                        min="0.01"
                        size="sm" />
                    <flux:input
                        label="Precio Unitario"
                        wire:model.live="precio_unitario"
                        type="number"
                        step="0.01"
                        min="0"
                        size="sm" />
                </div>

                <!-- Segunda fila: Descripción (ocupa todo el ancho) -->
                <div>
                    <flux:textarea
                        label="Descripción"
                        wire:model="descripcion_producto"
                        placeholder="Descripción opcional..."
                        rows="2"
                        size="sm"
                        class="w-full" />
                </div>

                <!-- Tercera fila: Afectación IGV (al final, ocupa todo el ancho en móvil y 1/3 en desktop) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-start-3">
                        <flux:select
                            label="Afectación IGV"
                            wire:model="tipAfeIgv"
                            size="sm"
                            class="w-full">
                            @foreach ($tiposAfectacionIgv as $tipo)
                                <option value="{{ $tipo->codigo }}">{{ $tipo->codigo }} - {{ $tipo->descripcion }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </div>

            <!-- Resumen Compacto -->
            @if ($producto_id && $cantidad > 0 && $precio_unitario > 0)
                <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Valor Venta</div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">
                                S/ {{ number_format(($cantidad * $precio_unitario) / 1.18, 2) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">IGV</div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">
                                S/ {{ number_format($cantidad * $precio_unitario - ($cantidad * $precio_unitario) / 1.18, 2) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Total</div>
                            <div class="font-semibold text-emerald-600 dark:text-emerald-400">
                                S/ {{ number_format($cantidad * $precio_unitario, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer Minimalista -->
        <div class="bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 p-4">
            <div class="flex justify-end space-x-3">
                <flux:button
                    variant="outline"
                    wire:click="cerrarModalProductos"
                    size="sm">
                    Cancelar
                </flux:button>
                <flux:button
                    variant="primary"
                    wire:click="agregarProducto"
                    :disabled="!$producto_id || $cantidad <= 0 || $precio_unitario <= 0"
                    size="sm">
                    <flux:icon name="{{ $editando_producto ? 'check' : 'plus' }}" class="w-4 h-4 mr-2" />
                    {{ $editando_producto ? 'Actualizar' : 'Agregar' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Escoger Producto - Búsqueda y Selección -->
    <flux:modal wire:model="escojeProducto" max-width="3xl">
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                    Seleccionar Producto del Catálogo
                </h2>
                <flux:button variant="ghost" size="xs" wire:click="cerrarModalEscogerProducto" class="text-slate-400 hover:text-slate-600">
                    <flux:icon name="x-mark" class="w-4 h-4" />
                </flux:button>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <!-- Búsqueda -->
            <div class="space-y-3">
                <flux:input
                    label="Buscar producto"
                    wire:model.live="busquedaProducto"
                    placeholder="Código o nombre del producto..."
                    size="sm"
                    class="w-full"
                    icon="magnifying-glass" />
            </div>

            <!-- Lista de Productos -->
            @if (!empty($productosFiltrados))
                <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                    <div class="max-h-96 overflow-y-auto">
                        @foreach ($productosFiltrados as $producto)
                            <div class="p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-colors"
                                wire:click="seleccionarProductoDelCatalogo({{ $producto->id }})">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="font-medium text-slate-900 dark:text-slate-100">
                                            {{ $producto->code }}
                                        </div>
                                        <div class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                            {{ $producto->description }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-500 mt-1">
                                            Unidad: {{ $producto->unidadMedida->codigo ?? 'NIU' }}
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                            S/ {{ number_format($producto->price_venta, 2) }}
                                        </div>
                                        <div class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">
                                            Precio de venta
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon name="magnifying-glass" class="w-12 h-12 text-slate-400 dark:text-slate-500 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-2">No se encontraron productos</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Intenta con otros términos de búsqueda</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 p-4">
            <div class="flex justify-end space-x-3">
                <flux:button
                    variant="outline"
                    wire:click="cerrarModalEscogerProducto"
                    size="sm">
                    Cancelar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
edit
