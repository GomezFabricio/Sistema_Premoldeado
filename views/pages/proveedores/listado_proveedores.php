<?php
session_start();
$pageTitle = "Gestión de Proveedores";

// TODO: $data = ProveedorController::obtenerTodos();
$data = [];

$config = [
    'id' => 'proveedores_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre_completo' => ['label' => 'Proveedor'],
        'telefono' => ['label' => 'Teléfono'],
        'condicion_iva' => ['label' => 'Condición IVA'],
        'fecha_registro' => ['label' => 'Fecha Registro'],
        'activo' => ['label' => 'Estado']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verProveedor({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarProveedor({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-truck text-primary me-2"></i>Gestión de Proveedores</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Proveedor</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-truck fa-2x mb-2"></i>
            <h5>No hay proveedores registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Proveedor</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verProveedor(id) { window.location.href = `view.php?id=${id}`; }
function editarProveedor(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
