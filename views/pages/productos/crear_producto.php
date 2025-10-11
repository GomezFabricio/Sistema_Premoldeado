<?php
// Las variables vienen del controlador: $tiposProducto, $pageTitle, $usuario

// Mostrar mensajes flash si existen
if (isset($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                ($flash['type'] === 'error' ? 'alert-danger' : 'alert-info');
    echo '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($flash['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['flash_message']);
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-plus-circle text-success me-2"></i><?= htmlspecialchars($pageTitle ?? 'Crear Nuevo Producto') ?>
            </h1>
            <a href="/Sistema_Premoldeado/controllers/ProductoController.php?action=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>Información del Nuevo Producto
                </h5>
            </div>
            <div class="card-body">
                <form action="/Sistema_Premoldeado/controllers/ProductoController.php?action=store" method="POST" id="crearProductoForm">
                    
                    <!-- Información básica -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-info-circle me-2"></i>Información Básica
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_producto_id" class="form-label fw-bold">
                                <i class="fas fa-tags text-primary me-1"></i>Tipo de Producto *
                            </label>
                            <select class="form-select" name="tipo_producto_id" id="tipo_producto_id" required>
                                <option value="">Seleccione un tipo...</option>
                                <?php if (!empty($tiposProducto)): ?>
                                    <?php foreach ($tiposProducto as $tipo): ?>
                                        <option value="<?= $tipo['id'] ?>">
                                            <?= htmlspecialchars($tipo['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Seleccione la categoría del producto</div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="ancho" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-h text-primary me-1"></i>Ancho (cm) *
                            </label>
                            <input type="number" step="0.01" class="form-control" name="ancho" id="ancho" 
                                   required placeholder="60.00" min="0.01">
                            <div class="form-text">Medida en centímetros</div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="largo" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-v text-primary me-1"></i>Largo (cm) *
                            </label>
                            <input type="number" step="0.01" class="form-control" name="largo" id="largo" 
                                   required placeholder="125.00" min="0.01">
                            <div class="form-text">Medida en centímetros</div>
                        </div>
                    </div>

                    <!-- Inventario y Stock -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-warehouse me-2"></i>Inventario Inicial
                            </h6>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="cantidad_disponible" class="form-label fw-bold">
                                <i class="fas fa-cubes text-info me-1"></i>Cantidad Inicial
                            </label>
                            <input type="number" class="form-control" name="cantidad_disponible" id="cantidad_disponible" 
                                   value="0" min="0" placeholder="0">
                            <div class="form-text">Stock inicial disponible</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="stock_minimo" class="form-label fw-bold">
                                <i class="fas fa-exclamation-triangle text-warning me-1"></i>Stock Mínimo
                            </label>
                            <input type="number" class="form-control" name="stock_minimo" id="stock_minimo" 
                                   value="1" min="1" placeholder="1">
                            <div class="form-text">Alerta cuando sea menor o igual</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="precio_unitario" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave text-success me-1"></i>Precio Unitario *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="precio_unitario" id="precio_unitario" 
                                       required min="0.01" placeholder="0.00">
                            </div>
                            <div class="form-text">Precio de venta por unidad</div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>Información del Sistema
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Los productos se identifican automáticamente por: <strong>Tipo + Dimensiones</strong></li>
                                    <li>Ejemplo: "Alcantarilla 60cm x 125cm"</li>
                                    <li>El stock se puede ajustar posteriormente desde el listado</li>
                                    <li>Los precios son por unidad individual del producto</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="/Sistema_Premoldeado/controllers/ProductoController.php?action=index" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" form="crearProductoForm" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Crear Producto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>