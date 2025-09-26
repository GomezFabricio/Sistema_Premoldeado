<?php
session_start();
include_once __DIR__ . '/../../layouts/header.php';
$tiposProducto = [
    ['id' => 1, 'nombre' => 'Alcantarilla'],
    ['id' => 2, 'nombre' => 'Pilar'],
    ['id' => 3, 'nombre' => 'Cámara'],
    ['id' => 4, 'nombre' => 'Cámara Séptica']
];
$mensajeExito = $_SESSION['mensaje_exito'] ?? '';
$mensajeError = $_SESSION['mensaje_error'] ?? '';
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>
<script>
function mostrarCamposPorTipo() {
    var tipo = document.getElementById('tipo_producto_id').value;
    // Mostrar/ocultar campos según el tipo
    document.getElementById('campo-ancho').style.display = (tipo == '1' || tipo == '2' || tipo == '3' || tipo == '4') ? 'block' : 'none';
    document.getElementById('campo-largo').style.display = (tipo == '1' || tipo == '2' || tipo == '3' || tipo == '4') ? 'block' : 'none';
    document.getElementById('campo-cantidad').style.display = 'block';
    document.getElementById('campo-stock-minimo').style.display = 'block';
    document.getElementById('campo-precio-unitario').style.display = 'block';
    // Ejemplo: podrías personalizar más según el tipo
    // if (tipo == '1') { ... } // Alcantarilla
}
window.onload = mostrarCamposPorTipo;
</script>
<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Sistema Premoldeado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="fas fa-plus-circle me-2"></i>
                Crear Nuevo Producto
            </h1>
            <a href="listado_productos.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver al Listado
            </a>
        </div>

    <!-- Mensajes -->
    <?php 
    $mensajeExito = $mensajeExito ?? '';
    $mensajeError = $mensajeError ?? '';
    ?>
    <?php if ($mensajeExito): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($mensajeExito); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($mensajeError): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($mensajeError); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario Simplificado -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-cube me-2"></i>Nuevo Producto Premoldeado
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    
                    <!-- Tipo de Producto -->
                    <div class="col-md-6">
                        <label for="tipo_producto_id" class="form-label">
                            <i class="fas fa-tag me-1"></i>Tipo de Producto *
                        </label>
                        <select class="form-select" id="tipo_producto_id" name="tipo_producto_id" required>
                            <option value="">Seleccionar tipo...</option>
                            <?php foreach ($tiposProducto as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo['id']); ?>"
                                        <?php echo ($datosFormulario['tipo_producto_id'] ?? '') == $tipo['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Solo se gestionan estos 4 tipos de productos</div>
                    </div>

                    <!-- Dimensiones -->
                    <div class="col-md-3">
                        <label for="ancho" class="form-label">
                            <i class="fas fa-arrows-alt-h me-1"></i>Ancho *
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="ancho" name="ancho" required
                                   value="<?php echo htmlspecialchars($datosFormulario['ancho'] ?? ''); ?>"
                                   placeholder="60">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="largo" class="form-label">
                            <i class="fas fa-arrows-alt-v me-1"></i>Largo *
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="largo" name="largo" required
                                   value="<?php echo htmlspecialchars($datosFormulario['largo'] ?? ''); ?>"
                                   placeholder="1.25">
                            <span class="input-group-text">m</span>
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="col-md-4">
                        <label for="cantidad_disponible" class="form-label">
                            <i class="fas fa-boxes me-1"></i>Stock Inicial
                        </label>
                        <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" 
                               value="<?php echo htmlspecialchars($datosFormulario['cantidad_disponible'] ?? '0'); ?>"
                               min="0" placeholder="0">
                        <div class="form-text">Cantidad disponible al crear</div>
                    </div>

                    <div class="col-md-4">
                        <label for="stock_minimo" class="form-label">
                            <i class="fas fa-exclamation-triangle me-1"></i>Stock Mínimo
                        </label>
                        <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                               value="<?php echo htmlspecialchars($datosFormulario['stock_minimo'] ?? '1'); ?>"
                               min="1" placeholder="1">
                        <div class="form-text">Alerta cuando llegue a este nivel</div>
                    </div>

                    <div class="col-md-4">
                        <label for="precio_unitario" class="form-label">
                            <i class="fas fa-dollar-sign me-1"></i>Precio Unitario *
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" 
                                   required min="0.01" step="0.01"
                                   value="<?php echo htmlspecialchars($datosFormulario['precio_unitario'] ?? ''); ?>"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Guardar Producto
                            </button>
                            <a href="listado_productos.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="alert alert-info mt-4">
            <h6><i class="fas fa-info-circle me-2"></i>Sistema Simplificado</h6>
            <ul class="mb-0 small">
                <li><strong>Identificación:</strong> Los productos se identifican por tipo + dimensiones (Ej: "Alcantarilla 60cm x 1.25m")</li>
                <li><strong>Sin códigos:</strong> No se usan códigos de producto, las dimensiones son únicas</li>
                <li><strong>Gestión básica:</strong> Solo stock y precios, sin categorías complejas</li>
                <li><strong>Números de pedido:</strong> Se asignan en el módulo de pedidos, no aquí</li>
            </ul>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>