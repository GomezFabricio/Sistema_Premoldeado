<?php
session_start();
$pageTitle = "Editar Proveedor";

// TODO: $proveedor = ProveedorController::obtenerPorId($_GET['id']);

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-truck text-primary me-2"></i>Editar Proveedor</h2>
        <a href="listado_proveedores.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="condicion_iva" class="form-label">Condición IVA</label>
                            <select class="form-select" id="condicion_iva" name="condicion_iva" required>
                                <option value="">Seleccionar condición IVA...</option>
                                <option value="Responsable Inscripto">Responsable Inscripto</option>
                                <option value="Monotributo">Monotributo</option>
                                <option value="Exento">Exento</option>
                                <option value="Consumidor Final">Consumidor Final</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="iva" class="form-label">IVA (%)</label>
                            <select class="form-select" id="iva" name="iva" required>
                                <option value="">Seleccionar IVA...</option>
                                <option value="21">21%</option>
                                <option value="10.5">10.5%</option>
                                <option value="0">0%</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_registro" class="form-label">Fecha de Registro</label>
                            <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" value="" required>
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
                
                <hr>
                <h5 class="mb-3">Datos de la Persona</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombres" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" value="" required>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="listado_proveedores.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../layouts/footer.php'; ?>
