<?php
/**
 * Vista de Creación de Perfiles
 * Submódulo del módulo Usuarios
 */

// Esta vista recibe las siguientes variables del controlador:
// $modulos - Lista de todos los módulos disponibles
// $pageTitle - Título de la página
// $usuario - Usuario logueado

?>

<!-- Contenido de Crear Perfil -->
<div class="row">
    <div class="col-12">
        <!-- Título de la página -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-plus-circle me-2"></i>
                Crear Nuevo Perfil
            </h1>
            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?action=indexPerfiles" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
        
        <!-- Formulario de Crear Perfil -->
            <div class="card">
                <div class="card-body">
                    <form action="/Sistema_Premoldeado/controllers/UsuarioController.php?action=storePerfiles" method="POST" id="formCrearPerfil">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Perfil <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="activo" class="form-label">Estado</label>
                                    <select class="form-select" id="activo" name="activo">
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Módulos Asignados <span class="text-danger">*</span></label>
                            <div class="row">
                                <?php if (!empty($modulos)): ?>
                                    <?php foreach ($modulos as $modulo): ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="modulo_<?= $modulo['id'] ?>" name="modulos[]" value="<?= $modulo['id'] ?>">
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
                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?action=indexPerfiles" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('formCrearPerfil').addEventListener('submit', function(e) {
        const modulos = document.querySelectorAll('input[name="modulos[]"]:checked');
        if (modulos.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos un módulo para el perfil.');
            return false;
        }
    });
    </script>
