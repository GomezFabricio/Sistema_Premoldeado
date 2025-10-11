# Sistema de DataTables Flexible - Sistema Premoldeado

## Características del Sistema

El sistema de DataTables centralizado está diseñado para ser completamente flexible y adaptarse automáticamente a cualquier número de columnas en tus tablas.

## Configuraciones Automáticas Disponibles

### 1. Configuraciones Específicas por Módulo
- `usuarios` - Para listado de usuarios
- `perfiles` - Para listado de perfiles 
- `clientes` - Para listado de clientes
- `productos` - Para listado de productos
- `materiales` - Para listado de materiales
- `pedidos` - Para listado de pedidos
- `ventas` - Para listado de ventas
- `proveedores` - Para listado de proveedores
- `produccion` - Para listado de producción

### 2. Configuraciones Genéricas por Tamaño
- `simple` - Para tablas de 3-4 columnas
- `extended` - Para tablas de 7+ columnas
- `common` - Configuración base sin personalizaciones

## Cómo Usar el Sistema

### 1. Método Automático (Recomendado)
```html
<table class="table table-hover" id="mi_tabla" data-table-type="perfiles">
    <!-- Tu tabla aquí -->
</table>
```

El sistema detectará automáticamente:
- El número de columnas en tu tabla
- Aplicará las configuraciones correspondientes
- Centrará las columnas especificadas
- Deshabilitará el ordenamiento en columnas de acciones

### 2. Método Manual (Para Casos Especiales)
```javascript
// Inicializar tabla con configuración personalizada
DataTableManager.initTable('mi_tabla', 'usuarios', {
    "pageLength": 25,  // Configuración adicional
    "order": [[2, "desc"]]
});
```

### 3. Crear Nueva Configuración para Nuevo Módulo
```javascript
// En datatables-init.js, agregar nueva configuración:
mi_nuevo_modulo: {
    "order": [[0, "asc"]],
    "autoColumnConfig": true,
    "manualConfig": {
        "centerColumns": [0, 3, 4], // Columnas a centrar (ID, Estado, Acciones)
        "nonSortableColumns": [-1]  // Última columna no ordenable
    }
}
```

## Configuración Flexible de Columnas

### Índices Positivos y Negativos
```javascript
"centerColumns": [0, 2, -2, -1]  // Primera, tercera, antepenúltima, última
"nonSortableColumns": [-1]        // Última columna
```

- Índices positivos: `0, 1, 2, 3...` (desde el inicio)
- Índices negativos: `-1, -2, -3...` (desde el final)
  - `-1` = última columna
  - `-2` = antepenúltima columna

### Ejemplos por Número de Columnas

#### Tabla de 3 Columnas (ID, Nombre, Acciones)
```javascript
"manualConfig": {
    "centerColumns": [0, -1],  // ID y Acciones
    "nonSortableColumns": [-1] // Solo acciones
}
```

#### Tabla de 5 Columnas (ID, Nombre, Estado, Fecha, Acciones)
```javascript
"manualConfig": {
    "centerColumns": [0, 2, 3, 4], // ID, Estado, Fecha, Acciones
    "nonSortableColumns": [-1]     // Solo acciones
}
```

#### Tabla de 8 Columnas (ID, Nombre, Descripción, Precio, Stock, Estado, Fecha, Acciones)
```javascript
"manualConfig": {
    "centerColumns": [0, 3, 4, 5, 6, 7], // ID, Precio, Stock, Estado, Fecha, Acciones
    "nonSortableColumns": [-1]           // Solo acciones
}
```

## Funciones Útiles del Sistema

### Gestión de Tablas
```javascript
// Recargar datos de una tabla
DataTableManager.reloadTable('mi_tabla');

// Destruir tabla (útil antes de recargar página)
DataTableManager.destroyTable('mi_tabla');

// Actualizar datos sin recargar página
DataTableManager.updateTableData('mi_tabla', nuevosdatos);
```

### Utilidades
```javascript
// Formatear fecha
TableUtils.formatDate('2024-10-11'); // 11/10/2024

// Formatear moneda
TableUtils.formatCurrency(1500.50); // $1,500.50

// Confirmación de acción
TableUtils.confirmAction('¿Eliminar registro?', () => {
    // Tu código aquí
});
```

## Beneficios del Sistema Flexible

1. **Automático**: Solo agrega `data-table-type="tipo"` a tu tabla
2. **Escalable**: Funciona con cualquier número de columnas
3. **Mantenible**: Un solo lugar para configurar todos los DataTables
4. **Consistente**: Misma apariencia y comportamiento en toda la app
5. **Flexible**: Permite configuraciones específicas cuando se necesiten

## Migración de Tablas Existentes

Para migrar una tabla existente al sistema flexible:

1. **Elimina** el código JavaScript específico de esa tabla
2. **Agrega** `data-table-type="modulo"` a tu tabla HTML
3. **Incluye** `common-styles.php` en la página
4. **Listo** - El sistema se encarga del resto

El sistema detectará automáticamente el número de columnas y aplicará la configuración apropiada.