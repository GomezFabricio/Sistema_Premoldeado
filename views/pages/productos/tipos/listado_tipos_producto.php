<?php
session_start();
$pageTitle = "Gestión de Tipos de Producto";

// TODO: $data = TipoProductoController::obtenerTodos();
$data = [];

$config = [
    'id' => 'tipos_producto_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre' => ['label' => 'Tipo de Producto'],
        'activo' => ['label' => 'Estado']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verTipoProducto({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarTipoProducto({id})']
    ]
];

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-tags text-primary me-2"></i>Gestión de Tipos de Producto</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Tipo</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-tags fa-2x mb-2"></i>
            <h5>No hay tipos de producto registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Tipo</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verTipoProducto(id) { window.location.href = `view.php?id=${id}`; }
function editarTipoProducto(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../layouts/footer.php'; ?>
