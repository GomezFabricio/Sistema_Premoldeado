<?php
// Esta vista se renderiza desde MaterialController, no necesita session_start
$pageTitle = $title ?? "Crear Material";

// Los datos vienen del controlador
$categorias = $data['categorias'] ?? [];
$unidadesMedida = $data['unidadesMedida'] ?? [];
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-boxes text-primary me-2"></i>Crear Material</h2>
        <a href="?action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="?action=store" method="POST" id="materialForm">
                <div class="row">
                    <!-- Información básica -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="codigo_barras" class="form-label">Código del Material</label>
                            <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" 
                                   placeholder="Código único del material">
                            <div class="form-text">Opcional: código único del material</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Material *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   placeholder="Nombre descriptivo del material" required>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Descripción detallada del material (opcional)"></textarea>
                        </div>
                    </div>
                    
                    <!-- Categorización -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría *</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="categoria" name="categoria" 
                                       placeholder="Categoría del material" required list="categoriasList">
                                <datalist id="categoriasList">
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= htmlspecialchars($categoria) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-text">Selecciona una existente o escribe una nueva</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="unidad_medida_id" class="form-label">Unidad de Medida *</label>
                            <select class="form-select" id="unidad_medida_id" name="unidad_medida_id" required>
                                <option value="">Seleccionar unidad...</option>
                                <?php foreach ($unidadesMedida as $unidad): ?>
                                    <option value="<?= $unidad['id'] ?>">
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
                            <label for="costo_unitario" class="form-label">Costo Unitario *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="costo_unitario" 
                                       name="costo_unitario" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Control de stock -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cantidad_stock" class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" id="cantidad_stock" name="cantidad_stock" 
                                   step="0.01" min="0" value="0">
                            <div class="form-text">Cantidad inicial en inventario</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                                   step="0.01" min="0" value="1">
                            <div class="form-text">Nivel mínimo para alertas</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_maximo" class="form-label">Stock Máximo</label>
                            <input type="number" class="form-control" id="stock_maximo" name="stock_maximo" 
                                   step="0.01" min="0" placeholder="Opcional">
                            <div class="form-text">Nivel máximo de inventario</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="precio_venta" class="form-label">Precio de Venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio_venta" 
                                       name="precio_venta" step="0.01" min="0" placeholder="Opcional">
                            </div>
                            <div class="form-text">Precio sugerido de venta</div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="ubicacion_deposito" class="form-label">Ubicación en Depósito</label>
                            <input type="text" class="form-control" id="ubicacion_deposito" name="ubicacion_deposito" 
                                   placeholder="Ej: Estante A2, Sector 3, etc.">
                            <div class="form-text">Ubicación física del material</div>
                        </div>
                    </div>
                    
                    <!-- Información del proveedor (opcional) -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor Principal</label>
                            <select class="form-select" id="proveedor_id" name="proveedor_id">
                                <option value="">Sin proveedor asignado</option>
                                <!-- Se cargarían los proveedores desde el controlador -->
                            </select>
                            <div class="form-text">Opcional: proveedor principal del material</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Material
                    </button>
                    <a href="?action=index" class="btn btn-secondary">
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
        // Validaciones adicionales si son necesarias
        const nombre = $('#nombre').val().trim();
        const unidadMedida = $('#unidad_medida_id').val();
        const costoUnitario = $('#costo_unitario').val();
        
        if (!nombre || !unidadMedida || !costoUnitario) {
            e.preventDefault();
            showAlert('error', 'Por favor completa todos los campos obligatorios (*)');
            return false;
        }
        
        if (parseFloat(costoUnitario) < 0) {
            e.preventDefault();
            showAlert('error', 'El costo unitario debe ser mayor o igual a 0');
            return false;
        }
    });
    
    // Formateo automático de números
    $('#costo_unitario, #precio_venta, #cantidad_stock, #stock_minimo, #stock_maximo').on('blur', function() {
        const value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });
    
    // Generar código automático basado en nombre (opcional)
    $('#nombre').on('blur', function() {
        if ($('#codigo_barras').val() === '') {
            const nombre = $(this).val().substring(0, 6).toUpperCase().replace(/\s/g, '');
            if (nombre) {
                const timestamp = Date.now().toString().slice(-4);
                $('#codigo_barras').val(`${nombre}-${timestamp}`);
            }
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
