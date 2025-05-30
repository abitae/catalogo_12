<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ProductoAlmacenIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros
    public $almacen_filter = '';
    public $categoria_filter = '';
    public $stock_status = '';
    public $isActive_filter = '';

    // Modal Form Producto
    public $modal_form_producto = false;
    public $modal_form_eliminar_producto = false;
    public $producto_id = '';
    public $producto = null;
    public $productosExportar = null;
    public $nuevo_codigo_salida = '';
    public $codes_exit = [];

    // Variables para el formulario
    public $almacen_id = '';
    public $code = '';
    public $nombre = '';
    public $descripcion = '';
    public $categoria = '';
    public $unidad_medida = '';
    public $stock_minimo = '';
    public $stock_actual = '';
    public $precio_unitario = '';
    public $estado = true;
    public $codigo_barras = '';
    public $marca = '';
    public $modelo = '';
    public $imagen = '';

    // Propiedades para manejo de archivos
    public $tempImage = null;
    public $imagePreview = null;

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
            'almacen_filter',
            'categoria_filter',
            'perPage',
            'stock_status',
            'isActive_filter'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = ProductoAlmacen::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                        ->orWhere('codigo_barras', 'like', '%' . $this->search . '%')
                        ->orWhereJsonContains('codes_exit', $this->search);
                });
            })
            ->when($this->almacen_filter, function ($query) {
                $query->where('almacen_id', $this->almacen_filter);
            })
            ->when($this->categoria_filter, function ($query) {
                $query->where('categoria', $this->categoria_filter);
            })
            ->when($this->stock_status, function ($query) {
                if ($this->stock_status === 'in_stock') {
                    $query->where('stock_actual', '>', 0);
                } elseif ($this->stock_status === 'out_of_stock') {
                    $query->where('stock_actual', '=', 0);
                } elseif ($this->stock_status === 'low_stock') {
                    $query->whereRaw('stock_actual <= stock_minimo');
                }
            })
            ->when($this->isActive_filter !== '', function ($query) {
                $query->where('estado', $this->isActive_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $this->productosExportar = $query->get();

        return view('livewire.almacen.producto-almacen-index', [
            'productos' => $query->paginate($this->perPage),
            'almacenes' => WarehouseAlmacen::where('estado', true)->get(),
            'categorias' => ProductoAlmacen::distinct()->pluck('categoria'),
            'unidades_medida' => ProductoAlmacen::distinct()->pluck('unidad_medida')
        ]);
    }

    public function nuevoProducto()
    {
        $this->reset([
            'producto_id',
            'almacen_id',
            'code',
            'codes_exit',
            'nombre',
            'descripcion',
            'categoria',
            'unidad_medida',
            'stock_minimo',
            'stock_actual',
            'precio_unitario',
            'estado',
            'codigo_barras',
            'marca',
            'modelo',
            'imagen',
            'imagePreview',
            'tempImage'
        ]);
        $this->modal_form_producto = true;
    }

    public function editarProducto($id)
    {
        $this->producto_id = $id;
        $this->producto = ProductoAlmacen::find($id);
        $this->almacen_id = $this->producto->almacen_id;
        $this->code = $this->producto->code;
        $this->codes_exit = $this->producto->codes_exit ?? [];
        $this->nombre = $this->producto->nombre;
        $this->descripcion = $this->producto->descripcion;
        $this->categoria = $this->producto->categoria;
        $this->unidad_medida = $this->producto->unidad_medida;
        $this->stock_minimo = $this->producto->stock_minimo;
        $this->stock_actual = $this->producto->stock_actual;
        $this->precio_unitario = $this->producto->precio_unitario;
        $this->estado = $this->producto->estado;
        $this->codigo_barras = $this->producto->codigo_barras;
        $this->marca = $this->producto->marca;
        $this->modelo = $this->producto->modelo;
        $this->imagen = $this->producto->imagen;

        if ($this->imagen) {
            $this->imagePreview = asset('storage/' . $this->imagen);
        }

        $this->modal_form_producto = true;
    }

    public function eliminarProducto($id)
    {
        $this->producto_id = $id;
        $this->producto = ProductoAlmacen::find($id);
        if ($this->producto) {
            $this->modal_form_eliminar_producto = true;
        }
    }

    public function confirmarEliminarProducto()
    {
        if ($this->producto->imagen && Storage::exists($this->producto->imagen)) {
            Storage::delete($this->producto->imagen);
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

    public function removeImage()
    {
        if ($this->imagen && Storage::exists($this->imagen)) {
            Storage::delete($this->imagen);
        }
        $this->imagen = null;
        $this->tempImage = null;
        $this->imagePreview = null;
    }

    public function agregarCodigoSalida()
    {
        $this->validate([
            'nuevo_codigo_salida' => 'required|string|max:255'
        ], [
            'nuevo_codigo_salida.required' => 'Por favor, ingrese un código de salida',
            'nuevo_codigo_salida.max' => 'El código de salida no debe exceder los 255 caracteres'
        ]);

        if ($this->producto_id) {
            $producto = ProductoAlmacen::find($this->producto_id);
            if ($producto->agregarCodigoSalida($this->nuevo_codigo_salida)) {
                $this->nuevo_codigo_salida = '';
                $this->codes_exit = $producto->fresh()->codes_exit;
            }
        } else {
            if (!in_array($this->nuevo_codigo_salida, $this->codes_exit)) {
                $this->codes_exit[] = $this->nuevo_codigo_salida;
                $this->nuevo_codigo_salida = '';
            }
        }
    }

    public function eliminarCodigoSalida($code)
    {
        if ($this->producto_id) {
            $producto = ProductoAlmacen::find($this->producto_id);
            if ($producto->eliminarCodigoSalida($code)) {
                $this->codes_exit = $producto->fresh()->codes_exit;
            }
        } else {
            $this->codes_exit = array_values(array_diff($this->codes_exit, [$code]));
        }
    }

    public function guardarProducto()
    {
        $productoExistente = ProductoAlmacen::where('code', $this->code)
            ->where('almacen_id', $this->almacen_id)
            ->when($this->producto_id, function($query) {
                return $query->where('id', '!=', $this->producto_id);
            })
            ->first();

        if ($productoExistente) {
            $this->addError('code', 'Ya existe un producto con este código en el almacén seleccionado');
            return;
        }

        $rules = [
            'almacen_id' => 'required|exists:almacenes,id',
            'code' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'stock_minimo' => 'required|numeric|min:0',
            'stock_actual' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'estado' => 'boolean',
            'codigo_barras' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'tempImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ];

        $messages = [
            'almacen_id.required' => 'Por favor, seleccione un almacén',
            'almacen_id.exists' => 'El almacén seleccionado no es válido',
            'code.required' => 'Por favor, ingrese el código del producto',
            'code.unique' => 'Este código ya está registrado en el sistema',
            'nombre.required' => 'Por favor, ingrese el nombre del producto',
            'categoria.required' => 'Por favor, ingrese la categoría del producto',
            'unidad_medida.required' => 'Por favor, ingrese la unidad de medida',
            'stock_minimo.required' => 'Por favor, ingrese el stock mínimo',
            'stock_actual.required' => 'Por favor, ingrese el stock actual',
            'precio_unitario.required' => 'Por favor, ingrese el precio unitario',
            'tempImage.image' => 'El archivo debe ser una imagen válida',
            'tempImage.mimes' => 'La imagen debe ser en formato: jpeg, png, jpg, gif o svg',
            'tempImage.max' => 'La imagen no debe superar los 20MB',
        ];

        $data = $this->validate($rules, $messages);

        if ($this->tempImage) {
            if ($this->imagen && Storage::exists($this->imagen)) {
                Storage::delete($this->imagen);
            }
            $imagePath = $this->tempImage->store('productos/images', 'public');
            $data['imagen'] = $imagePath;
        }

        if ($this->producto_id) {
            $producto = ProductoAlmacen::find($this->producto_id);
            $producto->update($data);
            $producto->codes_exit = $this->codes_exit;
            $producto->save();
        } else {
            $data['codes_exit'] = $this->codes_exit;
            ProductoAlmacen::create($data);
        }

        $this->modal_form_producto = false;
        $this->reset([
            'producto_id',
            'producto',
            'tempImage',
            'imagePreview',
            'codes_exit',
            'nuevo_codigo_salida'
        ]);
        $this->resetValidation();
    }

    public function exportarProductos()
    {
        //return Excel::download(new ProductoAlmacenExport($this->productosExportar), 'productos_almacen_' . date('Y-m-d_H-i-s') . '.xlsx');
        $this->reset(['productosExportar']);
    }
}
