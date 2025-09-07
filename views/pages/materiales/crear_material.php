<?php
session_start();
$pageTitle = "Crear Material";

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-boxes text-primary me-2"></i>Crear Material</h2>
        <a href="listado_materiales.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Material</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del material" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                            <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" placeholder="Ej: kg, m, unidad" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" step="0.01" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="costo_unitario" class="form-label">Costo Unitario</label>
                            <input type="number" class="form-control" id="costo_unitario" name="costo_unitario" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" value="1" required>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar</button>
                    <a href="listado_materiales.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TODO: Implementar controlador MaterialController::crear() -->

<?php include '../../../layouts/footer.php'; ?>
