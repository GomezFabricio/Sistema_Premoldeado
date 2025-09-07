<?php
session_start();
$pageTitle = "Gestión de Materiales";

// TODO: $data = MaterialController::obtenerTodos();
$data = [];

$config = [
    'id' => 'materiales_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre' => ['label' => 'Material'],
        'unidad_medida' => ['label' => 'Unidad'],
        'cantidad' => ['label' => 'Cantidad'],
        'costo_unitario' => ['label' => 'Costo Unitario'],
        'stock_minimo' => ['label' => 'Stock Mínimo']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verMaterial({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarMaterial({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-cubes text-primary me-2"></i>Gestión de Materiales</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Material</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-cubes fa-2x mb-2"></i>
            <h5>No hay materiales registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Material</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verMaterial(id) { window.location.href = `view.php?id=${id}`; }
function editarMaterial(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
