<?php
session_start();
$pageTitle = "Gestión de Producción";

// TODO: $data = ProduccionController::obtenerTodos();
$data = [];

$config = [
    'id' => 'produccion_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'fecha_inicio' => ['label' => 'Fecha Inicio'],
        'fecha_entrega' => ['label' => 'Fecha Entrega'],
        'cantidad' => ['label' => 'Cantidad'],
        'estado_nombre' => ['label' => 'Estado'],
        'cliente_nombre' => ['label' => 'Cliente']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verProduccion({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarProduccion({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-industry text-primary me-2"></i>Gestión de Producción</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Producción</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-industry fa-2x mb-2"></i>
            <h5>No hay producciones registradas</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primera Producción</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verProduccion(id) { window.location.href = `view.php?id=${id}`; }
function editarProduccion(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
