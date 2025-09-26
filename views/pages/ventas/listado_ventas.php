<?php
include_once __DIR__ . '/../../layouts/header.php';
$pageTitle = $title ?? "Gestión de Ventas";
$items = $data['items'] ?? $ventas ?? [];
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-cash-register text-primary me-2"></i>Gestión de Ventas</h2>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Venta</a>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>N° Venta</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Productos</th>
                <th>Método Pago</th>
                <th>Estado</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $venta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($venta['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($venta['numero_venta'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($venta['cliente_nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($venta['fecha_venta'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($venta['productos_resumen'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($venta['metodo_pago'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($venta['estado'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($venta['monto_total'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No hay ventas registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

<style>
    .stat-card {
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.8rem;
        opacity: 0.9;
    }
</style>

<script>
    // Funciones para acciones de la tabla
    function editRecord(id) {
        window.location.href = '?action=edit&id=' + id;
    }

    function deleteRecord(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta venta?\n\nATENCIÓN: Solo se pueden eliminar ventas pendientes o canceladas.')) {
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

    function verVenta(id) {
        // Ver detalles de la venta
        window.location.href = '?action=view&id=' + id;
    }

    function generarFactura(id) {
        // Generar factura en PDF
        window.open('?action=factura&id=' + id, '_blank');
    }

    function completarVenta(id) {
        if (confirm('¿Marcar esta venta como completada?\n\nSe actualizará el inventario y se generarán los movimientos contables.')) {
            // Completar venta
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?action=completar';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id';
            input.value = id;
            form.appendChild(input);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function cancelarVenta(id) {
        const motivo = prompt('Ingrese el motivo de la cancelación:');
        if (motivo && motivo.trim() !== '') {
            // Cancelar venta
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?action=cancelar';
            
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.value = id;
            form.appendChild(inputId);
            
            const inputMotivo = document.createElement('input');
            inputMotivo.type = 'hidden';
            inputMotivo.name = 'motivo';
            inputMotivo.value = motivo;
            form.appendChild(inputMotivo);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Exportar Excel
    function exportarExcel() {
        window.location.href = '?action=export&format=excel';
    }
</script>
