
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Gestión de Perfiles') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../../components/common-styles.php';
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
                <i class="fas fa-user-shield me-2"></i>
                <?= htmlspecialchars($titulo ?? 'Gestión de Perfiles') ?>
            </h1>
            <p class="mb-0 opacity-75">Administra los perfiles de usuario y sus permisos</p>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=crear_perfil" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Nuevo Perfil
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=list" class="btn btn-outline-secondary">
                            <i class="fas fa-users me-2"></i>Ver Usuarios
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
                            Lista de Perfiles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="perfiles_table" data-table-type="perfiles">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Nombre del Perfil</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Usuarios</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                <tbody>
                    <?php if (empty($perfiles)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="fas fa-user-shield fa-2x mb-2"></i><br>
                                No hay perfiles registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($perfiles as $perfil): ?>
                            <?php $esPerfilCritico = in_array(strtolower($perfil['nombre']), ['administrador', 'admin']); ?>
                            <tr <?= $esPerfilCritico ? 'class="perfil-critico"' : '' ?>>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= htmlspecialchars($perfil['id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($perfil['nombre']) ?></strong>
                                    <?php if ($esPerfilCritico): ?>
                                        <span class="badge bg-warning ms-2" title="Perfil crítico del sistema">
                                            <i class="fas fa-shield-alt"></i> Sistema
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (($perfil['estado'] ?? 1) == 1): ?>
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
                                    <span class="badge bg-secondary"><?= intval($perfil['total_usuarios'] ?? 0) ?> usuarios</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Botón Editar - siempre disponible -->
                                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=editar_perfil&id=<?= $perfil['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <?php if (($perfil['estado'] ?? 1) == 1): ?>
                                            <!-- Perfil Activo - Botón Desactivar -->
                                            <?php 
                                            $totalUsuarios = intval($perfil['total_usuarios'] ?? 0);
                                            $esPerfilCritico = in_array(strtolower($perfil['nombre']), ['administrador', 'admin']);
                                            $puedeDesactivar = ($totalUsuarios == 0) && !$esPerfilCritico;
                                            ?>
                                            
                                            <?php if ($puedeDesactivar): ?>
                                                <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=eliminar_perfil&id=<?= $perfil['id'] ?>" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Desactivar perfil"
                                                   onclick="return confirm('¿Estás seguro de desactivar este perfil? El perfil se mantendrá en el sistema pero no podrá ser asignado a nuevos usuarios.')">
                                                    <i class="fas fa-toggle-off"></i>
                                                </a>
                                            <?php else: ?>
                                                <?php 
                                                $razonDeshabilitado = '';
                                                if ($esPerfilCritico) {
                                                    $razonDeshabilitado = 'Perfil crítico del sistema - No se puede desactivar';
                                                } elseif ($totalUsuarios > 0) {
                                                    $razonDeshabilitado = "Tiene {$totalUsuarios} usuario(s) activo(s) - Desactive primero los usuarios";
                                                }
                                                ?>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        title="<?= htmlspecialchars($razonDeshabilitado) ?>" 
                                                        disabled>
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <!-- Perfil Inactivo - Botón Reactivar -->
                                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=reactivar_perfil&id=<?= $perfil['id'] ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Reactivar perfil"
                                               onclick="return confirm('¿Estás seguro de reactivar este perfil? El perfil volverá a estar disponible para ser asignado a usuarios.')">
                                                <i class="fas fa-toggle-on"></i>
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

    <!-- Script personalizado para esta página -->
    <script>
    $(document).ready(function() {
        // La inicialización de DataTable se hace automáticamente por el data-table-type="perfiles"
        // Los tooltips se inicializan automáticamente por common-styles.php
        
        console.log('✅ Página de perfiles cargada correctamente');
    });
    </script>
</body>
</html>
