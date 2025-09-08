<?php
/**
 * Controlador Base
 * Proporciona funcionalidad común para todos los controladores
 */

require_once __DIR__ . '/AuthController.php';

class BaseController {
    protected $auth;
    protected $usuario;
    
    public function __construct() {
        $this->auth = new AuthController();
        
        // Verificar autenticación automáticamente
        $this->verificarAcceso();
        
        // Obtener datos del usuario logueado
        $this->usuario = $this->auth->getUsuarioLogueado();
    }
    
    /**
     * Verificar acceso a un módulo específico
     * 
     * @param int $moduloId ID del módulo
     * @param bool $redirectOnFail Si debe redireccionar en caso de fallo
     * @return bool
     */
    protected function verificarAccesoModulo($moduloId, $redirectOnFail = true) {
        $tieneAcceso = $this->auth->verificarAccesoModulo($moduloId);
        
        if (!$tieneAcceso && $redirectOnFail) {
            $this->redirect(
                '../dashboard.php', 
                'No tienes permisos para acceder a este módulo', 
                'error'
            );
        }
        
        return $tieneAcceso;
    }
    
    /**
     * Verificar acceso (override en controladores que no requieren auth)
     */
    protected function verificarAcceso() {
        $this->auth->requiereAutenticacion();
    }
    
    /**
     * Renderizar vista con datos
     */
    protected function render($vista, $datos = []) {
        // Hacer disponibles los datos en la vista
        extract($datos);
        
        // Datos del usuario siempre disponibles
        $usuario_nombre = $this->usuario['nombre'] ?? 'Usuario';
        $perfil_nombre = $this->usuario['perfil_nombre'] ?? 'Sin perfil';
        
        // Incluir la vista
        include $vista;
    }
    
    /**
     * Redirigir con mensaje
     */
    protected function redirect($url, $message = null, $type = 'info') {
        if ($message) {
            $_SESSION['flash_message'] = [
                'message' => $message,
                'type' => $type
            ];
        }
        
        header("Location: $url");
        exit;
    }
    
    /**
     * Respuesta JSON
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Obtener mensaje flash y eliminarlo de la sesión
     */
    protected function getFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }
    
    /**
     * Validar datos de entrada
     */
    protected function validarDatos($datos, $reglas) {
        $errores = [];
        
        foreach ($reglas as $campo => $regla) {
            $valor = $datos[$campo] ?? null;
            
            // Requerido
            if (isset($regla['required']) && $regla['required'] && empty($valor)) {
                $errores[$campo] = "El campo {$campo} es requerido";
                continue;
            }
            
            // Si el campo está vacío y no es requerido, continuar
            if (empty($valor)) {
                continue;
            }
            
            // Tipo de dato
            if (isset($regla['type'])) {
                switch ($regla['type']) {
                    case 'email':
                        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                            $errores[$campo] = "El campo {$campo} debe ser un email válido";
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($valor)) {
                            $errores[$campo] = "El campo {$campo} debe ser numérico";
                        }
                        break;
                    case 'string':
                        if (!is_string($valor)) {
                            $errores[$campo] = "El campo {$campo} debe ser texto";
                        }
                        break;
                }
            }
            
            // Longitud mínima
            if (isset($regla['min_length']) && strlen($valor) < $regla['min_length']) {
                $errores[$campo] = "El campo {$campo} debe tener al menos {$regla['min_length']} caracteres";
            }
            
            // Longitud máxima
            if (isset($regla['max_length']) && strlen($valor) > $regla['max_length']) {
                $errores[$campo] = "El campo {$campo} no puede tener más de {$regla['max_length']} caracteres";
            }
        }
        
        return $errores;
    }
    
    /**
     * Sanitizar datos de entrada
     */
    protected function sanitizarDatos($datos) {
        $datosSanitizados = [];
        
        foreach ($datos as $key => $value) {
            if (is_string($value)) {
                $datosSanitizados[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $datosSanitizados[$key] = $value;
            }
        }
        
        return $datosSanitizados;
    }
    
    /**
     * Verificar método HTTP
     */
    protected function verificarMetodo($metodoEsperado) {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($metodoEsperado)) {
            http_response_code(405);
            die("Método no permitido");
        }
    }
    
    /**
     * Verificar token CSRF (para implementar posteriormente)
     */
    protected function verificarCSRF($token) {
        // TODO: Implementar verificación CSRF
        return true;
    }
}
?>
