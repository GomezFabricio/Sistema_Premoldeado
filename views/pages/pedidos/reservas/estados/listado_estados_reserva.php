<?php
session_start();
$pageTitle = "Gestión de Estados de Reserva";

// TODO: $data = EstadoReservaController::obtenerTodos();
$data = [];

$config = [
    'id' => 'estados_reserva_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre' => ['label' => 'Estado de Reserva']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verEstadoReserva({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarEstadoReserva({id})']
    ]
];

include_once '../../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-calendar-alt text-primary me-2"></i>Gestión de Estados de Reserva</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Estado</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
            <h5>No hay estados de reserva registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Estado</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verEstadoReserva(id) { window.location.href = `view.php?id=${id}`; }
function editarEstadoReserva(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../../layouts/footer.php'; ?>
