<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                <i class="fas fa-edit me-2"></i>
                Editar Perfil: <?= htmlspecialchars($perfil['nombre']) ?>
            </h1>
            <p class="mb-0 opacity-75">Modifica la información del perfil y sus módulos asignados</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <!-- Botón de navegación -->
                <div class="d-flex justify-content-end mb-4">
                    <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=perfiles" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
                
                <!-- Formulario de Editar Perfil -->
            <div class="card">
                <div class="card-body">
                    <form action="/Sistema_Premoldeado/controllers/UsuarioController.php?a=actualizar_perfil&id=<?= $perfil['id'] ?>" method="POST" id="formEditarPerfil">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($perfil['id']) ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Perfil <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($perfil['nombre']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="activo" class="form-label">Estado</label>
                                    <select class="form-select" id="activo" name="activo">
                                        <option value="1" <?= ($perfil['activo'] ?? true) ? 'selected' : '' ?>>Activo</option>
                                        <option value="0" <?= !($perfil['activo'] ?? true) ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($perfil['descripcion'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Módulos Asignados <span class="text-danger">*</span></label>
                            <div class="row">
                                <?php if (!empty($modulos)): ?>
                                    <?php foreach ($modulos as $modulo): ?>
                                        <?php $isChecked = in_array($modulo['id'], $modulosAsignadosIds); ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="modulo_<?= $modulo['id'] ?>" name="modulos[]" value="<?= $modulo['id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="modulo_<?= $modulo['id'] ?>">
                                                    <i class="<?= htmlspecialchars($modulo['icono'] ?? 'fas fa-circle') ?> me-2"></i>
                                                    <?= htmlspecialchars($modulo['nombre']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p class="text-muted">No hay módulos disponibles.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=perfiles" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('formEditarPerfil').addEventListener('submit', function(e) {
        const modulos = document.querySelectorAll('input[name="modulos[]"]:checked');
        if (modulos.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos un módulo para el perfil.');
            return false;
        }
    });
    </script>
