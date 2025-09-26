<?php
// Vista: Estado de Producción
// Muestra el estado actual de cada proceso de producción

require_once '../../models/Produccion.php';

$producciones = Produccion::listarTodas(); // Asume método que devuelve todas las producciones

function estadoToBadge($estado_id) {
    switch ($estado_id) {
        case Produccion::ESTADO_PLANIFICADA:
            return '<span class="badge badge-secondary">Planificada</span>';
        case Produccion::ESTADO_EN_PROCESO:
            return '<span class="badge badge-info">En Proceso</span>';
        case Produccion::ESTADO_PAUSADA:
            return '<span class="badge badge-warning">Pausada</span>';
        case Produccion::ESTADO_COMPLETADA:
            return '<span class="badge badge-success">Completada</span>';
        case Produccion::ESTADO_CANCELADA:
            return '<span class="badge badge-danger">Cancelada</span>';
        default:
            return '<span class="badge badge-dark">Desconocido</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Producción</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <h1>Estado de Producción</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin Programada</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($producciones as $prod): ?>
            <tr>
                <td><?= $prod['id'] ?></td>
                <td><?= $prod['producto_nombre'] ?></td>
                <td><?= $prod['fecha_inicio'] ?></td>
                <td><?= $prod['fecha_fin_programada'] ?></td>
                <td><?= $prod['responsable'] ?></td>
                <td><?= estadoToBadge($prod['estado_produccion_id']) ?></td>
                <td><?= $prod['observaciones'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
