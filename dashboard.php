<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: views/pages/auth/login.php');
    exit;
}

require_once 'controllers/NavigationController.php';
require_once 'controllers/AuthController.php';

// Obtener datos del usuario
$auth = new AuthController();
$usuario = $auth->getUsuarioLogueado();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .navbar-clean {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-bottom: 1px solid #e9ecef;
        }
        
        .dashboard-container {
            padding: 2rem 1rem;
        }
        
        .welcome-header {
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
            border: 1px solid #e9ecef;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .module-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            height: 100%;
        }
        
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        
        .module-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
            color: white;
        }
        
        .bg-primary-soft { background-color: #007bff; }
        .bg-success-soft { background-color: #28a745; }
        .bg-info-soft { background-color: #17a2b8; }
        .bg-warning-soft { background-color: #ffc107; }
        .bg-danger-soft { background-color: #dc3545; }
        .bg-secondary-soft { background-color: #6c757d; }
        .bg-dark-soft { background-color: #343a40; }
        
        .submenu-area {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .submenu-item {
            display: block;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        
        .submenu-item:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
            color: #007bff;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Navbar Simple -->
        <nav class="navbar navbar-expand-lg navbar-clean">
            <div class="container">
                <a class="navbar-brand fw-bold" href="#">
                    <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                    Sistema Premoldeado
                </a>
                <div class="navbar-nav ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>
                            <?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Perfiles Asignados:</h6></li>
                            <?php 
                            $perfilesUsuario = $usuario['perfiles'] ?? [];
                            if (!empty($perfilesUsuario)): ?>
                                <?php foreach ($perfilesUsuario as $perfil): ?>
                                    <li><span class="dropdown-item-text">
                                        <i class="fas fa-user-tag me-2 text-primary"></i>
                                        <?php echo htmlspecialchars($perfil['nombre']); ?>
                                    </span></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item-text text-muted">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Sin perfiles asignados
                                </span></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="views/pages/auth/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <div class="container">
                <?php
                // Obtener módulos del usuario desde la sesión
                $modulosUsuario = $_SESSION['modulos'] ?? [];
                
                // Colores para las cards
                $colores = ['primary-soft', 'success-soft', 'info-soft', 'warning-soft', 'danger-soft', 'secondary-soft', 'dark-soft'];
                $colorIndex = 0;
                ?>
                
                <!-- Header de Bienvenida -->
                <div class="welcome-header text-center">
                    <h1 class="mb-3">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Bienvenido, <?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?>
                    </h1>
                    <p class="mb-0 opacity-75">Panel de control del Sistema Premoldeado</p>
                </div>

                <!-- Stats Cards Útiles -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-th-large text-primary fa-2x mb-2"></i>
                            <h5>Módulos Disponibles</h5>
                            <p class="text-primary mb-0 fw-semibold fs-4"><?php echo count($modulosUsuario); ?> módulos</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-user-shield text-success fa-2x mb-2"></i>
                            <h5>Perfiles Asignados</h5>
                            <?php 
                            $perfilesCount = count($usuario['perfiles'] ?? []);
                            $perfilesTexto = $perfilesCount > 0 ? $perfilesCount . ' perfil' . ($perfilesCount > 1 ? 'es' : '') : 'Sin perfiles';
                            ?>
                            <p class="text-success mb-0 fw-semibold fs-4"><?php echo $perfilesTexto; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Sección de Módulos -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-4 text-gradient">
                            <i class="fas fa-th-large me-2"></i>
                            Módulos Disponibles
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <?php
                    if (!empty($modulosUsuario)) {
                        foreach ($modulosUsuario as $index => $modulo) {
                            $colorClass = 'bg-' . $colores[$colorIndex % count($colores)];
                            $colorIndex++;
                            $tieneSubmodulos = !empty($modulo['submodulos']);
                            $collapseId = "submods-" . $modulo['id'];
                    ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="module-card">
                                    <!-- Icono del módulo -->
                                    <div class="module-icon <?php echo $colorClass; ?>">
                                        <i class="<?php echo htmlspecialchars($modulo['icono']); ?>"></i>
                                    </div>
                                    
                                    <!-- Título del módulo -->
                                    <h5 class="fw-bold mb-3">
                                        <?php echo htmlspecialchars($modulo['nombre']); ?>
                                    </h5>
                                    
                                    <!-- Contador de submódulos -->
                                    <?php if ($tieneSubmodulos): ?>
                                        <p class="text-muted small mb-3">
                                            <i class="fas fa-list me-1"></i>
                                            <?php echo count($modulo['submodulos']); ?> opciones
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Botón principal -->
                                    <a href="<?php echo htmlspecialchars($modulo['url']); ?>" 
                                       class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Acceder
                                    </a>
                                    
                                    <?php if ($tieneSubmodulos): ?>
                                        <!-- Botón para mostrar submódulos -->
                                        <button class="btn btn-outline-secondary btn-sm w-100" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#<?php echo $collapseId; ?>" 
                                                aria-expanded="false">
                                            <i class="fas fa-chevron-down me-1"></i>
                                            Ver opciones
                                        </button>
                                        
                                        <!-- Submódulos -->
                                        <div class="collapse" id="<?php echo $collapseId; ?>">
                                            <div class="submenu-area">
                                                <?php foreach ($modulo['submodulos'] as $submodulo): ?>
                                                    <a href="<?php echo htmlspecialchars($submodulo['url']); ?>" 
                                                       class="submenu-item">
                                                        <i class="fas fa-chevron-right me-2"></i>
                                                        <?php echo htmlspecialchars($submodulo['nombre']); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                    ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="module-icon bg-warning-soft mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <h4 class="mb-3">No hay módulos disponibles</h4>
                                <p class="text-muted mb-3">Tu perfil no tiene módulos asignados. Contacta al administrador.</p>
                                <button class="btn btn-primary" onclick="location.reload()">
                                    <i class="fas fa-sync-alt me-2"></i>Actualizar
                                </button>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript simple -->
    <script>
        // Funcionalidad básica para collapse
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('.fa-chevron-down');
                    if (icon) {
                        setTimeout(() => {
                            if (icon.classList.contains('fa-chevron-down')) {
                                icon.classList.remove('fa-chevron-down');
                                icon.classList.add('fa-chevron-up');
                            } else {
                                icon.classList.remove('fa-chevron-up');
                                icon.classList.add('fa-chevron-down');
                            }
                        }, 100);
                    }
                });
            });
        });
    </script>
</body>
</html>
