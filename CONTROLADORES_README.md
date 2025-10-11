# Flujo de Login, Autenticación, Navegación y Control de Acceso

## 1. Login (`login.php`)
- El usuario ingresa sus credenciales en el formulario de login.
- Al enviar el formulario, se instancia `AuthController` y se llama a su método `login($email, $password)`.
- Si el login es exitoso, el método retorna un array con `'success' => true` y la URL de redirección al dashboard.
- `login.php` redirige al usuario al dashboard usando `header('Location: ...')`.

## 2. Autenticación y Sesión (`AuthController.php`)
- El método `login` valida las credenciales y, si son correctas, llama a `crearSesion($usuario)`.
- `crearSesion` guarda los datos del usuario en la sesión y obtiene los módulos permitidos para su perfil.
- Para estructurar estos módulos, llama a `NavigationController::prepararModulosParaMenu($modulos)`, que enriquece la lista de módulos con iconos, URLs y submódulos.
- Los módulos enriquecidos se guardan en `$_SESSION['modulos']`.

## 3. Menú y Navegación (`NavigationController.php`)
- `NavigationController` tiene la función `prepararModulosParaMenu`, que toma la lista de módulos del usuario y la cruza con la configuración global (`getConfiguracionModulos`).
- Así, cada módulo del usuario tiene su icono, url y submódulos listos para ser usados en el menú de la interfaz.
- El menú de la aplicación se construye dinámicamente usando la información de `$_SESSION['modulos']`.

## 4. Control de Acceso (`BaseController.php` y `modules.php`)
- Todos los controladores heredan de `BaseController`.
- En el constructor de `BaseController`, se verifica automáticamente que el usuario esté autenticado.
- Para cada acción protegida (por ejemplo, ver usuarios), el controlador llama a `verificarAccesoModulo(ModuleConfig::USUARIOS)`.
- `modules.php` define constantes para cada módulo, facilitando el uso de nombres claros en vez de números.
- `verificarAccesoModulo` revisa si el módulo correspondiente está en la lista de módulos del usuario (`$_SESSION['modulos']`). Si no tiene acceso, se redirige o se muestra un error.

---

## Resumen del flujo
1. El usuario inicia sesión en `login.php`.
2. `AuthController` valida y crea la sesión, obteniendo los módulos permitidos y estructurándolos con `NavigationController`.
3. El menú de la aplicación se genera dinámicamente según los módulos del usuario.
4. Cada vez que el usuario accede a una sección, el controlador verifica si tiene permiso usando las constantes de `modules.php` y la lógica de `BaseController`.

Este diseño permite un control de acceso centralizado, un menú personalizado y una gestión clara de permisos y navegación.

---

## Nota sobre el menú actual
Actualmente, el componente `views/components/menu.php` **no utiliza la lógica de módulos dinámicos** guardados en la sesión (`$_SESSION['modulos']`). En su lugar, muestra un menú fijo y estático, igual para todos los usuarios, independientemente de sus permisos. 

Para que el menú sea realmente personalizado y seguro, se recomienda modificar este componente para que recorra y utilice la estructura de módulos almacenada en la sesión, generada por `NavigationController::prepararModulosParaMenu` al momento del login.
