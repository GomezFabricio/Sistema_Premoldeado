<?php
session_start();
$pageTitle = "Gestión de Reservas";

// TODO: $data = ReservaController::obtenerTodos();
$data = [];

$config = [
    'id' => 'reservas_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'fecha_reserva' => ['label' => 'Fecha'],
        'cliente_nombre' => ['label' => 'Cliente'],
        'cantidad' => ['label' => 'Cantidad'],
        'senia' => ['label' => 'Seña'],
        'estado_nombre' => ['label' => 'Estado']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verReserva({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarReserva({id})']
    ]
];

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-calendar-check text-primary me-2"></i>Gestión de Reservas</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Reserva</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-calendar-check fa-2x mb-2"></i>
            <h5>No hay reservas registradas</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primera Reserva</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verReserva(id) { window.location.href = `view.php?id=${id}`; }
function editarReserva(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../layouts/footer.php'; ?>
