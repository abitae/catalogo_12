<?php

namespace App\Livewire\Crm;

use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\MarcaCrm;
use App\Models\Crm\TipoNegocioCrm;
use App\Models\Shared\Customer;
use App\Models\User;
use App\Models\Crm\ContactCrm;
use App\Models\Crm\ActivityCrm;
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
            'etapa' => 'required|string|in:inicial,negociacion,propuesta,cierre',
            'customer_id' => 'required|exists:customers,id',
            'contact_id' => 'nullable|exists:contacts_crm,id',
            'tipo_negocio_id' => 'nullable|exists:tipos_negocio_crm,id',
            'marca_id' => 'nullable|exists:marcas_crm,id',
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
            'valor.numeric' => 'El valor debe ser un número',
            'valor.min' => 'El valor debe ser mayor o igual a 0',
            'etapa.required' => 'La etapa es requerida',
            'etapa.in' => 'La etapa seleccionada no es válida',
            'customer_id.required' => 'El cliente es requerido',
            'customer_id.exists' => 'El cliente seleccionado no existe',
            'contact_id.exists' => 'El contacto seleccionado no existe',
            'tipo_negocio_id.exists' => 'El tipo de negocio seleccionado no existe',
            'marca_id.exists' => 'La marca seleccionada no existe',
            'probabilidad.integer' => 'La probabilidad debe ser un número entero',
            'probabilidad.min' => 'La probabilidad debe ser mayor o igual a 0',
            'probabilidad.max' => 'La probabilidad debe ser menor o igual a 100',
            'fecha_cierre_esperada.date' => 'La fecha de cierre esperada debe ser una fecha válida',
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
            'contacts' => ContactCrm::all(),
            'users' => User::all(),
            'estados' => ['nueva', 'en_proceso', 'ganada', 'perdida'],
            'etapas' => ['inicial', 'negociacion', 'propuesta', 'cierre'],
            'fuentes' => ['web', 'referido', 'cold_call', 'email', 'evento', 'otro'],
        ]);
    }

    public function nuevaOpportunity()
    {
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
            $this->probabilidad = $this->opportunity->probabilidad;
            $this->fecha_cierre_esperada = $this->opportunity->fecha_cierre_esperada->format('Y-m-d');
            $this->fuente = $this->opportunity->fuente;
            $this->descripcion = $this->opportunity->descripcion;
            $this->notas = $this->opportunity->notas;

            if ($this->opportunity->image) {
                $this->imagePreview = Storage::url($this->opportunity->image);
            }
        }

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
        $this->opportunity->delete();
        $this->modal_form_eliminar_opportunity = false;
        $this->reset(['opportunity_id', 'opportunity']);
    }

    public function guardarOpportunity()
    {
        $data = $this->validate();

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
        } else {
            OpportunityCrm::create($data);
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
}
