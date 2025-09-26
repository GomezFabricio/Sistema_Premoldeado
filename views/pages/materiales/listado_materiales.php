<?php
require_once __DIR__ . '/../../layouts/header.php';
$items = $materiales ?? [];
?>
<div class="container">
    <h2>Listado de Materiales</h2>
    <table class="table table-striped table-hover mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $mat): ?>
                    <tr>
                        <td><?= htmlspecialchars($mat['id']) ?></td>
                        <td><?= htmlspecialchars($mat['nombre']) ?></td>
                        <td><?= htmlspecialchars($mat['unidad_medida']) ?></td>
                        <td><?= htmlspecialchars($mat['cantidad']) ?></td>
                        <td>$<?= htmlspecialchars($mat['precio_unitario']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No hay materiales registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


