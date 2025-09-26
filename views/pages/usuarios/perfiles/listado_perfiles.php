
<?php
// Vista limpia de listado de perfiles
require_once __DIR__ . '/../../../../controllers/AuthController.php';
require_once __DIR__ . '/../../../../models/Usuario.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new AuthController();
if (!$auth->verificarAccesoModulo(1)) {
    header('Location: http://localhost/Sistema_Premoldeado/views/pages/dashboard.php');
    $_SESSION['flash_message'] = [
        'message' => 'No tienes permisos para acceder a este módulo',
        'type' => 'error'
    ];
    exit;
}

$perfiles = Usuario::obtenerTodosPerfiles();

include __DIR__ . '/../../../layouts/header.php';
?>
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-user-shield me-2"></i>Gestión de Perfiles
        </h1>
        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?action=createPerfiles" class="btn btn-primary mb-3">
            <i class="fas fa-plus me-2"></i>Nuevo Perfil
        </a>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Módulos</th>
                    <th>Usuarios</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($perfiles)): ?>
                    <?php foreach ($perfiles as $perfil): ?>
                        <tr>
                            <td class="text-center"><span class="badge bg-primary"><?php echo htmlspecialchars($perfil['id']); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($perfil['nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($perfil['descripcion'] ?: 'Sin descripción'); ?></td>
                            <td class="text-center"><span class="badge bg-info"><?php echo intval($perfil['total_modulos'] ?: 0); ?> módulos</span></td>
                            <td class="text-center"><span class="badge bg-secondary"><?php echo intval($perfil['total_usuarios'] ?: 0); ?> usuarios</span></td>
                            <td class="text-center"><?php echo $perfil['activo'] ? 'Activo' : 'Inactivo'; ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($perfil['fecha_creacion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay perfiles registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../../layouts/footer.php'; ?>
                        }
