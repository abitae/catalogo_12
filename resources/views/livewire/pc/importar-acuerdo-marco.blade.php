<div>
    <!-- Toast notifications -->
    <x-mary-toast />

    <!-- Header con breadcrumb -->
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{route('dashboard')}}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <flux:icon.home class="w-5 h-5 mr-2.5" />
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <flux:icon.chevron-right class="w-6 h-6 text-gray-400" />
                                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">
                                    Convenio Marco
                                </span>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <flux:icon.chevron-right class="w-6 h-6 text-gray-400" />
                                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">
                                    Importar
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    Importar data Convenio Marco
                </h1>
            </div>
        </div>
    </div>

    <!-- Área de Importación -->
    <div class="p-4 bg-white dark:bg-gray-800">
        <div class="max-w-4xl mx-auto">
            <!-- Card de Importación -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Importar Archivo Excel
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Sube un archivo Excel (.xlsx o .xls) para importar datos del Convenio Marco
                            </p>
                        </div>
                                                                        <div class="flex space-x-2">
                            <flux:button color="gray" wire:click="downloadTemplate" icon="document-arrow-down" size="sm">
                                Descargar Template
                            </flux:button>
                            <flux:button color="red" wire:click="clearImportData" icon="trash" size="sm"
                                onclick="confirm('¿Estás seguro de que quieres eliminar todos los datos del convenio marco?')">
                                Limpiar Datos
                            </flux:button>
                        </div>
                    </div>

                    <!-- Área de Dropzone -->
                    <div class="relative">
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-primary-500 dark:hover:border-primary-400 transition-colors">
                            @if($tempFile)
                                <!-- Archivo seleccionado -->
                                <div class="mb-4">
                                    <flux:icon.document class="w-12 h-12 text-green-500 mx-auto mb-3" />
                                    <p class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                                        {{ $filePreview }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Archivo seleccionado para importar
                                    </p>
                                </div>

                                                                                                <!-- Botones de acción -->
                                <div class="flex justify-center space-x-3">
                                    <flux:button variant="primary" wire:click="import" wire:loading.attr="disabled" class="min-w-[120px]">
                                        <div wire:loading.remove wire:target="import">
                                            <flux:icon name="arrow-up" class="w-4 h-4 mr-2" />
                                            Importar
                                        </div>
                                        <div wire:loading wire:target="import" class="flex items-center">
                                            <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                                            Procesando...
                                        </div>
                                    </flux:button>

                                    <flux:button color="gray" wire:click="removeFile" wire:loading.attr="disabled" icon="x-mark">
                                        Cambiar
                                    </flux:button>
                                </div>

                                <!-- Estadísticas de procesamiento -->
                                @if($isProcessing)
                                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <div class="flex items-center justify-center space-x-2">
                                            <flux:icon name="arrow-path" class="w-4 h-4 animate-spin text-blue-600" />
                                            <span class="text-sm text-blue-600 dark:text-blue-400">
                                                Procesando archivo...
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                @if($registrosProcesados > 0)
                                    <div class="mt-4 space-y-2">
                                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                            <div class="text-center">
                                                <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                                    Registros procesados: {{ number_format($registrosProcesados) }}
                                                </p>
                                            </div>
                                        </div>

                                        @if($registrosConError > 0)
                                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                                <div class="text-center">
                                                    <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                                        Registros con errores: {{ number_format($registrosConError) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                            @else
                                <!-- Área de dropzone vacía -->
                                <flux:icon name="cloud-arrow-up" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                <p class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    Arrastra tu archivo aquí o haz clic para seleccionar
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    Formatos soportados: .xlsx, .xls (máximo 10MB)
                                </p>

                                                                                                                                                                <!-- Input de archivo -->
                                <div class="relative">
                                    <input wire:model="tempFile" id="tempFile" type="file" class="hidden"
                                           accept=".xlsx,.xls" />
                                    <flux:button variant="primary" class="min-w-[120px]" icon="folder-open"
                                               onclick="document.getElementById('tempFile').click()">
                                        Seleccionar Archivo
                                    </flux:button>
                                </div>
                            @endif
                        </div>

                        <!-- Errores de validación -->
                        @error('tempFile')
                            <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="flex items-center">
                                    <flux:icon name="exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" />
                                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                </div>
                            </div>
                        @enderror
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-600 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Información importante:
                        </h3>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li class="flex items-start">
                                <flux:icon name="information-circle" class="w-4 h-4 mr-2 mt-0.5 text-blue-500" />
                                Los registros existentes del convenio marco serán reemplazados
                            </li>
                            <li class="flex items-start">
                                <flux:icon name="information-circle" class="w-4 h-4 mr-2 mt-0.5 text-blue-500" />
                                El archivo debe contener las columnas requeridas en el formato correcto
                            </li>
                            <li class="flex items-start">
                                <flux:icon name="information-circle" class="w-4 h-4 mr-2 mt-0.5 text-blue-500" />
                                Se procesarán automáticamente las fechas y valores numéricos
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
