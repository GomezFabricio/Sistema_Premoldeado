<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../../../dashboard.php');
    exit;
}

require_once __DIR__ . '/../../../controllers/AuthController.php';

$error = '';
$message = '';
$authController = new AuthController();

// Verificar si hay mensaje de logout u otro mensaje
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Procesar login usando el controlador
    $resultado = $authController->login($email, $password);
    
    if ($resultado['success']) {
        // Redirigir al dashboard
        header('Location: ' . $resultado['redirect']);
        exit;
    } else {
        $error = $resultado['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
            min-height: 100vh;
        }
        
        .login-left {
            background: linear-gradient(45deg, #6f42c1, #007bff);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="row g-0">
                            <!-- Panel Izquierdo -->
                            <div class="col-lg-5 d-none d-lg-block">
                                <div class="login-left text-white p-5 h-100 d-flex flex-column justify-content-center">
                                    <div>
                                        <h1 class="display-6 fw-bold mb-3">
                                            <i class="fas fa-industry me-3"></i>PREMOLDEADO
                                        </h1>
                                        <p class="fs-5 opacity-75 mb-4">
                                            Sistema de Gestión Integral
                                        </p>
                                        
                                        <div class="mt-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas fa-shield-alt me-3 opacity-75"></i>
                                                <span>Seguridad Avanzada</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas fa-chart-line me-3 opacity-75"></i>
                                                <span>Reportes en Tiempo Real</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas fa-users me-3 opacity-75"></i>
                                                <span>Gestión Multi-usuario</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas fa-mobile-alt me-3 opacity-75"></i>
                                                <span>Diseño Responsivo</span>
                                            </div>
                                            
                                            <!-- Credenciales de Prueba -->
                                            <hr class="my-4 opacity-25">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user me-3 opacity-75"></i>
                                                <span class="small">Usuario: admin@sistema.com</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-key me-3 opacity-75"></i>
                                                <span class="small">Contraseña: admin123</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Panel Derecho - Formulario -->
                            <div class="col-lg-7">
                                <div class="p-5">
                                    <h2 class="h3 fw-bold text-dark mb-2">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </h2>
                                    <p class="text-muted mb-4">Accede a tu cuenta para continuar</p>
                                    
                                    <!-- Mensaje de Éxito -->
                                    <?php if ($message): ?>
                                        <div class="alert alert-success border-0 rounded-3 mb-4">
                                            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Mensaje de Error -->
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                                            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Credenciales Demo (versión móvil) -->
                                    <div class="alert alert-info border-0 rounded-3 d-lg-none mb-4">
                                        <div class="fw-semibold mb-2">
                                            <i class="fas fa-info-circle me-2"></i>Datos de Prueba
                                        </div>
                                        <small>
                                            <strong>Usuario:</strong> admin@sistema.com<br>
                                            <strong>Contraseña:</strong> admin123
                                        </small>
                                    </div>
                                    
                                    <form method="POST" novalidate>
                                        <div class="form-floating mb-4">
                                            <input type="email" 
                                                   class="form-control border-2 rounded-3" 
                                                   id="email" 
                                                   name="email" 
                                                   placeholder="nombre@ejemplo.com" 
                                                   value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                                                   required>
                                            <label for="email">
                                                <i class="fas fa-envelope me-2"></i>Correo Electrónico
                                            </label>
                                        </div>
                                        
                                        <div class="form-floating mb-4">
                                            <input type="password" 
                                                   class="form-control border-2 rounded-3" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Contraseña" 
                                                   required>
                                            <label for="password">
                                                <i class="fas fa-lock me-2"></i>Contraseña
                                            </label>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-semibold btn-login">
                                                <i class="fas fa-sign-in-alt me-2"></i>Ingresar al Sistema
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <div class="text-center mt-4">
                                        <small class="text-muted">
                                            <i class="fas fa-copyright me-1"></i>
                                            2025 Sistema Premoldeado. Todos los derechos reservados.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>