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
    
    <!-- JavaScript (carga al final del head para mejor rendimiento) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="/Sistema_Premoldeado/assets/js/app.js" defer></script>
    
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <!-- Logo y Brand -->
                    <div class="col-auto">
                        <div class="brand-container">
                            <a href="<?php 
                                require_once __DIR__ . '/../../controllers/NavigationController.php';
                                echo NavigationController::getDashboardUrl(); 
                            ?>" class="brand-link">
                                <i class="fas fa-industry"></i>
                                <span class="brand-text">Sistema Premoldeado</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Navegación Principal -->
                    <div class="col">
                        <nav class="main-navigation">
                            <!-- Toggle Button para móviles/tablets -->
                            <button class="nav-toggle" type="button" id="navToggle" aria-label="Toggle navigation">
                                <span class="nav-toggle-icon"></span>
                                <span class="nav-toggle-icon"></span>
                                <span class="nav-toggle-icon"></span>
                            </button>
                            
                            <!-- Menú Principal -->
                            <div class="nav-menu" id="navMenu">
                                <?php include __DIR__ . '/../components/menu.php'; ?>
                            </div>
                        </nav>
                    </div>
                    
                    <!-- Usuario Info -->
                    <div class="col-auto">
                        <div class="user-info">
                            <div class="dropdown">
                                <button class="user-dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user"></i>
                                    <span class="user-name"><?= htmlspecialchars($usuario_nombre) ?></span>
                                    <span class="user-role"><?= htmlspecialchars($perfil_nombre) ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu" aria-labelledby="userDropdown">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo NavigationController::getDashboardUrl(); ?>">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <span>Dashboard</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item logout-item" href="<?php 
                                            require_once __DIR__ . '/../../controllers/NavigationController.php';
                                            echo NavigationController::getLogoutUrl(); 
                                        ?>">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>Cerrar Sesión</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="container-fluid">
            <div class="content-wrapper">
