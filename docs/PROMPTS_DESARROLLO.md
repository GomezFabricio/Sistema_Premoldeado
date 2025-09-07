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
Necesito agregar los métodos para el submódulo {NOMBRE_SUBMODULO} al sistema de gestión de premoldeado dentro de {MODELO_PADRE}, en caso de que no exista el modelo padre, deberá ser creado.

**CONTEXTO DEL SISTEMA:**
- Base de datos: MySQL con conexión PDO en config/database.php
- Patrón: Modelos estáticos sin instanciación
- Estilo: Eliminación lógica con campo 'activo'
- Convención: Métodos camelCase, nombres de tabla en plural

**SUBMÓDULO A DESARROLLAR:**
{NOMBRE_SUBMODULO}

**INSTRUCCIONES:**
1. **Analizar la base de datos** para identificar:
   - La tabla del submódulo y sus campos
   - El módulo principal al que pertenece
   - Las relaciones con otras tablas
   - Los campos obligatorios y opcionales

2. **Crear estructura del modelo completa para ese submódulo con comentarios y debidamente separado con comentarios de las demas funciones del modelo principal no pertenecientes al submódulo**:
   - Determinar el nombre del modelo principal basándose en la configuración
   - Crear el archivo del modelo si no existe
   - Agregar métodos CRUD estándar para el submódulo:
     * obtenerTodos{NOMBRE_SUBMODULO}() - Listar registros activos
     * obtener{NOMBRE_SUBMODULO}PorId($id) - Obtener registro específico  
     * crear{NOMBRE_SUBMODULO}($datos) - Insertar nuevo registro
     * actualizar{NOMBRE_SUBMODULO}($id, $datos) - Modificar registro existente
     * eliminar{NOMBRE_SUBMODULO}($id) - Eliminación lógica (activo = 0)

3. **Implementar validaciones automáticas**:
   - Validación de campos obligatorios según estructura de tabla
   - Validación de unicidad donde corresponda
   - Sanitización de datos de entrada
   - Manejo de errores con try-catch

4. **Seguir patrones establecidos**:
   - Usar prepared statements para todas las consultas
   - Incluir documentación PHPDoc en métodos
   - Agrupar métodos del submódulo con comentario separador
   - Mantener consistencia con el modelo Usuario.php existente

**ESPECIFICACIONES TÉCNICAS:**
- Todos los métodos deben retornar arrays asociativos
- Implementar logging de errores críticos
- Usar el mismo patrón de conexión a base de datos que Usuario.php
- Considerar relaciones con tablas padre si las hay

Analiza la base de datos, revisa la configuración del sistema y crea/actualiza el modelo correspondiente para el submódulo {NOMBRE_SUBMODULO}.
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
Necesito agregar los métodos del controlador para el submódulo {NOMBRE_SUBMODULO} al sistema de gestión de premoldeado.

**CONTEXTO DEL SISTEMA:**
- Herencia: Extiende BaseController (autenticación automática)
- Patrón: Métodos estáticos para acciones CRUD
- Respuesta: JSON para AJAX, redirecciones para formularios
- Validación: Usar métodos heredados validarDatos() y sanitizarDatos()

**SUBMÓDULO A DESARROLLAR:**
{NOMBRE_SUBMODULO}

**INSTRUCCIONES:**
1. **Analizar automáticamente**:
   - Revisar configuración en Usuario.php para identificar el módulo principal
   - Determinar el nombre del controlador principal
   - Verificar el ID del módulo para permisos
   - Identificar los métodos del modelo disponibles

2. **Crear/actualizar controlador** con métodos estándar:
   - index{NOMBRE_SUBMODULO}() - Listar registros
   - create{NOMBRE_SUBMODULO}() - Mostrar formulario de creación
   - store{NOMBRE_SUBMODULO}() - Procesar creación de registro
   - edit{NOMBRE_SUBMODULO}($id) - Mostrar formulario de edición
   - update{NOMBRE_SUBMODULO}($id) - Procesar actualización
   - delete{NOMBRE_SUBMODULO}($id) - Eliminación lógica

3. **Implementar validaciones automáticas**:
   - Analizar estructura de tabla para generar reglas de validación
   - Usar verificarAccesoModulo() con el ID del módulo principal
   - Implementar sanitización de datos
   - Manejo de errores con try-catch

4. **Estructura estándar de métodos**:
   - Verificar acceso al módulo en cada método
   - Usar jsonResponse() para respuestas AJAX
   - Implementar redirecciones apropiadas
   - Incluir logs para operaciones críticas

**ESPECIFICACIONES TÉCNICAS:**
- Mantener consistencia con métodos existentes del controlador
- Seguir el mismo patrón de validación y respuesta
- Incluir documentación PHPDoc
- Agrupar métodos del submódulo con comentario separador
- Usar métodos heredados de BaseController

Analiza el sistema, identifica el controlador principal y agrega los métodos para el submódulo {NOMBRE_SUBMODULO}.
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
Necesito integrar completamente las vistas del submódulo {NOMBRE_SUBMODULO} con el sistema de gestión de premoldeado.

**CONTEXTO DEL SISTEMA:**
- Framework: Bootstrap 5.3.2 con Font Awesome 6.4.0
- Patrón: Formularios HTML con procesamiento PHP
- Componentes: table.php reutilizable para listados
- Estilo: Diseño consistente con el sistema existente

**SUBMÓDULO A INTEGRAR:**
{NOMBRE_SUBMODULO}

**INSTRUCCIONES:**
1. **Analizar automáticamente**:
   - Revisar configuración en Usuario.php para identificar módulo principal y URLs
   - Determinar la estructura de carpetas del submódulo
   - Identificar los campos de la tabla para formularios
   - Verificar métodos del controlador disponibles

2. **Configurar archivos de vista**:
   - Actualizar views/pages/[carpeta]/listado_[submodulo].php
   - Actualizar views/pages/[carpeta]/crear_[submodulo].php  
   - Actualizar views/pages/[carpeta]/editar_[submodulo].php

3. **Generar configuración automática de tabla**:
   - Analizar campos de la tabla para definir columnas
   - Crear configuración $config con campos apropiados
   - Implementar acciones estándar (Editar, Eliminar)
   - Generar funciones JavaScript correspondientes

4. **Crear formularios automáticos**:
   - Generar campos de formulario basados en estructura de tabla
   - Implementar validación HTML5 apropiada
   - Configurar elementos select para relaciones con otras tablas
   - Agregar campos de estado (activo/inactivo)

5. **Implementar navegación**:
   - Breadcrumbs automáticos: [Módulo Principal] → [Submódulo]
   - Botones de navegación entre vistas
   - Enlaces de regreso al módulo principal
   - Iconos Font Awesome apropiados

6. **Conectar con controlador**:
   - Integrar llamadas a métodos del controlador
   - Implementar procesamiento de formularios
   - Configurar respuestas AJAX para eliminación
   - Agregar mensajes de éxito/error

**ESPECIFICACIONES TÉCNICAS:**
- Mantener diseño Bootstrap consistente
- Implementar validación frontend y backend
- Usar iconos específicos para el tipo de submódulo
- Manejar estados de carga y error
- Responsive design para diferentes dispositivos

Analiza el sistema, identifica la estructura del submódulo y crea/actualiza todas las vistas necesarias para {NOMBRE_SUBMODULO}.
```

---

## 📝 EJEMPLOS DE USO SIMPLIFICADOS

### Ejemplo 1: Desarrollar submódulo "Perfiles"

**Paso 1B - Modelo:**
```
Necesito agregar los métodos para el submódulo Perfiles al sistema de gestión de premoldeado, en caso de que no exista el modelo padre, deberá ser creado.

[... resto del PROMPT 1B ...]

**SUBMÓDULO A DESARROLLAR:**
Perfiles
```

**Paso 2B - Controlador:**
```
Necesito agregar los métodos del controlador para el submódulo Perfiles al sistema de gestión de premoldeado.

[... resto del PROMPT 2B ...]

**SUBMÓDULO A DESARROLLAR:**
Perfiles
```

**Paso 3B - Vistas:**
```
Necesito integrar completamente las vistas del submódulo Perfiles con el sistema de gestión de premoldeado.

[... resto del PROMPT 3B ...]

**SUBMÓDULO A INTEGRAR:**
Perfiles
```

### Ejemplo 2: Desarrollar submódulo "TipoProducto"

**Solo necesitas cambiar el nombre:**
- PROMPT 1B: `{NOMBRE_SUBMODULO}` → `TipoProducto`
- PROMPT 2B: `{NOMBRE_SUBMODULO}` → `TipoProducto`  
- PROMPT 3B: `{NOMBRE_SUBMODULO}` → `TipoProducto`

### Ejemplo 3: Desarrollar submódulo "MetodoPago"

**Solo necesitas cambiar el nombre:**
- PROMPT 1B: `{NOMBRE_SUBMODULO}` → `MetodoPago`
- PROMPT 2B: `{NOMBRE_SUBMODULO}` → `MetodoPago`
- PROMPT 3B: `{NOMBRE_SUBMODULO}` → `MetodoPago`

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
- **🔄 FLUJO AUTOMATIZADO**: Solo proporcionar nombre del submódulo → IA analiza base de datos → Crea estructura completa
- **📋 PRIORIDAD**: Desarrollar submódulos simples ANTES que los módulos principales completos
- **🏗️ CONSTRUCCIÓN**: PROMPT 1B analiza y crea el modelo principal si no existe
- **🤖 INTELIGENCIA**: La IA determina automáticamente módulo padre, campos, relaciones y validaciones

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
