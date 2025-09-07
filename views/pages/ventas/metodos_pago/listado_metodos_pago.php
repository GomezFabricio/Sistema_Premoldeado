<?php
session_start();
$pageTitle = "Gestión de Métodos de Pago";

// TODO: $data = MetodoPagoController::obtenerTodos();
$data = [];

$config = [
    'id' => 'metodos_pago_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre' => ['label' => 'Método de Pago']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verMetodoPago({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarMetodoPago({id})']
    ]
];

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-credit-card text-primary me-2"></i>Gestión de Métodos de Pago</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Método</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-credit-card fa-2x mb-2"></i>
            <h5>No hay métodos de pago registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Método</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verMetodoPago(id) { window.location.href = `view.php?id=${id}`; }
function editarMetodoPago(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../layouts/footer.php'; ?>
