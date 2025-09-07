<?php
/**
 * Header del sistema - Layout principal
 * Incluye Bootstrap, Font Awesome y navegación
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación (excepto para páginas públicas)
$publicPages = ['login', 'logout'];
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

if (!in_array($currentPage, $publicPages)) {
    require_once __DIR__ . '/../../controllers/AuthController.php';
    $authController = new AuthController();
    $authController->requiereAutenticacion();
}

// Variables por defecto
$pageTitle = $pageTitle ?? 'Sistema Premoldeado';
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$perfil_nombre = $_SESSION['perfil_nombre'] ?? 'Sin perfil';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Sistema Premoldeado</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="/Sistema_Premoldeado/assets/css/style.css" rel="stylesheet">
    
</head>
<body>
    <!-- Navegación Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-industry me-2"></i>Sistema Premoldeado
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menú Principal Dinámico -->
                <?php include __DIR__ . '/../components/menu.php'; ?>
                
                <!-- Usuario Logueado -->
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?= htmlspecialchars($usuario_nombre) ?>
                            <span class="badge bg-secondary ms-2"><?= htmlspecialchars($perfil_nombre) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../pages/auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Contenido Principal -->
    <div class="container-fluid main-content">
        <div class="row">
            <div class="col-12">
