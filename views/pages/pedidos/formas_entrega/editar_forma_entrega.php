<?php
session_start();
$pageTitle = "Editar Forma de Entrega";

// TODO: $forma = FormaEntregaController::obtenerPorId($_GET['id']);

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-truck text-primary me-2"></i>Editar Forma de Entrega</h2>
        <a href="listado_formas_entrega.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Forma de Entrega</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="" required>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="listado_formas_entrega.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../../layouts/footer.php'; ?>
