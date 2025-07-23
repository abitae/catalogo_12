<?php

namespace App\Livewire\Pc;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Models\Pc\ProductoAcuerdoMarco;
use App\Traits\FileUploadTrait;
use App\Traits\NotificationTrait;
use App\Traits\TableTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class ImportarAcuerdoMarco extends Component
{
    use WithFileUploads, FileUploadTrait, NotificationTrait, TableTrait, Toast;

    // Variables para el archivo
    public $file;
    public $tempFile = null;
    public $filePreview = null;

    // Variables para estadísticas
    public $totalRegistros = 0;
    public $registrosProcesados = 0;
    public $registrosConError = 0;
    public $isProcessing = false;

    // Configuración de validación
    protected $validationAttributes = [
        'file' => 'archivo Excel',
    ];

    public function mount()
    {
        // Inicialización si es necesaria
    }

    public function render()
    {
        return view('livewire.pc.importar-acuerdo-marco');
    }

    public function updatedTempFile()
    {
        $this->validate([
            'tempFile' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB máximo
        ], [
            'tempFile.required' => 'Por favor, seleccione un archivo Excel',
            'tempFile.file' => 'El archivo seleccionado no es válido',
            'tempFile.mimes' => 'El archivo debe ser un Excel (.xlsx o .xls)',
            'tempFile.max' => 'El archivo no debe exceder los 10MB',
        ]);

        if ($this->tempFile) {
            $this->filePreview = $this->tempFile->getClientOriginalName();
            $this->success('Archivo seleccionado correctamente');
        }
    }

    public function removeFile()
    {
        $this->tempFile = null;
        $this->filePreview = null;
        $this->resetValidation();
        $this->info('Archivo removido');
    }

    public function import()
    {
        try {
            $this->validate([
                'tempFile' => 'required|file|mimes:xlsx,xls|max:10240',
            ], [
                'tempFile.required' => 'Por favor, seleccione un archivo Excel',
                'tempFile.file' => 'El archivo seleccionado no es válido',
                'tempFile.mimes' => 'El archivo debe ser un Excel (.xlsx o .xls)',
                'tempFile.max' => 'El archivo no debe exceder los 10MB',
            ]);

            $this->isProcessing = true;
            $this->registrosProcesados = 0;
            $this->registrosConError = 0;

            // Obtener estadísticas antes de la importación
            $registrosAntes = ProductoAcuerdoMarco::count();

            // Eliminar registros existentes del convenio marco específico
            $registrosEliminados = ProductoAcuerdoMarco::where('cod_acuerdo_marco', 'EXT-CE-2024-11')->delete();

            // Importar nuevos datos
            $import = new ProductsImport();
            Excel::import($import, $this->tempFile);

            // Obtener estadísticas de la importación
            $importStats = $import->getImportStats();
            $registrosDespues = ProductoAcuerdoMarco::count();
            $this->registrosProcesados = $importStats['processed'];
            $this->registrosConError = $importStats['errors'];

            // Log de auditoría para importación
            Log::info('Auditoría: Importación de Convenio Marco', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'import_acuerdo_marco',
                'archivo' => $this->tempFile->getClientOriginalName(),
                'tamaño_archivo' => $this->tempFile->getSize(),
                'registros_eliminados' => $registrosEliminados,
                'registros_procesados' => $this->registrosProcesados,
                'registros_antes' => $registrosAntes,
                'registros_despues' => $registrosDespues,
                'cod_acuerdo_marco' => 'EXT-CE-2024-11',
                'timestamp' => now()
            ]);

            $this->isProcessing = false;
            $this->tempFile = null;
            $this->filePreview = null;
            $this->resetValidation();

            $mensaje = "Importación completada exitosamente. Se procesaron {$this->registrosProcesados} registros.";
            if ($this->registrosConError > 0) {
                $mensaje .= " Se encontraron {$this->registrosConError} errores.";
            }

            $this->success($mensaje);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isProcessing = false;
            throw $e;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->error('Error al procesar el archivo. Verifique el formato y vuelva a intentar.');

            Log::error('Error en importación de Convenio Marco', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'archivo' => $this->tempFile ? $this->tempFile->getClientOriginalName() : 'N/A'
            ]);
        }
    }

    public function descargarTemplate()
    {
        try {
            $this->info('Preparando template para descarga...');

            return Excel::download(
                new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    public function array(): array {
                        return [
                            [
                                'cod_acuerdo_marco' => 'EXT-CE-2024-11',
                                'cod_producto' => 'PROD001',
                                'descripcion' => 'Producto de ejemplo',
                                'unidad_medida' => 'UNIDAD',
                                'cantidad' => 100,
                                'precio_unitario' => 150.00,
                                'precio_total' => 15000.00,
                                'marca' => 'Marca Ejemplo',
                                'proveedor' => 'Proveedor Ejemplo',
                                'fecha_entrega' => '2024-12-31',
                                'observaciones' => 'Observaciones del producto'
                            ]
                        ];
                    }

                    public function headings(): array {
                        return [
                            'cod_acuerdo_marco',
                            'cod_producto',
                            'descripcion',
                            'unidad_medida',
                            'cantidad',
                            'precio_unitario',
                            'precio_total',
                            'marca',
                            'proveedor',
                            'fecha_entrega',
                            'observaciones'
                        ];
                    }
                },
                'template_acuerdo_marco.xlsx'
            );
        } catch (\Exception $e) {
            $this->error('Error al generar el template');
            Log::error('Error al generar template', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function limpiarDatos()
    {
        try {
            $this->info('Iniciando limpieza de datos...');

            $registrosAntes = ProductoAcuerdoMarco::count();
            ProductoAcuerdoMarco::truncate();
            $registrosDespues = ProductoAcuerdoMarco::count();

            $mensaje = "Limpieza completada. Se eliminaron {$registrosAntes} registros.";
            $this->success($mensaje);

            Log::info('Auditoría: Limpieza de datos Convenio Marco', [
                'user_id' => Auth::id(),
                'registros_eliminados' => $registrosAntes,
                'registros_despues' => $registrosDespues,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            $this->error('Error al limpiar los datos');
            Log::error('Error en limpieza de datos', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
