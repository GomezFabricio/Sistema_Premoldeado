
<?php
include_once __DIR__ . '/../../layouts/header.php';
$pageTitle = $title ?? "Gestión de Clientes";
$items = $data['items'] ?? $data['clientes'] ?? [];
// Inicializar $clientes si no está definido
if (!isset($clientes) || !is_array($clientes)) {
    $clientes = [];
}
?>
<div class="container-fluid">
    <?php if (!empty($_SESSION['mensaje_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['mensaje_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-users text-primary me-2"></i>Gestión de Clientes</h2>
    <a href="crear_cliente.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Cliente</a>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>DNI/RUT</th>
                <th>Teléfono</th>
                <th>Fecha Alta</th>
                <th>Observaciones</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $cliente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cliente['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellido'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($cliente['documento'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cliente['fecha_alta'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cliente['observaciones'] ?? ''); ?></td>
                        <td><?php echo !empty($cliente['activo']) ? 'Activo' : 'Inactivo'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay clientes registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
        </div>
    </div>

    <!-- Tabla de clientes usando componente -->
    <!-- Fin del listado de clientes -->
</div>

<style>
    .stat-card {
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>

<script>
    // Funciones para acciones de la tabla
    function editRecord(id) {
        window.location.href = '?action=edit&id=' + id;
    }

    function deleteRecord(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este cliente?\n\nATENCIÓN: Solo se pueden eliminar clientes sin pedidos.')) {
            // Enviar petición de eliminación
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?action=delete';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id';
            input.value = id;
            form.appendChild(input);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function verPedidos(id) {
        // Redirigir a pedidos del cliente
        window.location.href = '../pedidos/?action=index&cliente_id=' + id;
    }

    function nuevoPedido(id) {
        // Redirigir a crear pedido para cliente
        window.location.href = '../pedidos/?action=create&cliente_id=' + id;
    }

    // Exportar Excel
    function exportarExcel() {
        window.location.href = '?action=export&format=excel';
    }
</script>