# Componentes de MaryUI Utilizados en el Dashboard

## Resumen

El dashboard implementado utiliza varios componentes nativos de MaryUI para crear una interfaz moderna y funcional. Todos los componentes utilizan el prefijo `mary-` según la configuración del proyecto.

## Componentes Utilizados

### 1. Contenedores y Layout

#### `x-mary-card`
- **Uso**: Contenedor principal para todas las secciones
- **Características**: 
  - Soporte para clases CSS personalizadas
  - Compatible con dark mode
  - Responsive design
- **Ejemplo**:
```blade
<x-mary-card class="bg-gradient-to-br from-blue-50 to-blue-100">
    <!-- Contenido -->
</x-mary-card>
```

### 2. Botones

#### `x-mary-button`
- **Uso**: Botones de acción y navegación
- **Propiedades**:
  - `color`: blue, green, purple, orange, red, gray
  - `size`: sm, md, lg
  - `icon`: Iconos de Heroicons
  - `spinner`: Muestra spinner durante carga
- **Ejemplo**:
```blade
<x-mary-button color="blue" icon="o-shopping-bag" size="sm" spinner>
    Ver Catálogo
</x-mary-button>
```

### 3. Gráficos

#### `x-chart`
- **Uso**: Gráficos interactivos nativos de MaryUI
- **Propiedades**:
  - `wire:model`: Array con configuración del gráfico
  - Tipos soportados: line, bar, pie, doughnut
- **Ejemplo**:
```blade
<x-chart wire:model="movimientosChart" />
```

### 4. Badges

#### `x-mary-badge`
- **Uso**: Indicadores de estado y valores
- **Propiedades**:
  - `value`: Texto a mostrar
  - `color`: blue, green, purple, orange, red, gray
  - `size`: sm, md, lg
- **Ejemplo**:
```blade
<x-mary-badge value="123" color="blue" size="lg" />
```

### 5. Dropdowns

#### `x-mary-dropdown`
- **Uso**: Menús desplegables
- **Estructura**:
```blade
<x-mary-dropdown>
    <x-slot:trigger>
        <x-mary-button icon="o-cog-6-tooth" />
    </x-slot:trigger>
    <x-mary-dropdown-item icon="o-arrow-path" label="Actualizar" />
    <x-mary-dropdown-item icon="o-document-arrow-down" label="Exportar" />
</x-mary-dropdown>
```

### 6. Tooltips

Los tooltips en MaryUI se manejan como atributos en otros componentes, no como componentes independientes.

- **Uso**: Información contextual
- **Propiedades**:
  - `tooltip`: Texto del tooltip
- **Ejemplo**:
```blade
<x-mary-button icon="o-information-circle" tooltip="Información del Dashboard" />
<x-mary-badge value="123" color="blue" tooltip="Total de productos" />
```

## Configuración de Gráficos

### Estructura del Array de Gráficos

```php
public array $movimientosChart = [
    'type' => 'line',
    'data' => [
        'labels' => ['Ene', 'Feb', 'Mar'],
        'datasets' => [
            [
                'label' => 'Movimientos',
                'data' => [12, 19, 3],
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'tension' => 0.4,
                'fill' => true
            ]
        ]
    ],
    'options' => [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => false
            ]
        ]
    ]
];
```

### Tipos de Gráficos Soportados

1. **line**: Gráfico de líneas para tendencias
2. **bar**: Gráfico de barras para comparaciones
3. **pie**: Gráfico circular para distribución
4. **doughnut**: Gráfico de dona para distribución

## Funcionalidades Interactivas

### Cambio de Tipo de Gráfico

```php
public function cambiarTipoGrafico($grafico)
{
    $tipos = ['line', 'bar', 'pie', 'doughnut'];
    $tipoActual = $this->{$grafico}['type'];
    $nuevoTipo = $tipos[(array_search($tipoActual, $tipos) + 1) % count($tipos)];
    
    Arr::set($this->{$grafico}, 'type', $nuevoTipo);
}
```

### Uso en Vista

```blade
<x-mary-button wire:click="cambiarTipoGrafico('movimientosChart')" spinner>
    Cambiar Tipo
</x-mary-button>
```

## Ventajas de Usar MaryUI

### 1. Consistencia Visual
- Todos los componentes siguen el mismo diseño
- Compatible con dark mode automáticamente
- Responsive design integrado

### 2. Integración con Livewire
- Componentes reactivos nativos
- Actualización automática sin JavaScript
- Estados de carga integrados

### 3. Configuración Simplificada
- Prefijo `mary-` para todos los componentes
- Propiedades intuitivas
- Documentación clara

### 4. Performance
- Carga optimizada
- Sin dependencias externas innecesarias
- Gráficos nativos de MaryUI

## Ejemplos de Uso Completo

### Tarjeta con Badge y Botón
```blade
<x-mary-card>
    <div class="flex justify-between items-center">
        <span class="text-sm text-gray-600">Productos Activos</span>
        <x-mary-badge value="123" color="blue" />
    </div>
    <x-mary-button size="sm" color="blue" icon="o-eye">
        Ver Detalles
    </x-mary-button>
</x-mary-card>
```

### Gráfico con Controles
```blade
<x-mary-card>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Movimientos</h3>
        <x-mary-button size="sm" icon="o-arrow-path" 
                       wire:click="cambiarTipoGrafico('movimientosChart')" />
    </div>
    <div class="h-64">
        <x-chart wire:model="movimientosChart" />
    </div>
</x-mary-card>
```

### Dropdown con Acciones
```blade
<x-mary-dropdown>
    <x-slot:trigger>
        <x-mary-button icon="o-cog-6-tooth" color="gray" />
    </x-slot:trigger>
    <x-mary-dropdown-item icon="o-arrow-path" label="Actualizar" />
    <x-mary-dropdown-item icon="o-document-arrow-down" label="Exportar" />
    <x-mary-dropdown-item icon="o-cog-6-tooth" label="Configuración" />
</x-mary-dropdown>
```

## Configuración del Proyecto

### Archivo de Configuración
```php
// config/mary.php
return [
    'prefix' => 'mary-',
    'route_prefix' => '',
    'components' => [
        'spotlight' => [
            'class' => 'App\Support\Spotlight',
        ]
    ]
];
```

### Dependencias
```json
{
    "require": {
        "robsontenorio/mary": "^2.4"
    }
}
```

## Conclusión

El uso de componentes nativos de MaryUI en el dashboard proporciona:

1. **Consistencia**: Todos los componentes siguen el mismo patrón de diseño
2. **Simplicidad**: Configuración intuitiva y propiedades claras
3. **Performance**: Componentes optimizados y sin dependencias innecesarias
4. **Funcionalidad**: Gráficos interactivos y componentes reactivos
5. **Mantenibilidad**: Código limpio y fácil de mantener

La implementación demuestra cómo MaryUI puede crear interfaces modernas y funcionales de manera eficiente y consistente. 
