<?php
/**
 * Logout del sistema
 * Cierra la sesión del usuario y redirige al login
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../controllers/AuthController.php';

try {
    $authController = new AuthController();
    
    // Procesar logout
    $resultado = $authController->logout();
    
    // Redirigir al login con mensaje de éxito
    if (isset($resultado['redirect'])) {
        $redirect = $resultado['redirect'] . '?message=' . urlencode('Sesión cerrada correctamente');
        header('Location: ' . $redirect);
    } else {
        header('Location: /Sistema_Premoldeado/views/pages/auth/login.php?message=' . urlencode('Sesión cerrada correctamente'));
    }
    
} catch (Exception $e) {
    // En caso de error, destruir sesión manualmente y redirigir
    session_destroy();
    header('Location: /Sistema_Premoldeado/views/pages/auth/login.php?message=' . urlencode('Sesión cerrada'));
}

exit;
?>
