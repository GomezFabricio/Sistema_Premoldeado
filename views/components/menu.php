<?php
/**
 * Componente de menú dinámico
 * Renderiza el menú basado en los permisos del usuario
 */

// Verificar que el usuario esté logueado y tenga módulos
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['modulos'])) {
    return;
}

// Obtener módulos del usuario desde la sesión
$menuModulos = $_SESSION['modulos'];
?>

<ul class="navbar-nav me-auto">
    <?php foreach ($menuModulos as $modulo): ?>
        <li class="nav-item dropdown">
            <?php if (!empty($modulo['submodulos'])): ?>
                <!-- Módulo con sub-opciones -->
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?php echo $modulo['id']; ?>" 
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="<?php echo $modulo['icono']; ?> me-1"></i>
                    <?php echo htmlspecialchars($modulo['nombre']); ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo $modulo['id']; ?>">
                    <!-- Sub-opciones -->
                    <?php foreach ($modulo['submodulos'] as $submodulo): ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo htmlspecialchars($submodulo['url']); ?>">
                                <i class="fas fa-angle-right me-2"></i>
                                <?php echo htmlspecialchars($submodulo['nombre']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <!-- Módulo sin sub-opciones -->
                <a class="nav-link" href="<?php echo htmlspecialchars($modulo['url']); ?>">
                    <i class="<?php echo $modulo['icono']; ?> me-1"></i>
                    <?php echo htmlspecialchars($modulo['nombre']); ?>
                </a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
