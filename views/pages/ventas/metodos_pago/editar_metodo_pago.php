<?php
session_start();
$pageTitle = "Editar Método de Pago";

// TODO: $metodo = MetodoPagoController::obtenerPorId($_GET['id']);

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-credit-card text-primary me-2"></i>Editar Método de Pago</h2>
        <a href="listado_metodos_pago.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Método</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="" required>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="listado_metodos_pago.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../../layouts/footer.php'; ?>
