<div>
    <!-- Previsualización directa del archivo de log -->
    @if($showLogContent && $logContent)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <flux:button wire:click="togglePreview" :icon="$previewOpen ? 'chevron-down' : 'chevron-right'" color="gray" size="sm"/>
                <h3 class="text-lg font-bold text-gray-900">Previsualización de archivo: {{ $selectedLogFile }}</h3>
                <span class="ml-3 text-xs text-gray-500 bg-gray-100 rounded px-2 py-1">{{ substr_count($logContent, "\n") + 1 }} líneas</span>
            </div>
            <flux:button icon="clipboard" color="primary" size="sm" onclick="navigator.clipboard.writeText(document.getElementById('log-preview-content').innerText)">
                Copiar todo
            </flux:button>
            <flux:button wire:click="closeLogContent" icon="x-mark" color="gray" size="sm" class="ml-2"/>
        </div>
        @if($previewOpen)
        <div>
            <div class="bg-gray-900 text-green-400 rounded-b p-4 text-xs overflow-x-auto max-h-[60vh] font-mono border-t border-gray-200" id="log-preview-content" style="white-space: pre;">
                {{ $logContent }}
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Selector de archivo de log -->
    <div class="flex items-center gap-4 mb-4">
        <label class="block text-sm font-medium text-gray-700">Archivo de log:</label>
        <select wire:model="selectedLogFile" wire:change="selectLogFile($event.target.value)"
            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach($logFiles as $file)
                <option value="{{ $file['name'] }}">
                    {{ $file['name'] }} ({{ number_format($file['size'] / 1024, 2) }} KB, {{ $file['modified'] ? $file['modified']->format('d/m/Y H:i') : '-' }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- Botón y lógica para Vista Código solo con Livewire -->
    <div class="flex justify-end mb-2">
        <flux:button wire:click="toggleCodeView" icon="code-bracket" color="gray" size="sm" :class="$codeView ? 'bg-blue-600 text-white' : ''">
            {{ $codeView ? 'Vista normal' : 'Vista código' }}
        </flux:button>
    </div>

    <!-- CONTENIDO ORIGINAL DEL COMPONENTE -->
    <div>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Visor de Logs</h2>
                    <p class="text-gray-600">Gestiona y visualiza los archivos de log del sistema</p>
                </div>
            </div>

            <!-- Estadísticas Detalladas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-800 rounded-lg">
                            <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-100" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Emergencias</p>
                            <p class="text-2xl font-bold text-red-800">{{ number_format($statistics['emergency']) }}</p>
                            @if($statistics['total'] > 0)
                            <p class="text-xs text-gray-500">{{ number_format(($statistics['emergency'] / $statistics['total']) * 100, 1) }}%</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Errores Críticos</p>
                            <p class="text-2xl font-bold text-red-600">{{ number_format($statistics['errors']) }}</p>
                            @if($statistics['total'] > 0)
                            <p class="text-xs text-gray-500">{{ number_format(($statistics['errors'] / $statistics['total']) * 100, 1) }}%</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <flux:icon name="exclamation-circle" class="w-6 h-6 text-yellow-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Advertencias</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ number_format($statistics['warnings']) }}</p>
                            @if($statistics['total'] > 0)
                            <p class="text-xs text-gray-500">{{ number_format(($statistics['warnings'] / $statistics['total']) * 100, 1) }}%</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <flux:icon name="information-circle" class="w-6 h-6 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Información</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($statistics['info']) }}</p>
                            @if($statistics['total'] > 0)
                            <p class="text-xs text-gray-500">{{ number_format(($statistics['info'] / $statistics['total']) * 100, 1) }}%</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas por Nivel -->
            {{-- SECCIÓN ELIMINADA --}}

            <!-- Archivo de Log -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Archivo de Log</h3>
                        <p class="text-sm text-gray-600">Visualiza y gestiona el archivo de log principal de la aplicación</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex flex-col md:flex-row md:items-center gap-2 text-sm text-gray-500">
                        <span><b>Nombre:</b> {{ $logFileName }}</span>
                        <span><b>Tamaño:</b> {{ number_format($logFileSize / 1024, 2) }} KB</span>
                        <span><b>Modificado:</b> {{ $logFileModified ? $logFileModified->format('d/m/Y H:i') : '-' }}</span>
                        <button wire:click="viewLogFile('{{ $logFileName }}')"
                                class="ml-2 text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors"
                                title="Ver contenido">
                            <flux:icon name="eye" class="w-5 h-5" />
                        </button>
                        <button wire:click="downloadLogFile('{{ $logFileName }}')"
                                class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition-colors"
                                title="Descargar">
                            <flux:icon name="arrow-down-tray" class="w-5 h-5" />
                        </button>
                        <button wire:click="clearLogFile('{{ $logFileName }}')"
                                class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors"
                                onclick="return confirm('¿Estás seguro de que quieres limpiar este archivo de log?')"
                                title="Limpiar archivo">
                            <flux:icon name="trash" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Filtros</h3>
                    <p class="text-sm text-gray-600">Filtra las entradas de log por diferentes criterios</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <input type="text" wire:model.live="search"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Buscar en mensajes o contexto...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
                            <select wire:model.live="logLevel"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos</option>
                                <option value="EMERGENCY">EMERGENCY</option>
                                <option value="ERROR">ERROR</option>
                                <option value="WARNING">WARNING</option>
                                <option value="INFO">INFO</option>
                                <option value="DEBUG">DEBUG</option>
                                <option value="NOTICE">NOTICE</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                            <input type="date" wire:model.live="fechaInicio"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                            <input type="date" wire:model.live="fechaFin"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <flux:button wire:click="clearFilters" icon="arrow-path" color="gray" size="sm"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entradas de Log -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Entradas de Log</h3>
                        <p class="text-sm text-gray-600">
                            Mostrando {{ $logEntries->count() }} entradas
                            @if($fechaInicio && $fechaFin)
                            del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-sm text-gray-500 mr-2">
                            Archivo: {{ $logFileName }}
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    @if($codeView)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensaje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logEntries as $entry)
                            <tr>
                                <td class="px-4 py-2">
                                    <pre class="bg-gray-900 text-green-400 rounded p-2 text-xs overflow-x-auto">{{ $entry['timestamp'] }} | {{ $entry['level'] }} | {{ $entry['channel'] }} | {{ $entry['message'] }}@if($entry['context'])
{{ json_encode($entry['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}@endif</pre>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="document-magnifying-glass" class="w-12 h-12 text-gray-400 mb-4" />
                                        <p class="text-gray-500 font-medium">No se encontraron entradas de log</p>
                                        <p class="text-gray-400 text-sm mt-1">
                                            Intenta ajustar los filtros aplicados o verifica el archivo
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Canal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensaje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logEntries as $entry)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($entry['timestamp'])->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($entry['timestamp'])->format('H:i:s') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-xs font-semibold text-gray-700">{{ $entry['level'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-600">{{ $entry['channel'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    <div class="truncate" title="{{ $entry['message'] }}">{{ $entry['message'] }}
                                        @if($entry['context'])
                                        <pre class="bg-gray-100 text-xs text-gray-700 rounded p-2 mt-1 overflow-x-auto">{{ json_encode($entry['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        <button class="text-blue-500 text-xs hover:underline mt-1" onclick="navigator.clipboard.writeText(JSON.stringify({{ json_encode($entry['context']) }})); return false;">Copiar contexto</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="document-magnifying-glass" class="w-12 h-12 text-gray-400 mb-4" />
                                        <p class="text-gray-500 font-medium">No se encontraron entradas de log</p>
                                        <p class="text-gray-400 text-sm mt-1">
                                            Intenta ajustar los filtros aplicados o verifica el archivo
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @endif
                </div>
                @if($totalPages > 1)
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Mostrando página {{ $page }} de {{ $totalPages }}
                            <span class="text-gray-500">({{ number_format($logEntries->count()) }} entradas en esta página)</span>
                        </div>
                        <div class="flex space-x-2">
                            @if($page > 1)
                            <button wire:click="previousPage"
                                    class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                                <flux:icon name="chevron-left" class="w-4 h-4 inline mr-1" />
                                Anterior
                            </button>
                            @endif
                            @if($page < $totalPages)
                            <button wire:click="nextPage"
                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                Siguiente
                                <flux:icon name="chevron-right" class="w-4 h-4 inline ml-1" />
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
