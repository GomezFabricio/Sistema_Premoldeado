<?php
session_start();
$pageTitle = "Gestión de Productos";

// TODO: $data = ProductoController::obtenerTodos();
$data = [];

$config = [
    'id' => 'productos_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'dimensiones' => ['label' => 'Dimensiones'],
        'tipo_nombre' => ['label' => 'Tipo'],
        'cantidad_disponible' => ['label' => 'Stock'],
        'precio_unitario' => ['label' => 'Precio'],
        'activo' => ['label' => 'Estado']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verProducto({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarProducto({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-boxes text-primary me-2"></i>Gestión de Productos</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Producto</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-boxes fa-2x mb-2"></i>
            <h5>No hay productos registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Producto</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verProducto(id) { window.location.href = `view.php?id=${id}`; }
function editarProducto(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
