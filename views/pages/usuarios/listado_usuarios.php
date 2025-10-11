<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-primary { background: linear-gradient(45deg, #007bff, #0056b3); }
        .table th { background-color: #f1f3f4; border-top: none; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #007bff; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users text-primary me-2"></i>
                        Gestión de Usuarios
                    </h1>
                    <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=crear" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Usuario
                    </a>
                </div>
                
                <!-- Tabla de usuarios -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Lista de Usuarios
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($usuarios) && is_array($usuarios) && count($usuarios) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>Email</th>
                                            <th>Perfil</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $user): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary"><?= $user['id'] ?></span>
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
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= htmlspecialchars($user['perfil_nombre'] ?? 'Sin perfil') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $activo = $user['activo'] ?? ($user['estado'] == 'Activo' ? 1 : 0);
                                                    ?>
                                                    <?php if ($activo): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Activo
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>Inactivo
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- Debug temporal para ver ID -->
                                                        <small style="display: block; font-size: 10px; color: #666;">
                                                            Debug: ID=<?= $user['id'] ?>
                                                        </small>
                                                        
                                                        <!-- Botón Editar -->
                                                        <a href="?a=edit&id=<?= $user['id'] ?>" 
                                                           class="btn btn-outline-primary" 
                                                           title="Editar usuario (ID: <?= $user['id'] ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <?php if ($activo): ?>
                                                            <!-- Botón Dar de Baja -->
                                                            <a href="?a=delete&id=<?= $user['id'] ?>" 
                                                               class="btn btn-outline-danger" 
                                                               title="Dar de baja (ID: <?= $user['id'] ?>)"
                                                               onclick="return confirm('¿Estás seguro de dar de baja este usuario?')">
                                                                <i class="fas fa-user-slash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <!-- Botón Reactivar -->
                                                            <a href="?a=activate&id=<?= $user['id'] ?>" 
                                                               class="btn btn-outline-success" 
                                                               title="Reactivar usuario (ID: <?= $user['id'] ?>)"
                                                               onclick="return confirm('¿Estás seguro de reactivar este usuario?')">
                                                                <i class="fas fa-user-check"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay usuarios registrados</h5>
                                <p class="text-muted">Crea el primer usuario del sistema</p>
                                <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?a=crear" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Crear Primer Usuario
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                // Crear formulario para enviar POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/Sistema_Premoldeado/controllers/UsuarioController.php?a=eliminar';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
