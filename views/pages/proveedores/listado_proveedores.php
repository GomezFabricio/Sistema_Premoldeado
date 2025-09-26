<?php
include_once __DIR__ . '/../../layouts/header.php';
$pageTitle = $title ?? "Gestión de Proveedores";
$items = $data['items'] ?? $proveedores ?? [];
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-truck text-primary me-2"></i>Gestión de Proveedores</h2>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Proveedor</a>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Proveedor</th>
                <th>CUIT</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Condición IVA</th>
                <th>Estado</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $proveedor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proveedor['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['razon_social'] ?? $proveedor['nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['cuit'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['telefono'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['direccion'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['condicion_iva'] ?? ''); ?></td>
                        <td><?php echo !empty($proveedor['estado']) || !empty($proveedor['activo']) ? 'Activo' : 'Inactivo'; ?></td>
                        <td><?php echo htmlspecialchars($proveedor['fecha_registro'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No hay proveedores registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

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
        if (confirm('¿Estás seguro de que deseas eliminar este proveedor?\n\nATENCIÓN: Solo se pueden eliminar proveedores sin compras asociadas.')) {
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

    function verProveedor(id) {
        // Ver detalles del proveedor
        window.location.href = '?action=view&id=' + id;
    }

    function verHistorialCompras(id) {
        // Ver historial de compras del proveedor
        window.location.href = '../compras/?proveedor_id=' + id;
    }

    function nuevaCompra(id) {
        // Nueva compra con proveedor preseleccionado
        window.location.href = '../compras/?action=create&proveedor_id=' + id;
    }

    // Exportar Excel
    function exportarExcel() {
        window.location.href = '?action=export&format=excel';
    }
</script>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-truck fa-2x mb-2"></i>
            <h5>No hay proveedores registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Proveedor</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verProveedor(id) { window.location.href = `view.php?id=${id}`; }
function editarProveedor(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
