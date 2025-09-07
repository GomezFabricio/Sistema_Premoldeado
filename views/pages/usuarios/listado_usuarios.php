<?php
session_start();
$pageTitle = "Gestión de Usuarios";

// TODO: $data = UsuarioController::obtenerTodos();
$data = [];

$config = [
    'id' => 'usuarios_table',
    'columns' => [
        'id' => ['label' => 'ID'],
        'nombre_usuarios' => ['label' => 'Usuario'],
        'nombre_completo' => ['label' => 'Nombre Completo'],
        'email' => ['label' => 'Email'],
        'telefono' => ['label' => 'Teléfono'],
        'perfil_nombre' => ['label' => 'Perfil'],
        'activo' => ['label' => 'Estado']
    ],
    'actions' => [
        ['label' => 'Ver', 'icon' => 'fas fa-eye', 'onclick' => 'verUsuario({id})'],
        ['label' => 'Editar', 'icon' => 'fas fa-edit', 'onclick' => 'editarUsuario({id})']
    ]
];

include_once '../../../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-users text-primary me-2"></i>Gestión de Usuarios</h2>
        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Usuario</a>
    </div>

    <?php if (!empty($data)): ?>
        <?php include '../../components/table.php'; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-users fa-2x mb-2"></i>
            <h5>No hay usuarios registrados</h5>
            <a href="create.php" class="btn btn-primary mt-2">Crear Primer Usuario</a>
        </div>
    <?php endif; ?>
</div>

<script>
function verUsuario(id) { window.location.href = `view.php?id=${id}`; }
function editarUsuario(id) { window.location.href = `edit.php?id=${id}`; }
</script>

<?php include '../../../layouts/footer.php'; ?>
