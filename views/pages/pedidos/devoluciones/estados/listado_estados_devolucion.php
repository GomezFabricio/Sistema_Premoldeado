<?php
session_start();
$pageTitle = "Gestión de Estados de Devolución";

// TODO: $data = EstadoDevolucionController::obtenerTodos();
$data = [];

$config = [
    'id' => 'estados_devolucion_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre' => ['label' => 'Estado de Devolución']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verEstadoDevolucion({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarEstadoDevolucion({id})']
    ]
];

include_once '../../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-undo-alt text-primary me-2"></i>Gestión de Estados de Devolución</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Estado</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-undo-alt fa-2x mb-2"></i>
            <h5>No hay estados de devolución registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Estado</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verEstadoDevolucion(id) { window.location.href = `view.php?id=${id}`; }
function editarEstadoDevolucion(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../../layouts/footer.php'; ?>
