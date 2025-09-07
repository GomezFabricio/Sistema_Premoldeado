<?php
session_start();
$pageTitle = "Gestión de Pedidos";

// TODO: $data = PedidoController::obtenerTodos();
$data = [];

$config = [
    'id' => 'pedidos_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'fecha' => ['label' => 'Fecha'],
        'cliente_nombre' => ['label' => 'Cliente'],
        'estado_nombre' => ['label' => 'Estado'],
        'forma_entrega_nombre' => ['label' => 'Entrega']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verPedido({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarPedido({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-shopping-cart text-primary me-2"></i>Gestión de Pedidos</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Pedido</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
            <h5>No hay pedidos registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Pedido</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verPedido(id) { window.location.href = iew.php?id=${id}; }
function editarPedido(id) { window.location.href = edit.php?id=${id}; }
</script>

<?php include '../../../layouts/footer.php'; ?>
