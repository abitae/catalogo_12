<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <!-- Header con Título y Acciones -->
    <div class="flex items-center justify-between mb-6 p-4 bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Vista general de tu sistema</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 p-2 transition" title="Información del Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/></svg>
            </button>
            <button class="inline-flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 p-2 transition" title="Actualizar datos">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582M20 20v-5h-.581M5.635 19.364A9 9 0 1 1 19.364 5.636"/></svg>
            </button>
            <!-- Dropdown simulación -->
            <div class="relative group">
                <button class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 p-2 transition" title="Opciones">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v.01M12 12v.01M12 18v.01"/></svg>
                </button>
                <div class="absolute right-0 mt-2 w-40 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition z-10">
                    <button class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-800 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582M20 20v-5h-.581M5.635 19.364A9 9 0 1 1 19.364 5.636"/></svg>Actualizar</button>
                    <button class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-800 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 17v-6m0 0V7m0 4h.01"/></svg>Exportar</button>
                    <button class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-800 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v.01M12 12v.01M12 18v.01"/></svg>Configuración</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Catálogo -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 shadow-lg hover:shadow-xl transition-all duration-300 p-6 flex flex-col">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Catálogo</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        {{ number_format($estadisticasCatalogo['total_productos']) }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400">Productos totales</p>
                </div>
                <flux:icon.shopping-bag class="h-8 w-8 text-blue-500" />
            </div>
        </div>
        <!-- Almacén -->
        <div class="rounded-2xl bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 shadow-lg hover:shadow-xl transition-all duration-300 p-6 flex flex-col">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Almacén</p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                        {{ number_format($estadisticasAlmacen['total_productos']) }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400">Productos en stock</p>
                </div>
                <flux:icon.building-storefront class="h-8 w-8 text-green-500" />
            </div>
        </div>
        <!-- CRM -->
        <div class="rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 shadow-lg hover:shadow-xl transition-all duration-300 p-6 flex flex-col">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">CRM</p>
                    <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                        {{ number_format($estadisticasCrm['total_oportunidades']) }}</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400">Oportunidades</p>
                </div>
                <flux:icon.user-group class="h-8 w-8 text-purple-500" />
            </div>
        </div>
        <!-- Valor Total -->
        <div class="rounded-2xl bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-700 shadow-lg hover:shadow-xl transition-all duration-300 p-6 flex flex-col">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Valor Total</p>
                    <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">S/
                        {{ number_format($estadisticasAlmacen['valor_total_inventario'], 2) }}</p>
                    <p class="text-xs text-orange-600 dark:text-orange-400">Inventario</p>
                </div>
                <flux:icon.currency-dollar class="h-8 w-8 text-orange-500" />
            </div>
        </div>
    </div>

    <!-- Gráficos Principales -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-x-12">
        <!-- Gráfico de Movimientos por Mes -->
        <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow-2xl p-8 flex flex-col min-h-[350px] mb-8 lg:mb-0 transition-colors duration-300">
            <div class="mb-4">
                <h3 class="text-2xl font-extrabold text-blue-700 dark:text-blue-300 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400 dark:text-blue-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/><rect width="4" height="10" x="7" y="7" rx="1"/><rect width="4" height="6" x="15" y="11" rx="1"/></svg>
                    Movimientos de Almacén
                </h3>
                <p class="text-base text-slate-600 dark:text-zinc-300 mt-1">Resumen mensual de movimientos registrados en almacén.</p>
            </div>
            <div class="flex-1 flex items-center justify-center min-h-[250px]">
                <canvas id="movimientosChart" class="w-full" height="300"></canvas>
            </div>
        </div>
        <!-- Gráfico de Productos por Categoría -->
        <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow-2xl p-8 flex flex-col min-h-[350px] mb-8 lg:mb-0 transition-colors duration-300">
            <div class="mb-4">
                <h3 class="text-2xl font-extrabold text-purple-700 dark:text-purple-300 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400 dark:text-purple-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l2.462 7.564a1 1 0 00.95.69h7.958c.969 0 1.371 1.24.588 1.81l-6.443 4.684a1 1 0 00-.364 1.118l2.462 7.564c.3.921-.755 1.688-1.54 1.118l-6.443-4.684a1 1 0 00-1.176 0l-6.443 4.684c-.784.57-1.838-.197-1.539-1.118l2.462-7.564a1 1 0 00-.364-1.118L2.049 12.99c-.783-.57-.38-1.81.588-1.81h7.958a1 1 0 00.95-.69l2.462-7.564z"/></svg>
                    Productos por Categoría
                </h3>
                <p class="text-base text-slate-600 dark:text-zinc-300 mt-1">Distribución de productos activos por categoría.</p>
            </div>
            <div class="flex-1 flex items-center justify-center min-h-[250px]">
                <canvas id="categoriasChart" class="w-full" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- SCRIPTS AL FINAL DEL BODY -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Datos desde Livewire/PHP
            const movimientosLabels = @json($movimientosLabels);
            const movimientosData = @json($movimientosData);
            const categoriasLabels = @json($categoriasLabels);
            const categoriasData = @json($categoriasData);
            const stockLabels = @json($stockLabels);
            const stockData = @json($stockData);
            const oportunidadesLabels = @json($oportunidadesLabels);
            const oportunidadesData = @json($oportunidadesData);

            // Depuración
            console.log('Movimientos:', movimientosLabels, movimientosData);
            console.log('Categorías:', categoriasLabels, categoriasData);
            console.log('Stock:', stockLabels, stockData);
            console.log('Oportunidades:', oportunidadesLabels, oportunidadesData);

            function getChartColors() {
                const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                return {
                    text: isDark ? '#f4f4f5' : '#1e293b', // azul oscuro en claro, gris claro en dark
                    legend: isDark ? '#f4f4f5' : '#1e293b',
                    grid: isDark ? '#52525b' : '#e4e4e7',
                    bg: isDark ? '#18181b' : '#fff',
                    barGradient: isDark
                        ? ['#2563eb', '#6366f1']
                        : ['#3b82f6', '#60a5fa'],
                    pie: isDark
                        ? ['#6366f1', '#22d3ee', '#fbbf24', '#a78bfa', '#f43f5e', '#f59e42', '#84cc16', '#eab308', '#f87171', '#10b981']
                        : ['#3b82f6', '#06b6d4', '#f59e42', '#a78bfa', '#f43f5e', '#fbbf24', '#22d3ee', '#84cc16', '#eab308', '#10b981'],
                    pieBorder: isDark ? '#18181b' : '#fff',
                };
            }

            function createBarGradient(ctx, colors) {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, colors[0]);
                gradient.addColorStop(1, colors[1]);
                return gradient;
            }

            function createBarChart(ctx, labels, data, label, title) {
                const colors = getChartColors();
                const gradient = createBarGradient(ctx, colors.barGradient);
                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: gradient,
                            borderRadius: 16,
                            borderSkipped: false,
                            hoverBackgroundColor: colors.barGradient[0],
                            borderWidth: 2,
                            borderColor: colors.grid,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 1500, easing: 'easeOutElastic' },
                        plugins: {
                            legend: { display: true, labels: { color: colors.legend, font: { weight: 'bold', size: 14 }, padding: 24 } },
                            tooltip: {
                                backgroundColor: colors.bg,
                                titleColor: colors.text,
                                bodyColor: colors.text,
                                borderColor: colors.grid,
                                borderWidth: 1,
                                padding: 14,
                                cornerRadius: 10,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return ` ${context.dataset.label}: ${context.parsed.y}`;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: title,
                                color: colors.legend,
                                font: { size: 18, weight: 'bold' },
                                padding: { top: 10, bottom: 20 }
                            }
                        },
                        scales: {
                            x: { 
                                title: {
                                    display: true,
                                    text: title.includes('Etapa') ? 'Etapa' : (title.includes('Almacén') ? 'Almacén' : 'Mes'),
                                    color: colors.legend,
                                    font: { weight: 'bold', size: 14 }
                                },
                                ticks: { color: colors.legend, font: { weight: 'bold', size: 13 }, padding: 8 }, 
                                grid: { color: colors.grid } 
                            },
                            y: { 
                                title: {
                                    display: true,
                                    text: 'Cantidad',
                                    color: colors.legend,
                                    font: { weight: 'bold', size: 14 }
                                },
                                ticks: { color: colors.legend, font: { weight: 'bold', size: 13 }, padding: 8 }, 
                                grid: { color: colors.grid } 
                            }
                        }
                    }
                });
            }

            function createPieChart(ctx, labels, data, label, title) {
                const colors = getChartColors();
                return new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: colors.pie,
                            borderColor: colors.pieBorder,
                            borderWidth: 3,
                            hoverOffset: 16
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { animateScale: true, duration: 1500, easing: 'easeOutBounce' },
                        plugins: {
                            legend: { display: true, labels: { color: colors.legend, font: { weight: 'bold', size: 14 }, padding: 24 } },
                            tooltip: {
                                backgroundColor: colors.bg,
                                titleColor: colors.text,
                                bodyColor: colors.text,
                                borderColor: colors.grid,
                                borderWidth: 1,
                                padding: 14,
                                cornerRadius: 10,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percent = ((value / total) * 100).toFixed(1);
                                        return ` ${label}: ${value} (${percent}%)`;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: title,
                                color: colors.text,
                                font: { size: 18, weight: 'bold' },
                                padding: { top: 10, bottom: 20 }
                            }
                        }
                    }
                });
            }

            function renderCharts() {
                if(window.movChart) window.movChart.destroy();
                if(window.catChart) window.catChart.destroy();
                if(window.stockChartObj) window.stockChartObj.destroy();
                if(window.oportunidadesChartObj) window.oportunidadesChartObj.destroy();
                const ctxMov = document.getElementById('movimientosChart').getContext('2d');
                const ctxCat = document.getElementById('categoriasChart').getContext('2d');
                const ctxStock = document.getElementById('stockChart').getContext('2d');
                const ctxOpo = document.getElementById('oportunidadesChart').getContext('2d');
                window.movChart = createBarChart(ctxMov, movimientosLabels, movimientosData, 'Movimientos', 'Movimientos por Mes');
                window.catChart = createPieChart(ctxCat, categoriasLabels, categoriasData, 'Productos', 'Productos por Categoría');
                window.stockChartObj = createBarChart(ctxStock, stockLabels, stockData, 'Stock', 'Stock por Almacén');
                window.oportunidadesChartObj = createBarChart(ctxOpo, oportunidadesLabels, oportunidadesData, 'Oportunidades', 'Oportunidades por Etapa');
            }
            renderCharts();
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', renderCharts);
        });
    </script>

    <!-- Gráficos Adicionales -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-x-12 mt-8">
        <!-- Stock por Almacén (Barra) -->
        <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow-2xl p-8 flex flex-col min-h-[350px] mb-8 lg:mb-0 transition-colors duration-300">
            <div class="mb-4">
                <h3 class="text-2xl font-extrabold text-green-700 dark:text-green-300 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-400 dark:text-green-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="10" width="4" height="10" rx="1"/><rect x="9" y="6" width="4" height="14" rx="1"/><rect x="15" y="2" width="4" height="18" rx="1"/></svg>
                    Stock por Almacén
                </h3>
                <p class="text-base text-slate-600 dark:text-zinc-300 mt-1">Cantidad de productos en cada almacén.</p>
            </div>
            <div class="flex-1 flex items-center justify-center min-h-[250px]">
                <canvas id="stockChart" class="w-full" height="300"></canvas>
            </div>
        </div>
        <!-- Oportunidades por Etapa (Barra) -->
        <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow-2xl p-8 flex flex-col min-h-[350px] mb-8 lg:mb-0 transition-colors duration-300">
            <div class="mb-4">
                <h3 class="text-2xl font-extrabold text-purple-700 dark:text-purple-300 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400 dark:text-purple-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="10" width="4" height="10" rx="1"/><rect x="9" y="6" width="4" height="14" rx="1"/><rect x="15" y="2" width="4" height="18" rx="1"/></svg>
                    Oportunidades por Etapa
                </h3>
                <p class="text-base text-slate-600 dark:text-zinc-300 mt-1">Cantidad de oportunidades en cada etapa del proceso.</p>
            </div>
            <div class="flex-1 flex items-center justify-center min-h-[250px]">
                <canvas id="oportunidadesChart" class="w-full" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Movimientos Recientes -->
        <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow p-6 flex flex-col mb-6 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Movimientos Recientes</h3>
                <flux:icon name="clock" class="h-5 w-5 text-gray-400 dark:text-gray-300" />
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse ($estadisticasAlmacen['movimientos_recientes']->take(5) as $movimiento)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-zinc-800 rounded transition-colors duration-200">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $movimiento->producto->nombre ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $movimiento->tipo_movimiento }} -
                                {{ $movimiento->almacen->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">{{ $movimiento->cantidad }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $movimiento->fecha_movimiento }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">Sin movimientos recientes.</div>
                @endforelse
            </div>
        </div>

        <!-- Actividades CRM Recientes -->
        <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow p-6 flex flex-col mb-6 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Actividades CRM</h3>
                <flux:icon name="calendar-days" class="h-5 w-5 text-gray-400 dark:text-gray-300" />
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse ($estadisticasCrm['actividades_recientes']->take(5) as $actividad)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-zinc-800 rounded transition-colors duration-200">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $actividad->tipo ?? 'Actividad' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $actividad->contacto->nombre ?? 'N/A' }} -
                                {{ $actividad->oportunidad->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $actividad->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">Sin actividades recientes.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Botones de Acción Rápida -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <button class="w-full inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Ver Catálogo Completo
        </button>

        <button class="w-full inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
            Gestionar Almacén
        </button>

        <button class="w-full inline-flex items-center justify-center rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">
            Administrar CRM
        </button>
    </div>

    <!-- Indicadores de Estado -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mt-8">
        <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow p-6 text-center flex flex-col items-center">
            <div class="flex items-center justify-center mb-2">
                <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 font-bold text-xs">OK</span>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-300">Sistema Operativo</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow p-6 text-center flex flex-col items-center">
            <div class="flex items-center justify-center mb-2">
                <span class="inline-block px-3 py-1 rounded-full {{ $estadisticasAlmacen['productos_stock_bajo'] > 0 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }} font-bold text-xs">
                    {{ $estadisticasAlmacen['productos_stock_bajo'] > 0 ? 'ALERTA' : 'OK' }}
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-300">Stock Bajo</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow p-6 text-center flex flex-col items-center">
            <div class="flex items-center justify-center mb-2">
                <span class="inline-block px-3 py-1 rounded-full {{ $estadisticasCrm['oportunidades_abiertas'] > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }} font-bold text-xs">
                    {{ $estadisticasCrm['oportunidades_abiertas'] > 0 ? 'ACTIVAS' : 'SIN ACTIVIDAD' }}
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-300">Oportunidades</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 shadow p-6 text-center flex flex-col items-center">
            <div class="flex items-center justify-center mb-2">
                <span class="inline-block px-3 py-1 rounded-full {{ $estadisticasCatalogo['productos_activos'] > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }} font-bold text-xs">
                    {{ $estadisticasCatalogo['productos_activos'] > 0 ? 'ACTIVO' : 'INACTIVO' }}
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-300">Catálogo</p>
        </div>
    </div>
</div>
