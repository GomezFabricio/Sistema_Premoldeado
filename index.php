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

// Enrutamiento por parámetro controller
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
        // Puedes agregar otros controladores aquí
    }
}
        $controller = strtolower($_GET['controller']);
        $action = $_GET['action'] ?? 'index';
        switch ($controller) {
            case 'produccion':
                require_once __DIR__ . '/controllers/ProduccionController.php';
                $produccionController = new ProduccionController();
                if ($action === 'listado') {
                    $produccionController->listado();
                } elseif (method_exists($produccionController, $action)) {
                    $produccionController->$action();
                } else {
                    $produccionController->listado();
                }
                exit;
            case 'producto':
                require_once __DIR__ . '/controllers/ProductoController.php';
                $productoController = new ProductoController();
                if ($action === 'listado') {
                    $productoController->listado();
                } elseif (method_exists($productoController, $action)) {
                    $productoController->$action();
                } else {
                    $productoController->listado();
                }
                exit;
            // Puedes agregar otros controladores aquí
        }

// Si no hay controller, redirigir al dashboard
header("Location: views/pages/dashboard.php");
exit;
?>
