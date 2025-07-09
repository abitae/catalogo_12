<?php

namespace App\Livewire\Crm;

use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\MarcaCrm;
use App\Models\Crm\TipoNegocioCrm;
use App\Models\Shared\Customer;
use App\Models\User;
use App\Models\Crm\ContactCrm;
use App\Models\Crm\ActivityCrm;
use App\Models\Shared\TipoCustomer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class OpportunityCrmIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $estado_filter = '';
    public $tipo_negocio_filter = '';
    public $marca_filter = '';
    public $customer_filter = '';
    public $user_filter = '';
    public $etapa_filter = '';

    // Variables para Oportunidad
    public $modal_form_opportunity = false;
    public $modal_form_eliminar_opportunity = false;
    public $opportunity_id = '';
    public $opportunity = null;
    public $nombre = '';
    public $valor = '';
    public $etapa = '';
    public $probabilidad = '';
    public $customer_id = '';
    public $contact_id = '';
    public $tipo_negocio_id = '';
    public $marca_id = '';
    public $user_id = '';
    public $fecha_cierre_esperada = '';
    public $fuente = '';
    public $descripcion = '';
    public $notas = '';
    public $tempImage = null;
    public $tempArchivo = null;
    public $imagePreview = null;

    // Variables para Actividades (separadas claramente)
    public $modal_actividades = false;
    public $selected_opportunity_id = '';
    public $selected_opportunity = null;

    // Variables del formulario de actividad
    public $activity_id = '';
    public $activity = null;
    public $tipo_activity = '';
    public $asunto_activity = '';
    public $estado_activity = '';
    public $prioridad_activity = '';
    public $descripcion_activity = '';
    public $contact_id_activity = '';
    public $tempImageActivity = null;
    public $tempArchivoActivity = null;
    public $imagePreviewActivity = null;

    // Variables para el formulario de cliente
    public $modal_form_customer = false;
    public $customer_id_form = '';
    public $customer_form = null;

    // Variables para el formulario de contacto
    public $modal_form_contacto = false;
    public $contact_id_form = '';
    public $contact_form = null;

    // Variables para el formulario de cliente
    public $tipoDoc = '';
    public $numDoc = '';
    public $rznSocial = '';
    public $nombreComercial = '';
    public $email = '';
    public $telefono = '';
    public $direccion = '';
    public $codigoPostal = '';
    public $notas_cliente = '';
    public $tipo_customer_id = '';

    // Variables para el formulario de contacto
    public $nombre_contacto = '';
    public $apellido_contacto = '';
    public $correo_contacto = '';
    public $telefono_contacto = '';
    public $cargo_contacto = '';
    public $empresa_contacto = '';
    public $notas_contacto = '';
    public $es_principal_contacto = false;

    // Manejo de archivos para cliente
    public $tempImageCustomer = null;
    public $tempArchivoCustomer = null;
    public $imagePreviewCustomer = null;

    // Lista de opciones para actividades
    public $tipos_activity = ['llamada', 'email', 'reunion', 'tarea', 'nota', 'propuesta'];
    public $estados_activity = ['pendiente', 'en_proceso', 'completada', 'cancelada'];
    public $prioridades_activity = ['baja', 'normal', 'alta', 'urgente'];

    protected $queryString = [
        'search' => ['except' => ''],
        'estado_filter' => ['except' => ''],
        'tipo_negocio_filter' => ['except' => ''],
        'marca_filter' => ['except' => ''],
        'customer_filter' => ['except' => ''],
        'user_filter' => ['except' => ''],
        'etapa_filter' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'etapa' => 'required|string|in:aceptada,entregada,pagada',
            'customer_id' => 'required|exists:customers,id',
            'contact_id' => 'required|exists:contacts_crm,id',
            'tipo_negocio_id' => 'nullable|exists:tipos_negocio_crm,id',
            'marca_id' => 'required|exists:marcas_crm,id',
            'user_id' => 'required|exists:users,id',
            'probabilidad' => 'nullable|integer|min:0|max:100',
            'fecha_cierre_esperada' => 'nullable|date',
            'fuente' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'notas' => 'nullable|string|max:1000',
            'tempImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ];
    }

    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'valor.required' => 'El valor es requerido',
            'valor.numeric' => 'El valor debe ser numérico',
            'valor.min' => 'El valor debe ser mayor a 0',
            'etapa.required' => 'La etapa es requerida',
            'etapa.in' => 'La etapa debe ser aceptada, entregada o pagada',
            'customer_id.required' => 'El cliente es requerido',
            'customer_id.exists' => 'El cliente seleccionado no existe',
            'contact_id.required' => 'El contacto es requerido',
            'contact_id.exists' => 'El contacto seleccionado no existe',
            'tipo_negocio_id.exists' => 'El tipo de negocio seleccionado no existe',
            'marca_id.required' => 'La marca es requerida',
            'marca_id.exists' => 'La marca seleccionada no existe',
            'user_id.required' => 'El encargado es requerido',
            'user_id.exists' => 'El encargado seleccionado no existe',
            'probabilidad.integer' => 'La probabilidad debe ser un número entero',
            'probabilidad.min' => 'La probabilidad debe ser mayor o igual a 0',
            'probabilidad.max' => 'La probabilidad debe ser menor o igual a 100',
            'fecha_cierre_esperada.date' => 'La fecha de cierre debe ser válida',
            'fuente.max' => 'La fuente no debe exceder 100 caracteres',
            'descripcion.max' => 'La descripción no debe exceder 1000 caracteres',
            'notas.max' => 'Las notas no deben exceder 1000 caracteres',
            'tempImage.image' => 'El archivo debe ser una imagen',
            'tempImage.mimes' => 'La imagen debe ser JPEG, PNG, JPG, GIF o SVG',
            'tempImage.max' => 'La imagen no debe exceder 20MB',
            'tempArchivo.file' => 'El archivo debe ser válido',
            'tempArchivo.mimes' => 'El archivo debe ser PDF, DOC, DOCX, XLS, XLSX, PPT o PPTX',
            'tempArchivo.max' => 'El archivo no debe exceder 10MB',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'sortField',
            'sortDirection',
            'estado_filter',
            'tipo_negocio_filter',
            'marca_filter',
            'customer_filter',
            'user_filter',
            'etapa_filter',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados');
    }
    public function mount() {
        $this->fecha_cierre_esperada = \Carbon\Carbon::now()->format('Y-m-d');
    }
    public function render()
    {
        $query = OpportunityCrm::query()
            ->with(['tipoNegocio', 'marca', 'cliente', 'usuario'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->estado_filter, function ($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->when($this->tipo_negocio_filter, function ($query) {
                $query->where('tipo_negocio_id', $this->tipo_negocio_filter);
            })
            ->when($this->marca_filter, function ($query) {
                $query->where('marca_id', $this->marca_filter);
            })
            ->when($this->customer_filter, function ($query) {
                $query->where('customer_id', $this->customer_filter);
            })
            ->when($this->user_filter, function ($query) {
                $query->where('user_id', $this->user_filter);
            })
            ->when($this->etapa_filter, function ($query) {
                $query->where('etapa', $this->etapa_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.opportunity-crm-index', [
            'opportunities' => $query->latest()->paginate($this->perPage),
            'tipos_negocio' => TipoNegocioCrm::all(),
            'marcas' => MarcaCrm::all(),
            'customers' => Customer::all(),
            'users' => User::all(),
            'estados' => ['nueva', 'en_proceso', 'ganada', 'perdida'],
            'etapas' => ['aceptada', 'entregada', 'pagada'],
            'fuentes' => ['web', 'referido', 'cold_call', 'email', 'evento', 'otro'],
            'tipos_customer' => TipoCustomer::all(),
        ]);
    }

    public function nuevaOpportunity()
    {
        $this->reset([
            'opportunity_id',
            'opportunity',
            'nombre',
            'valor',
            'etapa',
            'customer_id',
            'contact_id',
            'tipo_negocio_id',
            'marca_id',
            'user_id',
            'probabilidad',
            'fecha_cierre_esperada',
            'fuente',
            'descripcion',
            'notas',
            'tempImage',
            'tempArchivo',
            'imagePreview'
        ]);
        $this->fecha_cierre_esperada = \Carbon\Carbon::now()->format('Y-m-d');
        $this->resetValidation();
        $this->modal_form_opportunity = true;
    }

    public function editarOpportunity($id)
    {
        $this->opportunity_id = $id;
        $this->opportunity = OpportunityCrm::find($id);

        if ($this->opportunity) {
            $this->nombre = $this->opportunity->nombre;
            $this->valor = $this->opportunity->valor;
            $this->etapa = $this->opportunity->etapa;
            $this->customer_id = $this->opportunity->customer_id;
            $this->contact_id = $this->opportunity->contact_id;
            $this->tipo_negocio_id = $this->opportunity->tipo_negocio_id;
            $this->marca_id = $this->opportunity->marca_id;
            $this->user_id = $this->opportunity->user_id;
            $this->probabilidad = $this->opportunity->probabilidad;
            $this->fecha_cierre_esperada = $this->opportunity->fecha_cierre_esperada ? $this->opportunity->fecha_cierre_esperada->format('Y-m-d') : '';
            $this->fuente = $this->opportunity->fuente;
            $this->descripcion = $this->opportunity->descripcion;
            $this->notas = $this->opportunity->notas;

            if ($this->opportunity->image) {
                $this->imagePreview = Storage::url($this->opportunity->image);
            }
        }

        $this->resetValidation();
        $this->modal_form_opportunity = true;
    }

    public function eliminarOpportunity($id)
    {
        $this->opportunity_id = $id;
        $this->opportunity = OpportunityCrm::find($id);
        if ($this->opportunity) {
            $this->modal_form_eliminar_opportunity = true;
        }
    }

    public function confirmarEliminarOpportunity()
    {
        try {
            // Eliminar archivos si existen
            if ($this->opportunity->image && Storage::disk('public')->exists($this->opportunity->image)) {
                Storage::disk('public')->delete($this->opportunity->image);
            }
            if ($this->opportunity->archivo && Storage::disk('public')->exists($this->opportunity->archivo)) {
                Storage::disk('public')->delete($this->opportunity->archivo);
            }

            $this->opportunity->delete();
            $this->modal_form_eliminar_opportunity = false;
            $this->reset(['opportunity_id', 'opportunity']);
            $this->success('Oportunidad eliminada correctamente');
        } catch (\Exception $e) {
            $this->error('Error al eliminar la oportunidad: ' . $e->getMessage());
        }
    }

    public function guardarOpportunity()
    {
        // Validar primero - esto permitirá que Livewire muestre los errores
        $data = $this->validate();

        // Manejar fecha vacía
        if (empty($data['fecha_cierre_esperada'])) {
            $data['fecha_cierre_esperada'] = null;
        }

        // Validar que el contacto pertenece al cliente seleccionado
        if ($this->contact_id && $this->customer_id) {
            $contactBelongsToCustomer = ContactCrm::where('id', $this->contact_id)
                ->where('customer_id', $this->customer_id)
                ->exists();

            if (!$contactBelongsToCustomer) {
                $this->addError('contact_id', 'El contacto seleccionado no pertenece al cliente seleccionado.');
                return;
            }
        }

        try {
            // Manejar imagen
            if ($this->tempImage) {
                // Eliminar imagen anterior si existe
                if ($this->opportunity_id && $this->opportunity && $this->opportunity->image) {
                    Storage::disk('public')->delete($this->opportunity->image);
                }

                $imagePath = $this->tempImage->store('opportunities/images', 'public');
                $data['image'] = $imagePath;
            }

            // Manejar archivo
            if ($this->tempArchivo) {
                // Eliminar archivo anterior si existe
                if ($this->opportunity_id && $this->opportunity && $this->opportunity->archivo) {
                    Storage::disk('public')->delete($this->opportunity->archivo);
                }

                $archivoPath = $this->tempArchivo->store('opportunities/archivos', 'public');
                $data['archivo'] = $archivoPath;
            }

            // Remover campos temporales
            unset($data['tempImage'], $data['tempArchivo']);

            if ($this->opportunity_id) {
                $opportunity = OpportunityCrm::find($this->opportunity_id);
                $opportunity->update($data);
                $this->success('Oportunidad actualizada correctamente');
            } else {
                OpportunityCrm::create($data);
                $this->success('Oportunidad creada correctamente');
            }

            $this->modal_form_opportunity = false;
            $this->reset([
                'opportunity_id',
                'opportunity',
                'nombre',
                'valor',
                'etapa',
                'customer_id',
                'contact_id',
                'tipo_negocio_id',
                'marca_id',
                'probabilidad',
                'fecha_cierre_esperada',
                'fuente',
                'descripcion',
                'notas',
                'tempImage',
                'tempArchivo',
                'imagePreview'
            ]);
            $this->resetValidation();
        } catch (\Exception $e) {
            $this->error('Error al guardar la oportunidad: ' . $e->getMessage());
        }
    }

    public function updatedTempImage()
    {
        if ($this->tempImage) {
            $this->imagePreview = $this->tempImage->temporaryUrl();
        }
    }

    public function removeImage()
    {
        $this->tempImage = null;
        $this->imagePreview = null;
    }

    public function verActividades($id)
    {
        try {
            $this->selected_opportunity_id = $id;
            $this->selected_opportunity = OpportunityCrm::with(['cliente.contactos', 'actividades'])->find($id);

            if (!$this->selected_opportunity) {
                $this->error('No se encontró la oportunidad seleccionada.');
                return;
            }

            $this->resetActivityForm();
            $this->modal_actividades = true;
        } catch (\Exception $e) {
            $this->error('Error al cargar las actividades: ' . $e->getMessage());
        }
    }

    public function resetActivityForm()
    {
        $this->reset([
            'activity_id',
            'activity',
            'tipo_activity',
            'asunto_activity',
            'estado_activity',
            'prioridad_activity',
            'descripcion_activity',
            'contact_id_activity',
            'tempImageActivity',
            'tempArchivoActivity',
            'imagePreviewActivity'
        ]);
        $this->resetValidation();
    }

    public function nuevaActivity()
    {
        $this->resetActivityForm();
        $this->estado_activity = 'pendiente';
        $this->prioridad_activity = 'normal';
    }

    public function editarActivity($id)
    {
        try {
            $this->activity_id = $id;
            $this->activity = ActivityCrm::find($id);

            if ($this->activity) {
                // Verificar que la actividad pertenece a la oportunidad seleccionada
                if ($this->activity->opportunity_id != $this->selected_opportunity_id) {
                    $this->error('La actividad no pertenece a esta oportunidad.');
                    return;
                }

                $this->tipo_activity = $this->activity->tipo;
                $this->asunto_activity = $this->activity->asunto;
                $this->estado_activity = $this->activity->estado;
                $this->prioridad_activity = $this->activity->prioridad;
                $this->descripcion_activity = $this->activity->descripcion;
                $this->contact_id_activity = $this->activity->contact_id;

                if ($this->activity->image) {
                    $this->imagePreviewActivity = Storage::url($this->activity->image);
                }
            } else {
                $this->error('No se encontró la actividad a editar.');
            }
        } catch (\Exception $e) {
            $this->error('Error al cargar la actividad: ' . $e->getMessage());
        }
    }

    public function eliminarActivity($id)
    {
        try {
            $activity = ActivityCrm::find($id);
            if ($activity) {
                // Verificar que la actividad pertenece a la oportunidad seleccionada
                if ($activity->opportunity_id != $this->selected_opportunity_id) {
                    $this->error('La actividad no pertenece a esta oportunidad.');
                    return;
                }

                // Eliminar archivos si existen
                if ($activity->image && Storage::disk('public')->exists($activity->image)) {
                    Storage::disk('public')->delete($activity->image);
                }
                if ($activity->archivo && Storage::disk('public')->exists($activity->archivo)) {
                    Storage::disk('public')->delete($activity->archivo);
                }

                $activity->delete();
                $this->success('Actividad eliminada correctamente.');

                // Recargar la oportunidad con las actividades actualizadas
                $this->selected_opportunity = OpportunityCrm::with(['cliente.contactos', 'actividades'])->find($this->selected_opportunity_id);
            } else {
                $this->error('No se encontró la actividad a eliminar.');
            }
        } catch (\Exception $e) {
            $this->error('Error al eliminar la actividad: ' . $e->getMessage());
        }
    }

    public function guardarActivity()
    {
        // Validar que existe una oportunidad seleccionada
        if (!$this->selected_opportunity_id || !$this->selected_opportunity) {
            $this->error('No se ha seleccionado una oportunidad válida.');
            return;
        }

        // Validar primero - esto permitirá que Livewire muestre los errores
        $this->validate([
            'tipo_activity' => 'required|string|in:' . implode(',', $this->tipos_activity),
            'asunto_activity' => 'required|string|max:255',
            'estado_activity' => 'required|string|in:' . implode(',', $this->estados_activity),
            'prioridad_activity' => 'required|string|in:' . implode(',', $this->prioridades_activity),
            'descripcion_activity' => 'nullable|string|max:1000',
            'contact_id_activity' => 'nullable|exists:contacts_crm,id',
            'tempImageActivity' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivoActivity' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        // Validar que el contacto pertenece al cliente de la oportunidad
        if ($this->contact_id_activity) {
            $contactBelongsToCustomer = $this->selected_opportunity->cliente->contactos()
                ->where('id', $this->contact_id_activity)
                ->exists();

            if (!$contactBelongsToCustomer) {
                $this->error('El contacto seleccionado no pertenece al cliente de esta oportunidad.');
                return;
            }
        }

        $data = [
            'tipo' => $this->tipo_activity,
            'asunto' => $this->asunto_activity,
            'estado' => $this->estado_activity,
            'prioridad' => $this->prioridad_activity,
            'descripcion' => $this->descripcion_activity,
            'contact_id' => $this->contact_id_activity,
            'opportunity_id' => $this->selected_opportunity_id,
            'user_id' => Auth::user()->id,
        ];

        try {
            // Manejar imagen
            if ($this->tempImageActivity) {
                if ($this->activity_id && $this->activity && $this->activity->image) {
                    Storage::disk('public')->delete($this->activity->image);
                }
                $imagePath = $this->tempImageActivity->store('activities/images', 'public');
                $data['image'] = $imagePath;
            }

            // Manejar archivo
            if ($this->tempArchivoActivity) {
                if ($this->activity_id && $this->activity && $this->activity->archivo) {
                    Storage::disk('public')->delete($this->activity->archivo);
                }
                $archivoPath = $this->tempArchivoActivity->store('activities/archivos', 'public');
                $data['archivo'] = $archivoPath;
            }

            if ($this->activity_id) {
                $activity = ActivityCrm::find($this->activity_id);
                if ($activity) {
                    $activity->update($data);
                    $this->success('Actividad actualizada correctamente.');
                } else {
                    $this->error('No se encontró la actividad a actualizar.');
                    return;
                }
            } else {
                ActivityCrm::create($data);
                $this->success('Actividad creada correctamente.');
            }

            $this->resetActivityForm();
            // Recargar la oportunidad con las actividades actualizadas
            $this->selected_opportunity = OpportunityCrm::with(['cliente.contactos', 'actividades'])->find($this->selected_opportunity_id);

        } catch (\Exception $e) {
            $this->error('Error al guardar la actividad: ' . $e->getMessage());
        }
    }

    public function updatedTempImageActivity()
    {
        if ($this->tempImageActivity) {
            $this->imagePreviewActivity = $this->tempImageActivity->temporaryUrl();
        }
    }

    public function removeImageActivity()
    {
        $this->tempImageActivity = null;
        $this->imagePreviewActivity = null;
    }

    public function cerrarModalActividades()
    {
        $this->modal_actividades = false;
        $this->reset([
            'selected_opportunity_id',
            'selected_opportunity',
            'activity_id',
            'activity',
            'tipo_activity',
            'asunto_activity',
            'estado_activity',
            'prioridad_activity',
            'descripcion_activity',
            'contact_id_activity',
            'tempImageActivity',
            'tempArchivoActivity',
            'imagePreviewActivity'
        ]);
        $this->resetValidation();
        session()->forget(['success', 'error']);
    }

    public function cerrarModalOpportunity()
    {
        $this->modal_form_opportunity = false;
        $this->reset([
            'opportunity_id',
            'opportunity',
            'nombre',
            'valor',
            'etapa',
            'customer_id',
            'contact_id',
            'tipo_negocio_id',
            'marca_id',
            'user_id',
            'probabilidad',
            'fecha_cierre_esperada',
            'fuente',
            'descripcion',
            'notas',
            'tempImage',
            'tempArchivo',
            'imagePreview'
        ]);
        $this->fecha_cierre_esperada = \Carbon\Carbon::now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function updatedCustomerId()
    {
        // Limpiar el contacto seleccionado cuando cambia el cliente
        $this->contact_id = null;
    }

    public function nuevoCliente()
    {
        $this->modal_form_customer = true;
    }

    public function nuevoContacto()
    {
        $this->modal_form_contacto = true;
    }
    public function guardarCustomer() {
        // Validar primero - esto permitirá que Livewire muestre los errores
        $data = $this->validate([
            'tipoDoc' => 'required|string|max:10',
            'numDoc' => 'required|string|max:20|unique:customers,numDoc,' . $this->customer_id_form,
            'rznSocial' => 'required|string|max:255',
            'nombreComercial' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'codigoPostal' => 'nullable|string|max:10',
            'notas_cliente' => 'nullable|string|max:1000',
            'tipo_customer_id' => 'nullable|exists:tipo_customers,id',
            'tempImageCustomer' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivoCustomer' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ], [
            'tipoDoc.required' => 'El tipo de documento es requerido',
            'numDoc.required' => 'El número de documento es requerido',
            'numDoc.unique' => 'Este número de documento ya está registrado',
            'rznSocial.required' => 'La razón social es requerida',
            'email.email' => 'El email debe tener un formato válido',
            'tipo_customer_id.exists' => 'El tipo de cliente seleccionado no existe',
            'tempImageCustomer.image' => 'El archivo debe ser una imagen',
            'tempImageCustomer.mimes' => 'La imagen debe ser JPEG, PNG, JPG, GIF o SVG',
            'tempImageCustomer.max' => 'La imagen no debe exceder 20MB',
            'tempArchivoCustomer.file' => 'El archivo debe ser válido',
            'tempArchivoCustomer.mimes' => 'El archivo debe ser PDF, DOC, DOCX, XLS, XLSX, PPT o PPTX',
            'tempArchivoCustomer.max' => 'El archivo no debe exceder 10MB',
        ]);

        try {
            // Manejar imagen
            if ($this->tempImageCustomer) {
                $imagePath = $this->tempImageCustomer->store('customers/images', 'public');
                $data['image'] = $imagePath;
            }

            // Manejar archivo
            if ($this->tempArchivoCustomer) {
                $archivoPath = $this->tempArchivoCustomer->store('customers/archivos', 'public');
                $data['archivo'] = $archivoPath;
            }

            // Remover campos temporales
            unset($data['tempImageCustomer'], $data['tempArchivoCustomer']);

            // Mapear notas_cliente a notas para el modelo Customer
            $data['notas'] = $data['notas_cliente'];
            unset($data['notas_cliente']);

            if ($this->customer_id_form) {
                $customer = Customer::find($this->customer_id_form);
                $customer->update($data);
                $this->success('Cliente actualizado correctamente');
            } else {
                $customer = Customer::create($data);
                $this->success('Cliente creado correctamente');

                // Seleccionar automáticamente el cliente creado
                $this->customer_id = $customer->id;
            }

            $this->modal_form_customer = false;
            $this->resetCustomerForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el cliente: ' . $e->getMessage());
        }
    }

    public function guardarContacto() {
        // Validar primero - esto permitirá que Livewire muestre los errores
        $data = $this->validate([
            'nombre_contacto' => 'required|string|max:255',
            'apellido_contacto' => 'required|string|max:255',
            'correo_contacto' => 'required|email|max:255',
            'telefono_contacto' => 'nullable|string|max:20',
            'cargo_contacto' => 'nullable|string|max:255',
            'empresa_contacto' => 'nullable|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'notas_contacto' => 'nullable|string',
            'es_principal_contacto' => 'boolean',
        ], [
            'nombre_contacto.required' => 'El nombre es requerido',
            'apellido_contacto.required' => 'El apellido es requerido',
            'correo_contacto.required' => 'El correo es requerido',
            'correo_contacto.email' => 'El correo debe ser válido',
            'customer_id.required' => 'El cliente es requerido',
            'customer_id.exists' => 'El cliente seleccionado no existe',
        ]);

        try {
            // Mapear los datos para el modelo ContactCrm
            $contactData = [
                'nombre' => $data['nombre_contacto'],
                'apellido' => $data['apellido_contacto'],
                'correo' => $data['correo_contacto'],
                'telefono' => $data['telefono_contacto'],
                'cargo' => $data['cargo_contacto'],
                'empresa' => $data['empresa_contacto'],
                'customer_id' => $data['customer_id'],
                'notas' => $data['notas_contacto'],
                'es_principal' => $data['es_principal_contacto'],
                'ultima_fecha_contacto' => now(),
            ];

            if ($this->contact_id_form) {
                $contacto = ContactCrm::find($this->contact_id_form);
                $contacto->update($contactData);
                $this->success('Contacto actualizado correctamente');
            } else {
                $contacto = ContactCrm::create($contactData);
                $this->success('Contacto creado correctamente');

                // Seleccionar automáticamente el contacto creado
                $this->contact_id = $contacto->id;
            }

            $this->modal_form_contacto = false;
            $this->resetContactForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el contacto: ' . $e->getMessage());
        }
    }

    private function resetCustomerForm()
    {
        $this->reset([
            'customer_id_form',
            'customer_form',
            'tipoDoc',
            'numDoc',
            'rznSocial',
            'nombreComercial',
            'email',
            'telefono',
            'direccion',
            'codigoPostal',
            'notas_cliente',
            'tipo_customer_id',
            'tempImageCustomer',
            'tempArchivoCustomer',
            'imagePreviewCustomer'
        ]);
        $this->resetValidation();
    }

    private function resetContactForm()
    {
        $this->reset([
            'contact_id_form',
            'contact_form',
            'nombre_contacto',
            'apellido_contacto',
            'correo_contacto',
            'telefono_contacto',
            'cargo_contacto',
            'empresa_contacto',
            'notas_contacto',
            'es_principal_contacto'
        ]);
        $this->resetValidation();
    }

    public function updatedTempImageCustomer()
    {
        if ($this->tempImageCustomer) {
            $this->imagePreviewCustomer = $this->tempImageCustomer->temporaryUrl();
        }
    }

    public function removeImageCustomer()
    {
        $this->tempImageCustomer = null;
        $this->imagePreviewCustomer = null;
    }

    public function cerrarModalCustomer()
    {
        $this->modal_form_customer = false;
        $this->resetCustomerForm();
    }

    public function cerrarModalContacto()
    {
        $this->modal_form_contacto = false;
        $this->resetContactForm();
    }
}
