<?php
// Componente básico de tabla para listado de ventas
if (!isset($data) || !is_array($data) || empty($config['columns'])) {
    echo '<div class="alert alert-warning">No hay datos para mostrar.</div>';
    return;
}
?>
<table class="<?= $config['class'] ?? 'table table-striped' ?>" id="<?= $config['id'] ?? 'tabla' ?>">
    <thead>
        <tr>
            <?php foreach ($config['columns'] as $key => $col): ?>
                <th><?= $col['label'] ?? ucfirst($key) ?></th>
            <?php endforeach; ?>
            <?php if (!empty($config['actions'])): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data)): ?>
            <tr>
                <td colspan="<?= count($config['columns']) + (!empty($config['actions']) ? 1 : 0) ?>" class="text-center text-muted">
                    <i class="fas fa-info-circle me-2"></i> No hay ventas registradas.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($config['columns'] as $key => $col): ?>
                        <td>
                            <?php
                            if (isset($col['formatter']) && is_callable($col['formatter'])) {
                                echo $col['formatter']($row[$key] ?? null, $row);
                            } else {
                                echo htmlspecialchars($row[$key] ?? '');
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                    <?php if (!empty($config['actions'])): ?>
                        <td>
                            <!-- Aquí puedes agregar botones de acción (editar, eliminar, etc.) -->
                            <?php if (isset($config['actions']['edit'])): ?>
                                <a href="?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <?php endif; ?>
                            <?php if (isset($config['actions']['delete'])): ?>
                                <form method="POST" action="?action=delete" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar venta?')">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
