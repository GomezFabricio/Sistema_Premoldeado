
<?php
include_once __DIR__ . '/../../layouts/header.php';
$pageTitle = $title ?? "Gestión de Pedidos";
$items = $data['items'] ?? $pedidos ?? [];
?>
    <!-- ...existing code... -->
                            <?php
                            include_once __DIR__ . '/../../layouts/header.php';
                            $pageTitle = $title ?? "Gestión de Pedidos";
                            $items = $data['items'] ?? $pedidos ?? [];
                            ?>
                            <div class="container-fluid">
                                <div class="d-flex justify-content-between mb-4">
                                    <h2><i class="fas fa-shopping-cart text-primary me-2"></i>Gestión de Pedidos</h2>
                                    <a href="crear_pedido.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Pedido</a>
                                </div>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Estado</th>
                                            <th>Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($items)): ?>
                                            <?php foreach ($items as $pedido): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($pedido['id'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($pedido['fecha'] ?? $pedido['fecha_pedido'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($pedido['cliente_nombre'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($pedido['estado_nombre'] ?? $pedido['estado'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($pedido['forma_entrega_nombre'] ?? ''); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No hay pedidos registrados</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php include_once __DIR__ . '/../../layouts/footer.php'; ?>