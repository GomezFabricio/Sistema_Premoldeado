<?php
// Esta vista se renderiza desde MaterialController, no necesita session_start
$pageTitle = $title ?? "Editar Material";

// Los datos vienen del controlador
$material = $data['material'] ?? null;
$categorias = $data['categorias'] ?? [];
$unidadesMedida = $data['unidades_medida'] ?? [];

if (!$material) {
    echo '<div class="alert alert-danger">Material no encontrado</div>';
    return;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-boxes text-primary me-2"></i>Editar Material</h2>
        <a href="materiales" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="materiales/update/<?= $material['material_id'] ?>" method="POST" id="materialForm">
                <div class="row">
                    <!-- Información básica -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="codigo_material" class="form-label">Código del Material *</label>
                            <input type="text" class="form-control" id="codigo_material" name="codigo_material" 
                                   value="<?= htmlspecialchars($material['codigo_material']) ?>" required>
                            <div class="form-text">Debe ser único en el sistema</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Material *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($material['nombre']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($material['descripcion'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Categorización -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría *</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="categoria" name="categoria" 
                                       value="<?= htmlspecialchars($material['categoria']) ?>" required list="categoriasList">
                                <datalist id="categoriasList">
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= htmlspecialchars($categoria) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="unidad_medida_id" class="form-label">Unidad de Medida *</label>
                            <select class="form-select" id="unidad_medida_id" name="unidad_medida_id" required>
                                <option value="">Seleccionar unidad...</option>
                                <?php foreach ($unidadesMedida as $unidad): ?>
                                    <option value="<?= $unidad['id'] ?>" 
                                            <?= $material['unidad_medida_id'] == $unidad['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($unidad['nombre']) ?> 
                                        (<?= htmlspecialchars($unidad['simbolo']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Información financiera -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="precio_unitario" class="form-label">Costo Unitario *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio_unitario" 
                                       name="precio_unitario" step="0.01" min="0"
                                       value="<?= number_format($material['costo_unitario'], 2, '.', '') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Control de stock (solo mostrar, el stock actual se actualiza por separado) -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_actual_display" class="form-label">Stock Actual</label>
                            <input type="text" class="form-control" id="stock_actual_display" 
                                   value="<?= number_format($material['stock_actual'], 2) ?> <?= htmlspecialchars($material['unidad_simbolo']) ?>" 
                                   readonly>
                            <div class="form-text">
                                Para modificar el stock, usa el botón "Actualizar Stock" en el listado
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                                   step="0.01" min="0" value="<?= number_format($material['stock_minimo'], 2, '.', '') ?>">
                        </div>
                    </div>
                    
                    <!-- Información del proveedor (si se implementa) -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor Principal</label>
                            <select class="form-select" id="proveedor_id" name="proveedor_id">
                                <option value="">Sin proveedor asignado</option>
                                <!-- Se cargarían los proveedores desde el controlador -->
                            </select>
                        </div>
                    </div>
                    
                    <!-- Información de estado -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Estado del Stock</label>
                            <div class="form-control-plaintext">
                                <span class="badge <?= 
                                    $material['estado_stock'] === 'AGOTADO' ? 'bg-danger' : 
                                    ($material['estado_stock'] === 'BAJO' ? 'bg-warning text-dark' : 'bg-success') 
                                ?>">
                                    <?= $material['estado_stock'] ?>
                                </span>
                                
                                <?php if ($material['estado_stock'] === 'BAJO'): ?>
                                    <small class="text-warning ms-2">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Stock por debajo del mínimo
                                    </small>
                                <?php elseif ($material['estado_stock'] === 'AGOTADO'): ?>
                                    <small class="text-danger ms-2">
                                        <i class="fas fa-exclamation-circle"></i> 
                                        Material agotado
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Actualizar Material
                    </button>
                    <a href="materiales" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validación del formulario
    $('#materialForm').on('submit', function(e) {
        const codigo = $('#codigo_material').val().trim();
        const nombre = $('#nombre').val().trim();
        const categoria = $('#categoria').val().trim();
        const unidadMedida = $('#unidad_medida_id').val();
        const precioUnitario = $('#precio_unitario').val();
        
        if (!codigo || !nombre || !categoria || !unidadMedida || !precioUnitario) {
            e.preventDefault();
            showAlert('error', 'Por favor completa todos los campos obligatorios (*)');
            return false;
        }
        
        if (parseFloat(precioUnitario) < 0) {
            e.preventDefault();
            showAlert('error', 'El costo unitario debe ser mayor o igual a 0');
            return false;
        }
    });
    
    // Formateo automático de números
    $('#precio_unitario, #stock_minimo').on('blur', function() {
        const value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });
});

// Función para mostrar alertas
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container-fluid').prepend(alertHtml);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
