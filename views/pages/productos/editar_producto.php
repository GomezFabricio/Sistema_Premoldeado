
<?php
session_start();
include_once __DIR__ . '/../../layouts/header.php';
require_once '../../../models/Producto.php';
$producto = new Producto();
$tiposProducto = $producto->obtenerTiposProducto();
$mensajeExito = $_SESSION['mensaje_exito'] ?? '';
$mensajeError = $_SESSION['mensaje_error'] ?? '';
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);

$id = $_GET['id'] ?? null;
$datos = $id ? $producto->obtenerPorId($id) : null;
if (!$datos) {
	echo '<div class="alert alert-danger">Producto no encontrado.</div>';
	include_once __DIR__ . '/../../layouts/footer.php';
	exit;
}
?>
<script>

	<?php if ($mensajeExito): ?>
		<div class="alert alert-success"><?= htmlspecialchars($mensajeExito) ?></div>
	<?php endif; ?>
	<?php if ($mensajeError): ?>
		<div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
	<?php endif; ?>

	<form action="../../../controllers/ProductoController.php?action=actualizar" method="POST" class="card p-4 shadow-sm">
		<input type="hidden" name="id" value="<?= htmlspecialchars($datos['id']) ?>">
		<div class="row mb-3">
			<div class="col-md-6" id="campo-ancho">
				<label for="ancho" class="form-label">Ancho</label>
				<input type="number" step="0.01" class="form-control" name="ancho" id="ancho" value="<?= htmlspecialchars($datos['ancho']) ?>" required>
			</div>
			<div class="col-md-6" id="campo-largo">
				<label for="largo" class="form-label">Largo</label>
				<input type="number" step="0.01" class="form-control" name="largo" id="largo" value="<?= htmlspecialchars($datos['largo']) ?>" required>
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-md-6" id="campo-cantidad">
				<label for="cantidad_disponible" class="form-label">Cantidad Disponible</label>
				<input type="number" class="form-control" name="cantidad_disponible" id="cantidad_disponible" value="<?= htmlspecialchars($datos['cantidad_disponible']) ?>" required>
			</div>
			<div class="col-md-6" id="campo-stock-minimo">
				<label for="stock_minimo" class="form-label">Stock Mínimo</label>
				<input type="number" class="form-control" name="stock_minimo" id="stock_minimo" value="<?= htmlspecialchars($datos['stock_minimo']) ?>" required>
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-md-6" id="campo-precio-unitario">
				<label for="precio_unitario" class="form-label">Precio Unitario</label>
				<input type="number" step="0.01" class="form-control" name="precio_unitario" id="precio_unitario" value="<?= htmlspecialchars($datos['precio_unitario']) ?>" required>
			</div>
			<div class="col-md-6">
				<label for="tipo_producto_id" class="form-label">Tipo de Producto</label>
				<select class="form-select" name="tipo_producto_id" id="tipo_producto_id" onchange="mostrarCamposPorTipo()" required>
					<option value="">Seleccione...</option>
					<?php foreach ($tiposProducto as $tipo): ?>
						<option value="<?= $tipo['id'] ?>" <?= $datos['tipo_producto_id'] == $tipo['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre']) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="d-flex justify-content-end">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-save me-1"></i> Guardar Cambios
			</button>
		</div>
	</form>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
