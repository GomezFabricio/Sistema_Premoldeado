<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Crear Usuario') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../components/common-styles.php';
    ?>
    
    <!-- Estilos específicos para esta página -->
    <style>
        .page-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            background: linear-gradient(135deg, #28a745, #20c997);
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
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
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
            echo '<i class="fas fa-info-circle me-2"></i>' . htmlspecialchars($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <!-- Header de la página -->
        <div class="page-header text-center">
            <h1 class="mb-3">
                <i class="fas fa-user-plus me-2"></i>
                <?= htmlspecialchars($titulo ?? 'Crear Usuario') ?>
            </h1>
            <p class="mb-0 opacity-75">Complete el formulario para agregar un nuevo usuario al sistema</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <!-- Formulario de creación -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            Información del Usuario
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" 
                              action="/Sistema_Premoldeado/controllers/UsuarioController.php?a=guardarNuevoUsuario" 
                              id="formCrearUsuario"
                              novalidate>
                                
                                <div class="row">
                                    <!-- Nombre de Usuario -->
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
                                                   placeholder="Ej: juan.perez"
                                                   required 
                                                   minlength="3" 
                                                   maxlength="50">
                                            <div class="invalid-feedback">
                                                Por favor, ingrese un nombre de usuario válido (3-50 caracteres).
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Mínimo 3 caracteres, solo letras, números y guiones bajos
                                        </small>
                                    </div>
                                    
                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            Correo Electrónico <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="email" 
                                                   name="email" 
                                                   placeholder="usuario@ejemplo.com"
                                                   required 
                                                   maxlength="150">
                                            <div class="invalid-feedback">
                                                Por favor, ingrese un email válido.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Contraseña -->
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            Contraseña <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password" 
                                                   required 
                                                   minlength="8">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="eye-password"></i>
                                            </button>
                                            <div class="invalid-feedback">
                                                La contraseña debe tener al menos 6 caracteres.
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Mínimo 8 caracteres, debe incluir letras y números
                                        </small>
                                    </div>
                                    
                                    <!-- Confirmar Contraseña -->
                                    <div class="col-md-6 mb-3">
                                        <label for="confirmar_password" class="form-label">
                                            Confirmar Contraseña <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="confirmar_password" 
                                                   name="confirmar_password" 
                                                   required 
                                                   minlength="8">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    onclick="togglePassword('confirmar_password')">
                                                <i class="fas fa-eye" id="eye-confirmar_password"></i>
                                            </button>
                                            <div class="invalid-feedback">
                                                Las contraseñas no coinciden.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Perfil -->
                                    <div class="col-md-6 mb-3">
                                        <label for="perfil_id" class="form-label">
                                            Perfil <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user-shield"></i>
                                            </span>
                                            <select class="form-select" id="perfil_id" name="perfil_id" required>
                                                <option value="">Seleccione un perfil...</option>
                                                <?php if (!empty($perfiles) && is_array($perfiles)): ?>
                                                    <?php foreach ($perfiles as $perfil): ?>
                                                        <option value="<?= htmlspecialchars($perfil['id']) ?>">
                                                            <?= htmlspecialchars($perfil['nombre']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="1">Administrador (Por defecto)</option>
                                                    <option value="2">Cliente (Por defecto)</option>
                                                <?php endif; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, seleccione un perfil.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Domicilio -->
                                    <div class="col-md-6 mb-3">
                                        <label for="domicilio" class="form-label">Domicilio</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="domicilio" 
                                                   name="domicilio" 
                                                   placeholder="Dirección completa"
                                                   maxlength="200">
                                        </div>
                                    </div>
                                    
                                    <!-- Estado del Usuario -->
                                    <div class="col-12 mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="activo" 
                                                   name="activo" 
                                                   value="1"
                                                   checked>
                                            <label class="form-check-label" for="activo">
                                                <i class="fas fa-toggle-on text-success me-1"></i>
                                                Usuario activo al crear
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">
                                            El usuario podrá acceder al sistema inmediatamente
                                        </small>
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=list" 
                                               class="btn btn-secondary me-md-2">
                                                <i class="fas fa-times me-2"></i>Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Crear Usuario
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
    </div>

    <!-- Scripts personalizados -->
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
        console.log('✅ Página de crear usuario cargada correctamente');
        
        // Validación en tiempo real para confirmar contraseña
        $('#confirmar_password').on('input', function() {
            const password = $('#password').val();
            const confirm = $(this).val();
            
            if (password !== confirm) {
                this.setCustomValidity('Las contraseñas no coinciden');
                $(this).addClass('is-invalid');
            } else {
                this.setCustomValidity('');
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        $('#password').on('input', function() {
            const password = $(this).val();
            const confirm = $('#confirmar_password');
            
            if (confirm.val() && password !== confirm.val()) {
                confirm[0].setCustomValidity('Las contraseñas no coinciden');
                confirm.addClass('is-invalid');
            } else if (confirm.val()) {
                confirm[0].setCustomValidity('');
                confirm.removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Validación del formulario antes del envío
        $('#formCrearUsuario').on('submit', function(e) {
            const password = $('#password').val();
            const confirm = $('#confirmar_password').val();
            
            if (password !== confirm) {
                e.preventDefault();
                $('#confirmar_password')[0].setCustomValidity('Las contraseñas no coinciden');
                $('#confirmar_password').addClass('is-invalid');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                $('#password')[0].setCustomValidity('La contraseña debe tener al menos 8 caracteres');
                $('#password').addClass('is-invalid');
                return false;
            }
        });
    });
    </script>
</body>
</html>
