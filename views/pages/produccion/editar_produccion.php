<?php
session_start();
$pageTitle = "Editar Producción";

// TODO: $produccion = ProduccionController::obtenerPorId($_GET['id']);
// TODO: $reservas = ReservaController::obtenerTodos();
// TODO: $estados_produccion = EstadoProduccionController::obtenerTodos();

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-cogs text-primary me-2"></i>Editar Producción</h2>
        <a href="listado_producciones.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_entrega" class="form-label">Fecha de Entrega Estimada</label>
                            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" value="">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="estado_produccion_id" class="form-label">Estado de Producción</label>
                            <select class="form-select" id="estado_produccion_id" name="estado_produccion_id" required>
                                <option value="">Seleccionar estado...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                                <option value="1">Pendiente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="reserva_id" class="form-label">Reserva Asociada</label>
                            <select class="form-select" id="reserva_id" name="reserva_id" required>
                                <option value="">Seleccionar reserva...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="listado_producciones.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../layouts/footer.php'; ?>
