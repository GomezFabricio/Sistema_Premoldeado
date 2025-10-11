
<?php
// Vista del listado de clientes - Las variables vienen del controlador: $clientes, $pageTitle, $usuario

// Incluir header del layout
include_once __DIR__ . '/../../layouts/header.php';

// Mostrar mensajes flash si existen
if (isset($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                ($flash['type'] === 'error' ? 'alert-danger' : 'alert-info');
    echo '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($flash['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['flash_message']);
}

// Mostrar error específico si viene del controlador
if (isset($error)) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($error);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

// Validar datos
if (!isset($clientes) || !is_array($clientes)) {
    $clientes = [];
}
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-user-friends text-primary me-2"></i><?= htmlspecialchars($pageTitle ?? 'Gestión de Clientes') ?>
        </h1>
        
        <div class="mb-3">
            <a href="/Sistema_Premoldeado/controllers/ClienteController.php?action=create" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Registrar Nuevo Cliente
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="clientes_table">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Tipo Doc.</th>
                        <th>Número Doc.</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-users fa-2x mb-2"></i><br>
                                No hay clientes registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= htmlspecialchars($cliente['cliente_id'] ?? $cliente['id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?></strong>
                                    <?php if (!empty($cliente['razon_social'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($cliente['razon_social']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($cliente['tipo_documento'] ?? 'DNI') ?></span>
                                </td>
                                <td><?= htmlspecialchars($cliente['numero_documento'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (!empty($cliente['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($cliente['email']) ?>"><?= htmlspecialchars($cliente['email']) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">Sin email</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($cliente['telefono'])): ?>
                                        <i class="fas fa-phone text-success me-1"></i><?= htmlspecialchars($cliente['telefono']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sin teléfono</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $activo = $cliente['cliente_activo'] ?? $cliente['activo'] ?? true;
                                    $badgeClass = $activo ? 'bg-success' : 'bg-danger';
                                    $estado = $activo ? 'Activo' : 'Inactivo';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $estado ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="/Sistema_Premoldeado/controllers/ClienteController.php?action=edit&id=<?= $cliente['cliente_id'] ?? $cliente['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Editar cliente">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/Sistema_Premoldeado/controllers/ClienteController.php?action=crear_usuario&id=<?= $cliente['cliente_id'] ?? $cliente['id'] ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Crear acceso web para este cliente">
                                            <i class="fas fa-user-plus"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="eliminarCliente(<?= $cliente['cliente_id'] ?? $cliente['id'] ?>)"
                                                title="Eliminar cliente">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($clientes)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Total de clientes: <strong><?= count($clientes) ?></strong>
                </small>
            </div>
        <?php endif; ?>
    </div>
</div>
        </div>
    </div>

<script>
// Función para eliminar cliente con confirmación
function eliminarCliente(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este cliente?\n\nATENCIÓN: Esta acción no se puede deshacer.')) {
        // Crear formulario para enviar petición DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/Sistema_Premoldeado/controllers/ClienteController.php?action=delete&id=' + id;
        
        // Añadir campo oculto para confirmar la eliminación
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'confirmar_eliminacion';
        input.value = '1';
        form.appendChild(input);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Inicializar DataTables si está disponible
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#clientes_table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            responsive: true,
            order: [[0, 'desc']] // Ordenar por ID descendente
        });
    }
});
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>