<?php

namespace App\Livewire\Catalogo;

use App\Exports\ProducCatalogoExport;
use App\Models\Catalogo\BrandCatalogo;
use App\Models\Catalogo\CategoryCatalogo;
use App\Models\Catalogo\LineCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use App\Traits\FileUploadTrait;
use App\Traits\NotificationTrait;
use App\Traits\TableTrait;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class ProductoCatalogoIndex extends Component
{
    use TableTrait, FileUploadTrait, NotificationTrait, Toast;

    // Filtros específicos de productos
    public $brand_filter = '';
    public $category_filter = '';
    public $line_filter = '';
    public $stock_status = '';
    public $isActive_filter = '';
    public $price_range = '';

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

    // Configuración de búsqueda
    protected $searchFields = ['code', 'code_fabrica', 'code_peru', 'description'];

    protected $listeners = [
        'addCaracteristica' => 'addCaracteristica',
        'removeCaracteristica' => 'removeCaracteristica',
    ];

    public function mount()
    {
        $this->sortField = 'code';
        if (!is_array($this->caracteristicas)) {
            $this->caracteristicas = [];
        }
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'sortDirection',
            'brand_filter',
            'category_filter',
            'line_filter',
            'perPage',
            'stock_status',
            'isActive_filter',
            'price_range'
        ]);
        $this->sortField = 'code';
        $this->resetPage();
        $this->info('Filtros limpiados');
    }

    public function render()
    {
        $query = ProductoCatalogo::with(['brand', 'category', 'line'])
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
            ->when($this->price_range, function ($query) {
                $this->applyPriceRangeFilter($query);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $this->productosExportar = $query->get();

        // Estadísticas rápidas
        $total = ProductoCatalogo::count();
        $activos = ProductoCatalogo::where('isActive', 1)->count();
        $inactivos = ProductoCatalogo::where('isActive', 0)->count();
        $stock_bajo = ProductoCatalogo::where('stock', '<=', 5)->where('isActive', 1)->count();
        $sin_stock = ProductoCatalogo::where('stock', '=', 0)->count();
        $valor_total = ProductoCatalogo::where('isActive', 1)->get()->sum(function ($p) {
            return $p->stock * $p->price_venta;
        });

        $estadisticas = [
            'total' => $total,
            'activos' => $activos,
            'inactivos' => $inactivos,
            'stock_bajo' => $stock_bajo,
            'sin_stock' => $sin_stock,
            'valor_total' => $valor_total,
        ];

        return view('livewire.catalogo.producto-catalogo-index', [
            'productos' => $query->paginate($this->perPage),
            'brands' => BrandCatalogo::where('isActive', true)->orderBy('name')->get(),
            'categories' => CategoryCatalogo::where('isActive', true)->orderBy('name')->get(),
            'lines' => LineCatalogo::where('isActive', true)->orderBy('name')->get(),
            'estadisticas' => $estadisticas,
        ]);
    }

    protected function applyPriceRangeFilter($query)
    {
        switch ($this->price_range) {
            case 'low':
                $query->where('price_venta', '<=', 100);
                break;
            case 'medium':
                $query->whereBetween('price_venta', [100, 500]);
                break;
            case 'high':
                $query->where('price_venta', '>', 500);
                break;
        }
    }

    public function nuevoProducto()
    {
        $this->resetValidation();
        $this->reset([
            'producto_id',
            'producto',
            'tempImage',
            'tempArchivo',
            'tempArchivo2',
            'imagePreview',
            'archivoPreview',
            'archivo2Preview',
            'image',
            'archivo',
            'archivo2',
            'caracteristicas',
            'isActive',
            'brand_id',
            'category_id',
            'line_id',
            'code',
            'code_fabrica',
            'code_peru',
            'price_compra',
            'price_venta',
            'stock',
            'dias_entrega',
            'description',
            'garantia',
            'observaciones',
        ]);
        if (!is_array($this->caracteristicas)) {
            $this->caracteristicas = [];
        }
        $this->modal_form_producto = true;
    }

    public function editarProducto($id)
    {
        $this->resetValidation();
        $this->producto_id = $id;
        $this->producto = ProductoCatalogo::with(['brand', 'category', 'line'])->findOrFail($id);

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
        // Convertir caracteristicas a array de pares key-value para el formulario
        if (is_array($this->producto->caracteristicas)) {
            $this->caracteristicas = [];
            foreach ($this->producto->caracteristicas as $key => $value) {
                $this->caracteristicas[] = ['key' => $key, 'value' => $value];
            }
        } else {
            $this->caracteristicas = [];
        }
        // Refuerzo: asegurar que caracteristicas sea array
        if (!is_array($this->caracteristicas)) {
            $this->caracteristicas = [];
        }
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
        $this->producto = ProductoCatalogo::findOrFail($id);
        $this->modal_form_eliminar_producto = true;
    }

    public function confirmarEliminarProducto()
    {
        try {
            // Eliminar archivos asociados
            $this->deleteFile($this->producto->image);
            $this->deleteFile($this->producto->archivo);
            $this->deleteFile($this->producto->archivo2);

            $this->producto->delete();

            $this->modal_form_eliminar_producto = false;
            $this->reset(['producto_id', 'producto']);

            $this->handleSuccess('Producto eliminado correctamente', 'eliminación de producto');
        } catch (\Exception $e) {
            $this->handleError($e, 'eliminación de producto');
        }
    }

    public function updatedTempImage()
    {
        $this->validate($this->validateImage('tempImage'));

        if ($this->tempImage) {
            $this->imagePreview = $this->tempImage->temporaryUrl();
        }
    }

    public function updatedTempArchivo()
    {
        $this->validate($this->validateFile('tempArchivo'));

        if ($this->tempArchivo) {
            $this->archivoPreview = $this->tempArchivo->getClientOriginalName();
        }
    }

    public function updatedTempArchivo2()
    {
        $this->validate($this->validateFile('tempArchivo2'));

        if ($this->tempArchivo2) {
            $this->archivo2Preview = $this->tempArchivo2->getClientOriginalName();
        }
    }

    public function removeImage()
    {
        $this->deleteFile($this->image);
        $this->image = null;
        $this->tempImage = null;
        $this->imagePreview = null;
    }

    public function removeArchivo()
    {
        $this->deleteFile($this->archivo);
        $this->archivo = null;
        $this->tempArchivo = null;
        $this->archivoPreview = null;
    }

    public function removeArchivo2()
    {
        $this->deleteFile($this->archivo2);
        $this->archivo2 = null;
        $this->tempArchivo2 = null;
        $this->archivo2Preview = null;
    }

    public function guardarProducto()
    {
        // Refuerzo: asegurar que caracteristicas sea array antes de cualquier foreach
        if (!is_array($this->caracteristicas)) {
            $this->caracteristicas = [];
        }
        $ruleUniqueCode = $this->producto_id ? 'unique:producto_catalogos,code,' . $this->producto_id : 'unique:producto_catalogos,code';

        // Validaciones
        $rules = [
            'brand_id' => 'required|exists:brand_catalogos,id',
            'category_id' => 'required|exists:category_catalogos,id',
            'line_id' => 'required|exists:line_catalogos,id',
            'code' => 'required|string|max:255|' . $ruleUniqueCode,
            'code_fabrica' => 'required|string|max:255',
            'code_peru' => 'required|string|max:255',
            'price_compra' => 'required|numeric|min:0',
            'price_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'dias_entrega' => 'nullable|integer|min:0',
            'description' => 'required|string|max:500',
            'garantia' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
            'isActive' => 'boolean',
        ];

        // Validar características como array de pares clave-valor
        if (is_array($this->caracteristicas)) {
            foreach ($this->caracteristicas as $i => $car) {
                $rules["caracteristicas.$i.key"] = 'required|string|max:255';
                $rules["caracteristicas.$i.value"] = 'required|string|max:1000';
            }
        }

        // Agregar validaciones de archivos
        $rules = array_merge($rules, $this->validateImage('tempImage'));
        $rules = array_merge($rules, $this->validateFile('tempArchivo'));
        $rules = array_merge($rules, $this->validateFile('tempArchivo2'));

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
            'price_compra.min' => 'El precio de compra debe ser mayor o igual a 0',
            'price_venta.required' => 'Por favor, ingrese el precio de venta',
            'price_venta.min' => 'El precio de venta debe ser mayor o igual a 0',
            'stock.required' => 'Por favor, ingrese la cantidad en stock',
            'stock.min' => 'El stock debe ser mayor o igual a 0',
            'description.required' => 'Por favor, ingrese la descripción del producto',
            'description.max' => 'La descripción no debe exceder los 500 caracteres',
            'dias_entrega.min' => 'Los días de entrega deben ser mayor o igual a 0',
            'garantia.max' => 'La garantía no debe exceder los 255 caracteres',
            'observaciones.max' => 'Las observaciones no deben exceder los 1000 caracteres',
        ];

        // Mensajes para características
        if (is_array($this->caracteristicas)) {
            foreach ($this->caracteristicas as $i => $car) {
                $messages["caracteristicas.$i.key.required"] = 'Ingrese la clave de la característica';
                $messages["caracteristicas.$i.value.required"] = 'Ingrese el valor de la característica';
            }
        }

        // Agregar mensajes de validación de archivos
        $messages = array_merge($messages, $this->getFileValidationMessages());

        $data = $this->validate($rules, $messages);

        // Procesar características como array asociativo
        $data['caracteristicas'] = [];
        if (is_array($this->caracteristicas)) {
            foreach ($this->caracteristicas as $car) {
                if (!empty($car['key']) && !empty($car['value'])) {
                    $data['caracteristicas'][$car['key']] = $car['value'];
                }
            }
        }

        try {
            // Procesar archivos usando el trait
            $data['image'] = $this->processImage($this->tempImage, $this->image, 'productos/images');
            $data['archivo'] = $this->processFile($this->tempArchivo, $this->archivo, 'productos/archivos');
            $data['archivo2'] = $this->processFile($this->tempArchivo2, $this->archivo2, 'productos/archivos');

            if ($this->producto_id) {
                $producto = ProductoCatalogo::findOrFail($this->producto_id);
                $producto->update($data);
                $message = 'Producto actualizado correctamente';
                $context = 'actualización de producto';
            } else {
                ProductoCatalogo::create($data);
                $message = 'Producto creado correctamente';
                $context = 'creación de producto';
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
                'archivo2Preview',
                'image',
                'archivo',
                'archivo2',
                'caracteristicas',
                'isActive',
                'brand_id',
                'category_id',
                'line_id',
                'code',
                'code_fabrica',
                'code_peru',
                'price_compra',
                'price_venta',
                'stock',
                'dias_entrega',
                'description',
                'garantia',
                'observaciones',
            ]);
            $this->resetValidation();

            $this->handleSuccess($message, $context);
        } catch (\Exception $e) {
            $this->handleError($e, 'guardado de producto');
        }
    }

    public function exportarProductos()
    {
        try {
            return Excel::download(
                new ProducCatalogoExport($this->productosExportar),
                'productos_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
        } catch (\Exception $e) {
            $this->handleError($e, 'exportación de productos');
        }
    }

    public function toggleProductStatus($id)
    {
        try {
            $producto = ProductoCatalogo::findOrFail($id);
            $producto->update(['isActive' => !$producto->isActive]);

            $this->handleSuccess('Estado del producto actualizado correctamente', 'cambio de estado');
        } catch (\Exception $e) {
            $this->handleError($e, 'cambio de estado');
        }
    }

    public function addCaracteristica()
    {
        $this->caracteristicas = is_array($this->caracteristicas) ? $this->caracteristicas : [];
        $this->caracteristicas[] = ['key' => '', 'value' => ''];
    }

    public function removeCaracteristica($index)
    {
        if (is_array($this->caracteristicas) && isset($this->caracteristicas[$index])) {
            array_splice($this->caracteristicas, $index, 1);
        }
    }
}
