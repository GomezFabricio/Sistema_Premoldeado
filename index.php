
<?php
// Diagnóstico: mostrar errores fatales y warnings en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Punto de entrada principal del sistema
 * Sistema Premoldeados - Redirección al login o dashboard
 */

require_once __DIR__ . '/controllers/AuthController.php';

// Crear instancia del controlador de autenticación
$authController = new AuthController();

// Verificar si el usuario está logueado
if (!$authController->verificarAutenticacion()) {
    // Si no está logueado, redirigir al login
    header("Location: views/pages/auth/login.php");
    exit;
}

// Enrutamiento por parámetro module (nuevo sistema)
if (isset($_GET['module'])) {
    $module = strtolower($_GET['module']);
    $action = $_GET['a'] ?? 'index';
    
    switch ($module) {
        case 'usuarios':
            require_once __DIR__ . '/controllers/UsuarioController.php';
            try {
                $usuarioController = new UsuarioController();
                $usuarioController->handleRequest();
            } catch (Exception $e) {
                echo "Error en módulo usuarios: " . $e->getMessage();
            }
            exit;

        case 'clientes':
            require_once __DIR__ . '/controllers/ClienteController.php';
            try {
                $clienteController = new ClienteController();
                $clienteController->handleRequest();
            } catch (Exception $e) {
                echo "Error en módulo clientes: " . $e->getMessage();
            }
            exit;
            
        case 'productos':
            require_once __DIR__ . '/controllers/ProductoController.php';
            $productoController = new ProductoController();
            if (method_exists($productoController, $action)) {
                $productoController->$action();
            } else {
                $productoController->listado();
            }
            exit;
            
        case 'produccion':
            require_once __DIR__ . '/controllers/ProduccionController.php';
            $produccionController = new ProduccionController();
            if (method_exists($produccionController, $action)) {
                $produccionController->$action();
            } else {
                $produccionController->index();
            }
            exit;
            
        // Agregar más módulos aquí según sea necesario
        default:
            echo "Módulo no encontrado: " . htmlspecialchars($module);
            exit;
    }
}

// Enrutamiento por parámetro controller (sistema anterior - compatibilidad)
if (isset($_GET['controller'])) {
    $controller = strtolower($_GET['controller']);
    $action = $_GET['action'] ?? 'index';
    
    switch ($controller) {
        case 'produccion':
            require_once __DIR__ . '/controllers/ProduccionController.php';
            $produccionController = new ProduccionController();
            if (method_exists($produccionController, $action)) {
                $produccionController->$action();
            } else {
                $produccionController->index();
            }
            exit;
            
        case 'producto':
            require_once __DIR__ . '/controllers/ProductoController.php';
            $productoController = new ProductoController();
            if (method_exists($productoController, $action)) {
                $productoController->$action();
            } else {
                $productoController->listado();
            }
            exit;
    }
}

// Si no hay controller, redirigir al dashboard principal
header("Location: dashboard.php");
exit;
?>
