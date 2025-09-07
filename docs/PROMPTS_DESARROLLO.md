# Prompts para Desarrollo Sistemático de Módulos
## Sistema Premoldeado - Templates de Desarrollo Automatizado

Esta documentación contiene los tres prompts base para desarrollar sistemáticamente cada módulo del sistema. Cada prompt debe ser personalizado reemplazando los placeholders indicados.

---

## 📋 ORDEN DE DESARROLLO RECOMENDADO

### Nivel 1: Submódulos Simples (Entidades de catálogo dentro de módulos principales)
**⚠️ IMPORTANTE**: Estos submódulos se desarrollan DENTRO del modelo del módulo padre correspondiente
**📋 DESARROLLO PRIORITARIO**: Comenzar con estos submódulos usando PROMPT 1B (crear modelo padre si no existe)

1. **Perfiles** → *Pertenece al módulo USUARIOS (ID: 1)*
2. **Tipos de Producto** → *Pertenece al módulo PRODUCTOS (ID: 3)*
3. **Estados de Pedido** → *Pertenece al módulo PEDIDOS Y RESERVAS (ID: 5)*
4. **Estados de Devolución** → *Pertenece al módulo PEDIDOS Y RESERVAS (ID: 5)*
5. **Estados de Reserva** → *Pertenece al módulo PEDIDOS Y RESERVAS (ID: 5)*
6. **Formas de Entrega** → *Pertenece al módulo PEDIDOS Y RESERVAS (ID: 5)*
7. **Estados de Producción** → *Pertenece al módulo PRODUCCIÓN (ID: 6)*
8. **Métodos de Pago** → *Pertenece al módulo VENTAS (ID: 9)*

### Nivel 2: Módulos Principales Simples (Sin dependencias complejas)
9. **Clientes** - Gestión de clientes (módulo independiente)
10. **Proveedores** - Gestión de proveedores (módulo independiente)
11. **Materiales** - Gestión de materiales (módulo independiente)

### Nivel 3: Módulos Principales con Submódulos (Dependencias moderadas)
12. **Usuarios** - Gestión de usuarios (incluye submódulo Perfiles)
13. **Productos** - Gestión de productos (incluye submódulo Tipos de Producto)

### Nivel 4: Módulos Complejos (Múltiples dependencias)
14. **Pedidos y Reservas** - Gestión completa de pedidos (incluye submódulos: Estados Pedido, Estados Devolución, Estados Reserva, Formas Entrega)
15. **Producción** - Gestión de producción (incluye submódulo Estados Producción)
16. **Compras** - Gestión de compras (depende de Materiales, Proveedores)
17. **Ventas** - Gestión de ventas (incluye submódulo Métodos de Pago)

---

## 🔧 PROMPT 1A: CREACIÓN DE MODELO PRINCIPAL
**Propósito**: Crear el modelo PHP principal con operaciones CRUD y submódulos incluidos

```
Necesito crear el modelo {NOMBRE_MODELO}.php para el módulo {NOMBRE_MODULO} del sistema de gestión de premoldeado. 

**CONTEXTO DEL SISTEMA:**
- Base de datos: MySQL con conexión PDO en config/database.php
- Patrón: Modelos estáticos sin instanciación
- Estilo: Eliminación lógica con campo 'activo'
- Convención: Métodos camelCase, nombres de tabla en plural

**ESTRUCTURA DE LA TABLA PRINCIPAL:**
Tabla: {NOMBRE_TABLA}
Campos principales:
{LISTAR_CAMPOS_PRINCIPALES}

**SUBMÓDULOS INCLUIDOS:**
{LISTAR_SUBMODULOS_Y_TABLAS}

**DEPENDENCIAS:**
{LISTAR_TABLAS_RELACIONADAS}

**ESPECIFICACIONES DEL MODELO:**

1. **Métodos CRUD básicos (entidad principal):**
   - obtenerTodos() - Listar registros activos con joins necesarios
   - obtenerPorId($id) - Obtener registro específico
   - crear($datos) - Insertar nuevo registro
   - actualizar($id, $datos) - Modificar registro existente
   - eliminar($id) - Eliminación lógica (activo = 0)

2. **Métodos para cada submódulo:**
   {METODOS_SUBMODULOS}

3. **Métodos específicos del módulo:**
   {METODOS_ESPECIFICOS}

4. **Validaciones requeridas:**
   - Validación de campos obligatorios
   - Validación de unicidad donde aplique
   - Sanitización de datos de entrada

5. **Estructura de respuesta:**
   - Todos los métodos deben retornar arrays asociativos
   - Manejo de errores con try-catch
   - Logging de errores críticos

**PATRONES A SEGUIR:**
- Usar prepared statements para todas las consultas
- Implementar validación de datos antes de operaciones
- Seguir el patrón del modelo Usuario.php existente
- Incluir documentación PHPDoc en métodos
- Agrupar métodos por entidad (principal + submódulos)

Crear el archivo models/{NOMBRE_MODELO}.php siguiendo estos lineamientos.
```

---

## 🔧 PROMPT 1B: CREACIÓN DE SUBMÓDULO EN MODELO EXISTENTE
**Propósito**: Agregar funcionalidad de submódulo a un modelo principal ya existente

```
Necesito agregar los métodos para el submódulo {NOMBRE_SUBMODULO} al modelo {NOMBRE_MODELO}.php existente en el sistema de gestión de premoldeado, en caso de que no exista, deberá ser creado.

**CONTEXTO DEL SISTEMA:**
- Modelo: models/{NOMBRE_MODELO}.php (crear si no existe)
- Submódulo: {NOMBRE_SUBMODULO} (tabla: {TABLA_SUBMODULO})
- Patrón: Métodos estáticos, eliminación lógica

**ESTRUCTURA DEL SUBMÓDULO:**
Tabla: {TABLA_SUBMODULO}
Campos: {CAMPOS_SUBMODULO}
Relación con módulo principal: {RELACION_PRINCIPAL}

**MÉTODOS A AGREGAR:**

```php
// ========================================
// MÉTODOS PARA {NOMBRE_SUBMODULO}
// ========================================

/**
 * Obtener todos los {NOMBRE_SUBMODULO} activos
 */
public static function obtenerTodos{NOMBRE_SUBMODULO}() {
    // Implementar consulta con joins si es necesario
}

/**
 * Obtener {NOMBRE_SUBMODULO} por ID
 */
public static function obtener{NOMBRE_SUBMODULO}PorId($id) {
    // Implementar consulta específica
}

/**
 * Crear nuevo {NOMBRE_SUBMODULO}
 */
public static function crear{NOMBRE_SUBMODULO}($datos) {
    // Validar y crear registro
}

/**
 * Actualizar {NOMBRE_SUBMODULO}
 */
public static function actualizar{NOMBRE_SUBMODULO}($id, $datos) {
    // Validar y actualizar registro
}

/**
 * Eliminar {NOMBRE_SUBMODULO} (lógico)
 */
public static function eliminar{NOMBRE_SUBMODULO}($id) {
    // Eliminación lógica
}
```

**ESPECIFICACIONES:**
- Mantener consistencia con métodos existentes del modelo
- Usar el mismo patrón de validación y sanitización
- Incluir documentación PHPDoc
- Agrupar métodos del submódulo con comentario separador

Agregar estos métodos al archivo models/{NOMBRE_MODELO}.php existente.
```

---

## 🎛️ PROMPT 2A: CREACIÓN DE CONTROLADOR PRINCIPAL
**Propósito**: Crear el controlador PHP principal que maneja la lógica de negocio y rutas (incluye submódulos)

```
Necesito crear el controlador {NOMBRE_CONTROLADOR}Controller.php para el módulo {NOMBRE_MODULO} del sistema de gestión de premoldeado.

**CONTEXTO DEL SISTEMA:**
- Herencia: Extiende BaseController (autenticación automática)
- Patrón: Métodos estáticos para acciones CRUD
- Respuesta: JSON para AJAX, redirecciones para formularios
- Validación: Usar métodos heredados validarDatos() y sanitizarDatos()

**MODELO ASOCIADO:**
- Modelo: {NOMBRE_MODELO}.php
- Métodos disponibles: {METODOS_DEL_MODELO}

**SUBMÓDULOS INCLUIDOS:**
{LISTAR_SUBMODULOS_CON_METODOS}

**ACCIONES REQUERIDAS:**

1. **Gestión de entidad principal:**
   - index() - Cargar listado con datos
   - create() - Mostrar formulario de creación
   - store() - Procesar creación de registro
   - edit($id) - Mostrar formulario de edición
   - update($id) - Procesar actualización
   - delete($id) - Eliminación lógica

2. **Gestión de submódulos:**
   {METODOS_SUBMODULOS_CONTROLADOR}

3. **Validaciones específicas:**
   {REGLAS_VALIDACION}

4. **Dependencias externas:**
   {CONTROLADORES_RELACIONADOS}

**ESTRUCTURA DEL CONTROLADOR:**

```php
<?php
require_once __DIR__ . '/../models/{NOMBRE_MODELO}.php';
require_once __DIR__ . '/BaseController.php';

class {NOMBRE_CONTROLADOR}Controller extends BaseController {
    
    // ========================================
    // MÉTODOS PARA ENTIDAD PRINCIPAL
    // ========================================
    
    public static function index() {
        // Verificar acceso al módulo
        // Obtener datos del modelo
        // Renderizar vista de listado
    }
    
    public static function create() {
        // Verificar acceso al módulo
        // Cargar datos para selects (si aplica)
        // Renderizar vista de creación
    }
    
    public static function store() {
        // Validar datos POST
        // Sanitizar entrada
        // Llamar al modelo
        // Responder con JSON o redirección
    }
    
    // ... resto de métodos CRUD principales
    
    // ========================================
    // MÉTODOS PARA SUBMÓDULOS
    // ========================================
    
    {METODOS_SUBMODULOS_PLACEHOLDER}
}
```

**ESPECIFICACIONES TÉCNICAS:**
- Verificar acceso con verificarAccesoModulo('{CODIGO_MODULO}')
- Usar jsonResponse() para respuestas AJAX
- Implementar validación robusta antes de operaciones
- Manejar errores con try-catch
- Incluir logs para operaciones críticas
- Agrupar métodos por entidad (principal + submódulos)

**PATRONES A SEGUIR:**
- Seguir estructura del UsuarioController.php existente
- Usar métodos heredados de BaseController
- Mantener separación entre lógica de vista y negocio
- Documentar métodos con PHPDoc

Crear el archivo controllers/{NOMBRE_CONTROLADOR}Controller.php con esta estructura.
```

---

## 🎛️ PROMPT 2B: AGREGAR MÉTODOS DE SUBMÓDULO A CONTROLADOR
**Propósito**: Agregar métodos para submódulo a un controlador principal existente

```
Necesito agregar los métodos para el submódulo {NOMBRE_SUBMODULO} al controlador {NOMBRE_CONTROLADOR}Controller.php existente.

**CONTEXTO:**
- Controlador existente: controllers/{NOMBRE_CONTROLADOR}Controller.php
- Submódulo: {NOMBRE_SUBMODULO}
- Métodos del modelo disponibles: {METODOS_MODELO_SUBMODULO}

**MÉTODOS A AGREGAR:**

```php
// ========================================
// MÉTODOS PARA {NOMBRE_SUBMODULO}
// ========================================

/**
 * Listar {NOMBRE_SUBMODULO}
 */
public static function index{NOMBRE_SUBMODULO}() {
    // Verificar acceso al módulo
    // Obtener datos del modelo
    // Renderizar vista específica del submódulo
}

/**
 * Crear {NOMBRE_SUBMODULO}
 */
public static function create{NOMBRE_SUBMODULO}() {
    // Verificar acceso
    // Renderizar formulario
}

/**
 * Procesar creación de {NOMBRE_SUBMODULO}
 */
public static function store{NOMBRE_SUBMODULO}() {
    // Validar datos POST
    // Crear registro
    // Responder
}

/**
 * Editar {NOMBRE_SUBMODULO}
 */
public static function edit{NOMBRE_SUBMODULO}($id) {
    // Verificar acceso
    // Obtener datos existentes
    // Renderizar formulario de edición
}

/**
 * Actualizar {NOMBRE_SUBMODULO}
 */
public static function update{NOMBRE_SUBMODULO}($id) {
    // Validar datos POST
    // Actualizar registro
    // Responder
}

/**
 * Eliminar {NOMBRE_SUBMODULO}
 */
public static function delete{NOMBRE_SUBMODULO}($id) {
    // Verificar acceso
    // Eliminar (lógico)
    // Responder JSON
}
```

**ESPECIFICACIONES:**
- Mantener consistencia con métodos existentes
- Usar verificarAccesoModulo() con el ID del módulo principal
- Seguir el mismo patrón de validación y respuesta
- Incluir documentación PHPDoc

Agregar estos métodos al controlador {NOMBRE_CONTROLADOR}Controller.php existente.
```

---

## 🖼️ PROMPT 3A: INTEGRACIÓN DE VISTAS PRINCIPAL CON SUBMÓDULOS
**Propósito**: Conectar las vistas del módulo principal y sus submódulos con el controlador

```
Necesito integrar las vistas del módulo {NOMBRE_MODULO} y sus submódulos con el controlador {NOMBRE_CONTROLADOR}Controller.php ya creado.

**CONTEXTO DEL SISTEMA:**
- Vistas base: Ya existen archivos PHP en views/pages/{CARPETA_MODULO}/
- Submódulos: {LISTAR_SUBMODULOS_Y_CARPETAS}
- Framework: Bootstrap 5.3.2 con Font Awesome 6.4.0
- Patrón: Formularios HTML con procesamiento PHP
- Componentes: table.php reutilizable para listados

**ARCHIVOS A MODIFICAR:**

1. **Módulo Principal**:
   - views/pages/{CARPETA_MODULO}/listado_{NOMBRE_MODULO}.php
   - views/pages/{CARPETA_MODULO}/crear_{NOMBRE_MODULO}.php
   - views/pages/{CARPETA_MODULO}/editar_{NOMBRE_MODULO}.php

2. **Submódulos**:
   {ARCHIVOS_SUBMODULOS}

**CONFIGURACIÓN DE TABLA PRINCIPAL:**
```php
$config = [
    'id' => '{TABLA_ID}',
    'columns' => [
        {COLUMNAS_TABLA_PRINCIPAL}
    ],
    'actions' => [
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editar{NOMBRE_MODULO}({id})'],
        ['label' => 'Eliminar', 'icon' => 'fas fa-trash', 'onclick' => 'eliminar{NOMBRE_MODULO}({id})']
    ]
];
```

**CONFIGURACIONES DE SUBMÓDULOS:**
{CONFIGURACIONES_SUBMODULOS}

**CAMPOS DE FORMULARIO PRINCIPAL:**
{CAMPOS_FORMULARIO_PRINCIPAL}

**CAMPOS DE SUBMÓDULOS:**
{CAMPOS_SUBMODULOS}

**TAREAS ESPECÍFICAS:**

1. **Actualizar vistas principales:**
   - Conectar con {NOMBRE_CONTROLADOR}Controller::index()
   - Configurar tabla principal con datos reales
   - Implementar formularios de creación/edición

2. **Conectar submódulos:**
   - Agregar enlaces a submódulos en vista principal
   - Conectar cada submódulo con sus métodos del controlador
   - Implementar navegación entre módulo principal y submódulos

3. **Funcionalidad AJAX:**
   - Implementar eliminación con confirmación
   - Manejar respuestas de submódulos
   - Feedback visual para todas las operaciones

**NAVEGACIÓN ENTRE MÓDULOS:**
```php
// En vista principal, agregar enlaces a submódulos
<div class="mb-3">
    <h5>Gestión de Submódulos</h5>
    {ENLACES_SUBMODULOS}
</div>
```

**ESTRUCTURA JAVASCRIPT:**
```javascript
// Funciones para módulo principal
function editar{NOMBRE_MODULO}(id) {
    window.location.href = `editar_{NOMBRE_MODULO}.php?id=${id}`;
}

function eliminar{NOMBRE_MODULO}(id) {
    if (confirm('¿Está seguro de eliminar este registro?')) {
        // Implementar llamada AJAX
    }
}

// Funciones para submódulos
{FUNCIONES_JAVASCRIPT_SUBMODULOS}
```

**ESPECIFICACIONES:**
- Mantener diseño Bootstrap consistente
- Implementar breadcrumbs de navegación
- Agregar enlaces de navegación entre módulo principal y submódulos
- Usar iconos Font Awesome apropiados para cada submódulo
- Manejar estados de carga y error para todas las entidades

Modificar los archivos de vista para conectarlos completamente con el controlador {NOMBRE_CONTROLADOR}Controller.php.
```

---

## 🖼️ PROMPT 3B: INTEGRACIÓN DE VISTAS DE SUBMÓDULO ESPECÍFICO
**Propósito**: Integrar las vistas de un submódulo específico con los métodos del controlador principal

```
Necesito integrar las vistas del submódulo {NOMBRE_SUBMODULO} (que pertenece al módulo {NOMBRE_MODULO}) con los métodos correspondientes del controlador {NOMBRE_CONTROLADOR}Controller.php.

**CONTEXTO:**
- Submódulo: {NOMBRE_SUBMODULO}
- Carpeta: views/pages/{CARPETA_SUBMODULO}/
- Métodos del controlador: {METODOS_CONTROLADOR_SUBMODULO}

**ARCHIVOS A MODIFICAR:**
- views/pages/{CARPETA_SUBMODULO}/listado_{NOMBRE_SUBMODULO}.php
- views/pages/{CARPETA_SUBMODULO}/crear_{NOMBRE_SUBMODULO}.php  
- views/pages/{CARPETA_SUBMODULO}/editar_{NOMBRE_SUBMODULO}.php

**CONFIGURACIÓN DE TABLA:**
```php
$config = [
    'id' => '{SUBMODULO_TABLE_ID}',
    'columns' => [
        {COLUMNAS_SUBMODULO}
    ],
    'actions' => [
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editar{NOMBRE_SUBMODULO}({id})'],
        ['label' => 'Eliminar', 'icon' => 'fas fa-trash', 'onclick' => 'eliminar{NOMBRE_SUBMODULO}({id})']
    ]
];
```

**CAMPOS DEL FORMULARIO:**
{CAMPOS_FORMULARIO_SUBMODULO}

**NAVEGACIÓN:**
- Breadcrumb: {NOMBRE_MODULO} → {NOMBRE_SUBMODULO}
- Botón "Volver al módulo principal"
- Enlaces de navegación entre submódulos relacionados

**CONEXIONES CON CONTROLADOR:**
- Listado: {NOMBRE_CONTROLADOR}Controller::index{NOMBRE_SUBMODULO}()
- Creación: {NOMBRE_CONTROLADOR}Controller::create{NOMBRE_SUBMODULO}() y store{NOMBRE_SUBMODULO}()
- Edición: {NOMBRE_CONTROLADOR}Controller::edit{NOMBRE_SUBMODULO}() y update{NOMBRE_SUBMODULO}()
- Eliminación: {NOMBRE_CONTROLADOR}Controller::delete{NOMBRE_SUBMODULO}()

**ESPECIFICACIONES:**
- Mantener consistencia visual con el módulo principal
- Implementar validación frontend apropiada
- Agregar navegación clara de regreso al módulo principal
- Usar iconos específicos para el tipo de submódulo

Modificar las vistas del submódulo {NOMBRE_SUBMODULO} para integrarlas completamente.
```

---

## 📝 EJEMPLOS DE USO

### Ejemplo 1: Desarrollar módulo principal con submódulos (PRODUCTOS)

**Paso 1A - Modelo Principal:**
Usar PROMPT 1A reemplazando:
- {NOMBRE_MODELO} → "Producto"
- {NOMBRE_MODULO} → "Productos"
- {NOMBRE_TABLA} → "productos"
- {LISTAR_CAMPOS_PRINCIPALES} → "id, ancho, largo, cantidad_disponible, stock_minimo, precio_unitario, tipo_producto_id, activo"
- {LISTAR_SUBMODULOS_Y_TABLAS} → "Tipos de Producto (tabla: tipo_producto)"
- {LISTAR_TABLAS_RELACIONADAS} → "tipo_producto (relación many-to-one)"
- {METODOS_SUBMODULOS} → "obtenerTodosTipoProducto(), crearTipoProducto(), actualizarTipoProducto(), eliminarTipoProducto()"

**Paso 2A - Controlador Principal:**
Usar PROMPT 2A reemplazando:
- {NOMBRE_CONTROLADOR} → "Producto"
- {CODIGO_MODULO} → "3"
- {LISTAR_SUBMODULOS_CON_METODOS} → "Tipos de Producto: indexTipoProducto(), createTipoProducto(), storeTipoProducto()"

**Paso 3A - Vistas Principal con Submódulos:**
Usar PROMPT 3A reemplazando:
- {NOMBRE_MODULO} → "productos"
- {CARPETA_MODULO} → "productos"
- {LISTAR_SUBMODULOS_Y_CARPETAS} → "Tipos de Producto (views/pages/productos/tipos/)"

### Ejemplo 2: Desarrollar submódulo específico (MÉTODOS DE PAGO en VENTAS)

**Paso 1B - Agregar al Modelo Ventas:**
Usar PROMPT 1B reemplazando:
- {NOMBRE_SUBMODULO} → "MetodoPago"
- {NOMBRE_MODELO} → "Venta"
- {TABLA_SUBMODULO} → "metodo_pago"
- {CAMPOS_SUBMODULO} → "id, nombre, activo"

**Paso 2B - Agregar al Controlador Ventas:**
Usar PROMPT 2B reemplazando:
- {NOMBRE_SUBMODULO} → "MetodoPago"
- {NOMBRE_CONTROLADOR} → "Venta"

**Paso 3B - Integrar Vistas del Submódulo:**
Usar PROMPT 3B reemplazando:
- {NOMBRE_SUBMODULO} → "metodos_pago"
- {NOMBRE_MODULO} → "Ventas"
- {CARPETA_SUBMODULO} → "ventas/metodos_pago"

### Ejemplo 3: Desarrollar módulo independiente simple (CLIENTES)

**Paso 1A - Modelo (sin submódulos):**
Usar PROMPT 1A reemplazando:
- {NOMBRE_MODELO} → "Cliente"
- {NOMBRE_MODULO} → "Clientes"
- {LISTAR_SUBMODULOS_Y_TABLAS} → "Ninguno"
- {METODOS_SUBMODULOS} → "No aplica"

**Paso 2A - Controlador (sin submódulos):**
Usar PROMPT 2A reemplazando:
- {NOMBRE_CONTROLADOR} → "Cliente"
- {CODIGO_MODULO} → "2"
- {LISTAR_SUBMODULOS_CON_METODOS} → "Ninguno"

**Paso 3A - Vistas (sin submódulos):**
Usar PROMPT 3A reemplazando:
- {NOMBRE_MODULO} → "clientes"
- {LISTAR_SUBMODULOS_Y_CARPETAS} → "Ninguno"

---

## 🚀 NOTAS DE IMPLEMENTACIÓN

### **Estrategia de Desarrollo Corregida**

1. **Módulos Principales Primero**: Desarrollar módulos principales (Usuarios, Productos, Pedidos, etc.) usando PROMPT 1A, 2A, 3A
2. **Submódulos Después**: Agregar submódulos a módulos principales existentes usando PROMPT 1B, 2B, 3B
3. **Módulos Independientes**: Usar PROMPT 1A, 2A, 3A (sin submódulos) para Clientes, Proveedores, Materiales

### **Orden de Ejecución Recomendado**

1. **Nivel 1 - Submódulos Simples (DESARROLLO PRIORITARIO)**:
   - Perfiles (crear modelo Usuario.php si no existe)
   - Tipos de Producto (crear modelo Producto.php si no existe)
   - Estados de Pedido (crear modelo Pedido.php si no existe)
   - Estados de Devolución (agregar al modelo Pedido.php)
   - Estados de Reserva (agregar al modelo Pedido.php)
   - Formas de Entrega (agregar al modelo Pedido.php)
   - Estados de Producción (crear modelo Produccion.php si no existe)
   - Métodos de Pago (crear modelo Venta.php si no existe)

2. **Nivel 2 - Módulos Independientes**:
   - Clientes (sin submódulos)
   - Proveedores (sin submódulos) 
   - Materiales (sin submódulos)

3. **Nivel 3 - Completar Módulos con Submódulos**:
   - Completar módulo Usuarios (ya tiene submódulo Perfiles)
   - Completar módulo Productos (ya tiene submódulo Tipos de Producto)
   - Completar módulo Pedidos y Reservas (ya tiene todos sus submódulos)
   - Completar módulo Producción (ya tiene submódulo Estados de Producción)
   - Completar módulo Ventas (ya tiene submódulo Métodos de Pago)

4. **Nivel 4 - Módulos Complejos con Dependencias**:
   - Compras (depende de Materiales, Proveedores)

### **Diferencias Clave en el Enfoque**

- **⚠️ CRÍTICO**: Los submódulos NO tienen su propio modelo separado
- **✅ CORRECTO**: Los submódulos son métodos adicionales en el modelo del módulo principal
- **🔄 FLUJO ACTUALIZADO**: Crear submódulos primero (usando PROMPT 1B) → Completar modelo principal → Crear controlador principal → Agregar métodos de submódulo → Integrar todas las vistas
- **📋 PRIORIDAD**: Desarrollar submódulos simples ANTES que los módulos principales completos
- **🏗️ CONSTRUCCIÓN**: Usar PROMPT 1B para submódulos (crea el modelo principal si no existe)

### **Validación de Estructura**

Antes de implementar, verificar en Usuario.php líneas 60-145:
- Módulos principales tienen su propia entrada en la configuración
- Submódulos aparecen en el array 'submodulos' del módulo principal
- Las URLs de submódulos apuntan a carpetas anidadas

### **Testing y Dependencias**

1. **Testing**: Probar módulo principal y todos sus submódulos antes de continuar
2. **Dependencias**: Respetar el orden de desarrollo para evitar errores de relaciones
3. **Consistencia**: Mantener patrones de nomenclatura entre módulo principal y submódulos
4. **Documentación**: Actualizar este documento con lecciones aprendidas específicas de submódulos

---

**Fecha de creación**: <?php echo date('Y-m-d H:i:s'); ?>
**Versión del sistema**: 1.0
**Autor**: Sistema de Desarrollo Automatizado
