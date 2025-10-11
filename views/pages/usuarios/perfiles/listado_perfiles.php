
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Gestión de Perfiles') ?> - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .page-header {
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
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
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table th {
            background-color: #f8f9fa;
        }
        
        /* Estilos para tooltips */
        .tooltip {
            font-size: 0.875rem;
        }
        
        .tooltip-inner {
            max-width: 300px;
            text-align: left;
            background-color: #212529;
            border-radius: 8px;
            padding: 0.75rem;
        }
        
        .badge[data-bs-toggle="tooltip"] {
            transition: all 0.2s ease;
        }
        
        .badge[data-bs-toggle="tooltip"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,123,255,0.3);
            border-top: none;
            color: #495057;
            font-weight: 600;
        }
        
        .badge {
            border-radius: 20px;
            padding: 0.5em 0.75em;
        }
        
        /* Estilos para perfiles protegidos */
        .perfil-critico {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }
        
        .btn-protegido {
            cursor: not-allowed;
            opacity: 0.6;
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
                            <table class="table table-hover" id="perfiles_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Nombre del Perfil</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Módulos</th>
                                        <th class="text-center">Usuarios</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                <tbody>
                    <?php if (empty($perfiles)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
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
                                    <?php 
                                    $totalModulos = intval($perfil['total_modulos'] ?? 0);
                                    $modulosNombres = $perfil['modulos_nombres'] ?? [];
                                    
                                    if ($totalModulos > 0 && !empty($modulosNombres)) {
                                        // Crear contenido del tooltip con viñetas
                                        $tooltipItems = array_map(function($modulo) {
                                            return '• ' . htmlspecialchars($modulo);
                                        }, $modulosNombres);
                                        $tooltipContent = '<strong>Módulos asignados:</strong><br>' . implode('<br>', $tooltipItems);
                                    ?>
                                        <span class="badge bg-info position-relative" 
                                              data-bs-toggle="tooltip" 
                                              data-bs-placement="top" 
                                              data-bs-html="true"
                                              data-bs-title="<?= htmlspecialchars($tooltipContent) ?>"
                                              style="cursor: help;">
                                            <?= $totalModulos ?> módulos
                                            <i class="fas fa-info-circle ms-1 opacity-75" style="font-size: 0.8em;"></i>
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">0 módulos</span>
                                    <?php } ?>
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#perfiles_table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/Spanish.json"
            },
            "pageLength": 15,
            "order": [[0, "asc"]],
            "responsive": true,
            "columnDefs": [
                { "targets": [0, 2, 3, 4, 5], "className": "text-center" }
            ]
        });
        
        // Inicializar todos los tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true,
                delay: { "show": 300, "hide": 100 }
            });
        });
        
        // Confirmación de desactivación - Ya manejado por onclick inline
    });
    </script>
</body>
</html>
