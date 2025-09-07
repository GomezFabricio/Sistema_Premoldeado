<?php
session_start();
$pageTitle = "Gestión de Compras";

// TODO: $data = CompraController::obtenerTodos();
$data = [];

$config = [
    'id' => 'compras_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'fecha' => ['label' => 'Fecha'],
        'proveedor_nombre' => ['label' => 'Proveedor'],
        'total_compra' => ['label' => 'Total']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verCompra({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarCompra({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-shopping-bag text-primary me-2"></i>Gestión de Compras</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Compra</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-shopping-bag fa-2x mb-2"></i>
            <h5>No hay compras registradas</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primera Compra</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verCompra(id) { window.location.href = `view.php?id=${id}`; }
function editarCompra(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
