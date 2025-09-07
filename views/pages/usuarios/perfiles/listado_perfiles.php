<?php
session_start();
$pageTitle = "Gestión de Perfiles de Usuario";

// TODO: $data = PerfilController::obtenerTodos();
$data = [];

$config = [
    'id' => 'perfiles_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre' => ['label' => 'Nombre del Perfil']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verPerfil({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarPerfil({id})']
    ]
];

include_once '../../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-user-tag text-primary me-2"></i>Gestión de Perfiles</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Perfil</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-user-tag fa-2x mb-2"></i>
            <h5>No hay perfiles registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Perfil</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verPerfil(id) { window.location.href = `view.php?id=${id}`; }
function editarPerfil(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../../layouts/footer.php'; ?>
