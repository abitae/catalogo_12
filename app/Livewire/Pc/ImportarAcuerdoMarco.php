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
            $this->toast('Archivo seleccionado correctamente', 'success');
        }
    }

    public function removeFile()
    {
        $this->tempFile = null;
        $this->filePreview = null;
        $this->resetValidation();
        $this->toast('Archivo removido', 'info');
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

            $this->toast($mensaje, 'success');
            $this->handleSuccess($mensaje, 'importación de convenio marco');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isProcessing = false;
            throw $e;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->registrosConError++;

            Log::error('Error en importación de Convenio Marco', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'import_acuerdo_marco_error',
                'archivo' => $this->tempFile ? $this->tempFile->getClientOriginalName() : 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);

            $this->toast('Error al procesar el archivo. Verifique el formato y vuelva a intentar.', 'error');
            $this->handleError($e, 'importación de convenio marco');
        }
    }

    public function downloadTemplate()
    {
        try {
            // Crear un template básico con las columnas requeridas
            $headers = [
                'orden_electronica',
                'ruc_proveedor',
                'razon_proveedor',
                'ruc_entidad',
                'razon_entidad',
                'unidad_ejecutora',
                'procedimiento',
                'tipo',
                'estado_orden_electronica',
                'link_documento',
                'total_entrega',
                'num_doc_estado',
                'orden_fisica',
                'fecha_doc_estado',
                'fecha_estado_oc',
                'sub_total_orden',
                'igv_orden',
                'total_orden',
                'orden_digital_fisica',
                'sustento_fisica',
                'fecha_publicacion',
                'fecha_aceptacion',
                'usuario_create_oc',
                'acuerdo_marco',
                'ubigeo_proveedor',
                'direccion_proveedor',
                'monto_documento_estado',
                'catalogo',
                'categoria',
                'descripcion_ficha_producto',
                'marca_ficha_producto',
                'numero_parte',
                'link_ficha_producto',
                'monto_flete',
                'numero_entrega',
                'fecha_inicio',
                'plazo_entrega',
                'fecha_fin',
                'cantidad',
                'entrega_afecto_igv',
                'precio_unitario',
                'sub_total',
                'igv_entrega',
                'total_monto'
            ];

            // Crear un ejemplo de datos
            $exampleData = [
                'EXT-CE-2024-11 001',
                '20123456789',
                'EMPRESA EJEMPLO S.A.C.',
                '20123456789',
                'ENTIDAD EJEMPLO',
                'UNIDAD EJECUTORA',
                'PROCEDIMIENTO',
                'TIPO',
                'ACTIVO',
                'https://ejemplo.com/documento',
                '1000.00',
                'DOC001',
                'ORD001',
                '01/01/2024',
                '01/01/2024',
                '847.46',
                '152.54',
                '1000.00',
                'DIGITAL',
                'SUSTENTO',
                '01/01/2024',
                '01/01/2024',
                'USUARIO',
                'ACUERDO MARCO',
                '150101',
                'DIRECCION EJEMPLO',
                '1000.00',
                'CATALOGO',
                'CATEGORIA',
                'DESCRIPCION PRODUCTO',
                'MARCA',
                'PART001',
                'https://ejemplo.com/ficha',
                '50.00',
                '001',
                '01/01/2024',
                '30',
                '31/01/2024',
                '10',
                '847.46',
                '84.75',
                '847.46',
                '152.54',
                '1000.00'
            ];

            // Generar el archivo Excel
            $filename = 'template_convenio_marco_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(
                new class($headers, $exampleData) implements \Maatwebsite\Excel\Concerns\FromArray {
                    private $headers;
                    private $exampleData;

                    public function __construct($headers, $exampleData) {
                        $this->headers = $headers;
                        $this->exampleData = $exampleData;
                    }

                    public function array(): array {
                        return [
                            $this->headers,
                            $this->exampleData
                        ];
                    }
                },
                $filename
            );

        } catch (\Exception $e) {
            $this->toast('Error al generar el template', 'error');
            $this->handleError($e, 'descarga de template');
        }
    }

    public function clearImportData()
    {
        try {
            $registrosEliminados = ProductoAcuerdoMarco::where('cod_acuerdo_marco', 'EXT-CE-2024-11')->delete();

            Log::info('Auditoría: Limpieza de datos de Convenio Marco', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'N/A',
                'action' => 'clear_acuerdo_marco_data',
                'registros_eliminados' => $registrosEliminados,
                'cod_acuerdo_marco' => 'EXT-CE-2024-11',
                'timestamp' => now()
            ]);

            $mensaje = "Se eliminaron {$registrosEliminados} registros del convenio marco.";
            $this->toast($mensaje, 'success');
            $this->handleSuccess($mensaje, 'limpieza de datos');
        } catch (\Exception $e) {
            $this->toast('Error al limpiar los datos', 'error');
            $this->handleError($e, 'limpieza de datos');
        }
    }
}
