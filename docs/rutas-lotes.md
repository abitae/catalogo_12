# Documentación de Rutas - Sistema de Lotes

## Rutas Web

### Reportes de Lotes
- **URL**: `/almacen/reportes/lotes`
- **Nombre**: `almacen.reportes.lotes`
- **Componente**: `ReporteLotesIndex`
- **Descripción**: Dashboard completo de reportes de lotes con estadísticas, filtros y análisis

### Alertas de Lotes
- **URL**: `/almacen/alertas/lotes`
- **Nombre**: `almacen.alertas.lotes`
- **Componente**: `AlertasLotesIndex`
- **Descripción**: Sistema de alertas para vencimiento, stock bajo y movimientos inusuales

### Exportación de Reportes
- **URL**: `/almacen/reportes/lotes/export`
- **Nombre**: `almacen.reportes.lotes.export`
- **Descripción**: Exportación de reportes de lotes a Excel

### Detalle de Lote Específico
- **URL**: `/almacen/lotes/{lote}/detalle`
- **Nombre**: `almacen.lotes.detalle`
- **Parámetros**: `{lote}` - Número de lote
- **Descripción**: Vista detallada de un lote específico

### Movimientos de Lote Específico
- **URL**: `/almacen/lotes/{lote}/movimientos`
- **Nombre**: `almacen.lotes.movimientos`
- **Parámetros**: `{lote}` - Número de lote
- **Descripción**: Historial de movimientos de un lote específico

## Rutas API

### Base URL
Todas las rutas API están bajo el prefijo `/api/almacen/lotes` y requieren autenticación con Sanctum.

### Obtener Todos los Lotes
- **URL**: `GET /api/almacen/lotes`
- **Nombre**: `api.lotes.index`
- **Respuesta**: Lista de todos los lotes únicos

### Productos por Lote
- **URL**: `GET /api/almacen/lotes/{lote}/productos`
- **Nombre**: `api.lotes.productos`
- **Parámetros**: `{lote}` - Número de lote
- **Respuesta**: Productos asociados al lote

### Estadísticas de Lote
- **URL**: `GET /api/almacen/lotes/{lote}/estadisticas`
- **Nombre**: `api.lotes.estadisticas`
- **Parámetros**: `{lote}` - Número de lote
- **Respuesta**: Estadísticas detalladas del lote

### Movimientos de Lote
- **URL**: `GET /api/almacen/lotes/{lote}/movimientos`
- **Nombre**: `api.lotes.movimientos`
- **Parámetros**: `{lote}` - Número de lote
- **Respuesta**: Historial de movimientos del lote

### Verificar Stock de Lote
- **URL**: `GET /api/almacen/lotes/{lote}/stock`
- **Nombre**: `api.lotes.stock`
- **Parámetros**: 
  - `{lote}` - Número de lote
  - `almacen_id` (query) - ID del almacén (opcional)
  - `cantidad` (query) - Cantidad a verificar (opcional)
- **Respuesta**: Información de stock disponible

### Alertas de Vencimiento
- **URL**: `GET /api/almacen/lotes/alertas/vencimiento`
- **Nombre**: `api.lotes.alertas.vencimiento`
- **Respuesta**: Productos por vencer

### Alertas de Stock Bajo
- **URL**: `GET /api/almacen/lotes/alertas/stock-bajo`
- **Nombre**: `api.lotes.alertas.stock-bajo`
- **Respuesta**: Productos con stock bajo

### Movimientos Inusuales
- **URL**: `GET /api/almacen/lotes/alertas/movimientos-inusuales`
- **Nombre**: `api.lotes.alertas.movimientos-inusuales`
- **Respuesta**: Movimientos inusuales detectados

## Ejemplos de Uso

### Obtener Productos de un Lote
```bash
curl -X GET "http://localhost:8000/api/almacen/lotes/LOT001/productos" \
  -H "Authorization: Bearer {token}"
```

### Verificar Stock de un Lote
```bash
curl -X GET "http://localhost:8000/api/almacen/lotes/LOT001/stock?almacen_id=1&cantidad=50" \
  -H "Authorization: Bearer {token}"
```

### Obtener Alertas
```bash
curl -X GET "http://localhost:8000/api/almacen/lotes/alertas/stock-bajo" \
  -H "Authorization: Bearer {token}"
```

## Respuestas de API

### Formato Estándar
```json
{
  "success": true,
  "data": [...],
  "total": 10,
  "lote": "LOT001" // cuando aplica
}
```

### Error
```json
{
  "success": false,
  "message": "Mensaje de error",
  "errors": {...}
}
```

## Middleware

Todas las rutas están protegidas por:
- `auth` - Autenticación web
- `auth:sanctum` - Autenticación API

## Filtros Disponibles

### Reportes Web
- `search` - Búsqueda por nombre, código o lote
- `almacen_filter` - Filtrar por almacén
- `lote_filter` - Filtrar por lote específico
- `categoria_filter` - Filtrar por categoría
- `estado_stock_filter` - Filtrar por estado de stock

### API
- `almacen_id` - Filtrar por almacén
- `cantidad` - Cantidad para verificación de stock
- `fecha_inicio` - Fecha de inicio para movimientos
- `fecha_fin` - Fecha de fin para movimientos 

## Funcionalidades Implementadas

### 1. Selección Automática de Productos por Lotes

El sistema ahora permite la selección automática de productos basándose en los lotes disponibles, mejorando significativamente la experiencia del usuario.

#### Características Principales:

- **Filtrado por Lotes**: Los usuarios pueden seleccionar un lote específico y el sistema automáticamente filtra los productos disponibles.
- **Selección Automática**: Si un lote contiene solo un producto, el sistema lo selecciona automáticamente.
- **Validación de Stock**: Verificación automática de stock disponible por lote.
- **Interfaz Intuitiva**: Selectores de lotes con información detallada (número de productos y stock total).

#### Implementación en Movimientos:

```php
// En MovimientoAlmacenIndex.php
public function actualizarProductosPorLote()
{
    if (!$this->almacen_id || empty($this->lote_producto)) {
        $this->actualizarProductosDisponibles();
        return;
    }

    $productos = ProductoAlmacen::query()
        ->where('estado', true)
        ->where('almacen_id', $this->almacen_id)
        ->where('lote', $this->lote_producto);

    if ($this->tipo_movimiento === 'salida') {
        $productos->where('stock_actual', '>', 0);
    }

    $this->productos_disponibles = $productos->get() ?: collect();
}
```

#### Implementación en Transferencias:

```php
// En TransferenciaAlmacenIndex.php
public function updatedLoteProducto()
{
    if (!empty($this->lote_producto)) {
        $this->actualizarProductosPorLote();
        
        // Si solo hay un producto en el lote, seleccionarlo automáticamente
        if ($this->productos_disponibles->count() === 1) {
            $this->producto_seleccionado = $this->productos_disponibles->first()->id;
            $this->updatedProductoSeleccionado();
        }
    } else {
        $this->actualizarProductosDisponibles();
    }
}
```

### 2. Rutas API para Funcionalidades de Lotes

#### Obtener Lotes Disponibles en un Almacén
```http
GET /api/lotes/almacen/{almacenId}
```

**Respuesta:**
```json
{
    "success": true,
    "data": ["LOTE001", "LOTE002", "LOTE003"],
    "message": "Lotes obtenidos correctamente"
}
```

#### Obtener Productos en un Lote Específico
```http
GET /api/lotes/{lote}/productos/{almacenId}
```

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "PROD001",
            "nombre": "Producto A",
            "stock_actual": 50.00,
            "unidad_medida": "unidades",
            "lote": "LOTE001"
        }
    ],
    "message": "Productos del lote obtenidos correctamente"
}
```

#### Verificar Stock Disponible en un Lote
```http
POST /api/lotes/verificar-stock
Content-Type: application/json

{
    "lote": "LOTE001",
    "almacen_id": 1,
    "cantidad": 25
}
```

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "lote": "LOTE001",
        "almacen_id": 1,
        "cantidad_solicitada": 25,
        "stock_disponible": 50,
        "tiene_stock_suficiente": true
    },
    "message": "Stock suficiente disponible"
}
```

#### Obtener Estadísticas de un Lote
```http
GET /api/lotes/{lote}/estadisticas/{almacenId}
```

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "lote": "LOTE001",
        "total_productos": 5,
        "total_stock": 150.00,
        "productos_bajo_stock": 1,
        "almacen_id": 1
    },
    "message": "Estadísticas del lote obtenidas correctamente"
}
```

#### Obtener Alertas de Lotes
```http
GET /api/lotes/alertas/{almacenId}
```

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "tipo": "stock_bajo",
            "lote": "LOTE001",
            "productos": 2,
            "stock_total": 15.00,
            "mensaje": "Lote LOTE001 tiene 2 productos con stock bajo"
        },
        {
            "tipo": "sin_stock",
            "lote": "LOTE002",
            "mensaje": "Lote LOTE002 sin stock disponible"
        }
    ],
    "total_alertas": 2,
    "message": "Alertas de lotes obtenidas correctamente"
}
```

### 3. Funcionalidades JavaScript

#### Componente Alpine.js para Selección de Lotes

```javascript
Alpine.data('lotesSelector', () => ({
    lotes: [],
    productos: [],
    loteSeleccionado: '',
    productoSeleccionado: '',
    almacenId: null,
    cargando: false,

    async init() {
        this.$watch('almacenId', async (value) => {
            if (value) {
                await this.cargarLotes();
            }
        });

        this.$watch('loteSeleccionado', async (value) => {
            if (value) {
                await this.cargarProductosPorLote();
            } else {
                this.productos = [];
            }
        });
    },

    async cargarLotes() {
        if (!this.almacenId) return;

        this.cargando = true;
        try {
            const response = await fetch(`/api/lotes/almacen/${this.almacenId}`);
            const data = await response.json();
            
            if (data.success) {
                this.lotes = data.data;
            }
        } catch (error) {
            console.error('Error al cargar lotes:', error);
        } finally {
            this.cargando = false;
        }
    }
}));
```

### 4. Interfaz de Usuario Mejorada

#### Selector de Lotes en Movimientos y Transferencias

```html
<!-- Selector de lotes -->
<div class="mb-4">
    <flux:select
        label="Filtrar por Lote (Opcional)"
        wire:model.live="lote_producto"
        :disabled="!$almacen_id || ($movimiento_id && $estado !== 'pendiente')"
        class="w-full md:w-1/3"
    >
        <option value="">Todos los lotes</option>
        @foreach ($this->getLotesDisponibles() as $lote)
            <option value="{{ $lote }}">
                Lote: {{ $lote }}
                @php
                    $productosEnLote = $this->getProductosEnLote($lote);
                    $totalStock = $productosEnLote->sum('stock_actual');
                @endphp
                ({{ $productosEnLote->count() }} productos, {{ number_format($totalStock, 2) }} unidades)
            </option>
        @endforeach
    </flux:select>
</div>
```

### 5. Validaciones y Seguridad

#### Validación de Stock por Lote

```php
// Validación en movimientos y transferencias
if (!empty($this->lote_producto)) {
    $stockLote = ProductoAlmacen::tieneStockSuficienteEnLoteYAlmacen(
        $this->lote_producto,
        $this->almacen_id,
        $value
    );

    if (!$stockLote) {
        $stockDisponible = ProductoAlmacen::getStockTotalPorLoteYAlmacen(
            $this->lote_producto,
            $this->almacen_id
        );
        $fail("Stock insuficiente en lote {$this->lote_producto}. Disponible: {$stockDisponible}, Solicitado: {$value}");
    }
}
```

### 6. Beneficios de la Implementación

1. **Eficiencia Operativa**: Reducción del tiempo de selección de productos.
2. **Precisión**: Validación automática de stock por lote.
3. **Trazabilidad**: Mejor control y seguimiento de productos por lotes.
4. **Experiencia de Usuario**: Interfaz intuitiva y responsive.
5. **Escalabilidad**: Sistema preparado para manejar grandes volúmenes de lotes.

### 7. Casos de Uso

#### Caso 1: Selección de Productos por Lote
1. El usuario selecciona un almacén.
2. El sistema carga automáticamente los lotes disponibles.
3. El usuario selecciona un lote específico.
4. El sistema filtra y muestra solo los productos de ese lote.
5. Si el lote tiene un solo producto, se selecciona automáticamente.

#### Caso 2: Validación de Stock
1. El usuario ingresa una cantidad para un producto de un lote específico.
2. El sistema verifica automáticamente el stock disponible en ese lote.
3. Si hay stock suficiente, permite continuar.
4. Si no hay stock suficiente, muestra un mensaje de error con el stock disponible.

#### Caso 3: Alertas de Lotes
1. El sistema monitorea constantemente los lotes.
2. Detecta lotes con stock bajo o sin stock.
3. Genera alertas automáticas para el usuario.
4. Permite tomar acciones preventivas.

### 8. Configuración y Mantenimiento

#### Requisitos del Sistema
- Laravel 11+
- Base de datos con soporte para JSON
- Alpine.js para funcionalidades frontend
- CSRF protection habilitada

#### Configuración de Rutas
Las rutas API están configuradas en `routes/api.php` y se cargan automáticamente en `bootstrap/app.php`.

#### Monitoreo y Logs
- Todas las operaciones de lotes se registran en los logs de Laravel
- Las alertas se pueden configurar para envío por email
- Dashboard de monitoreo disponible en `/almacen/alertas-lotes`

### 9. Próximas Mejoras

1. **Integración con Códigos QR**: Generación automática de códigos QR para lotes.
2. **Notificaciones Push**: Alertas en tiempo real para stock bajo.
3. **Reportes Avanzados**: Análisis de tendencias por lotes.
4. **Integración con Proveedores**: Trazabilidad completa desde el proveedor.
5. **Móvil**: Aplicación móvil para gestión de lotes en campo.

---

**Nota**: Esta documentación se actualiza regularmente. Para la versión más reciente, consulte el repositorio del proyecto.
