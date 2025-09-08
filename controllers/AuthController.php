<?php
/**
 * Controlador de Autenticación
 * Maneja login, logout y verificación de sesiones
 */

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Procesar login del usuario
     */
    public function login($email, $password) {
        try {
            // Validar datos de entrada
            if (empty($email) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Por favor complete todos los campos'
                ];
            }
            
            // Sanitizar email
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'message' => 'El formato del email no es válido'
                ];
            }
            
            // Intentar autenticar
            $usuario = $this->usuarioModel->autenticar($email, $password);
            
            if ($usuario) {
                // Crear sesión
                $this->crearSesion($usuario);
                
                return [
                    'success' => true,
                    'message' => 'Login exitoso',
                    'redirect' => '../dashboard.php'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Email o contraseña incorrectos'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
    
    /**
     * Crear sesión del usuario
     */
    private function crearSesion($usuario) {
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
        
        // Guardar datos del usuario en sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre_usuarios'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['perfil_id'] = $usuario['perfiles_id'];
        $_SESSION['perfil_nombre'] = $usuario['perfil_nombre'];
        $_SESSION['login_time'] = time();
        
        // Obtener módulos del perfil y prepararlos para el menú
        try {
            $modulos = $this->usuarioModel->obtenerModulosPorPerfil($usuario['perfiles_id']);
            
            // NavigationController se encarga de asignar URLs y configuración
            require_once __DIR__ . '/NavigationController.php';
            $_SESSION['modulos'] = NavigationController::prepararModulosParaMenu($modulos);
        } catch (Exception $e) {
            error_log("Error obteniendo módulos: " . $e->getMessage());
            $_SESSION['modulos'] = [];
        }
    }
    
    /**
     * Cerrar sesión del usuario
     */
    public function logout() {
        // Destruir datos de sesión
        $_SESSION = [];
        
        // Destruir cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir sesión
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Sesión cerrada correctamente',
            'redirect' => 'login.php'
        ];
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function verificarAutenticacion() {
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }
    
    /**
     * Verificar si el usuario tiene acceso a un módulo específico
     */
    public function verificarAccesoModulo($moduloId) {
        if (!$this->verificarAutenticacion()) {
            return false;
        }
        
        if (!isset($_SESSION['modulos'])) {
            return false;
        }
        
        foreach ($_SESSION['modulos'] as $modulo) {
            if ($modulo['id'] == $moduloId) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtener datos del usuario logueado
     */
    public function getUsuarioLogueado() {
        if (!$this->verificarAutenticacion()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'perfil_id' => $_SESSION['perfil_id'],
            'perfil_nombre' => $_SESSION['perfil_nombre'],
            'modulos' => $_SESSION['modulos'] ?? []
        ];
    }
    
    /**
     * Middleware para proteger rutas
     */
    public function requiereAutenticacion($redirectUrl = null) {
        if (!$this->verificarAutenticacion()) {
            // Si no se especifica URL, determinar la ruta correcta al login
            if ($redirectUrl === null) {
                // Determinar la ruta relativa correcta basada en la ubicación actual
                $currentPath = $_SERVER['REQUEST_URI'];
                if (strpos($currentPath, '/views/pages/') !== false) {
                    $redirectUrl = '../auth/login.php';
                } else {
                    $redirectUrl = 'views/pages/auth/login.php';
                }
            }
            
            header("Location: $redirectUrl");
            exit;
        }
    }
}
?>
