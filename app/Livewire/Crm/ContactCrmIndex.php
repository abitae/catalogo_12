<?php

namespace App\Livewire\Crm;

use App\Models\Crm\ContactCrm;
use App\Models\Shared\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ContactCrmIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $customer_filter = '';
    public $empresa_filter = '';
    public $es_principal_filter = '';
    public $cargo_filter = '';

    // Modal Form Contacto
    public $modal_form_contacto = false;
    public $modal_form_eliminar_contacto = false;
    public $contacto_id = '';
    public $contacto = null;

    // Variables para el formulario
    public $nombre = '';
    public $apellido = '';
    public $correo = '';
    public $telefono = '';
    public $cargo = '';
    public $empresa = '';
    public $customer_id = '';
    public $notas = '';
    public $es_principal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'customer_filter' => ['except' => ''],
        'empresa_filter' => ['except' => ''],
        'es_principal_filter' => ['except' => ''],
        'cargo_filter' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'notas' => 'nullable|string',
            'es_principal' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'apellido.required' => 'El apellido es requerido',
            'correo.required' => 'El correo es requerido',
            'correo.email' => 'El correo debe ser válido',
            'customer_id.required' => 'El cliente es requerido',
            'customer_id.exists' => 'El cliente seleccionado no existe'
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
            'customer_filter',
            'empresa_filter',
            'es_principal_filter',
            'cargo_filter',
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados correctamente');
    }

    public function render()
    {
        $query = ContactCrm::query()
            ->with(['cliente'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('apellido', 'like', '%' . $this->search . '%')
                        ->orWhere('correo', 'like', '%' . $this->search . '%')
                        ->orWhere('empresa', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->customer_filter, function ($query) {
                $query->where('customer_id', $this->customer_filter);
            })
            ->when($this->empresa_filter, function ($query) {
                $query->where('empresa', 'like', '%' . $this->empresa_filter . '%');
            })
            ->when($this->es_principal_filter !== '', function ($query) {
                $query->where('es_principal', $this->es_principal_filter);
            })
            ->when($this->cargo_filter, function ($query) {
                $query->where('cargo', 'like', '%' . $this->cargo_filter . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.crm.contact-crm-index', [
            'contactos' => $query->paginate($this->perPage),
            'customers' => Customer::all()
        ]);
    }

    public function nuevoContacto()
    {
        $this->reset([
            'contacto_id',
            'contacto',
            'nombre',
            'apellido',
            'correo',
            'telefono',
            'cargo',
            'empresa',
            'customer_id',
            'notas',
            'es_principal'
        ]);
        $this->es_principal = false;
        $this->resetValidation();
        $this->modal_form_contacto = true;
    }

    public function editarContacto($id)
    {
        $this->contacto_id = $id;
        $this->contacto = ContactCrm::find($id);
        $this->nombre = $this->contacto->nombre;
        $this->apellido = $this->contacto->apellido;
        $this->correo = $this->contacto->correo;
        $this->telefono = $this->contacto->telefono;
        $this->cargo = $this->contacto->cargo;
        $this->empresa = $this->contacto->empresa;
        $this->customer_id = $this->contacto->customer_id;
        $this->notas = $this->contacto->notas;
        $this->es_principal = (bool) $this->contacto->es_principal;

        $this->modal_form_contacto = true;
    }

    public function eliminarContacto($id)
    {
        $this->contacto_id = $id;
        $this->contacto = ContactCrm::find($id);
        if ($this->contacto) {
            $this->modal_form_eliminar_contacto = true;
        }
    }

    public function confirmarEliminarContacto()
    {
        try {
            $this->contacto->delete();
            $this->modal_form_eliminar_contacto = false;
            $this->reset(['contacto_id', 'contacto']);
            $this->success('Contacto eliminado correctamente');
        } catch (\Exception $e) {
            $this->error('Error al eliminar el contacto: ' . $e->getMessage());
        }
    }

    public function guardarContacto()
    {
        $data = $this->validate();

        // Asignar automáticamente la fecha actual
        $data['ultima_fecha_contacto'] = now();

        // Asegurar que es_principal sea boolean
        $data['es_principal'] = (bool) $data['es_principal'];

        // Asegurar que customer_id sea integer
        $data['customer_id'] = (int) $data['customer_id'];

        try {
            if ($this->contacto_id) {
                $contacto = ContactCrm::find($this->contacto_id);
                $contacto->update($data);
                $this->success('Contacto actualizado correctamente');
            } else {
                ContactCrm::create($data);
                $this->success('Contacto creado correctamente');
            }

            $this->modal_form_contacto = false;
            $this->reset([
                'contacto_id',
                'contacto',
                'nombre',
                'apellido',
                'correo',
                'telefono',
                'cargo',
                'empresa',
                'customer_id',
                'notas',
                'es_principal'
            ]);
            $this->es_principal = false;
            $this->resetValidation();
        } catch (\Exception $e) {
            $this->error('Error al guardar el contacto: ' . $e->getMessage());
        }
    }
}
