# Sistema de Controladores - Documentación

## Resumen
He implementado un sistema completo de controladores para manejar la autenticación y el flujo de información del sistema de premoldeado.

## Archivos Creados/Modificados

### 1. AuthController.php
**Ubicación:** `controllers/AuthController.php`
**Función:** Maneja toda la lógica de autenticación
- `login()` - Procesa el login y crea la sesión
- `logout()` - Cierra la sesión del usuario
- `verificarAutenticacion()` - Verifica si el usuario está logueado
- `verificarAccesoModulo()` - Verifica permisos por módulo
- `crearSesion()` - Crea la sesión con datos del usuario y módulos

### 2. BaseController.php
**Ubicación:** `controllers/BaseController.php`
**Función:** Controlador base con funcionalidad común
- Verificación automática de autenticación
- Métodos para renderizar vistas
- Validación y sanitización de datos
- Manejo de redirecciones y mensajes flash
- Respuestas JSON

### 3. UsuarioController.php (Ejemplo)
**Ubicación:** `controllers/UsuarioController.php`
**Función:** Ejemplo de implementación de un controlador específico
- Extiende BaseController
- Verifica permisos por módulo
- Muestra cómo estructurar métodos CRUD

## Archivos Modificados

### 1. login.php
**Cambios:**
- Ahora usa AuthController en lugar de lógica directa
- Mejor manejo de errores
- Código más limpio y mantenible

### 2. index.php
**Cambios:**
- Simplificado a solo redirección usando AuthController
- Eliminado código HTML mezclado

### 3. header.php
**Cambios:**
- Integrado con AuthController para verificación de autenticación

## Archivos Nuevos

### 1. dashboard.php
**Ubicación:** `views/pages/dashboard.php`
**Función:** Controlador y vista del dashboard principal unificados

### 2. logout.php
**Ubicación:** `views/pages/auth/logout.php`
**Función:** Maneja el cierre de sesión

## Flujo de Funcionamiento

### 1. Inicio de Sesión
```
index.php → AuthController → verifica sesión
   ↓
Si no logueado: login.php
Si logueado: dashboard.php
```

### 2. Login
```
login.php → AuthController.login() → autentica usuario
   ↓
Si éxito: crea sesión + obtiene módulos → dashboard
Si falla: muestra error
```

### 3. Dashboard
```
dashboard.php → DashboardController → verifica autenticación
   ↓
Obtiene datos del usuario y módulos → renderiza vista
```

### 4. Verificación de Módulos
```
Cualquier página → BaseController → AuthController
   ↓
Verifica si usuario tiene acceso al módulo específico
Si no: redirige con error
Si sí: permite acceso
```

## Características Implementadas

### ✅ Seguridad
- Verificación automática de autenticación
- Verificación de permisos por módulo
- Sanitización de datos de entrada
- Regeneración de ID de sesión
- Manejo seguro de cookies

### ✅ Estructura MVC
- Separación clara de controladores y vistas
- Controlador base con funcionalidad común
- Reutilización de código

### ✅ Manejo de Errores
- Validación de datos de entrada
- Mensajes flash para feedback al usuario
- Logging de errores

### ✅ Modularidad
- Sistema de módulos basado en permisos
- Menú dinámico según permisos del usuario
- Fácil extensión para nuevos módulos

## Cómo Usar

### 1. Para crear un nuevo controlador:
```php
<?php
require_once __DIR__ . '/BaseController.php';

class MiControlador extends BaseController {
    public function index() {
        // Tu lógica aquí
        $datos = ['pageTitle' => 'Mi Página'];
        $this->render('ruta/a/vista.php', $datos);
    }
}
?>
```

### 2. Para verificar acceso a módulo:
```php
// En tu controlador
if (!$this->auth->verificarAccesoModulo(ID_MODULO)) {
    $this->redirect('../dashboard.php', 'Sin permisos', 'error');
}
```

### 3. Para validar datos:
```php
$reglas = [
    'nombre' => ['required' => true, 'type' => 'string'],
    'email' => ['required' => true, 'type' => 'email']
];
$errores = $this->validarDatos($_POST, $reglas);
```

## Estado Actual
- ✅ Sistema de autenticación completo
- ✅ Controladores base implementados
- ✅ Verificación de permisos por módulo
- ✅ Menú dinámico funcionando
- ✅ Dashboard con información del usuario
- ✅ Manejo de sesiones seguro

## Próximos Pasos
1. Implementar controladores para cada módulo
2. Crear vistas para los formularios CRUD
3. Conectar con la base de datos real
4. Implementar validaciones específicas
5. Agregar más funcionalidades de seguridad (CSRF, etc.)

El sistema ahora está listo para ser usado y extendido fácilmente.
