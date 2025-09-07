<?php
session_start();
$pageTitle = "Editar Venta (Factura)";

// TODO: $factura = FacturaController::obtenerPorId($_GET['id']);
// TODO: $clientes = ClienteController::obtenerTodos();
// TODO: $metodos_pago = MetodoPagoController::obtenerTodos();
// TODO: $pedidos = PedidoController::obtenerTodos();

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-receipt text-primary me-2"></i>Editar Venta (Factura)</h2>
        <a href="listado_ventas.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha de Factura</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="monto_total" class="form-label">Monto Total</label>
                            <input type="number" class="form-control" id="monto_total" name="monto_total" step="0.01" min="0" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="clientes_id" class="form-label">Cliente</label>
                            <select class="form-select" id="clientes_id" name="clientes_id" required>
                                <option value="">Seleccionar cliente...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="metodo_pago_id" class="form-label">Método de Pago</label>
                            <select class="form-select" id="metodo_pago_id" name="metodo_pago_id" required>
                                <option value="">Seleccionar método...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="pedidos_id" class="form-label">Pedido Asociado</label>
                            <select class="form-select" id="pedidos_id" name="pedidos_id" required>
                                <option value="">Seleccionar pedido...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="listado_ventas.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../layouts/footer.php'; ?>
