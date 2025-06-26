<?php

namespace App\Livewire\Almacen;

use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

class ProductoAlmacenIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Propiedades de paginación y búsqueda
    public $search = '';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Filtros avanzados
    public $almacen_filter = '';
    public $categoria_filter = '';
    public $stock_status = '';
    public $lote_filter = '';
    public $isActive_filter = '';
    public $marca_filter = '';
    public $unidad_medida_filter = '';

    // Estados de modales
    public $modal_form_producto = false;
    public $modal_form_eliminar_producto = false;
    public $modal_detalle_producto = false;

    // Propiedades del producto
    public $producto_id = '';
    public $producto = null;
    public $productosExportar = null;
    public $nuevo_codigo_salida = '';
    public $codes_exit = [];

    // Variables del formulario
    public $almacen_id = '';
    public $code = '';
    public $nombre = '';
    public $descripcion = '';
    public $categoria = '';
    public $unidad_medida = '';
    public $stock_minimo = '';
    public $stock_actual = '';
    public $precio_unitario = '';
    public $lote = '';
    public $estado = true;
    public $codigo_barras = '';
    public $marca = '';
    public $modelo = '';
    public $imagen = '';

    // Manejo de archivos
    public $tempImage = null;
    public $imagePreview = null;

    // Propiedades para lotes
    public $lotes_disponibles = [];
    public $productos_por_lote = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'almacen_filter' => ['except' => ''],
        'categoria_filter' => ['except' => ''],
        'stock_status' => ['except' => ''],
        'lote_filter' => ['except' => ''],
        'isActive_filter' => ['except' => ''],
        'marca_filter' => ['except' => ''],
        'unidad_medida_filter' => ['except' => ''],
        'sortField' => ['except' => 'code'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        // Validación compuesta: código único por almacén y lote
        $ruleUniqueCode = $this->producto_id
            ? Rule::unique('productos_almacen', 'code')
                ->where('almacen_id', $this->almacen_id)
                ->where('lote', $this->lote)
                ->ignore($this->producto_id)
            : Rule::unique('productos_almacen', 'code')
                ->where('almacen_id', $this->almacen_id)
                ->where('lote', $this->lote);

        return [
            'almacen_id' => 'required|exists:almacenes,id',
            'code' => ['required', 'string', 'max:50', $ruleUniqueCode],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria' => 'required|string|max:100',
            'unidad_medida' => 'required|string|max:50',
            'stock_minimo' => 'required|numeric|min:0',
            'stock_actual' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'lote' => 'nullable|string|max:255',
            'estado' => 'boolean',
            'codigo_barras' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'tempImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ];
    }

    protected function messages()
    {
        return [
            'almacen_id.required' => 'Debe seleccionar un almacén',
            'almacen_id.exists' => 'El almacén seleccionado no existe',
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'Este código ya está registrado en el almacén seleccionado para el lote especificado.',
            'nombre.required' => 'El nombre es obligatorio',
            'categoria.required' => 'La categoría es obligatoria',
            'unidad_medida.required' => 'La unidad de medida es obligatoria',
            'stock_minimo.required' => 'El stock mínimo es obligatorio',
            'stock_minimo.numeric' => 'El stock mínimo debe ser un número',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo',
            'stock_actual.required' => 'El stock actual es obligatorio',
            'stock_actual.numeric' => 'El stock actual debe ser un número',
            'stock_actual.min' => 'El stock actual no puede ser negativo',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'precio_unitario.min' => 'El precio unitario no puede ser negativo',
            'tempImage.image' => 'El archivo debe ser una imagen',
            'tempImage.mimes' => 'La imagen debe ser JPEG, PNG, JPG, GIF o SVG',
            'tempImage.max' => 'La imagen no debe exceder 20MB',
        ];
    }

    public function mount()
    {
        $this->cargarLotesDisponibles();
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
            'almacen_filter',
            'categoria_filter',
            'perPage',
            'stock_status',
            'lote_filter',
            'isActive_filter',
            'marca_filter',
            'unidad_medida_filter'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->construirQuery();

        $this->productosExportar = $query->get();

        return view('livewire.almacen.producto-almacen-index', [
            'productos' => $query->paginate($this->perPage),
            'almacenes' => WarehouseAlmacen::where('estado', true)->get(),
            'categorias' => ProductoAlmacen::distinct()->pluck('categoria')->filter(),
            'lotes' => ProductoAlmacen::distinct()->pluck('lote')->filter(),
            'unidades_medida' => ProductoAlmacen::distinct()->pluck('unidad_medida')->filter(),
            'marcas' => ProductoAlmacen::distinct()->pluck('marca')->filter(),
            'estadisticas' => $this->obtenerEstadisticas(),
        ]);
    }

    private function construirQuery()
    {
        return ProductoAlmacen::query()
            ->with(['almacen'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                        ->orWhere('codigo_barras', 'like', '%' . $this->search . '%')
                        ->orWhere('lote', 'like', '%' . $this->search . '%')
                        ->orWhere('marca', 'like', '%' . $this->search . '%')
                        ->orWhere('modelo', 'like', '%' . $this->search . '%')
                        ->orWhereJsonContains('codes_exit', $this->search);
                });
            })
            ->when($this->almacen_filter, function ($query) {
                $query->where('almacen_id', $this->almacen_filter);
            })
            ->when($this->categoria_filter, function ($query) {
                $query->where('categoria', $this->categoria_filter);
            })
            ->when($this->lote_filter, function ($query) {
                $query->where('lote', $this->lote_filter);
            })
            ->when($this->marca_filter, function ($query) {
                $query->where('marca', $this->marca_filter);
            })
            ->when($this->unidad_medida_filter, function ($query) {
                $query->where('unidad_medida', $this->unidad_medida_filter);
            })
            ->when($this->stock_status, function ($query) {
                switch ($this->stock_status) {
                    case 'in_stock':
                        $query->where('stock_actual', '>', 0);
                        break;
                    case 'out_of_stock':
                        $query->where('stock_actual', '=', 0);
                        break;
                    case 'low_stock':
                        $query->whereRaw('stock_actual <= stock_minimo');
                        break;
                    case 'overstock':
                        $query->where('stock_actual', '>', DB::raw('stock_minimo * 2'));
                        break;
                }
            })
            ->when($this->isActive_filter !== '', function ($query) {
                $query->where('estado', $this->isActive_filter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    private function obtenerEstadisticas()
    {
        return [
            'total_productos' => ProductoAlmacen::count(),
            'productos_activos' => ProductoAlmacen::where('estado', true)->count(),
            'productos_con_stock' => ProductoAlmacen::where('stock_actual', '>', 0)->count(),
            'productos_sin_stock' => ProductoAlmacen::where('stock_actual', '=', 0)->count(),
            'productos_stock_bajo' => ProductoAlmacen::whereRaw('stock_actual <= stock_minimo')->count(),
            'total_lotes' => ProductoAlmacen::distinct()->count('lote'),
            'valor_total_inventario' => ProductoAlmacen::sum(DB::raw('stock_actual * precio_unitario')),
        ];
    }

    public function cargarLotesDisponibles()
    {
        $this->lotes_disponibles = ProductoAlmacen::distinct()
            ->whereNotNull('lote')
            ->where('lote', '!=', '')
            ->pluck('lote')
            ->filter()
            ->values();
    }

    public function obtenerProductosPorLote($lote)
    {
        return ProductoAlmacen::where('lote', $lote)
            ->with(['almacen'])
            ->get();
    }

    public function nuevoProducto()
    {
        $this->resetearFormulario();
        $this->modal_form_producto = true;
    }

    public function editarProducto($id)
    {
        $this->producto = ProductoAlmacen::findOrFail($id);
        $this->cargarDatosProducto();
        $this->modal_form_producto = true;
    }

    public function verDetalleProducto($id)
    {
        $this->producto = ProductoAlmacen::with(['almacen'])->findOrFail($id);
        $this->modal_detalle_producto = true;
    }

    public function eliminarProducto($id)
    {
        $this->producto = ProductoAlmacen::findOrFail($id);
        $this->modal_form_eliminar_producto = true;
    }

    public function confirmarEliminarProducto()
    {
        try {
            if ($this->producto->imagen && Storage::exists($this->producto->imagen)) {
                Storage::delete($this->producto->imagen);
            }

            $this->producto->delete();

            session()->flash('message', 'Producto eliminado correctamente');
            $this->modal_form_eliminar_producto = false;
            $this->reset(['producto_id', 'producto']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
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
            if ($producto && $producto->agregarCodigoSalida($this->nuevo_codigo_salida)) {
                $this->nuevo_codigo_salida = '';
                $this->codes_exit = $producto->fresh()->codes_exit;
                session()->flash('message', 'Código de salida agregado correctamente');
            }
        } else {
            if (!in_array($this->nuevo_codigo_salida, $this->codes_exit)) {
                $this->codes_exit[] = $this->nuevo_codigo_salida;
                $this->nuevo_codigo_salida = '';
                session()->flash('message', 'Código de salida agregado correctamente');
            } else {
                session()->flash('error', 'Este código de salida ya existe');
            }
        }
    }

    public function eliminarCodigoSalida($code)
    {
        if ($this->producto_id) {
            $producto = ProductoAlmacen::find($this->producto_id);
            if ($producto && $producto->eliminarCodigoSalida($code)) {
                $this->codes_exit = $producto->fresh()->codes_exit;
                session()->flash('message', 'Código de salida eliminado correctamente');
            }
        } else {
            $this->codes_exit = array_values(array_filter($this->codes_exit, function ($c) use ($code) {
                return $c !== $code;
            }));
            session()->flash('message', 'Código de salida eliminado correctamente');
        }
    }

    public function guardarProducto()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = $this->obtenerDatosFormulario();

            if ($this->tempImage) {
                $data['imagen'] = $this->guardarImagen();
            }

            if ($this->producto_id) {
                $producto = ProductoAlmacen::findOrFail($this->producto_id);
                $producto->update($data);
                $mensaje = 'Producto actualizado correctamente';
            } else {
                ProductoAlmacen::create($data);
                $mensaje = 'Producto creado correctamente';
            }

            DB::commit();

            session()->flash('message', $mensaje);
            $this->modal_form_producto = false;
            $this->resetearFormulario();
            $this->cargarLotesDisponibles();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el producto: ' . $e->getMessage());
        }
    }

    private function obtenerDatosFormulario()
    {
        return [
            'almacen_id' => $this->almacen_id,
            'code' => $this->code,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'unidad_medida' => $this->unidad_medida,
            'stock_minimo' => $this->stock_minimo,
            'stock_actual' => $this->stock_actual,
            'precio_unitario' => $this->precio_unitario,
            'lote' => $this->lote,
            'estado' => $this->estado,
            'codigo_barras' => $this->codigo_barras,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'codes_exit' => $this->codes_exit,
        ];
    }

    private function guardarImagen()
    {
        if ($this->tempImage) {
            $path = $this->tempImage->store('productos', 'public');
            $this->tempImage = null;
            return $path;
        }
        return $this->imagen;
    }

    private function resetearFormulario()
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
            'lote',
            'estado',
            'codigo_barras',
            'marca',
            'modelo',
            'imagen',
            'imagePreview',
            'tempImage',
            'nuevo_codigo_salida'
        ]);
        $this->resetValidation();
    }

    private function cargarDatosProducto()
    {
        $this->producto_id = $this->producto->id;
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
        $this->lote = $this->producto->lote;
        $this->estado = $this->producto->estado;
        $this->codigo_barras = $this->producto->codigo_barras;
        $this->marca = $this->producto->marca;
        $this->modelo = $this->producto->modelo;
        $this->imagen = $this->producto->imagen;

        if ($this->imagen) {
            $this->imagePreview = asset('storage/' . $this->imagen);
        }
    }

    public function exportarProductos()
    {
        try {
            return Excel::download(
                new \App\Exports\ProducCatalogoExport($this->productosExportar),
                'productos_almacen_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    public function generarCodigoAutomatico()
    {
        $ultimoProducto = ProductoAlmacen::orderBy('id', 'desc')->first();
        $numero = $ultimoProducto ? intval(substr($ultimoProducto->code, 3)) + 1 : 1;
        $this->code = 'PRO' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function updatedAlmacenId()
    {
        if ($this->almacen_id) {
            $this->cargarLotesDisponibles();
        }
    }
}
