
<?php
// Vista del listado de productos - Las variables vienen del controlador: $productos, $pageTitle, $usuario

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
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-boxes text-primary me-2"></i><?= htmlspecialchars($pageTitle ?? 'Listado de Productos') ?>
        </h1>
        
        <div class="mb-3">
            <a href="/Sistema_Premoldeado/controllers/ProductoController.php?action=create" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Nuevo Producto
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="productos_table">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Ancho (cm)</th>
                        <th>Largo (cm)</th>
                        <th>Cantidad Disponible</th>
                        <th>Stock Mínimo</th>
                        <th>Precio Unitario</th>
                        <th>Tipo Producto</th>
                        <th>Estado Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                No hay productos registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $item): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= htmlspecialchars($item['id']) ?></span>
                                </td>
                                <td class="text-center"><?= htmlspecialchars($item['ancho']) ?> cm</td>
                                <td class="text-center"><?= htmlspecialchars($item['largo']) ?> cm</td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= htmlspecialchars($item['cantidad_disponible'] ?? 0) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($item['stock_minimo'] ?? 0) ?></span>
                                </td>
                                <td class="text-end">
                                    <strong>$<?= number_format($item['precio_unitario'], 2) ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark"><?= htmlspecialchars($item['tipo_producto_id']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $cantidad = (int)($item['cantidad_disponible'] ?? 0);
                                    $minimo = (int)($item['stock_minimo'] ?? 1);
                                    
                                    if ($cantidad <= 0): ?>
                                        <span class="badge bg-danger">AGOTADO</span>
                                    <?php elseif ($cantidad <= $minimo): ?>
                                        <span class="badge bg-warning text-dark">BAJO</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">OK</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Acciones">
                                        <a href="/Sistema_Premoldeado/controllers/ProductoController.php?action=edit&id=<?= urlencode($item['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/Sistema_Premoldeado/controllers/ProductoController.php?action=delete&id=<?= urlencode($item['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger" title="Eliminar"
                                           onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>