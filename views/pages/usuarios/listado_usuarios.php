<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Gestión de Usuarios') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../components/common-styles.php';
    ?>
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
                <i class="fas fa-users me-2"></i>
                <?= htmlspecialchars($titulo ?? 'Gestión de Usuarios') ?>
            </h1>
            <p class="mb-0 opacity-75">Administra los usuarios del sistema y sus permisos</p>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=crear" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Nuevo Usuario
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=perfiles" class="btn btn-outline-secondary">
                            <i class="fas fa-user-shield me-2"></i>Gestionar Perfiles
                        </a>
                        <a href="/Sistema_Premoldeado/dashboard.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
                
                <!-- Tarjeta de la tabla -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2"></i>
                            Lista de Usuarios
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="usuarios_table" data-table-type="usuarios">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th class="text-center">Perfil</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($usuarios)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                <i class="fas fa-users fa-2x mb-2"></i><br>
                                                No hay usuarios registrados
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($usuarios as $user): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?= htmlspecialchars($user['id']) ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar d-flex align-items-center justify-content-center text-white me-2">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <strong><?= htmlspecialchars($user['nombre_usuario']) ?></strong>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">
                                                        <?= htmlspecialchars($user['perfil_nombre'] ?? 'Sin perfil') ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                    $esActivo = $user['estado'] === 'Activo';
                                                    ?>
                                                    <?php if ($esActivo): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Activo
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i>Inactivo
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <!-- Botón Editar -->
                                                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=editar&id=<?= $user['id'] ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <?php if ($esActivo): ?>
                                                            <!-- Botón Desactivar -->
                                                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=eliminar&id=<?= $user['id'] ?>" 
                                                               class="btn btn-sm btn-outline-danger" 
                                                               title="Desactivar usuario"
                                                               onclick="return confirm('¿Estás seguro de desactivar este usuario? El usuario se mantendrá en el sistema pero no podrá acceder.')">
                                                                <i class="fas fa-user-slash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <!-- Botón Reactivar -->
                                                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=reactivar&id=<?= $user['id'] ?>" 
                                                               class="btn btn-sm btn-outline-success" 
                                                               title="Reactivar usuario"
                                                               onclick="return confirm('¿Estás seguro de reactivar este usuario? El usuario volverá a tener acceso al sistema.')">
                                                                <i class="fas fa-user-check"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script personalizado para esta página -->
    <script>
    $(document).ready(function() {
        // La inicialización de DataTable se hace automáticamente por el data-table-type="usuarios"
        // Los tooltips se inicializan automáticamente por common-styles.php
        
        console.log('✅ Página de usuarios cargada correctamente');
        
        // Mensaje de confirmación personalizado para usuarios
        $('#usuarios_table').on('click', '.btn-outline-danger', function(e) {
            const userName = $(this).closest('tr').find('strong').text();
            const defaultMessage = $(this).attr('onclick');
            if (defaultMessage) {
                e.preventDefault();
                if (confirm(`¿Estás seguro de desactivar al usuario "${userName}"? El usuario se mantendrá en el sistema pero no podrá acceder.`)) {
                    window.location.href = $(this).attr('href');
                }
            }
        });
    });
    </script>
</body>
</html>
