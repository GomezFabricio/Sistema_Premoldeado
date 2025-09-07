<?php
session_start();
$pageTitle = "Gestión de Clientes";

// TODO: $data = ClienteController::obtenerTodos();
$data = [];

$config = [
    'id' => 'clientes_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre_completo' => ['label' => 'Cliente'],
        'telefono' => ['label' => 'Teléfono'],
        'fecha_alta' => ['label' => 'Fecha Alta'],
        'observaciones' => ['label' => 'Observaciones'],
        'activo' => ['label' => 'Estado']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verCliente({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarCliente({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-users text-primary me-2"></i>Gestión de Clientes</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Cliente</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-users fa-2x mb-2"></i>
            <h5>No hay clientes registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Cliente</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verCliente(id) { window.location.href = `view.php?id=${id}`; }
function editarCliente(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
