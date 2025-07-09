# Corrección de Relaciones en Modelos

## Problema Identificado

Se encontró un error SQL al intentar cargar el dashboard:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'producto_catalogos.category_catalogo_id' in 'WHERE'
```

## Causa del Problema

El error se debía a que las relaciones en los modelos no especificaban correctamente las claves foráneas, causando que Laravel buscara columnas con nombres incorrectos.

## Correcciones Realizadas

### 1. Modelo CategoryCatalogo

**Antes**:
```php
public function products()
{
    return $this->hasMany(ProductoCatalogo::class);
}
```

**Después**:
```php
public function products()
{
    return $this->hasMany(ProductoCatalogo::class, 'category_id');
}
```

### 2. Modelo ProductoCatalogo

**Antes**:
```php
public function category()
{
    return $this->belongsTo(CategoryCatalogo::class);
}

public function brand()
{
    return $this->belongsTo(BrandCatalogo::class);
}

public function line()
{
    return $this->belongsTo(LineCatalogo::class);
}
```

**Después**:
```php
public function category()
{
    return $this->belongsTo(CategoryCatalogo::class, 'category_id');
}

public function brand()
{
    return $this->belongsTo(BrandCatalogo::class, 'brand_id');
}

public function line()
{
    return $this->belongsTo(LineCatalogo::class, 'line_id');
}
```

### 3. Modelo BrandCatalogo

**Antes**:
```php
public function products()
{
    return $this->hasMany(ProductoCatalogo::class);
}
```

**Después**:
```php
public function products()
{
    return $this->hasMany(ProductoCatalogo::class, 'brand_id');
}
```

### 4. Modelo LineCatalogo

**Antes**:
```php
public function productos()
{
    return $this->hasMany(ProductoCatalogo::class);
}
```

**Después**:
```php
public function productos()
{
    return $this->hasMany(ProductoCatalogo::class, 'line_id');
}
```

## Estructura de la Base de Datos

### Tabla producto_catalogos
```sql
CREATE TABLE `producto_catalogos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `line_id` bigint unsigned NOT NULL,
  -- otros campos...
  PRIMARY KEY (`id`),
  KEY `producto_catalogos_brand_id_foreign` (`brand_id`),
  KEY `producto_catalogos_category_id_foreign` (`category_id`),
  KEY `producto_catalogos_line_id_foreign` (`line_id`)
);
```

### Tabla category_catalogos
```sql
CREATE TABLE `category_catalogos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  -- otros campos...
  PRIMARY KEY (`id`)
);
```

## Explicación del Problema

### Comportamiento por Defecto de Laravel

Cuando no se especifica la clave foránea en una relación, Laravel asume que:

1. **Para belongsTo**: Busca una columna con el nombre del modelo en singular + `_id`
2. **Para hasMany**: Busca una columna con el nombre del modelo en singular + `_id`

### Ejemplo del Problema

**Relación sin especificar clave foránea**:
```php
// En CategoryCatalogo
public function products()
{
    return $this->hasMany(ProductoCatalogo::class);
}
```

**Laravel asume**:
- Busca una columna llamada `category_catalogo_id` en `producto_catalogos`
- Pero la columna real se llama `category_id`

### Solución

Especificar explícitamente la clave foránea:
```php
public function products()
{
    return $this->hasMany(ProductoCatalogo::class, 'category_id');
}
```

## Verificación de Otras Relaciones

### Modelos de Almacén ✅
Las relaciones en los modelos de Almacén ya estaban correctas:
- `ProductoAlmacen` → `WarehouseAlmacen` (almacen_id)
- `WarehouseAlmacen` → `ProductoAlmacen` (almacen_id)

### Modelos de CRM ✅
Las relaciones en los modelos de CRM ya estaban correctas:
- `OpportunityCrm` → `MarcaCrm` (marca_id)
- `OpportunityCrm` → `TipoNegocioCrm` (tipo_negocio_id)
- `OpportunityCrm` → `Customer` (customer_id)

## Mejores Prácticas

### 1. Siempre Especificar Claves Foráneas
```php
// ✅ Correcto
public function products()
{
    return $this->hasMany(ProductoCatalogo::class, 'category_id');
}

// ❌ Evitar (puede causar errores)
public function products()
{
    return $this->hasMany(ProductoCatalogo::class);
}
```

### 2. Usar Nombres Consistentes
```php
// ✅ Nombres descriptivos
'category_id', 'brand_id', 'line_id'

// ❌ Evitar nombres confusos
'cat_id', 'brand', 'line'
```

### 3. Documentar Relaciones
```php
/**
 * Relación con productos de esta categoría
 * 
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function products()
{
    return $this->hasMany(ProductoCatalogo::class, 'category_id');
}
```

## Testing de Relaciones

### Verificar que las Relaciones Funcionen
```php
// Test básico
$category = CategoryCatalogo::first();
$products = $category->products; // Debe funcionar sin errores

// Test con withCount
$categories = CategoryCatalogo::withCount('products')->get(); // Debe funcionar
```

### Verificar Consultas Complejas
```php
// Test de consultas complejas como las del dashboard
$productosPorCategoria = CategoryCatalogo::withCount('products')
    ->where('isActive', true)
    ->orderBy('products_count', 'desc')
    ->limit(5)
    ->get();
```

## Conclusión

Las correcciones realizadas aseguran que:

1. **Todas las relaciones especifiquen correctamente las claves foráneas**
2. **Las consultas del dashboard funcionen sin errores**
3. **El código sea más explícito y mantenible**
4. **Se eviten errores similares en el futuro**

### Archivos Modificados

1. `app/Models/Catalogo/CategoryCatalogo.php`
2. `app/Models/Catalogo/ProductoCatalogo.php`
3. `app/Models/Catalogo/BrandCatalogo.php`
4. `app/Models/Catalogo/LineCatalogo.php`

### Resultado

El dashboard ahora debería cargar correctamente sin errores SQL, mostrando todas las estadísticas y gráficos como se esperaba. 
