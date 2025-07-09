<?php

namespace App\Livewire\Shared;

use App\Models\Shared\Customer;
use App\Models\Shared\TipoCustomer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class CustomerIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public $search = '';
    public $sortField = 'rznSocial';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $tipo_customer_filter = '';
    public $tipo_doc_filter = '';

    // Modal Form Customer
    public $modal_form_customer = false;
    public $modal_form_eliminar_customer = false;
    public $customer_id = '';
    public $customer = null;

    // Variables para el formulario
    public $tipoDoc = '';
    public $numDoc = '';
    public $rznSocial = '';
    public $nombreComercial = '';
    public $email = '';
    public $telefono = '';
    public $direccion = '';
    public $codigoPostal = '';
    public $notas = '';
    public $tipo_customer_id = '';

    // Manejo de archivos
    public $tempImage = null;
    public $tempArchivo = null;
    public $imagePreview = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'tipo_customer_filter' => ['except' => ''],
        'tipo_doc_filter' => ['except' => ''],
        'sortField' => ['except' => 'rznSocial'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'tipoDoc' => 'required|string|max:10',
            'numDoc' => 'required|string|max:20|unique:customers,numDoc,' . $this->customer_id,
            'rznSocial' => 'required|string|max:255',
            'nombreComercial' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'codigoPostal' => 'nullable|string|max:10',
            'notas' => 'nullable|string|max:1000',
            'tipo_customer_id' => 'nullable|exists:tipo_customers,id',
            'tempImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ];
    }

    protected function messages()
    {
        return [
            'tipoDoc.required' => 'El tipo de documento es requerido',
            'numDoc.required' => 'El número de documento es requerido',
            'numDoc.unique' => 'Este número de documento ya está registrado',
            'rznSocial.required' => 'La razón social es requerida',
            'email.email' => 'El email debe tener un formato válido',
            'tipo_customer_id.exists' => 'El tipo de cliente seleccionado no existe',
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

    public function updatingTipoCustomerFilter()
    {
        $this->resetPage();
    }

    public function updatingTipoDocFilter()
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
            'tipo_customer_filter',
            'tipo_doc_filter',
            'sortField',
            'sortDirection',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados');
    }

    public function render()
    {
        $query = Customer::with('tipoCustomer')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('rznSocial', 'like', '%' . $this->search . '%')
                        ->orWhere('nombreComercial', 'like', '%' . $this->search . '%')
                        ->orWhere('numDoc', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->tipo_customer_filter, function ($query) {
                $query->where('tipo_customer_id', $this->tipo_customer_filter);
            })
            ->when($this->tipo_doc_filter, function ($query) {
                $query->where('tipoDoc', $this->tipo_doc_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.shared.customer-index', [
            'customers' => $query->paginate($this->perPage),
            'tipos_customer' => TipoCustomer::all(),
            'tipos_doc' => Customer::distinct()->pluck('tipoDoc')->filter(),
        ]);
    }

    public function nuevoCustomer()
    {
        $this->resetForm();
        $this->modal_form_customer = true;
    }

    public function editarCustomer($id)
    {
        $this->customer_id = $id;
        $this->customer = Customer::find($id);

        if ($this->customer) {
            $this->tipoDoc = $this->customer->tipoDoc;
            $this->numDoc = $this->customer->numDoc;
            $this->rznSocial = $this->customer->rznSocial;
            $this->nombreComercial = $this->customer->nombreComercial;
            $this->email = $this->customer->email;
            $this->telefono = $this->customer->telefono;
            $this->direccion = $this->customer->direccion;
            $this->codigoPostal = $this->customer->codigoPostal;
            $this->notas = $this->customer->notas;
            $this->tipo_customer_id = $this->customer->tipo_customer_id;

            if ($this->customer->image) {
                $this->imagePreview = Storage::url($this->customer->image);
            }
        }

        $this->modal_form_customer = true;
    }

    public function eliminarCustomer($id)
    {
        $this->customer_id = $id;
        $this->customer = Customer::find($id);
        if ($this->customer) {
            $this->modal_form_eliminar_customer = true;
        }
    }

    public function confirmarEliminarCustomer()
    {
        if ($this->customer) {
            // Eliminar archivos si existen
            if ($this->customer->image && Storage::disk('public')->exists($this->customer->image)) {
                Storage::disk('public')->delete($this->customer->image);
            }
            if ($this->customer->archivo && Storage::disk('public')->exists($this->customer->archivo)) {
                Storage::disk('public')->delete($this->customer->archivo);
            }

            $this->customer->delete();
            $this->success('Cliente eliminado correctamente');
        } else {
            $this->error('No se encontró el cliente a eliminar');
        }

        $this->modal_form_eliminar_customer = false;
        $this->reset(['customer_id', 'customer']);
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

    private function resetForm()
    {
        $this->reset([
            'customer_id',
            'customer',
            'tipoDoc',
            'numDoc',
            'rznSocial',
            'nombreComercial',
            'email',
            'telefono',
            'direccion',
            'codigoPostal',
            'notas',
            'tipo_customer_id',
            'tempImage',
            'tempArchivo',
            'imagePreview'
        ]);
        $this->resetValidation();
    }

    public function guardarCustomer()
    {
        $data = $this->validate();

        // Manejar imagen
        if ($this->tempImage) {
            // Eliminar imagen anterior si existe
            if ($this->customer_id && $this->customer && $this->customer->image) {
                Storage::disk('public')->delete($this->customer->image);
            }

            $imagePath = $this->tempImage->store('customers/images', 'public');
            $data['image'] = $imagePath;
        }

        // Manejar archivo
        if ($this->tempArchivo) {
            // Eliminar archivo anterior si existe
            if ($this->customer_id && $this->customer && $this->customer->archivo) {
                Storage::disk('public')->delete($this->customer->archivo);
            }

            $archivoPath = $this->tempArchivo->store('customers/archivos', 'public');
            $data['archivo'] = $archivoPath;
        }

        // Remover campos temporales
        unset($data['tempImage'], $data['tempArchivo']);

        try {
            if ($this->customer_id) {
                $customer = Customer::find($this->customer_id);
                $customer->update($data);
                $this->success('Cliente actualizado correctamente');
            } else {
                Customer::create($data);
                $this->success('Cliente creado correctamente');
            }

            $this->modal_form_customer = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el cliente: ' . $e->getMessage());
        }
    }
}
