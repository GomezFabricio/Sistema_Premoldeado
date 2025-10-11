# Sistema de Estilos y DataTables Comunes

Este documento describe cómo usar el sistema de estilos y DataTables comunes implementado en el Sistema Premoldeado para evitar duplicación de código y mantener consistencia visual.

## Archivos Creados

### 1. `assets/css/datatables-custom.css`
Contiene todos los estilos comunes para:
- Estilos base (body, cards, botones)
- Header de páginas
- Tablas y DataTables
- Badges y tooltips
- Alertas y notificaciones
- Elementos específicos del sistema (perfiles críticos, etc.)
- Responsive design
- Animaciones y utilidades

### 2. `assets/js/datatables-init.js`
Contiene la configuración JavaScript para:
- Configuraciones base de DataTables
- Configuraciones específicas por tipo de tabla
- Clase `DataTableManager` para manejo avanzado
- Clase `TableUtils` para funciones de utilidad
- Inicialización automática de tablas
- Manejo de tooltips

### 3. `views/components/common-styles.php`
Componente PHP que incluye:
- Enlaces a CSS externos (Bootstrap, FontAwesome, DataTables)
- Enlaces a archivos CSS personalizados
- Scripts externos (jQuery, Bootstrap, DataTables)
- Scripts personalizados del sistema
- Configuración global JavaScript
- Meta tags y estilos adicionales
- Loader y utilidades

### 4. `views/templates/listado_template.php`
Plantilla de ejemplo que muestra:
- Cómo estructurar una página de listado
- Uso correcto del componente common-styles
- Implementación de DataTables automática
- Mejores prácticas para el HTML

## Cómo Usar

### Para Páginas de Listado

1. **Incluir el componente común en el `<head>`:**
```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Título') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../components/common-styles.php';
    ?>
</head>
```

2. **Configurar la tabla con data-table-type:**
```html
<table class="table table-hover" id="mi_tabla" data-table-type="usuarios">
    <!-- contenido de la tabla -->
</table>
```

3. **Tipos de tabla disponibles:**
- `usuarios` - Para listados de usuarios
- `perfiles` - Para listados de perfiles
- `clientes` - Para listados de clientes
- `productos` - Para listados de productos
- `materiales` - Para listados de materiales
- `pedidos` - Para listados de pedidos
- `ventas` - Para listados de ventas
- `proveedores` - Para listados de proveedores
- `produccion` - Para listados de producción

### Configuración Automática

Al agregar `data-table-type="tipo"` a una tabla:
- Se inicializa automáticamente con DataTables
- Se aplican los estilos apropiados
- Se configuran las columnas según el tipo
- Se inicializan los tooltips
- Se aplica la traducción al español

### JavaScript Personalizado

Si necesitas JavaScript específico para la página:

```javascript
$(document).ready(function() {
    // La inicialización es automática, solo agrega lógica específica
    
    // Ejemplo: Evento personalizado
    $('#mi_tabla tbody').on('click', 'tr', function() {
        console.log('Fila seleccionada');
    });
});
```

### Funciones de Utilidad Disponibles

#### DataTableManager
```javascript
// Inicializar tabla manualmente (si necesario)
DataTableManager.initTable('mi_tabla', 'usuarios', {configuracion_custom});

// Actualizar datos
DataTableManager.updateTableData('mi_tabla', nuevos_datos);

// Recargar tabla
DataTableManager.reloadTable('mi_tabla');

// Destruir tabla
DataTableManager.destroyTable('mi_tabla');
```

#### SistemaUtils
```javascript
// Mostrar/ocultar loader
SistemaUtils.showLoader();
SistemaUtils.hideLoader();

// Formatear números y moneda
SistemaUtils.formatNumber(1234.56, 2); // "1.234,56"
SistemaUtils.formatCurrency(1234.56);   // "$1.234,56"

// Validaciones
SistemaUtils.isValidEmail('test@example.com'); // true

// Utilidades
SistemaUtils.scrollTo('elemento_id');
SistemaUtils.copyToClipboard('texto');
SistemaUtils.generateId(); // Genera ID único
```

## Migración de Páginas Existentes

Para migrar una página existente:

1. **Reemplazar los includes de CSS/JS:**
```php
// ANTES:
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
// ... más enlaces

// DESPUÉS:
<?php include_once __DIR__ . '/../../components/common-styles.php'; ?>
```

2. **Eliminar CSS duplicado:**
Remover todos los estilos que ya están en `datatables-custom.css`

3. **Actualizar la tabla:**
```html
<!-- ANTES: -->
<table class="table table-hover" id="mi_tabla">

<!-- DESPUÉS: -->
<table class="table table-hover" id="mi_tabla" data-table-type="usuarios">
```

4. **Simplificar JavaScript:**
```javascript
// ANTES:
$('#mi_tabla').DataTable({
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/Spanish.json"
    },
    "pageLength": 15,
    "order": [[0, "asc"]],
    "responsive": true,
    "columnDefs": [
        { "targets": [0, 2, 3, 4, 5], "className": "text-center" }
    ]
});

// DESPUÉS:
// ¡Nada! Es automático con data-table-type
```

## Beneficios

1. **Reducción de código duplicado** - Menos CSS y JavaScript repetido
2. **Consistencia visual** - Todos los listados tienen el mismo aspecto
3. **Mantenimiento fácil** - Cambios en un solo lugar se reflejan en todo el sistema
4. **Carga más rápida** - Archivos cacheables y optimizados
5. **Escalabilidad** - Fácil agregar nuevos tipos de tabla
6. **Accesibilidad** - Mejores prácticas implementadas centralmente

## Estructura de Archivos

```
Sistema_Premoldeado/
├── assets/
│   ├── css/
│   │   └── datatables-custom.css
│   └── js/
│       └── datatables-init.js
├── views/
│   ├── components/
│   │   └── common-styles.php
│   ├── templates/
│   │   └── listado_template.php
│   └── pages/
│       └── usuarios/
│           └── perfiles/
│               └── listado_perfiles.php (refactorizado)
```

## Notas Importantes

- El componente `common-styles.php` debe incluirse en el `<head>` de cada página
- Los archivos CSS y JS se cargan en el orden correcto automáticamente
- La función `getBaseUrl()` detecta automáticamente la URL base del proyecto
- El sistema es compatible con el modo debug (se puede activar definiendo `DEBUG_MODE`)
- Todos los tooltips se reinicializan automáticamente cuando se agrega contenido dinámico

## Próximos Pasos

1. Migrar todas las páginas de listado existentes al nuevo sistema
2. Agregar más configuraciones para tipos específicos según sea necesario
3. Implementar funciones de exportación (Excel, PDF) si es requerido
4. Considerar agregar funciones de búsqueda avanzada

---

**Autor:** Sistema Premoldeado  
**Fecha:** Octubre 2025  
**Versión:** 1.0.0