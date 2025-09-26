<?php

$pageTitle = $titulo ?? "Crear Pedido";
$clientes = $clientes ?? [];
$formas_entrega = $formas_entrega ?? [];
$estados = $estados ?? [];

include_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-shopping-cart text-primary me-2"></i>Crear Pedido</h2>
        <a href="listado_pedidos.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha del Pedido</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo htmlspecialchars($cliente['cliente_id']); ?>">
                                        <?php echo htmlspecialchars($cliente['nombre_completo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="forma_entrega_id" class="form-label">Forma de Entrega</label>
                            <select class="form-select" id="forma_entrega_id" name="forma_entrega_id" required>
                                <option value="">Seleccionar forma de entrega...</option>
                                <?php foreach ($formas_entrega as $forma): ?>
                                    <option value="<?php echo htmlspecialchars($forma['id']); ?>">
                                        <?php echo htmlspecialchars($forma['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado_pedido_id" class="form-label">Estado del Pedido</label>
                            <select class="form-select" id="estado_pedido_id" name="estado_pedido_id" required>
                                <option value="">Seleccionar estado...</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo htmlspecialchars($estado['id']); ?>">
                                        <?php echo htmlspecialchars($estado['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="mt-4 mb-2">Agregar productos/materiales al pedido</h5>
                <div id="items-pedido">
                    <div class="row align-items-end mb-2">
                        <div class="col-md-6">
                            <label for="material_id_0" class="form-label">Producto/Material</label>
                            <select class="form-select" id="material_id_0" name="items[0][material_id]" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach (($materiales ?? []) as $material): ?>
                                    <option value="<?php echo htmlspecialchars($material['id']); ?>">
                                        <?php echo htmlspecialchars($material['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad_0" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad_0" name="items[0][cantidad]" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success" onclick="agregarItemPedido()"><i class="fas fa-plus"></i> Agregar otro</button>
                        </div>
                    </div>
                </div>

                <script>
                let itemIndex = 1;
                function agregarItemPedido() {
                    const container = document.getElementById('items-pedido');
                    const row = document.createElement('div');
                    row.className = 'row align-items-end mb-2';
                    row.innerHTML = `
                        <div class="col-md-6">
                            <label for="material_id_${itemIndex}" class="form-label">Producto/Material</label>
                            <select class="form-select" id="material_id_${itemIndex}" name="items[${itemIndex}][material_id]" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach (($materiales ?? []) as $material): ?>
                                    <option value="<?php echo htmlspecialchars($material['id']); ?>">
                                        <?php echo htmlspecialchars($material['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad_${itemIndex}" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad_${itemIndex}" name="items[${itemIndex}][cantidad]" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger" onclick="this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i> Quitar</button>
                        </div>
                    `;
                    container.appendChild(row);
                    itemIndex++;
                }
                </script>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar</button>
                    <a href="listado_pedidos.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TODO: Implementar controlador PedidoController::crear() -->

<?php include __DIR__ . '/../../layouts/footer.php'; ?>