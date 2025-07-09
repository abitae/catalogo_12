# Ejemplos de Tooltips en MaryUI

## Introducción

Los tooltips en MaryUI son una excelente manera de proporcionar información adicional a los usuarios sin ocupar espacio en la interfaz. Se pueden usar en botones, badges, iconos y otros componentes.

## Sintaxis Básica

```blade
<x-mary-button label="Botón" tooltip="Información del tooltip" />
```

## Ejemplos por Componente

### 1. Botones con Tooltips

#### Botón Simple
```blade
<x-mary-button 
    label="Guardar" 
    tooltip="Guardar cambios en la base de datos" 
/>
```

#### Botón con Icono
```blade
<x-mary-button 
    icon="o-document-arrow-down" 
    label="Exportar" 
    tooltip="Descargar reporte en formato Excel" 
/>
```

#### Botón de Acción
```blade
<x-mary-button 
    icon="o-trash" 
    size="sm"
    color="red"
    tooltip="Eliminar registro permanentemente" 
/>
```

#### Botón con Evento Livewire
```blade
<x-mary-button 
    icon="o-arrow-path" 
    size="sm"
    wire:click="actualizarDatos"
    tooltip="Actualizar datos en tiempo real" 
/>
```

### 2. Badges con Tooltips

#### Badge de Estado
```blade
<x-mary-badge 
    value="Activo" 
    color="green"
    tooltip="Usuario con acceso completo al sistema" 
/>
```

#### Badge de Contador
```blade
<x-mary-badge 
    value="{{ $totalProductos }}" 
    color="blue"
    tooltip="Total de productos en el catálogo" 
/>
```

#### Badge de Alerta
```blade
<x-mary-badge 
    value="Crítico" 
    color="red"
    tooltip="Stock por debajo del mínimo requerido" 
/>
```

### 3. Iconos con Tooltips

#### Icono Informativo
```blade
<x-mary-button 
    icon="o-information-circle" 
    size="sm"
    tooltip="Información adicional sobre esta función" 
/>
```

#### Icono de Ayuda
```blade
<x-mary-button 
    icon="o-question-mark-circle" 
    size="sm"
    tooltip="Haz clic para ver la guía de uso" 
/>
```

### 4. Enlaces con Tooltips

#### Enlace de Navegación
```blade
<x-mary-button 
    label="Dashboard" 
    icon="o-home"
    href="/dashboard"
    tooltip="Ir al panel principal" 
/>
```

## Ejemplos Avanzados

### 1. Tooltips con Contenido Dinámico

```blade
<x-mary-button 
    label="Ver Detalles" 
    tooltip="Última actualización: {{ $producto->updated_at->diffForHumans() }}" 
/>
```

### 2. Tooltips Condicionales

```blade
<x-mary-button 
    label="Editar" 
    tooltip="{{ $usuario->puedeEditar ? 'Editar registro' : 'No tienes permisos para editar' }}" 
    :disabled="!$usuario->puedeEditar"
/>
```

### 3. Tooltips con HTML (si es soportado)

```blade
<x-mary-button 
    label="Información" 
    tooltip="<strong>Importante:</strong> Este cambio no se puede deshacer" 
/>
```

## Ejemplos en el Dashboard

### Botones de Acción en Gráficos

```blade
<!-- Botón para cambiar tipo de gráfico -->
<x-mary-button 
    size="sm" 
    icon="o-arrow-path" 
    wire:click="cambiarTipoGrafico('stockChart')"
    tooltip="Cambiar tipo de gráfico (barra, línea, donut)" 
/>

<!-- Botón de información del gráfico -->
<x-mary-button 
    size="sm" 
    icon="o-chart-bar" 
    tooltip="Gráfico de barras - Stock por almacén"
    class="text-gray-400" 
/>
```

### Badges con Información Contextual

```blade
<!-- Badge de productos activos -->
<x-mary-badge 
    value="{{ number_format($estadisticasCatalogo['productos_activos']) }}" 
    color="blue"
    tooltip="Productos disponibles para venta" 
/>

<!-- Badge de productos sin stock -->
<x-mary-badge 
    value="{{ number_format($estadisticasCatalogo['productos_sin_stock']) }}" 
    color="red"
    tooltip="Productos que requieren reposición" 
/>
```

### Iconos de Navegación

```blade
<!-- Icono del módulo Catálogo -->
<x-mary-button 
    icon="o-shopping-bag" 
    size="sm"
    tooltip="Ver todos los productos del catálogo"
    class="text-blue-500 hover:text-blue-600" 
/>

<!-- Icono del módulo Almacén -->
<x-mary-button 
    icon="o-building-storefront" 
    size="sm"
    tooltip="Gestionar inventario y almacenes"
    class="text-green-500 hover:text-green-600" 
/>
```

## Mejores Prácticas

### 1. Texto Claro y Conciso
```blade
<!-- ✅ Bueno -->
<x-mary-button tooltip="Guardar cambios" />

<!-- ❌ Evitar -->
<x-mary-button tooltip="Haz clic aquí para guardar todos los cambios que has realizado en el formulario" />
```

### 2. Información Útil
```blade
<!-- ✅ Útil -->
<x-mary-button tooltip="Última actualización: hace 5 minutos" />

<!-- ❌ Redundante -->
<x-mary-button tooltip="Botón" />
```

### 3. Consistencia en el Tono
```blade
<!-- ✅ Consistente -->
<x-mary-button tooltip="Eliminar registro" />
<x-mary-button tooltip="Editar registro" />
<x-mary-button tooltip="Ver detalles" />

<!-- ❌ Inconsistente -->
<x-mary-button tooltip="Eliminar registro" />
<x-mary-button tooltip="Click para editar" />
<x-mary-button tooltip="Más información aquí" />
```

### 4. Accesibilidad
```blade
<!-- ✅ Accesible -->
<x-mary-button 
    label="Guardar" 
    tooltip="Guardar cambios en la base de datos"
    aria-label="Guardar cambios" 
/>
```

## Casos de Uso Comunes

### 1. Botones de Acción
```blade
<div class="flex gap-2">
    <x-mary-button 
        icon="o-eye" 
        size="sm"
        tooltip="Ver detalles completos" 
    />
    <x-mary-button 
        icon="o-pencil" 
        size="sm"
        tooltip="Editar información" 
    />
    <x-mary-button 
        icon="o-trash" 
        size="sm"
        color="red"
        tooltip="Eliminar permanentemente" 
    />
</div>
```

### 2. Indicadores de Estado
```blade
<div class="flex gap-2">
    <x-mary-badge 
        value="En línea" 
        color="green"
        tooltip="Usuario activo en el sistema" 
    />
    <x-mary-badge 
        value="Pendiente" 
        color="yellow"
        tooltip="Esperando aprobación" 
    />
    <x-mary-badge 
        value="Bloqueado" 
        color="red"
        tooltip="Cuenta suspendida temporalmente" 
    />
</div>
```

### 3. Navegación
```blade
<div class="flex gap-2">
    <x-mary-button 
        icon="o-home" 
        tooltip="Panel principal" 
    />
    <x-mary-button 
        icon="o-cog-6-tooth" 
        tooltip="Configuración del sistema" 
    />
    <x-mary-button 
        icon="o-user" 
        tooltip="Perfil de usuario" 
    />
</div>
```

## Personalización

### Posición del Tooltip
```blade
<!-- Por defecto (arriba) -->
<x-mary-button tooltip="Tooltip arriba" />

<!-- Con posición específica (si es soportado) -->
<x-mary-button tooltip="Tooltip abajo" tooltip-position="bottom" />
```

### Duración del Tooltip
```blade
<!-- Tooltip que permanece visible -->
<x-mary-button tooltip="Información persistente" tooltip-persistent />
```

## Consideraciones de Accesibilidad

### 1. Texto Alternativo
```blade
<x-mary-button 
    icon="o-trash" 
    tooltip="Eliminar registro"
    aria-label="Eliminar registro" 
/>
```

### 2. Navegación por Teclado
```blade
<x-mary-button 
    label="Acción" 
    tooltip="Descripción de la acción"
    tabindex="0" 
/>
```

## Troubleshooting

### Problema: Tooltip no aparece
**Solución**: Verificar que el componente tenga el atributo `tooltip` correctamente escrito.

### Problema: Tooltip aparece en posición incorrecta
**Solución**: Verificar que no haya CSS que interfiera con el posicionamiento del tooltip.

### Problema: Tooltip no es legible
**Solución**: Asegurar que el texto del tooltip tenga suficiente contraste con el fondo.

## Conclusión

Los tooltips en MaryUI son una herramienta poderosa para mejorar la experiencia del usuario. Proporcionan información contextual sin saturar la interfaz y ayudan a los usuarios a entender mejor las funcionalidades disponibles.

### Recursos Adicionales

- [Documentación oficial de MaryUI](https://mary-ui.com/)
- [Guía de accesibilidad](https://www.w3.org/WAI/WCAG21/quickref/)
- [Mejores prácticas de UX](https://www.nngroup.com/articles/tooltip-guidelines/) 
