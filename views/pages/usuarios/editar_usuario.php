<?php
$pageTitle = "Editar Usuario";
include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-user-edit text-primary me-2"></i>Editar Usuario</h2>
        <a href="../../../controllers/UsuarioController.php?a=list" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="../../../controllers/UsuarioController.php?a=update&id=<?= $usuario['id'] ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Dejar vacío para mantener actual">
                            <small class="form-text text-muted">Solo ingrese si desea cambiar la contraseña</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="perfil_id" class="form-label">Perfil</label>
                            <select class="form-select" id="perfil_id" name="perfil_id" required>
                                <option value="">Seleccionar perfil...</option>
                                <option value="1" <?= $usuario['perfil_id'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                <option value="2" <?= $usuario['perfil_id'] == 2 ? 'selected' : '' ?>>Cliente</option>
                                <option value="3" <?= $usuario['perfil_id'] == 3 ? 'selected' : '' ?>>Empleado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="domicilio" class="form-label">Domicilio</label>
                            <input type="text" class="form-control" id="domicilio" name="domicilio" value="<?= htmlspecialchars($usuario['domicilio'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="activo" class="form-label">Estado</label>
                            <select class="form-select" id="activo" name="activo" required>
                                <option value="1" <?= $usuario['activo'] == 1 ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= $usuario['activo'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="../../../controllers/UsuarioController.php?a=list" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../layouts/footer.php'; ?>
