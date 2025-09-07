<?php
session_start();
$pageTitle = "Gestión de Formas de Entrega";

// TODO: $data = FormaEntregaController::obtenerTodos();
$data = [];

$config = [
    'id' => 'formas_entrega_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre' => ['label' => 'Forma de Entrega']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verFormaEntrega({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarFormaEntrega({id})']
    ]
];

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-truck text-primary me-2"></i>Gestión de Formas de Entrega</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Forma</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-truck fa-2x mb-2"></i>
            <h5>No hay formas de entrega registradas</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primera Forma</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verFormaEntrega(id) { window.location.href = `view.php?id=${id}`; }
function editarFormaEntrega(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../layouts/footer.php'; ?>
