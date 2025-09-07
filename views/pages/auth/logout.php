<?php
/**
 * Logout del sistema
 * Cierra la sesión del usuario y redirige al login
 */

require_once __DIR__ . '/../../../controllers/AuthController.php';

$authController = new AuthController();

// Procesar logout
$resultado = $authController->logout();

// Redirigir al login
header('Location: ' . $resultado['redirect']);
exit;
?>
