<?php
/**
 * Dashboard Principal del Sistema
 * Página de inicio después del login
 */

require_once __DIR__ . '/../../controllers/AuthController.php';

// Crear una instancia del AuthController para verificar autenticación
$auth = new AuthController();

// Verificar autenticación
if (!$auth->verificarAutenticacion()) {
    header('Location: ../auth/login.php');
    exit;
}

// Datos para el dashboard
$pageTitle = 'Dashboard';
$pageIcon = 'fas fa-tachometer-alt';
$breadcrumb = [['title' => 'Dashboard']];
$usuario = $auth->getUsuarioLogueado();

// Función para obtener estadísticas
function obtenerEstadisticas() {
    return [
        [
            'title' => 'Usuarios',
            'value' => '25',
            'icon' => 'fas fa-users',
            'color' => 'primary',
            'url' => 'usuarios/listado_usuarios.php'
        ],
        [
            'title' => 'Clientes',
            'value' => '150',
            'icon' => 'fas fa-user-tie',
            'color' => 'success',
            'url' => 'clientes/listado_clientes.php'
        ],
        [
            'title' => 'Productos',
            'value' => '89',
            'icon' => 'fas fa-boxes',
            'color' => 'info',
            'url' => 'productos/listado_productos.php'
        ],
        [
            'title' => 'Pedidos',
            'value' => '42',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'warning',
            'url' => 'pedidos/listado_pedidos.php'
        ]
    ];
}

$statsCards = obtenerEstadisticas();

// Incluir header
include __DIR__ . '/../layouts/header.php';
?>
        
        <!-- Contenido del Dashboard -->
        <div class="row">
            <div class="col-12">
                <!-- Título de la página -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="<?php echo $pageIcon; ?> me-2"></i>
                        <?php echo $pageTitle; ?>
                    </h1>
                    <small class="text-muted">
                        Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>
                    </small>
                </div>

                <!-- Tarjetas de estadísticas -->
                <div class="row mb-4">
                    <?php foreach ($statsCards as $card): ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-start border-<?php echo $card['color']; ?> border-4 shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs fw-bold text-<?php echo $card['color']; ?> text-uppercase mb-1">
                                                <?php echo $card['title']; ?>
                                            </div>
                                            <div class="h5 mb-0 fw-bold text-gray-800">
                                                <?php echo $card['value']; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="<?php echo $card['icon']; ?> fa-2x text-<?php echo $card['color']; ?>"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="<?php echo $card['url']; ?>" class="btn btn-sm btn-outline-<?php echo $card['color']; ?>">
                                        Ver más <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Información del sistema -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 fw-bold text-primary">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Estado del Sistema
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success" role="alert">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Sistema Funcionando Correctamente
                                    </h5>
                                    <p class="mb-0">
                                        Todos los módulos están operativos. Sistema con controladores implementados.
                                    </p>
                                </div>
                                
                                <h6 class="fw-bold">Módulos Disponibles para tu Perfil:</h6>
                                <ul class="list-group list-group-flush">
                                    <?php if (!empty($usuario['modulos'])): ?>
                                        <?php foreach ($usuario['modulos'] as $modulo): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="<?php echo $modulo['icono']; ?> me-2"></i>
                                                    <?php echo htmlspecialchars($modulo['nombre']); ?>
                                                </span>
                                                <span class="badge bg-success rounded-pill">Activo</span>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item">
                                            <span class="text-muted">No tienes módulos asignados</span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 fw-bold text-primary">
                                    <i class="fas fa-user me-2"></i>
                                    Información del Usuario
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Perfil:</strong></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo htmlspecialchars($usuario['perfil_nombre']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Módulos:</strong></td>
                                        <td><?php echo count($usuario['modulos']); ?> disponibles</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Accesos Rápidos -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 fw-bold text-primary">
                                    <i class="fas fa-bolt me-2"></i>
                                    Accesos Rápidos
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <?php if (!empty($usuario['modulos'])): ?>
                                        <?php $maxAccesos = 4; $contador = 0; ?>
                                        <?php foreach ($usuario['modulos'] as $modulo): ?>
                                            <?php if ($contador >= $maxAccesos) break; ?>
                                            <a href="<?php echo $modulo['url']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="<?php echo $modulo['icono']; ?> me-2"></i>
                                                <?php echo htmlspecialchars($modulo['nombre']); ?>
                                            </a>
                                            <?php $contador++; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
// Incluir footer
include __DIR__ . '/../layouts/footer.php';
?>
