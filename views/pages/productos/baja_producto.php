<?php
include_once __DIR__ . '/../../layouts/header.php';
$id = $_GET['id'] ?? null;
?>
<div class="container mt-4">
    <h2>Baja lógica de producto</h2>
    <form method="POST" action="marcar_inactivo.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <div class="alert alert-warning">¿Seguro que deseas marcar este producto como inactivo? No se eliminará físicamente.</div>
        <button type="submit" class="btn btn-danger">Confirmar baja lógica</button>
        <a href="listado_productos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
