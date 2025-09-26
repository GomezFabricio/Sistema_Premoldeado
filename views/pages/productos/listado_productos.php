
<?php
// Listado de productos activos
include_once __DIR__ . '/../../layouts/header.php';
$titulo = "Listado de Productos";
if (!isset($items)) {
    require_once '../../../models/Producto.php';
    $producto = new Producto();
    $items = $producto->listar();
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-boxes text-primary me-2"></i><?= $titulo ?></h2>
    </div>
    <div class="mb-3">
        <a href="/Sistema_Premoldeado/views/pages/productos/crear_producto.php" class="btn btn-success">Nuevo Producto</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover" id="productos_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ancho</th>
                    <th>Largo</th>
                    <th>Cantidad</th>
                    <th>Stock Mínimo</th>
                    <th>Precio Unitario</th>
                    <th>Tipo Producto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="8" style="text-align:center;">No hay productos activos.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id']) ?></td>
                            <td><?= htmlspecialchars($item['ancho']) ?></td>
                            <td><?= htmlspecialchars($item['largo']) ?></td>
                            <td><?= htmlspecialchars($item['cantidad_disponible']) ?></td>
                            <td><?= htmlspecialchars($item['stock_minimo']) ?></td>
                            <td><?= htmlspecialchars($item['precio_unitario']) ?></td>
                            <td><?= htmlspecialchars($item['tipo_producto_id']) ?></td>
                            <td>
                                <a href="/Sistema_Premoldeado/views/pages/productos/editar_producto.php?id=<?= urlencode($item['id']) ?>" class="btn btn-sm btn-primary">Editar</a>
                                <a href="/Sistema_Premoldeado/views/pages/productos/baja_producto.php?id=<?= urlencode($item['id']) ?>" class="btn btn-sm btn-warning">Baja lógica</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include __DIR__ . '/../../layouts/footer.php'; ?>