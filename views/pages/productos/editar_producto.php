<?php
session_start();
$pageTitle = "Editar Producto";

// TODO: $producto = ProductoController::obtenerPorId($_GET['id']);
// TODO: $tipos_producto = TipoProductoController::obtenerTodos();

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-box text-primary me-2"></i>Editar Producto</h2>
        <a href="listado_productos.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ancho" class="form-label">Ancho</label>
                            <input type="text" class="form-control" id="ancho" name="ancho" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="largo" class="form-label">Largo</label>
                            <input type="text" class="form-control" id="largo" name="largo" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cantidad_disponible" class="form-label">Cantidad Disponible</label>
                            <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" min="0" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="precio_unitario" class="form-label">Precio Unitario</label>
                            <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" step="0.01" min="0" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_producto_id" class="form-label">Tipo de Producto</label>
                            <select class="form-select" id="tipo_producto_id" name="tipo_producto_id" required>
                                <option value="">Seleccionar tipo...</option>
                                <!-- TODO: Cargar desde base de datos con valor seleccionado -->
                                <option value="1">Alcantarilla</option>
                                <option value="2">Pilar</option>
                                <option value="3">Cámara</option>
                                <option value="5">Cámara Séptica</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="activo" class="form-label">Estado</label>
                            <select class="form-select" id="activo" name="activo" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="listado_productos.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../layouts/footer.php'; ?>
