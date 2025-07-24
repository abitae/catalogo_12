<?php

namespace App\Livewire\Facturacion;

use App\Models\Facturacion\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ClientFacturacionIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $modal_client = false;
    public $editingClient = null;
    public $client_id;
    public $tipoDoc;
    public $numDoc;
    public $rznSocial;
    public $email;
    public $telephone;
    public $direccion;
    public $departamento;
    public $provincia;
    public $distrito;
    public $urbanizacion;
    public $codLocal;
    public $ubigueo;
    public $codigoPais;

    protected function rules()
    {
        $rules = [
            'tipoDoc' => 'required|string|max:10',
            'numDoc' => 'required|string|max:20|unique:clients,numDoc' . ($this->editingClient ? ',' . $this->editingClient->id : ''),
            'rznSocial' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            // Dirección
            'direccion' => 'required|string|max:255',
            'departamento' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'urbanizacion' => 'nullable|string|max:100',
            'codLocal' => 'nullable|string|max:20',
            'ubigueo' => 'nullable|string|max:20',
            'codigoPais' => 'nullable|string|max:10',
        ];
        return $rules;
    }

    protected function messages()
    {
        return [
            'tipoDoc.required' => 'El tipo de documento es obligatorio.',
            'numDoc.required' => 'El número de documento es obligatorio.',
            'numDoc.unique' => 'El número de documento ya existe.',
            'rznSocial.required' => 'La razón social es obligatoria.',
            'email.email' => 'El email no es válido.',
        ];
    }

    public function render()
    {
        $clients = Client::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('numDoc', 'like', '%' . $this->search . '%')
                    ->orWhere('rznSocial', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        })
            ->orderBy('rznSocial')
            ->paginate(10);
        return view('livewire.facturacion.client-facturacion-index', [
            'clients' => $clients,
        ]);
    }

    public function crearClient()
    {
        $this->resetForm();
        $this->modal_client = true;
    }

    public function editarClient($id)
    {
        $client = Client::with('address')->find($id);
        if (!$client) {
            $this->error('Cliente no encontrado');
            return;
        }
        $this->editingClient = $client;
        $this->client_id = $client->id;
        $this->tipoDoc = $client->tipoDoc;
        $this->numDoc = $client->numDoc;
        $this->rznSocial = $client->rznSocial;
        $this->email = $client->email;
        $this->telephone = $client->telephone;
        // Cargar dirección
        $this->direccion = $client->address->direccion ?? '';
        $this->departamento = $client->address->departamento ?? '';
        $this->provincia = $client->address->provincia ?? '';
        $this->distrito = $client->address->distrito ?? '';
        $this->urbanizacion = $client->address->urbanizacion ?? '';
        $this->codLocal = $client->address->codLocal ?? '';
        $this->ubigueo = $client->address->ubigueo ?? '';
        $this->codigoPais = $client->address->codigoPais ?? '';
        $this->modal_client = true;
    }

    public function guardarClient()
    {
        $this->validate($this->rules(), $this->messages());
        // Crear o actualizar dirección
        $addressData = [
            'direccion' => $this->direccion,
            'departamento' => $this->departamento,
            'provincia' => $this->provincia,
            'distrito' => $this->distrito,
            'urbanizacion' => $this->urbanizacion,
            'codLocal' => $this->codLocal,
            'ubigueo' => $this->ubigueo,
            'codigoPais' => $this->codigoPais,
        ];
        if ($this->editingClient && $this->editingClient->address) {
            $this->editingClient->address->update($addressData);
            $address = $this->editingClient->address;
        } else {
            $address = \App\Models\Facturacion\Address::create($addressData);
        }
        $data = [
            'tipoDoc' => $this->tipoDoc,
            'numDoc' => $this->numDoc,
            'rznSocial' => $this->rznSocial,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'address_id' => $address->id,
        ];
        if ($this->editingClient) {
            $this->editingClient->update($data);
            $this->success('Cliente actualizado correctamente');
        } else {
            Client::create($data);
            $this->success('Cliente creado correctamente');
        }
        $this->cerrarModal();
    }

    public function eliminarClient($id)
    {
        $client = Client::find($id);
        if (!$client) {
            $this->error('Cliente no encontrado');
            return;
        }
        $client->delete();
        $this->success('Cliente eliminado correctamente');
    }

    public function cerrarModal()
    {
        $this->modal_client = false;
        $this->editingClient = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'client_id',
            'tipoDoc',
            'numDoc',
            'rznSocial',
            'email',
            'telephone',
            // Dirección
            'direccion',
            'departamento',
            'provincia',
            'distrito',
            'urbanizacion',
            'codLocal',
            'ubigueo',
            'codigoPais',
        ]);
        $this->resetErrorBag();
    }
    public function limpiarFiltros()
    {
        $this->reset(['search']);
        $this->info('Filtros limpiados correctamente');
    }
}
