<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Perfil - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
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
            background: linear-gradient(135deg, #007bff, #0056b3);
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
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .module-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }
        
        .module-card:hover {
            background-color: #f8f9fa;
        }
        
        .module-card.selected {
            background-color: #e7f3ff;
            border-color: #007bff;
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
            echo '<i class="fas fa-exclamation-triangle me-2"></i>' . htmlspecialchars($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>
        
        <!-- Header de la página -->
        <div class="page-header text-center">
            <h1 class="mb-3">
                <i class="fas fa-plus-circle me-2"></i>
                Crear Nuevo Perfil
            </h1>
            <p class="mb-0 opacity-75">Configura un nuevo perfil de usuario y asigna módulos</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <!-- Botón de navegación -->
                <div class="d-flex justify-content-end mb-4">
                    <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=perfiles" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
                
                <!-- Formulario de Crear Perfil -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-shield me-2"></i>
                            Información del Perfil
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="/Sistema_Premoldeado/controllers/UsuarioController.php?a=guardar_perfil" method="POST" id="formCrearPerfil">
                            <!-- Información básica -->
                            <div class="mb-4">
                                <label for="nombre" class="form-label fw-bold">
                                    <i class="fas fa-tag me-2"></i>Nombre del Perfil <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="nombre" name="nombre" 
                                       placeholder="Ej: Administrador, Vendedor, etc." required>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Selección de Módulos -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-th-large me-2"></i>
                            Asignación de Módulos <span class="text-danger">*</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Selecciona los módulos a los que tendrá acceso este perfil:</p>
                        
                        <div class="row" form="formCrearPerfil">
                            <?php if (!empty($modulos)): ?>
                                <?php foreach ($modulos as $modulo): ?>
                                    <div class="col-lg-6 mb-3">
                                        <div class="module-card">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="modulo_<?= $modulo['id'] ?>" 
                                                       name="modulos[]" 
                                                       value="<?= $modulo['id'] ?>"
                                                       form="formCrearPerfil">
                                                <label class="form-check-label w-100" for="modulo_<?= $modulo['id'] ?>">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <i class="<?= htmlspecialchars($modulo['icono'] ?? 'fas fa-circle') ?> fa-lg text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1"><?= htmlspecialchars($modulo['nombre']) ?></h6>
                                                            <small class="text-muted">ID: <?= $modulo['id'] ?></small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No hay módulos disponibles para asignar.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=perfiles" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" form="formCrearPerfil">
                                <i class="fas fa-save me-2"></i>Guardar Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Validación del formulario
    document.getElementById('formCrearPerfil').addEventListener('submit', function(e) {
        const modulos = document.querySelectorAll('input[name="modulos[]"]:checked');
        if (modulos.length === 0) {
            e.preventDefault();
            alert('⚠️ Debe seleccionar al menos un módulo para el perfil.');
            return false;
        }
    });
    
    // Efecto visual para las tarjetas de módulos
    document.querySelectorAll('.module-card input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.module-card');
            if (this.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    });
    
    // Botón para seleccionar/deseleccionar todos
    const headerCard = document.querySelector('.card-header');
    const toggleButton = document.createElement('button');
    toggleButton.type = 'button';
    toggleButton.className = 'btn btn-sm btn-outline-light';
    toggleButton.innerHTML = '<i class="fas fa-check-double me-1"></i>Todos';
    
    let allSelected = false;
    toggleButton.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="modulos[]"]');
        allSelected = !allSelected;
        
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = allSelected;
            const card = checkbox.closest('.module-card');
            if (allSelected) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
        
        this.innerHTML = allSelected ? 
            '<i class="fas fa-times me-1"></i>Ninguno' : 
            '<i class="fas fa-check-double me-1"></i>Todos';
    });
    
    headerCard.appendChild(toggleButton);
    </script>
</body>
</html>
