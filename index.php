<?php
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
} else {
    // Si está logueado, redirigir al dashboard
    header("Location: views/pages/dashboard.php");
    exit;
}
?>
