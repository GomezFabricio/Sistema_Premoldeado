<?php
session_start();
$pageTitle = "Editar Pedido";

// TODO: $pedido = PedidoController::obtenerPorId($_GET['id']);
// TODO: $clientes = ClienteController::obtenerTodos();
// TODO: $formas_entrega = FormaEntregaController::obtenerTodos();
// TODO: $estados_pedido = EstadoPedidoController::obtenerTodos();

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-shopping-cart text-primary me-2"></i>Editar Pedido</h2>
        <a href="listado_pedidos.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha del Pedido</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="clientes_id" class="form-label">Cliente</label>
                            <select class="form-select" id="clientes_id" name="clientes_id" required>
                                <option value="">Seleccionar cliente...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="forma_entrega_id" class="form-label">Forma de Entrega</label>
                            <select class="form-select" id="forma_entrega_id" name="forma_entrega_id" required>
                                <option value="">Seleccionar forma de entrega...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                                <option value="1">A domicilio</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado_pedido_id" class="form-label">Estado del Pedido</label>
                            <select class="form-select" id="estado_pedido_id" name="estado_pedido_id" required>
                                <option value="">Seleccionar estado...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                                <option value="1">En proceso</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="listado_pedidos.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../layouts/footer.php'; ?>
