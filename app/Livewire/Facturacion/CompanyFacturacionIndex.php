<?php

namespace App\Livewire\Facturacion;

use App\Models\Facturacion\Company;
use App\Models\Facturacion\Address;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;

class CompanyFacturacionIndex extends Component
{
    use WithPagination, Toast, WithFileUploads;

    public $search = '';
    public $modal_company = false;
    public $editingCompany = null;
    public $company_id;
    public $ruc;
    public $razonSocial;
    public $nombreComercial;
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
    public $ctaBanco;
    public $nroMtc;
    public $logo_path;
    public $sol_user;
    public $sol_pass;
    public $cert_path;
    public $client_id;
    public $client_secret;
    public $isProduction = false;
    public $isActive = true;
    public $logoFile;
    public $certFile;
    public $company_active_filter = '';
    public $company_production_filter = '';
    public $inicio_suscripcion;
    public $fin_suscripcion;
    public $inicio_produccion;
    public $fin_produccion;

    protected function rules()
    {
        $rules = [
            'ruc' => 'required|string|max:20|unique:companies,ruc' . ($this->editingCompany ? ',' . $this->editingCompany->id : ''),
            'razonSocial' => 'required|string|max:255',
            'nombreComercial' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            // Dirección
            'direccion' => 'required|string|max:255',
            'departamento' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'distrito' => 'required|string|max:100',
            'urbanizacion' => 'required|string|max:100',
            'codLocal' => 'required|string|max:20',
            'ubigueo' => 'required|string|max:20',
            'codigoPais' => 'required|string|max:10',
            // Otros campos
            'ctaBanco' => 'nullable|string|max:50',
            'nroMtc' => 'nullable|string|max:50',
            'logoFile' => 'nullable|image|max:2048',
            'logo_path' => 'nullable|string|max:255',
            'sol_user' => 'nullable|string|max:100',
            'sol_pass' => 'nullable|string|max:100',
            'cert_path' => 'nullable|string|max:255',
            'client_id' => 'nullable|string|max:100',
            'client_secret' => 'nullable|string|max:100',
            'isProduction' => 'boolean',
            'isActive' => 'boolean',
            'inicio_suscripcion' => 'nullable|date',
            'fin_suscripcion' => 'nullable|date|after_or_equal:inicio_suscripcion',
            'inicio_produccion' => 'nullable|date',
            'fin_produccion' => 'nullable|date|after_or_equal:inicio_produccion',
            'certFile' => 'nullable|file|max:4096',
        ];
        return $rules;
    }

    protected function messages()
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.unique' => 'El RUC ya existe.',
            'razonSocial.required' => 'La razón social es obligatoria.',
            'email.email' => 'El email no es válido.',
            'direccion.required' => 'La dirección es obligatoria.',
            'codigoPais.required' => 'El código de país es obligatorio.',
            'inicio_suscripcion.date' => 'La fecha de inicio de suscripción es obligatoria.',
            'fin_suscripcion.date' => 'La fecha de fin de suscripción es obligatoria.',
            'fin_suscripcion.after_or_equal' => 'La fecha de fin de suscripción debe ser mayor o igual a la fecha de inicio de suscripción.',
            'inicio_produccion.date' => 'La fecha de inicio de producción es obligatoria.',
            'fin_produccion.date' => 'La fecha de fin de producción es obligatoria.',
            'fin_produccion.after_or_equal' => 'La fecha de fin de producción debe ser mayor o igual a la fecha de inicio de producción.',
        ];
    }

    public function render()
    {
        $companies = Company::with('address')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ruc', 'like', '%' . $this->search . '%')
                        ->orWhere('razonSocial', 'like', '%' . $this->search . '%')
                        ->orWhere('nombreComercial', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->company_active_filter !== '', function ($query) {
                $query->where('isActive', $this->company_active_filter);
            })
            ->when($this->company_production_filter !== '', function ($query) {
                $query->where('isProduction', $this->company_production_filter);
            })
            ->latest()
            ->orderBy('razonSocial')
            ->paginate(10);
        return view('livewire.facturacion.company-facturacion-index', [
            'companies' => $companies,
        ]);
    }

    public function crearCompany()
    {
        $this->resetForm();
        $this->modal_company = true;
    }

    public function editarCompany($id)
    {
        $company = Company::with('address')->find($id);
        if (!$company) {
            $this->error('Compañía no encontrada');
            return;
        }
        $this->editingCompany = $company;
        $this->company_id = $company->id;
        $this->ruc = $company->ruc;
        $this->razonSocial = $company->razonSocial;
        $this->nombreComercial = $company->nombreComercial;
        $this->email = $company->email;
        $this->telephone = $company->telephone;
        $this->ctaBanco = $company->ctaBanco;
        $this->nroMtc = $company->nroMtc;
        $this->logo_path = $company->logo_path;
        $this->sol_user = $company->sol_user;
        $this->sol_pass = $company->sol_pass;
        $this->cert_path = $company->cert_path;
        $this->client_id = $company->client_id;
        $this->client_secret = $company->client_secret;
        $this->isProduction = $company->isProduction;
        $this->isActive = $company->isActive;
        $this->inicio_suscripcion = $company->inicio_suscripcion;
        $this->fin_suscripcion = $company->fin_suscripcion;
        $this->inicio_produccion = $company->inicio_produccion;
        $this->fin_produccion = $company->fin_produccion;
        // Cargar dirección
        $this->direccion = $company->address->direccion ?? '';
        $this->departamento = $company->address->departamento ?? '';
        $this->provincia = $company->address->provincia ?? '';
        $this->distrito = $company->address->distrito ?? '';
        $this->urbanizacion = $company->address->urbanizacion ?? '';
        $this->codLocal = $company->address->codLocal ?? '';
        $this->ubigueo = $company->address->ubigueo ?? '';
        $this->codigoPais = $company->address->codigoPais ?? '';
        $this->modal_company = true;
    }

    public function guardarCompany()
    {
        $this->validate($this->rules(), $this->messages());
        // Subir logo si se seleccionó
        if ($this->logoFile) {
            $this->logo_path = $this->logoFile->store('companies/logos', 'public');
        }
        // Subir certificado si se seleccionó
        if ($this->certFile) {
            $this->cert_path = $this->certFile->store('companies/certs', 'public');
        }
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
        if ($this->editingCompany && $this->editingCompany->address) {
            $this->editingCompany->address->update($addressData);
            $address = $this->editingCompany->address;
        } else {
            $address = Address::create($addressData);
        }
        $data = [
            'ruc' => $this->ruc,
            'razonSocial' => $this->razonSocial,
            'nombreComercial' => $this->nombreComercial,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'address_id' => $address->id,
            'ctaBanco' => $this->ctaBanco,
            'nroMtc' => $this->nroMtc,
            'logo_path' => $this->logo_path,
            'sol_user' => $this->sol_user,
            'sol_pass' => $this->sol_pass,
            'cert_path' => $this->cert_path,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'isProduction' => $this->isProduction,
            'isActive' => $this->isActive,
            'inicio_suscripcion' => $this->inicio_suscripcion ? \Carbon\Carbon::parse($this->inicio_suscripcion) : null,
            'fin_suscripcion' => $this->fin_suscripcion ? \Carbon\Carbon::parse($this->fin_suscripcion) : null,
            'inicio_produccion' => $this->inicio_produccion ? \Carbon\Carbon::parse($this->inicio_produccion) : null,
            'fin_produccion' => $this->fin_produccion ? \Carbon\Carbon::parse($this->fin_produccion) : null,
        ];
        if ($this->editingCompany) {
            $this->editingCompany->update($data);
            $this->success('Compañía actualizada correctamente');
        } else {
            Company::create($data);
            $this->success('Compañía creada correctamente');
        }
        $this->cerrarModal();
    }

    public function eliminarCompany($id)
    {
        $company = Company::find($id);
        if (!$company) {
            $this->error('Compañía no encontrada');
            return;
        }
        $company->delete();
        $this->success('Compañía eliminada correctamente');
    }

    public function cerrarModal()
    {
        $this->modal_company = false;
        $this->editingCompany = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'company_id',
            'ruc',
            'razonSocial',
            'nombreComercial',
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
            // Nuevos campos
            'ctaBanco',
            'nroMtc',
            'logo_path',
            'logoFile',
            'sol_user',
            'sol_pass',
            'cert_path',
            'client_id',
            'client_secret',
            'isProduction',
            'isActive',
            'inicio_suscripcion',
            'fin_suscripcion',
            'inicio_produccion',
            'fin_produccion',
            'certFile',
        ]);
        $this->resetErrorBag();
    }
    public function limpiarFiltros()
    {
        $this->reset(['search']);
        $this->info('Filtros limpiados correctamente');
    }
}
