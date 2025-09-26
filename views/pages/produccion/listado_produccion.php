

<?php
require_once __DIR__ . '/../../layouts/header.php';
?>
<div class="container">
    <h2>Listado de Producciones</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha Inicio</th>
                <th>Fecha Entrega</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Reserva</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['id']) ?></td>
                        <td><?= htmlspecialchars($prod['fecha_inicio']) ?></td>
                        <td><?= htmlspecialchars($prod['fecha_entrega']) ?></td>
                        <td><?= htmlspecialchars($prod['cantidad']) ?></td>
                        <td><?= htmlspecialchars($prod['estado'] ?? $prod['estado_produccion_id']) ?></td>
                        <td><?= htmlspecialchars($prod['reserva_id']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No hay producciones registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

