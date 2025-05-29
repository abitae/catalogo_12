<?php

namespace App\Livewire\Catalogo;

use App\Exports\ProducCatalogoExport;
use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ProductoCatalogoIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $brand_filter = '';
    public $category_filter = '';
    public $line_filter = '';
    public $stock_status = '';
    public $isActive_filter = '';

    // Modal Form Producto
    public $modal_form_producto = false;
    public $modal_form_eliminar_producto = false;
    public $producto_id = '';
    public $producto = null;
    public $productosExportar = null;

    // Variables para el formulario
    public $brand_id = '';
    public $category_id = '';
    public $line_id = '';
    public $code = '';
    public $code_fabrica = '';
    public $code_peru = '';
    public $price_compra = '';
    public $price_venta = '';
    public $stock = '';
    public $dias_entrega = '';
    public $description = '';
    public $garantia = '';
    public $observaciones = '';
    public $image = '';
    public $archivo = '';
    public $archivo2 = '';
    public $caracteristicas = '';
    public $isActive = true;

    // Propiedades para manejo de archivos
    public $tempImage = null;
    public $tempArchivo = null;
    public $tempArchivo2 = null;
    public $imagePreview = null;
    public $archivoPreview = null;
    public $archivo2Preview = null;


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
            'brand_filter',
            'category_filter',
            'line_filter',
            'perPage',
            'stock_status',
            'isActive_filter'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = ProductoCatalogo::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('code_fabrica', 'like', '%' . $this->search . '%')
                        ->orWhere('code_peru', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->brand_filter, function ($query) {
                $query->where('brand_id', $this->brand_filter);
            })
            ->when($this->category_filter, function ($query) {
                $query->where('category_id', $this->category_filter);
            })
            ->when($this->line_filter, function ($query) {
                $query->where('line_id', $this->line_filter);
            })
            ->when($this->stock_status, function ($query) {
                if ($this->stock_status === 'in_stock') {
                    $query->where('stock', '>', 0);
                } elseif ($this->stock_status === 'out_of_stock') {
                    $query->where('stock', '=', 0);
                }
            })
            ->when($this->isActive_filter !== '', function ($query) {
                $query->where('isActive', $this->isActive_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $this->productosExportar = $query->get();

        return view('livewire.catalogo.producto-catalogo-index', [
            'productos' => $query->paginate($this->perPage),
            'brands' => BrandCatalogo::where('isActive', true)->get(),
            'categories' => CategoryCatalogo::where('isActive', true)->get(),
            'lines' => LineCatalogo::where('isActive', true)->get(),
        ]);
    }

    public function nuevoProducto()
    {
        $this->modal_form_producto = true;
    }

    public function editarProducto($id)
    {
        $this->producto_id = $id;
        $this->producto = ProductoCatalogo::find($id);
        $this->brand_id = $this->producto->brand_id;
        $this->category_id = $this->producto->category_id;
        $this->line_id = $this->producto->line_id;
        $this->code = $this->producto->code;
        $this->code_fabrica = $this->producto->code_fabrica;
        $this->code_peru = $this->producto->code_peru;
        $this->price_compra = $this->producto->price_compra;
        $this->price_venta = $this->producto->price_venta;
        $this->stock = $this->producto->stock;
        $this->dias_entrega = $this->producto->dias_entrega;
        $this->description = $this->producto->description;
        $this->garantia = $this->producto->garantia;
        $this->observaciones = $this->producto->observaciones;
        $this->image = $this->producto->image;
        $this->archivo = $this->producto->archivo;
        $this->archivo2 = $this->producto->archivo2;
        $this->caracteristicas = $this->producto->caracteristicas;
        $this->isActive = $this->producto->isActive;

        // Establecer las vistas previas
        if ($this->image) {
            $this->imagePreview = asset('storage/' . $this->image);
        }
        if ($this->archivo) {
            $this->archivoPreview = basename($this->archivo);
        }
        if ($this->archivo2) {
            $this->archivo2Preview = basename($this->archivo2);
        }

        $this->modal_form_producto = true;
    }

    public function eliminarProducto($id)
    {
        $this->producto_id = $id;
        $this->producto = ProductoCatalogo::find($id);
        if ($this->producto) {
            $this->modal_form_eliminar_producto = true;
        }
    }

    public function confirmarEliminarProducto()
    {
        if ($this->producto->image && Storage::exists($this->producto->image)) {
            Storage::delete($this->producto->image);
        }
        if ($this->producto->archivo && Storage::exists($this->producto->archivo)) {
            Storage::delete($this->producto->archivo);
        }
        if ($this->producto->archivo2 && Storage::exists($this->producto->archivo2)) {
            Storage::delete($this->producto->archivo2);
        }
        $this->producto->delete();
        $this->modal_form_eliminar_producto = false;
        $this->reset(['producto_id', 'producto']);
    }

    public function updatedTempImage()
    {
        $this->validate([
            'tempImage' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480'
        ]);

        if ($this->tempImage) {
            $this->imagePreview = $this->tempImage->temporaryUrl();
        }
    }

    public function updatedTempArchivo()
    {
        $this->validate([
            'tempArchivo' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480'
        ]);

        if ($this->tempArchivo) {
            $this->archivoPreview = $this->tempArchivo->getClientOriginalName();
        }
    }

    public function updatedTempArchivo2()
    {
        $this->validate([
            'tempArchivo2' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480'
        ]);

        if ($this->tempArchivo2) {
            $this->archivo2Preview = $this->tempArchivo2->getClientOriginalName();
        }
    }

    public function removeImage()
    {
        if ($this->image && Storage::exists($this->image)) {
            Storage::delete($this->image);
        }
        $this->image = null;
        $this->tempImage = null;
        $this->imagePreview = null;
    }

    public function removeArchivo()
    {
        if ($this->archivo && Storage::exists($this->archivo)) {
            Storage::delete($this->archivo);
        }
        $this->archivo = null;
        $this->tempArchivo = null;
        $this->archivoPreview = null;
    }

    public function removeArchivo2()
    {
        if ($this->archivo2 && Storage::exists($this->archivo2)) {
            Storage::delete($this->archivo2);
        }
        $this->archivo2 = null;
        $this->tempArchivo2 = null;
        $this->archivo2Preview = null;
    }

    public function guardarProducto()
    {

        $ruleUniqueCode = $this->producto_id ? 'unique:producto_catalogos,code,' . $this->producto_id : 'unique:producto_catalogos,code';


        // validaciones
        $rules = [
            'brand_id' => 'required|exists:brand_catalogos,id',
            'category_id' => 'required|exists:category_catalogos,id',
            'line_id' => 'required|exists:line_catalogos,id',
            'code' => $ruleUniqueCode,
            'code_fabrica' => 'required|string|max:255',
            'code_peru' => 'required|string|max:255',
            'price_compra' => 'required|numeric',
            'price_venta' => 'required|numeric',
            'stock' => 'required|numeric',
            'dias_entrega' => 'nullable|numeric',
            'description' => 'required|string|max:255',
            'garantia' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:255',
            'tempImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'tempArchivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'tempArchivo2' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'isActive' => 'boolean',
        ];

        $messages = [
            'brand_id.required' => 'Por favor, seleccione una marca',
            'brand_id.exists' => 'La marca seleccionada no es válida',
            'category_id.required' => 'Por favor, seleccione una categoría',
            'category_id.exists' => 'La categoría seleccionada no es válida',
            'line_id.required' => 'Por favor, seleccione una línea',
            'line_id.exists' => 'La línea seleccionada no es válida',
            'code.required' => 'Por favor, ingrese el código del producto',
            'code.unique' => 'Este código ya está registrado en el sistema',
            'code_fabrica.required' => 'Por favor, ingrese el código de fábrica',
            'code_peru.required' => 'Por favor, ingrese el código Perú',
            'price_compra.required' => 'Por favor, ingrese el precio de compra',
            'price_venta.required' => 'Por favor, ingrese el precio de venta',
            'stock.required' => 'Por favor, ingrese la cantidad en stock',
            'description.required' => 'Por favor, ingrese la descripción del producto',
            'isActive.required' => 'Por favor, seleccione el estado del producto',
            'isActive.boolean' => 'El estado seleccionado no es válido',
            'tempImage.image' => 'El archivo debe ser una imagen válida',
            'tempImage.mimes' => 'La imagen debe ser en formato: jpeg, png, jpg, gif o svg',
            'tempImage.max' => 'La imagen no debe superar los 20MB',
            'tempArchivo.file' => 'El archivo adjunto no es válido',
            'tempArchivo.mimes' => 'El archivo debe ser en formato: pdf, doc, docx, xls, xlsx, ppt o pptx',
            'tempArchivo.max' => 'El archivo no debe superar los 20MB',
            'tempArchivo2.file' => 'El segundo archivo adjunto no es válido',
            'tempArchivo2.mimes' => 'El segundo archivo debe ser en formato: pdf, doc, docx, xls, xlsx, ppt o pptx',
            'tempArchivo2.max' => 'El segundo archivo no debe superar los 20MB',
            'observaciones.string' => 'Las observaciones deben ser texto',
            'observaciones.max' => 'Las observaciones no deben exceder los 255 caracteres',
            'dias_entrega.numeric' => 'Los días de entrega deben ser un número',
            'dias_entrega.max' => 'Los días de entrega no deben exceder los 255 caracteres',
            'garantia.string' => 'La garantía debe ser texto',
            'garantia.max' => 'La garantía no debe exceder los 255 caracteres'
        ];

        $data = $this->validate($rules, $messages);

        // Procesar imagen
        if ($this->tempImage) {
            // Eliminar imagen anterior si existe
            if ($this->image && Storage::exists($this->image)) {
                Storage::delete($this->image);
            }
            $imagePath = $this->tempImage->store('productos/images', 'public');
            $data['image'] = $imagePath;
        }

        // Procesar archivo 1
        if ($this->tempArchivo) {
            // Eliminar archivo anterior si existe
            if ($this->archivo && Storage::exists($this->archivo)) {
                Storage::delete($this->archivo);
            }
            $archivoPath = $this->tempArchivo->store('productos/archivos', 'public');
            $data['archivo'] = $archivoPath;
        }

        // Procesar archivo 2
        if ($this->tempArchivo2) {
            // Eliminar archivo anterior si existe
            if ($this->archivo2 && Storage::exists($this->archivo2)) {
                Storage::delete($this->archivo2);
            }
            $archivo2Path = $this->tempArchivo2->store('productos/archivos', 'public');
            $data['archivo2'] = $archivo2Path;
        }

        if ($this->producto_id) {
            $producto = ProductoCatalogo::find($this->producto_id);
            $producto->update($data);
        } else {
            ProductoCatalogo::create($data);
        }

        $this->modal_form_producto = false;
        $this->reset([
            'producto_id',
            'producto',
            'tempImage',
            'tempArchivo',
            'tempArchivo2',
            'imagePreview',
            'archivoPreview',
            'archivo2Preview'
        ]);
        $this->resetValidation();
    }

    public function exportarProductos()
    {
        return Excel::download(new ProducCatalogoExport($this->productosExportar), 'productos_' . date('Y-m-d_H-i-s') . '.xlsx');
        $this->reset(['productosExportar']);
    }
}
