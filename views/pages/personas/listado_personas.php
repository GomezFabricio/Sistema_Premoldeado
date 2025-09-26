<?php
$pageTitle = $titulo ?? 'Listado de Personas';
$items = $data['items'] ?? $personas ?? [];
?>
<div class="container">
    <h2><?= $pageTitle ?></h2>
    <a href="?action=create" class="btn btn-primary mb-3">Nueva Persona</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Apellidos</th>
                <th>Nombres</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personas as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['apellidos']) ?></td>
                    <td><?= htmlspecialchars($p['nombres']) ?></td>
                    <td><?= htmlspecialchars($p['tipo_documento'] . ' ' . $p['numero_documento']) ?></td>
                    <td><?= htmlspecialchars($p['telefono']) ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td>
                        <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar persona?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
