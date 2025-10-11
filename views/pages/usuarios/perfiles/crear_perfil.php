<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Crear Perfil') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../../components/common-styles.php';
    ?>
    
    <!-- Estilos específicos para esta página -->
    <style>
        .page-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .form-check-input:checked {
            background-color: #007bff;
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
                <?= htmlspecialchars($titulo ?? 'Crear Perfil') ?>
            </h1>
            <p class="mb-0 opacity-75">Crea un nuevo perfil de usuario y asigna sus permisos</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <!-- Tarjeta del formulario -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Información del Perfil
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/Sistema_Premoldeado/controllers/UsuarioController.php?a=guardar_perfil">
                            <!-- Información básica del perfil -->
                            <div class="mb-4">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-tag me-2"></i>Nombre del Perfil *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre" 
                                       name="nombre" 
                                       required 
                                       placeholder="Ej: Supervisor, Operador, Gerente..."
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                                <div class="form-text">
                                    El nombre debe ser único y descriptivo del rol que cumplirá el usuario
                                </div>
                            </div>

                            <!-- Selección de módulos -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-th-large me-2"></i>Módulos del Sistema
                                </label>
                                <p class="text-muted mb-3">Selecciona los módulos a los que tendrá acceso este perfil</p>
                                
                                <div class="row">
                                    <?php if (!empty($modulos)): ?>
                                        <?php foreach ($modulos as $modulo): ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="module-card">
                                                    <div class="form-check">
                                                        <input class="form-check-input module-checkbox" 
                                                               type="checkbox" 
                                                               name="modulos[]" 
                                                               value="<?= $modulo['id'] ?>" 
                                                               id="modulo_<?= $modulo['id'] ?>"
                                                               <?= (in_array($modulo['id'], $_POST['modulos'] ?? [])) ? 'checked' : '' ?>>
                                                        <label class="form-check-label w-100" for="modulo_<?= $modulo['id'] ?>">
                                                            <div class="d-flex align-items-center">
                                                                <i class="<?= htmlspecialchars($modulo['icono'] ?? 'fas fa-cube') ?> me-2 text-primary"></i>
                                                                <div>
                                                                    <strong><?= htmlspecialchars($modulo['nombre']) ?></strong>
                                                                    <?php if (!empty($modulo['descripcion'])): ?>
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            <?= htmlspecialchars($modulo['descripcion']) ?>
                                                                        </small>
                                                                    <?php endif; ?>
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
                                                No hay módulos disponibles en el sistema.
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between">
                                    <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=perfiles" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save me-2"></i>Guardar Perfil
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Ayuda y información adicional -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            Información Importante
                        </h6>
                        <ul class="mb-0">
                            <li>Los perfiles definen qué módulos del sistema puede acceder un usuario</li>
                            <li>Un usuario solo puede tener un perfil asignado</li>
                            <li>Puedes modificar los módulos de un perfil después de crearlo</li>
                            <li>Los usuarios con perfiles desactivados no podrán acceder al sistema</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts personalizados -->
    <script>
    $(document).ready(function() {
        // Manejar selección visual de módulos
        $('.module-checkbox').on('change', function() {
            const card = $(this).closest('.module-card');
            if ($(this).is(':checked')) {
                card.addClass('selected');
            } else {
                card.removeClass('selected');
            }
        });

        // Aplicar estado inicial
        $('.module-checkbox:checked').each(function() {
            $(this).closest('.module-card').addClass('selected');
        });

        // Validar formulario antes de enviar
        $('form').on('submit', function(e) {
            const nombre = $('#nombre').val().trim();
            
            if (nombre.length < 3) {
                e.preventDefault();
                alert('El nombre del perfil debe tener al menos 3 caracteres.');
                $('#nombre').focus();
                return false;
            }

            // Confirmar envío
            const modulosSeleccionados = $('.module-checkbox:checked').length;
            if (modulosSeleccionados === 0) {
                const confirmSinModulos = confirm('No has seleccionado ningún módulo. ¿Estás seguro de crear el perfil sin acceso a módulos?');
                if (!confirmSinModulos) {
                    e.preventDefault();
                    return false;
                }
            }

            return true;
        });

        console.log('✅ Página de crear perfil cargada correctamente');
    });
    </script>
</body>
</html>

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
    
    // Agregar botón para seleccionar/deseleccionar todos después del título de módulos
    const modulosTitle = document.querySelector('h6');
    if (modulosTitle) {
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.className = 'btn btn-sm btn-outline-primary ms-2';
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
        
        modulosTitle.appendChild(toggleButton);
    }
    </script>
</body>
</html>
