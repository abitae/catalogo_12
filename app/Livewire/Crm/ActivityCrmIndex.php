<?php

namespace App\Livewire\Crm;

use App\Models\Crm\ActivityCrm;
use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\ContactCrm;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

class ActivityCrmIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Filtros
    public $tipo_filter = '';
    public $estado_filter = '';
    public $prioridad_filter = '';
    public $opportunity_filter = '';
    public $contact_filter = '';
    public $user_filter = '';

    // Modal Form Actividad
    public $modal_form_activity = false;
    public $modal_form_eliminar_activity = false;
    public $activity_id = '';
    public $activity = null;

    // Variables para el formulario
    public $tipo = '';
    public $asunto = '';
    public $descripcion = '';
    public $estado = 'pendiente';
    public $prioridad = 'normal';
    public $opportunity_id = '';
    public $contact_id = '';
    public $user_id = '';
    public $image = '';
    public $archivo = '';

    // Manejo de archivos
    public $tempImage = null;
    public $tempArchivo = null;
    public $imagePreview = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'tipo_filter' => ['except' => ''],
        'estado_filter' => ['except' => ''],
        'prioridad_filter' => ['except' => ''],
        'opportunity_filter' => ['except' => ''],
        'contact_filter' => ['except' => ''],
        'user_filter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'tipo' => 'required|string|in:llamada,reunion,email,tarea',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|string|in:pendiente,completada,cancelada',
            'prioridad' => 'required|string|in:baja,normal,alta,urgente',
            'opportunity_id' => 'nullable|exists:opportunities_crm,id',
            'contact_id' => 'nullable|exists:contacts_crm,id',
            'user_id' => 'nullable|exists:users,id',
            'tempImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ];
    }

    protected function messages()
    {
        return [
            'tipo.required' => 'El tipo es requerido',
            'tipo.in' => 'El tipo seleccionado no es válido',
            'asunto.required' => 'El asunto es requerido',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado seleccionado no es válido',
            'prioridad.required' => 'La prioridad es requerida',
            'prioridad.in' => 'La prioridad seleccionada no es válida',
            'opportunity_id.exists' => 'La oportunidad seleccionada no existe',
            'contact_id.exists' => 'El contacto seleccionado no existe',
            'user_id.exists' => 'El usuario seleccionado no existe',
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
            'tipo_filter',
            'estado_filter',
            'prioridad_filter',
            'opportunity_filter',
            'contact_filter',
            'user_filter',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados correctamente');
    }

    public function render()
    {
        $query = ActivityCrm::query()
            ->with(['oportunidad', 'contacto', 'usuario'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('asunto', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->tipo_filter, function ($query) {
                $query->where('tipo', $this->tipo_filter);
            })
            ->when($this->estado_filter, function ($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->when($this->prioridad_filter, function ($query) {
                $query->where('prioridad', $this->prioridad_filter);
            })
            ->when($this->opportunity_filter, function ($query) {
                $query->where('opportunity_id', $this->opportunity_filter);
            })
            ->when($this->contact_filter, function ($query) {
                $query->where('contact_id', $this->contact_filter);
            })
            ->when($this->user_filter, function ($query) {
                $query->where('user_id', $this->user_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.activity-crm-index', [
            'activities' => $query->paginate($this->perPage),
            'opportunities' => OpportunityCrm::all(),
            'contacts' => ContactCrm::all(),
            'users' => User::all(),
            'tipos' => ['llamada', 'reunion', 'email', 'tarea'],
            'estados' => ['pendiente', 'en_proceso', 'completada', 'cancelada'],
            'prioridades' => ['baja', 'normal', 'alta', 'urgente'],
        ]);
    }

    public function nuevaActivity()
    {
        $this->modal_form_activity = true;
    }

    public function editarActivity($id)
    {
        $this->activity_id = $id;
        $this->activity = ActivityCrm::find($id);
        $this->tipo = $this->activity->tipo;
        $this->asunto = $this->activity->asunto;
        $this->descripcion = $this->activity->descripcion;
        $this->estado = $this->activity->estado;
        $this->prioridad = $this->activity->prioridad;
        $this->opportunity_id = $this->activity->opportunity_id;
        $this->contact_id = $this->activity->contact_id;
        $this->user_id = $this->activity->user_id;
        $this->image = $this->activity->image;
        $this->archivo = $this->activity->archivo;

        $this->modal_form_activity = true;
    }

    public function eliminarActivity($id)
    {
        $this->activity_id = $id;
        $this->activity = ActivityCrm::find($id);
        if ($this->activity) {
            $this->modal_form_eliminar_activity = true;
        }
    }

    public function confirmarEliminarActivity()
    {
        try {
            // Eliminar archivos si existen
            if ($this->activity->image && Storage::disk('public')->exists($this->activity->image)) {
                Storage::disk('public')->delete($this->activity->image);
            }
            if ($this->activity->archivo && Storage::disk('public')->exists($this->activity->archivo)) {
                Storage::disk('public')->delete($this->activity->archivo);
            }

            $this->activity->delete();
            $this->modal_form_eliminar_activity = false;
            $this->reset(['activity_id', 'activity']);
            $this->success('Actividad eliminada correctamente');
        } catch (\Exception $e) {
            $this->error('Error al eliminar la actividad: ' . $e->getMessage());
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
        $this->image = '';
    }

    public function guardarActivity()
    {
        try {
            $data = $this->validate();

            // Manejar imagen
            if ($this->tempImage) {
                // Eliminar imagen anterior si existe
                if ($this->activity_id && $this->activity && $this->activity->image) {
                    Storage::disk('public')->delete($this->activity->image);
                }
                $imagePath = $this->tempImage->store('activities/images', 'public');
                $data['image'] = $imagePath;
            }

            // Manejar archivo
            if ($this->tempArchivo) {
                // Eliminar archivo anterior si existe
                if ($this->activity_id && $this->activity && $this->activity->archivo) {
                    Storage::disk('public')->delete($this->activity->archivo);
                }
                $archivoPath = $this->tempArchivo->store('activities/files', 'public');
                $data['archivo'] = $archivoPath;
            }

            // Remover campos temporales
            unset($data['tempImage'], $data['tempArchivo']);

            if ($this->activity_id) {
                $activity = ActivityCrm::find($this->activity_id);
                $activity->update($data);

                // Log de auditoría para actualización de actividad
                Log::info('Auditoría: Actividad actualizada', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'update_activity',
                    'activity_id' => $this->activity_id,
                    'activity_type' => $data['tipo'],
                    'activity_subject' => $data['asunto'],
                    'activity_status' => $data['estado'],
                    'activity_priority' => $data['prioridad'],
                    'opportunity_id' => $data['opportunity_id'] ?? null,
                    'contact_id' => $data['contact_id'] ?? null,
                    'timestamp' => now()
                ]);

                $this->success('Actividad actualizada correctamente');
            } else {
                $activity = ActivityCrm::create($data);

                // Log de auditoría para creación de actividad
                Log::info('Auditoría: Actividad creada', [
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'N/A',
                    'action' => 'create_activity',
                    'activity_id' => $activity->id,
                    'activity_type' => $data['tipo'],
                    'activity_subject' => $data['asunto'],
                    'activity_status' => $data['estado'],
                    'activity_priority' => $data['prioridad'],
                    'opportunity_id' => $data['opportunity_id'] ?? null,
                    'contact_id' => $data['contact_id'] ?? null,
                    'timestamp' => now()
                ]);

                $this->success('Actividad creada correctamente');
            }

            $this->modal_form_activity = false;
            $this->reset([
                'activity_id',
                'activity',
                'tipo',
                'asunto',
                'descripcion',
                'estado',
                'prioridad',
                'opportunity_id',
                'contact_id',
                'user_id',
                'image',
                'archivo',
                'tempImage',
                'tempArchivo',
                'imagePreview'
            ]);
            $this->resetValidation();
        } catch (\Exception $e) {
            $this->error('Error al guardar la actividad: ' . $e->getMessage());
        }
    }
}
