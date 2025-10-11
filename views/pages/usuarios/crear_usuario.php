<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-label { font-weight: 600; }
        .required { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Crear Nuevo Usuario
                    </h1>
                    <a href="/Sistema_Premoldeado/controllers/UsuarioController.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
                
                <!-- Formulario -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>Datos del Usuario
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/Sistema_Premoldeado/controllers/UsuarioController.php?a=guardarNuevoUsuario" id="formUsuario">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="nombre_usuario" class="form-label">
                                        Nombre de Usuario <span class="required">*</span>
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
                                    </div>
                                    <small class="form-text text-muted">
                                        Mínimo 3 caracteres, máximo 50
                                    </small>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">
                                        Correo Electrónico <span class="required">*</span>
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
                                               maxlength="100">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Contraseña <span class="required">*</span>
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
                                    </div>
                                    <small class="form-text text-muted">
                                        Mínimo 8 caracteres con letras y números
                                    </small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirmar_password" class="form-label">
                                        Confirmar Contraseña <span class="required">*</span>
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
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="perfil_id" class="form-label">
                                        Perfil <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-shield-alt"></i>
                                        </span>
                                        <select class="form-select" id="perfil_id" name="perfil_id" required>
                                            <option value="">Seleccione un perfil...</option>
                                            <?php if (!empty($perfiles)): ?>
                                                <?php foreach ($perfiles as $perfil): ?>
                                                    <option value="<?= $perfil['id'] ?>">
                                                        <?= htmlspecialchars($perfil['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="1">Administrador (Por defecto)</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="activo" 
                                               name="activo" 
                                               checked>
                                        <label class="form-check-label" for="activo">
                                            <i class="fas fa-toggle-on text-success me-1"></i>
                                            Usuario activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php" class="btn btn-outline-secondary me-md-2">
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        
        // Validación en tiempo real
        document.getElementById('confirmar_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (password !== confirm) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const confirm = document.getElementById('confirmar_password');
            
            if (confirm.value && password !== confirm.value) {
                confirm.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirm.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
