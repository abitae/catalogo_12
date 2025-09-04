<?php

namespace App\Livewire\Facturacion;

use App\Models\Facturacion\Sucursal;
use App\Models\Facturacion\Company;
use App\Models\Facturacion\Address;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

class SucursalFacturacionIndex extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public $search = '';
    public $modal_sucursal = false;
    public $editingSucursal = null;
    public $sucursal_id;
    public $name;
    public $ruc;
    public $razonSocial;
    public $nombreComercial;
    public $email;
    public $telephone;
    public $company_id;
    public $isActive = true;
    public $logo_path;
    public $logo_temp;
    public $series_suffix;
    public $direccion;
    public $departamento;
    public $provincia;
    public $distrito;
    public $urbanizacion;
    public $codLocal;
    public $ubigueo;
    public $codigoPais;
    public $codigoSunat;
    public $company_filter = '';

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'ruc' => 'required|string|max:20',
            'razonSocial' => 'required|string|max:255',
            'nombreComercial' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'company_id' => 'required|exists:companies,id',
            'isActive' => 'boolean',
            'logo_path' => 'nullable|string|max:500',
            'logo_temp' => 'nullable|image|max:1024|mimes:jpeg,png,jpg,gif',
            'series_suffix' => 'nullable|string|max:2|regex:/^[0-9]{2}$/',
            // Dirección
            'direccion' => 'required|string|max:255',
            'departamento' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'urbanizacion' => 'nullable|string|max:100',
            'codLocal' => 'nullable|string|max:20',
            'ubigueo' => 'nullable|string|max:20',
            'codigoPais' => 'nullable|string|max:10',
            'codigoSunat' => 'nullable|string|max:20',
        ];
        return $rules;
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.unique' => 'El RUC ya existe.',
            'razonSocial.required' => 'La razón social es obligatoria.',
            'email.email' => 'El email no es válido.',
            'company_id.required' => 'Debe seleccionar una compañía.',
            'company_id.exists' => 'La compañía seleccionada no existe.',
            'direccion.required' => 'La dirección es obligatoria.',
            'codigoPais.string' => 'El código de país debe ser una cadena de texto.',
            'series_suffix.regex' => 'El sufijo de serie debe ser exactamente 2 dígitos numéricos (01-99).',
            'logo_temp.image' => 'El archivo debe ser una imagen válida.',
            'logo_temp.max' => 'La imagen no debe superar 1MB.',
            'logo_temp.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
        ];
    }

    public function render()
    {
        $sucursales = Sucursal::with(['address', 'company'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ruc', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->company_filter, function ($query) {
                $query->where('company_id', $this->company_filter);
            })
            ->orderBy('company_id')
            ->orderBy('id')
            ->paginate(10);
        $companies = Company::orderBy('razonSocial')->get();
        return view('livewire.facturacion.sucursal-facturacion-index', [
            'sucursales' => $sucursales,
            'companies' => $companies,
        ]);
    }

    public function crearSucursal()
    {
        $this->resetForm();
        $this->modal_sucursal = true;
    }

    public function editarSucursal($id)
    {
        $sucursal = Sucursal::with('address')->find($id);
        if (!$sucursal) {
            $this->error('Sucursal no encontrada');
            return;
        }
        $this->editingSucursal = $sucursal;
        $this->sucursal_id = $sucursal->id;
        $this->name = $sucursal->name;
        $this->ruc = $sucursal->ruc;
        $this->razonSocial = $sucursal->razonSocial;
        $this->nombreComercial = $sucursal->nombreComercial;
        $this->email = $sucursal->email;
        $this->telephone = $sucursal->telephone;
        $this->company_id = $sucursal->company_id;
        $this->isActive = $sucursal->isActive;
        $this->logo_path = $sucursal->logo_path;
        $this->logo_temp = null;
        $this->series_suffix = $sucursal->series_suffix;
        // Cargar dirección
        $this->direccion = $sucursal->address->direccion ?? '';
        $this->departamento = $sucursal->address->departamento ?? '';
        $this->provincia = $sucursal->address->provincia ?? '';
        $this->distrito = $sucursal->address->distrito ?? '';
        $this->urbanizacion = $sucursal->address->urbanizacion ?? '';
        $this->codLocal = $sucursal->address->codLocal ?? '';
        $this->ubigueo = $sucursal->address->ubigueo ?? '';
        $this->codigoPais = $sucursal->address->codigoPais ?? '';
        $this->codigoSunat = $sucursal->codigoSunat ?? '';
        $this->modal_sucursal = true;
    }

    public function guardarSucursal()
    {
        $this->validate($this->rules(), $this->messages());

        // Manejar la subida de la imagen
        $logoPath = $this->logo_path;
        if ($this->logo_temp) {
            // Eliminar imagen anterior si existe
            if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
                Storage::disk('public')->delete($this->logo_path);
            }

            // Guardar nueva imagen
            $logoPath = $this->logo_temp->store('logos/sucursales', 'public');
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
        if ($this->editingSucursal && $this->editingSucursal->address) {
            $this->editingSucursal->address->update($addressData);
            $address = $this->editingSucursal->address;
        } else {
            $address = Address::create($addressData);
        }
        $data = [
            'name' => $this->name,
            'ruc' => $this->ruc,
            'razonSocial' => $this->razonSocial,
            'nombreComercial' => $this->nombreComercial,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'company_id' => $this->company_id,
            'isActive' => $this->isActive,
            'logo_path' => $logoPath,
            'series_suffix' => $this->series_suffix,
            'codigoSunat' => $this->codigoSunat,
            'address_id' => $address->id,
        ];
        if ($this->editingSucursal) {
            $this->editingSucursal->update($data);
            $this->success('Sucursal actualizada correctamente');
        } else {
            Sucursal::create($data);
            $this->success('Sucursal creada correctamente');
        }
        $this->cerrarModal();
    }

    public function eliminarSucursal($id)
    {
        $sucursal = Sucursal::find($id);
        if (!$sucursal) {
            $this->error('Sucursal no encontrada');
            return;
        }
        $sucursal->delete();
        $this->success('Sucursal eliminada correctamente');
    }

    public function cerrarModal()
    {
        $this->modal_sucursal = false;
        $this->editingSucursal = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'sucursal_id',
            'name',
            'ruc',
            'razonSocial',
            'nombreComercial',
            'email',
            'telephone',
            'company_id',
            'isActive',
            'logo_path',
            'logo_temp',
            'series_suffix',
            // Dirección
            'direccion',
            'departamento',
            'provincia',
            'distrito',
            'urbanizacion',
            'codLocal',
            'ubigueo',
            'codigoPais',
            'codigoSunat',
        ]);
        $this->resetErrorBag();
    }
    public function limpiarFiltros()
    {
        $this->reset(['search']);
        $this->info('Filtros limpiados correctamente');
    }

    public function updatedCompanyId($value)
    {
        if ($value) {
            $company = Company::find($value);
            if ($company) {
                $this->name = $company->razonSocial . ' - Sucursal';
                $this->ruc = $company->ruc;
                $this->razonSocial = $company->razonSocial;
                $this->nombreComercial = $company->nombreComercial;
            }
        } else {
            $this->name = '';
            $this->ruc = '';
            $this->razonSocial = '';
            $this->nombreComercial = '';
        }
    }

    public function eliminarImagen()
    {
        $this->logo_temp = null;
        $this->logo_path = null;
    }
}
