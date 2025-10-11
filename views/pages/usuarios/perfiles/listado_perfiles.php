
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
            border-top: none;
            color: #495057;
            font-weight: 600;
        }
        
        .badge {
            border-radius: 20px;
            padding: 0.5em 0.75em;
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
                                        <th class="text-center">Módulos</th>
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
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= htmlspecialchars($perfil['id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($perfil['nombre']) ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= intval($perfil['total_modulos'] ?? 0) ?> módulos</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= intval($perfil['total_usuarios'] ?? 0) ?> usuarios</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=editar_perfil&id=<?= $perfil['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (($perfil['total_usuarios'] ?? 0) == 0): ?>
                                            <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=eliminar_perfil&id=<?= $perfil['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               title="Eliminar"
                                               onclick="return confirm('¿Estás seguro de eliminar este perfil?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    title="No se puede eliminar (tiene usuarios asignados)" 
                                                    disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
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
        $('#perfiles_table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/Spanish.json"
            },
            "pageLength": 15,
            "order": [[0, "asc"]],
            "responsive": true,
            "columnDefs": [
                { "targets": [0, 2, 3, 4], "className": "text-center" }
            ]
        });
        
        // Confirmación de eliminación
        document.querySelectorAll('a[onclick*="confirm"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro de eliminar este perfil? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</body>
</html>
