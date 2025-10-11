
<?php
// Las variables vienen del controlador: $producto, $tiposProducto, $pageTitle

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
                <i class="fas fa-edit text-primary me-2"></i><?= htmlspecialchars($pageTitle ?? 'Editar Producto') ?>
            </h1>
            <a href="/Sistema_Premoldeado/controllers/ProductoController.php?action=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>Información del Producto #<?= htmlspecialchars($producto['id']) ?>
                </h5>
            </div>
            <div class="card-body"

                <form action="/Sistema_Premoldeado/controllers/ProductoController.php?action=update&id=<?= urlencode($producto['id']) ?>" method="POST">
                    
                    <!-- Información básica -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-info-circle me-2"></i>Información Básica
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ancho" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-h text-primary me-1"></i>Ancho (cm) *
                            </label>
                            <input type="number" step="0.01" class="form-control" name="ancho" id="ancho" 
                                   value="<?= htmlspecialchars($producto['ancho']) ?>" required
                                   placeholder="Ingrese el ancho en centímetros">
                            <div class="form-text">Medida del ancho del producto en centímetros</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="largo" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-v text-primary me-1"></i>Largo (cm) *
                            </label>
                            <input type="number" step="0.01" class="form-control" name="largo" id="largo" 
                                   value="<?= htmlspecialchars($producto['largo']) ?>" required
                                   placeholder="Ingrese el largo en centímetros">
                            <div class="form-text">Medida del largo del producto en centímetros</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_producto_id" class="form-label fw-bold">
                                <i class="fas fa-tags text-primary me-1"></i>Tipo de Producto *
                            </label>
                            <select class="form-select" name="tipo_producto_id" id="tipo_producto_id" required>
                                <option value="">Seleccione un tipo...</option>
                                <?php foreach ($tiposProducto as $tipo): ?>
                                    <option value="<?= $tipo['id'] ?>" <?= $producto['tipo_producto_id'] == $tipo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Categoría a la que pertenece el producto</div>
                        </div>
                    </div>

                    <!-- Inventario y Stock -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-warehouse me-2"></i>Inventario y Stock
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cantidad_disponible" class="form-label fw-bold">
                                <i class="fas fa-cubes text-success me-1"></i>Cantidad Disponible
                            </label>
                            <input type="number" class="form-control" name="cantidad_disponible" id="cantidad_disponible" 
                                   value="<?= htmlspecialchars($producto['cantidad_disponible']) ?>" min="0"
                                   placeholder="Cantidad actual en inventario">
                            <div class="form-text">Cantidad actual disponible en inventario</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="stock_minimo" class="form-label fw-bold">
                                <i class="fas fa-exclamation-triangle text-warning me-1"></i>Stock Mínimo
                            </label>
                            <input type="number" class="form-control" name="stock_minimo" id="stock_minimo" 
                                   value="<?= htmlspecialchars($producto['stock_minimo']) ?>" min="1"
                                   placeholder="Cantidad mínima requerida">
                            <div class="form-text">Cantidad mínima antes de alertar por stock bajo</div>
                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-dollar-sign me-2"></i>Información de Precio
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="precio_unitario" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave text-success me-1"></i>Precio Unitario *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="precio_unitario" id="precio_unitario" 
                                       value="<?= htmlspecialchars($producto['precio_unitario']) ?>" required min="0.01"
                                       placeholder="0.00">
                            </div>
                            <div class="form-text">Precio de venta por unidad del producto</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-calculator text-info me-1"></i>Estado del Stock
                            </label>
                            <div class="p-3 bg-light rounded">
                                <?php 
                                $cantidad = (int)($producto['cantidad_disponible'] ?? 0);
                                $minimo = (int)($producto['stock_minimo'] ?? 1);
                                
                                if ($cantidad <= 0): ?>
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-times me-1"></i>AGOTADO
                                    </span>
                                    <div class="text-muted small mt-1">El producto no tiene stock disponible</div>
                                <?php elseif ($cantidad <= $minimo): ?>
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="fas fa-exclamation-triangle me-1"></i>STOCK BAJO
                                    </span>
                                    <div class="text-muted small mt-1">Stock por debajo del mínimo requerido</div>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check me-1"></i>STOCK OK
                                    </span>
                                    <div class="text-muted small mt-1">Nivel de stock adecuado</div>
                                <?php endif; ?>
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
                    <button type="submit" form="editarProductoForm" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Agregar el ID al formulario para poder referenciarlo desde el botón
document.querySelector('form').id = 'editarProductoForm';

// Actualizar estado del stock en tiempo real
function actualizarEstadoStock() {
    const cantidad = parseInt(document.getElementById('cantidad_disponible').value) || 0;
    const minimo = parseInt(document.getElementById('stock_minimo').value) || 1;
    
    // Esta función se puede expandir para mostrar el estado dinámicamente
}

// Agregar listeners para actualización en tiempo real
document.getElementById('cantidad_disponible').addEventListener('input', actualizarEstadoStock);
document.getElementById('stock_minimo').addEventListener('input', actualizarEstadoStock);
</script>
