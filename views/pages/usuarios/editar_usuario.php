<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Editar Usuario') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../components/common-styles.php';
    ?>
    
    <!-- Estilos específicos para esta página -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .page-header {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.25rem;
        }
        
        .btn {
            border-radius: 10px;
            font-weight: 500;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
        
        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }
    </style>
</head>
<body>

    <div class="container-fluid py-4">
        <?php
        // Mostrar mensajes flash si existen
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                        ($flash['type'] === 'error' ? 'alert-danger' : 'alert-info');
            echo '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <!-- Header de la página -->
        <div class="page-header text-center">
            <h1 class="mb-3">
                <i class="fas fa-edit me-2"></i>
                Editar Usuario: <?= htmlspecialchars($usuario['nombre_usuario'] ?? 'Usuario') ?>
            </h1>
            <p class="mb-0 opacity-75">Modifica la información del usuario y sus datos de acceso</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Botones de navegación -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=list" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>

                <!-- Formulario de edición -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Datos del Usuario
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="/Sistema_Premoldeado/controllers/UsuarioController.php?a=actualizar&id=<?= $usuario['id'] ?>" method="POST" id="formEditarUsuario">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_usuario" class="form-label">
                                        Nombre de Usuario <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nombre_usuario" 
                                               name="nombre_usuario" 
                                               value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" 
                                               required 
                                               minlength="3" 
                                               maxlength="50">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?= htmlspecialchars($usuario['email']) ?>" 
                                               required 
                                               maxlength="150">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Dejar vacío para mantener actual">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="eye-password"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">
                                        Solo ingrese si desea cambiar la contraseña (mínimo 6 caracteres)
                                    </small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="perfil_id" class="form-label">
                                        Perfil <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user-shield"></i>
                                        </span>
                                        <select class="form-select" id="perfil_id" name="perfil_id" required>
                                            <option value="">Seleccionar perfil...</option>
                                            <?php if (isset($perfiles) && is_array($perfiles)): ?>
                                                <?php foreach ($perfiles as $perfil): ?>
                                                    <option value="<?= $perfil['id'] ?>" 
                                                            <?= $usuario['perfil_id'] == $perfil['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($perfil['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="1" <?= $usuario['perfil_id'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                                <option value="2" <?= $usuario['perfil_id'] == 2 ? 'selected' : '' ?>>Cliente</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-8 mb-3">
                                    <label for="domicilio" class="form-label">Domicilio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               id="domicilio" 
                                               name="domicilio" 
                                               value="<?= htmlspecialchars($usuario['domicilio'] ?? '') ?>" 
                                               maxlength="200">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="activo" class="form-label">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-toggle-on"></i>
                                        </span>
                                        <select class="form-select" id="activo" name="activo" required>
                                            <option value="1" <?= $usuario['activo'] == 1 ? 'selected' : '' ?>>Activo</option>
                                            <option value="0" <?= $usuario['activo'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=list" 
                                           class="btn btn-secondary me-md-2">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Actualizar Usuario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script personalizado para esta página -->
    <script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const eye = document.getElementById('eye-' + fieldId);
        
        if (field.type === 'password') {
            field.type = 'text';
            eye.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            eye.className = 'fas fa-eye';
        }
    }

    $(document).ready(function() {
        console.log('✅ Página de editar usuario cargada correctamente');
        
        // Validación del formulario
        $('#formEditarUsuario').on('submit', function(e) {
            const password = $('#password').val();
            
            if (password && password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
        });
    });
    </script>
</body>
</html>
