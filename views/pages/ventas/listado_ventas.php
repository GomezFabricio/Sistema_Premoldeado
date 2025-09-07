<?php
session_start();
$pageTitle = "Gestión de Ventas";

// TODO: $data = VentaController::obtenerTodos();
$data = [];

$config = [
    'id' => 'ventas_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'fecha' => ['label' => 'Fecha'],
        'cliente_nombre' => ['label' => 'Cliente'],
        'monto_total' => ['label' => 'Total'],
        'metodo_pago' => ['label' => 'Método de Pago']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verVenta({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarVenta({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-cash-register text-primary me-2"></i>Gestión de Ventas</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Venta</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-cash-register fa-2x mb-2"></i>
            <h5>No hay ventas registradas</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primera Venta</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verVenta(id) { window.location.href = `view.php?id=${id}`; }
function editarVenta(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
