<?php
/**
 * Controlador de Usuarios
 * Ejemplo de implementación usando BaseController
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/modules.php';

class UsuarioController extends BaseController {
    
    public function listar() {
        // Verificar acceso al módulo de usuarios
        $this->verificarAccesoModulo(ModuleConfig::USUARIOS);
        
        // Datos para la vista
        $datos = [
            'pageTitle' => 'Gestión de Usuarios',
            'usuarios' => $this->obtenerUsuarios(), // Aquí irían los datos reales
            'usuario' => $this->usuario
        ];
        
        // Renderizar vista
        $this->render(__DIR__ . '/../views/pages/usuarios/listado_usuarios.php', $datos);
    }
    
    public function crear() {
        // Verificar acceso al módulo de usuarios
        $this->verificarAccesoModulo(ModuleConfig::USUARIOS);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar creación
            $this->procesarCreacion();
        } else {
            // Mostrar formulario
            $datos = [
                'pageTitle' => 'Crear Usuario',
                'usuario' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/usuarios/crear_usuario.php', $datos);
        }
    }
    
    private function obtenerUsuarios() {
        // TODO: Implementar consulta real a la base de datos
        return [
            [
                'id' => 1,
                'nombre' => 'Administrador',
                'email' => 'admin@sistema.com',
                'perfil' => 'Administrador',
                'activo' => 1
            ],
            [
                'id' => 2,
                'nombre' => 'Usuario Demo',
                'email' => 'usuario@sistema.com',
                'perfil' => 'Usuario',
                'activo' => 1
            ]
        ];
    }
    
    private function procesarCreacion() {
        // Validar datos
        $reglas = [
            'nombre' => ['required' => true, 'type' => 'string', 'max_length' => 100],
            'email' => ['required' => true, 'type' => 'email'],
            'password' => ['required' => true, 'min_length' => 6],
            'perfil_id' => ['required' => true, 'type' => 'numeric']
        ];
        
        $datos = $this->sanitizarDatos($_POST);
        $errores = $this->validarDatos($datos, $reglas);
        
        if (empty($errores)) {
            // TODO: Implementar creación en base de datos
            $this->redirect('listado_usuarios.php', 'Usuario creado exitosamente', 'success');
        } else {
            // Mostrar errores
            $this->redirect('crear_usuario.php', 'Error en los datos: ' . implode(', ', $errores), 'error');
        }
    }
    
    // ============================================================================
    // SUBMÓDULO PERFILES - Métodos del controlador para gestión de perfiles
    // ============================================================================
    
    /**
     * Lista todos los perfiles del sistema
     * 
     * @return void
     */
    public static function indexPerfiles() {
        require_once __DIR__ . '/NavigationController.php';
        
        try {
            // Verificar acceso al módulo de usuarios
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                NavigationController::redirect(
                    NavigationController::getDashboardUrl(),
                    'No tienes permisos para acceder a este módulo',
                    'error'
                );
            }
            
            // Obtener perfiles del modelo
            $perfiles = Usuario::obtenerTodosPerfiles();
            
            // Preparar datos para la vista
            $pageTitle = 'Gestión de Perfiles';
            $usuario = $auth->getUsuarioLogueado();
            
            // Renderizar la vista usando los archivos reales
            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/pages/usuarios/perfiles/listado_perfiles.php';
            include __DIR__ . '/../views/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en indexPerfiles: " . $e->getMessage());
            NavigationController::redirect(
                NavigationController::getDashboardUrl(),
                'Error interno del servidor',
                'error'
            );
        }
    }
    
    /**
     * Muestra el formulario de creación de perfil
     * 
     * @return void
     */
    public static function createPerfiles() {
        require_once __DIR__ . '/NavigationController.php';
        
        try {
            // Verificar acceso al módulo de usuarios
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                NavigationController::redirect(
                    NavigationController::getDashboardUrl(),
                    'No tienes permisos para acceder a este módulo',
                    'error'
                );
            }
            
            // Obtener todos los módulos para el formulario
            $modulos = Usuario::obtenerTodosModulos();
            
            // Preparar datos para la vista
            $pageTitle = 'Crear Perfil';
            $usuario = $auth->getUsuarioLogueado();
            
            // Renderizar la vista usando los archivos reales
            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/pages/usuarios/perfiles/crear_perfil.php';
            include __DIR__ . '/../views/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en createPerfiles: " . $e->getMessage());
            NavigationController::redirect(
                NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),
                'Error interno del servidor',
                'error'
            );
        }
    }
    
    /**
     * Procesa la creación de un nuevo perfil
     * 
     * @return void
     */
    public static function storePerfiles() {
        try {
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                exit;
            }
            
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Sanitizar datos de entrada
            $datos = [
                'nombre' => isset($_POST['nombre']) ? trim(htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8')) : ''
            ];
            
            // Validar datos
            $reglas = [
                'nombre' => [
                    'required' => true,
                    'type' => 'string',
                    'max_length' => 45,
                    'min_length' => 2
                ]
            ];
            
            $errores = [];
            foreach ($reglas as $campo => $regla) {
                $valor = $datos[$campo] ?? null;
                
                // Campo requerido
                if (isset($regla['required']) && $regla['required'] && empty($valor)) {
                    $errores[$campo] = "El campo {$campo} es requerido";
                    continue;
                }
                
                if (!empty($valor)) {
                    // Longitud mínima
                    if (isset($regla['min_length']) && strlen($valor) < $regla['min_length']) {
                        $errores[$campo] = "El campo {$campo} debe tener al menos {$regla['min_length']} caracteres";
                    }
                    
                    // Longitud máxima
                    if (isset($regla['max_length']) && strlen($valor) > $regla['max_length']) {
                        $errores[$campo] = "El campo {$campo} no puede tener más de {$regla['max_length']} caracteres";
                    }
                }
            }
            
            if (!empty($errores)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errores)]);
                exit;
            }
            
            // Crear perfil
            $resultado = Usuario::crearPerfil($datos);
            
            // Asignar módulos si se seleccionaron
            if ($resultado['success'] && isset($_POST['modulos']) && is_array($_POST['modulos'])) {
                $modulosAsignados = Usuario::asignarModulosAPerfil($resultado['id'], $_POST['modulos']);
                if (!$modulosAsignados['success']) {
                    error_log("Error al asignar módulos al perfil: " . $modulosAsignados['message']);
                }
            }
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en storePerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
    
    /**
     * Muestra el formulario de edición de perfil
     * 
     * @param int $id ID del perfil a editar
     * @return void
     */
    public static function editPerfiles($id) {
        require_once __DIR__ . '/NavigationController.php';
        
        try {
            // Verificar acceso al módulo de usuarios
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                NavigationController::redirect(
                    NavigationController::getDashboardUrl(),
                    'No tienes permisos para acceder a este módulo',
                    'error'
                );
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                NavigationController::redirect(
                    NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),
                    'ID de perfil inválido',
                    'error'
                );
            }
            
            // Obtener datos del perfil
            $perfil = Usuario::obtenerPerfilPorId($id);
            if (!$perfil) {
                NavigationController::redirect(
                    NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),
                    'El perfil especificado no existe',
                    'error'
                );
            }
            
            // Obtener módulos disponibles y asignados
            $modulos = Usuario::obtenerTodosModulos();
            $modulosAsignados = Usuario::obtenerModulosAsignadosPorPerfil($id);
            $modulosAsignadosIds = array_column($modulosAsignados, 'id');
            
            // Preparar datos para la vista
            $pageTitle = 'Editar Perfil';
            $usuario = $auth->getUsuarioLogueado();
            
            // Renderizar la vista usando los archivos reales
            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/pages/usuarios/perfiles/editar_perfil.php';
            include __DIR__ . '/../views/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en editPerfiles: " . $e->getMessage());
            NavigationController::redirect(
                NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),
                'Error interno del servidor',
                'error'
            );
        }
    }
    
    /**
     * Procesa la actualización de un perfil
     * 
     * @param int $id ID del perfil a actualizar
     * @return void
     */
    public static function updatePerfiles($id) {
        try {
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                exit;
            }
            
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de perfil inválido']);
                exit;
            }
            
            // Sanitizar datos de entrada
            $datos = [
                'nombre' => isset($_POST['nombre']) ? trim(htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8')) : ''
            ];
            
            // Validar datos
            $reglas = [
                'nombre' => [
                    'required' => true,
                    'type' => 'string',
                    'max_length' => 45,
                    'min_length' => 2
                ]
            ];
            
            $errores = [];
            foreach ($reglas as $campo => $regla) {
                $valor = $datos[$campo] ?? null;
                
                // Campo requerido
                if (isset($regla['required']) && $regla['required'] && empty($valor)) {
                    $errores[$campo] = "El campo {$campo} es requerido";
                    continue;
                }
                
                if (!empty($valor)) {
                    // Longitud mínima
                    if (isset($regla['min_length']) && strlen($valor) < $regla['min_length']) {
                        $errores[$campo] = "El campo {$campo} debe tener al menos {$regla['min_length']} caracteres";
                    }
                    
                    // Longitud máxima
                    if (isset($regla['max_length']) && strlen($valor) > $regla['max_length']) {
                        $errores[$campo] = "El campo {$campo} no puede tener más de {$regla['max_length']} caracteres";
                    }
                }
            }
            
            if (!empty($errores)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errores)]);
                exit;
            }
            
            // Actualizar perfil
            $resultado = Usuario::actualizarPerfil($id, $datos);
            
            // Asignar módulos si se actualizaron
            if ($resultado['success'] && isset($_POST['modulos'])) {
                $modulosSeleccionados = is_array($_POST['modulos']) ? $_POST['modulos'] : [];
                $modulosAsignados = Usuario::asignarModulosAPerfil($id, $modulosSeleccionados);
                if (!$modulosAsignados['success']) {
                    error_log("Error al asignar módulos al perfil: " . $modulosAsignados['message']);
                }
            }
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en updatePerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
    
    /**
     * Elimina un perfil del sistema
     * 
     * @param int $id ID del perfil a eliminar
     * @return void
     */
    public static function deletePerfiles($id) {
        try {
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                exit;
            }
            
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de perfil inválido']);
                exit;
            }
            
            // Eliminar perfil
            $resultado = Usuario::eliminarPerfil($id);
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en deletePerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
    
    /**
     * Obtiene los módulos asignados a un perfil específico (para AJAX)
     * 
     * @param int $id ID del perfil
     * @return void
     */
    public static function getModulosPerfiles($id) {
        try {
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de perfil inválido']);
                exit;
            }
            
            // Obtener módulos del perfil
            $modulos = Usuario::obtenerModulosAsignadosPorPerfil($id);
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'modulos' => $modulos]);
            
        } catch (Exception $e) {
            error_log("Error en getModulosPerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
}

// Manejador de rutas para llamadas directas con parámetros GET
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'indexPerfiles':
            UsuarioController::indexPerfiles();
            break;
        case 'createPerfiles':
            UsuarioController::createPerfiles();
            break;
        case 'editPerfiles':
            if (isset($_GET['id'])) {
                UsuarioController::editPerfiles($_GET['id']);
            }
            break;
        case 'storePerfiles':
            UsuarioController::storePerfiles();
            break;
        case 'updatePerfiles':
            if (isset($_GET['id'])) {
                UsuarioController::updatePerfiles($_GET['id']);
            }
            break;
        case 'deletePerfiles':
            if (isset($_GET['id'])) {
                UsuarioController::deletePerfiles($_GET['id']);
            }
            break;
        case 'getModulosPerfiles':
            if (isset($_GET['id'])) {
                UsuarioController::getModulosPerfiles($_GET['id']);
            }
            break;
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Acción no encontrada']);
            break;
    }
}
?>
