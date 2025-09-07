<?php
session_start();
$pageTitle = "Gestión de Devoluciones";

// TODO: $data = DevolucionController::obtenerTodos();
$data = [];

$config = [
    'id' => 'devoluciones_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'fecha' => ['label' => 'Fecha'],
        'cliente_nombre' => ['label' => 'Cliente'],
        'producto_nombre' => ['label' => 'Producto'],
        'descripcion' => ['label' => 'Descripción'],
        'estado_nombre' => ['label' => 'Estado']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verDevolucion({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarDevolucion({id})']
    ]
];

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-undo text-primary me-2"></i>Gestión de Devoluciones</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Devolución</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-undo fa-2x mb-2"></i>
            <h5>No hay devoluciones registradas</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primera Devolución</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verDevolucion(id) { window.location.href = `view.php?id=${id}`; }
function editarDevolucion(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../layouts/footer.php'; ?>
