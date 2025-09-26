<?php
include_once __DIR__ . '/../../layouts/header.php';
$pageTitle = $title ?? "Gestión de Usuarios";
$items = $data['items'] ?? $usuarios ?? [];
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-users text-primary me-2"></i>Gestión de Usuarios</h2>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Usuario</a>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Nombre Completo</th>
                <th>Perfil</th>
                <th>Estado</th>
                <th>Último Acceso</th>
                <th>Creado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($usuario['usuario'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($usuario['perfil'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($usuario['estado'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($usuario['ultimo_acceso'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($usuario['created_at'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay usuarios registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</script>
