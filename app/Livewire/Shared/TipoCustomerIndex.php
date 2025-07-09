<?php

namespace App\Livewire\Shared;

use App\Models\Shared\TipoCustomer;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class TipoCustomerIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal Form Tipo Customer
    public $modal_form_tipo_customer = false;
    public $modal_form_eliminar_tipo_customer = false;
    public $tipo_customer_id = '';
    public $tipo_customer = null;

    // Variables para el formulario
    public $nombre = '';
    public $descripcion = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:tipo_customers,nombre,' . $this->tipo_customer_id,
            'descripcion' => 'nullable|string|max:1000',
        ];
    }

    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'nombre.unique' => 'Este nombre ya está registrado',
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
            'perPage'
        ]);
        $this->resetPage();
        $this->info('Filtros limpiados');
    }

    public function render()
    {
        $query = TipoCustomer::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.shared.tipo-customer-index', [
            'tipos_customer' => $query->paginate($this->perPage),
        ]);
    }

    public function nuevoTipoCustomer()
    {
        $this->resetForm();
        $this->modal_form_tipo_customer = true;
    }

    public function editarTipoCustomer($id)
    {
        $this->tipo_customer_id = $id;
        $this->tipo_customer = TipoCustomer::find($id);

        if ($this->tipo_customer) {
            $this->nombre = $this->tipo_customer->nombre;
            $this->descripcion = $this->tipo_customer->descripcion;
        }

        $this->modal_form_tipo_customer = true;
    }

    public function eliminarTipoCustomer($id)
    {
        $this->tipo_customer_id = $id;
        $this->tipo_customer = TipoCustomer::find($id);
        if ($this->tipo_customer) {
            $this->modal_form_eliminar_tipo_customer = true;
        }
    }

    public function confirmarEliminarTipoCustomer()
    {
        if ($this->tipo_customer) {
            $this->tipo_customer->delete();
            $this->success('Tipo de cliente eliminado correctamente');
        } else {
            $this->error('No se encontró el tipo de cliente a eliminar');
        }

        $this->modal_form_eliminar_tipo_customer = false;
        $this->reset(['tipo_customer_id', 'tipo_customer']);
    }

    private function resetForm()
    {
        $this->reset([
            'tipo_customer_id',
            'tipo_customer',
            'nombre',
            'descripcion'
        ]);
        $this->resetValidation();
    }

    public function guardarTipoCustomer()
    {
        try {
            $data = $this->validate();

            if ($this->tipo_customer_id) {
                $tipo_customer = TipoCustomer::find($this->tipo_customer_id);
                $tipo_customer->update($data);
                $this->success('Tipo de cliente actualizado correctamente');
            } else {
                TipoCustomer::create($data);
                $this->success('Tipo de cliente creado correctamente');
            }

            $this->modal_form_tipo_customer = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('Error al guardar el tipo de cliente: ' . $e->getMessage());
        }
    }
}
