<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <!-- Header con Título y Acciones -->
    <div class="flex items-center justify-between mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Vista general de tu sistema</p>
        </div>
        <div class="flex items-center gap-3">
            <x-mary-button icon="o-information-circle" color="gray" size="sm" tooltip="Información del Dashboard" />
            <x-mary-button icon="o-arrow-path" color="blue" size="sm" tooltip="Actualizar datos" />
            <x-mary-dropdown>
                <x-slot:trigger>
                    <x-mary-button icon="o-cog-6-tooth" color="gray" size="sm" />
                </x-slot:trigger>
                <x-mary-menu-item icon="o-arrow-path" label="Actualizar" />
                <x-mary-menu-item icon="o-document-arrow-down" label="Exportar" />
                <x-mary-menu-item icon="o-cog-6-tooth" label="Configuración" />
            </x-mary-dropdown>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Catálogo -->
        <x-mary-card class="bg-gradient-to-br from-[var(--color-blue-50)] to-[var(--color-blue-100)] dark:from-[var(--color-blue-900)]/20 dark:to-[var(--color-blue-800)]/20 border border-[var(--color-blue-200)] dark:border-[var(--color-blue-700)] shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-blue-600)] dark:text-[var(--color-blue-400)]">Catálogo</p>
                    <p class="text-2xl font-bold text-[var(--color-blue-900)] dark:text-[var(--color-blue-100)]">
                        {{ number_format($estadisticasCatalogo['total_productos']) }}</p>
                    <p class="text-xs text-[var(--color-blue-600)] dark:text-[var(--color-blue-400)]">Productos totales</p>
                </div>
                <flux:icon.shopping-bag class="h-8 w-8 text-[var(--color-blue-500)]" />
            </div>
        </x-mary-card>

        <!-- Almacén -->
        <x-mary-card class="bg-gradient-to-br from-[var(--color-green-50)] to-[var(--color-green-100)] dark:from-[var(--color-green-900)]/20 dark:to-[var(--color-green-800)]/20 border border-[var(--color-green-200)] dark:border-[var(--color-green-700)] shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-green-600)] dark:text-[var(--color-green-400)]">Almacén</p>
                    <p class="text-2xl font-bold text-[var(--color-green-900)] dark:text-[var(--color-green-100)]">
                        {{ number_format($estadisticasAlmacen['total_productos']) }}</p>
                    <p class="text-xs text-[var(--color-green-600)] dark:text-[var(--color-green-400)]">Productos en stock</p>
                </div>
                <flux:icon.building-storefront class="h-8 w-8 text-[var(--color-green-500)]" />
            </div>
        </x-mary-card>

        <!-- CRM -->
        <x-mary-card
            class="bg-gradient-to-br from-[var(--color-purple-50)] to-[var(--color-purple-100)] dark:from-[var(--color-purple-900)]/20 dark:to-[var(--color-purple-800)]/20 border border-[var(--color-purple-200)] dark:border-[var(--color-purple-700)] shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-purple-600)] dark:text-[var(--color-purple-400)]">CRM</p>
                    <p class="text-2xl font-bold text-[var(--color-purple-900)] dark:text-[var(--color-purple-100)]">
                        {{ number_format($estadisticasCrm['total_oportunidades']) }}</p>
                    <p class="text-xs text-[var(--color-purple-600)] dark:text-[var(--color-purple-400)]">Oportunidades</p>
                </div>
                <flux:icon.user-group class="h-8 w-8 text-[var(--color-purple-500)]" />
            </div>
        </x-mary-card>

        <!-- Valor Total -->
        <x-mary-card
            class="bg-gradient-to-br from-[var(--color-orange-50)] to-[var(--color-orange-100)] dark:from-[var(--color-orange-900)]/20 dark:to-[var(--color-orange-800)]/20 border border-[var(--color-orange-200)] dark:border-[var(--color-orange-700)] shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-orange-600)] dark:text-[var(--color-orange-400)]">Valor Total</p>
                    <p class="text-2xl font-bold text-[var(--color-orange-900)] dark:text-[var(--color-orange-100)]">S/
                        {{ number_format($estadisticasAlmacen['valor_total_inventario'], 2) }}</p>
                    <p class="text-xs text-[var(--color-orange-600)] dark:text-[var(--color-orange-400)]">Inventario</p>
                </div>
                <flux:icon.currency-dollar class="h-8 w-8 text-[var(--color-orange-500)]" />
            </div>
        </x-mary-card>
    </div>

    <!-- Gráficos Principales -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Gráfico de Movimientos por Mes -->
        <x-mary-card class="border border-gray-200 dark:border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-t-lg border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Movimientos de Almacén</h3>
                <div class="flex items-center gap-2">
                    <x-mary-button size="sm" icon="o-arrow-path" color="green" tooltip="Actualizar datos" />
                    <flux:icon.chart-bar class="h-5 w-5 text-gray-400" />
                </div>
            </div>
            <div class="h-64 p-4">
                <x-mary-chart wire:model="movimientosChart" />
            </div>
        </x-mary-card>

        <!-- Gráfico de Productos por Categoría -->
        <x-mary-card class="border border-gray-200 dark:border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-t-lg border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Productos por Categoría</h3>
                <div class="flex items-center gap-2">
                    <x-mary-button size="sm" icon="o-arrow-path" color="green" tooltip="Actualizar datos" />
                    <flux:icon.chart-pie class="h-5 w-5 text-gray-400" />
                </div>
            </div>
            <div class="h-64 p-4">
                <x-mary-chart wire:model="categoriasChart" />
            </div>
        </x-mary-card>
    </div>

    <!-- Gráficos Adicionales -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Stock por Almacén -->
        <x-mary-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Stock por Almacén</h3>
                <div class="flex items-center gap-2">
                    <x-mary-button size="sm" icon="o-chart-bar" tooltip="Gráfico" class="text-gray-400" />
                </div>
            </div>
            <div class="h-64">
                <x-mary-chart wire:model="stockChart" />
            </div>
        </x-mary-card>

        <!-- Oportunidades por Etapa -->
        <x-mary-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Oportunidades por Etapa</h3>
                <div class="flex items-center gap-2">
                    <x-mary-button size="sm" icon="o-chart-pie" tooltip="Gráfico" class="text-gray-400" />
                </div>
            </div>
            <div class="h-64">
                <x-mary-chart wire:model="oportunidadesChart" />
            </div>
        </x-mary-card>
    </div>

    <!-- Actividad Reciente -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Movimientos Recientes -->
        <x-mary-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Movimientos Recientes</h3>
                <flux:icon.clock class="h-5 w-5 text-gray-400" />
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @foreach ($estadisticasAlmacen['movimientos_recientes']->take(5) as $movimiento)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium">{{ $movimiento->producto->nombre ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $movimiento->tipo_movimiento }} -
                                {{ $movimiento->almacen->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold">{{ $movimiento->cantidad }}</p>
                            <p class="text-xs text-gray-500">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-mary-card>

        <!-- Actividades CRM Recientes -->
        <x-mary-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Actividades CRM</h3>
                <flux:icon.calendar class="h-5 w-5 text-gray-400" />
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @foreach ($estadisticasCrm['actividades_recientes']->take(5) as $actividad)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium">{{ $actividad->tipo ?? 'Actividad' }}</p>
                            <p class="text-xs text-gray-500">{{ $actividad->contacto->nombre ?? 'N/A' }} -
                                {{ $actividad->oportunidad->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ $actividad->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-mary-card>
    </div>

    <!-- Botones de Acción Rápida -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-mary-button class="w-full" color="blue" icon="o-shopping-bag">
            Ver Catálogo Completo
        </x-mary-button>

        <x-mary-button class="w-full" color="green" icon="o-building-storefront">
            Gestionar Almacén
        </x-mary-button>

        <x-mary-button class="w-full" color="purple" icon="o-user-group">
            Administrar CRM
        </x-mary-button>
    </div>

    <!-- Indicadores de Estado -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <x-mary-card class="text-center">
            <div class="flex items-center justify-center mb-2">
                <x-mary-badge value="OK" color="green" size="lg" />
            </div>
            <p class="text-sm text-gray-600">Sistema Operativo</p>
        </x-mary-card>

        <x-mary-card class="text-center">
            <div class="flex items-center justify-center mb-2">
                <x-mary-badge value="{{ $estadisticasAlmacen['productos_stock_bajo'] > 0 ? 'ALERTA' : 'OK' }}"
                    color="{{ $estadisticasAlmacen['productos_stock_bajo'] > 0 ? 'orange' : 'green' }}"
                    size="lg" />
            </div>
            <p class="text-sm text-gray-600">Stock Bajo</p>
        </x-mary-card>

        <x-mary-card class="text-center">
            <div class="flex items-center justify-center mb-2">
                <x-mary-badge
                    value="{{ $estadisticasCrm['oportunidades_abiertas'] > 0 ? 'ACTIVAS' : 'SIN ACTIVIDAD' }}"
                    color="{{ $estadisticasCrm['oportunidades_abiertas'] > 0 ? 'blue' : 'gray' }}" size="lg" />
            </div>
            <p class="text-sm text-gray-600">Oportunidades</p>
        </x-mary-card>

        <x-mary-card class="text-center">
            <div class="flex items-center justify-center mb-2">
                <x-mary-badge value="{{ $estadisticasCatalogo['productos_activos'] > 0 ? 'ACTIVO' : 'INACTIVO' }}"
                    color="{{ $estadisticasCatalogo['productos_activos'] > 0 ? 'green' : 'red' }}" size="lg" />
            </div>
            <p class="text-sm text-gray-600">Catálogo</p>
        </x-mary-card>
    </div>
</div>
