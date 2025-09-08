<?php
/**
 * Componente de menú dinámico responsivo
 * Renderiza el menú basado en los permisos del usuario
 */

// Verificar que el usuario esté logueado y tenga módulos
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['modulos'])) {
    return;
}

// Obtener módulos del usuario desde la sesión
$menuModulos = $_SESSION['modulos'];

// DEBUG: Mostrar qué URLs se están generando (remover después)
if (isset($_GET['debug'])) {
    echo "<pre>";
    print_r($menuModulos);
    echo "</pre>";
}
?>

<!-- Cabecera del menú móvil -->
<div class="nav-menu-header d-md-none">
    <div class="d-flex justify-content-between align-items-center">
        <div class="menu-title">
            <i class="fas fa-bars me-2"></i>
            <span>Menú de Navegación</span>
        </div>
        <button class="nav-close-btn" type="button" id="navClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Menú Principal Responsivo -->
<div class="nav-menu-container">
    <!-- Cabecera del menú móvil -->
    <div class="nav-menu-header d-md-none">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="menu-title mb-0">
                <i class="fas fa-bars me-2"></i>
                Navegación
            </h5>
            <button type="button" class="nav-close-btn" id="navClose" aria-label="Cerrar menú">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <!-- Items del menú -->
    <?php foreach ($menuModulos as $modulo): ?>
        <div class="nav-item">
            <?php if (!empty($modulo['submodulos']) && is_array($modulo['submodulos'])): ?>
                <!-- Módulo con submódulos -->
                <div class="nav-dropdown">
                    <button class="nav-link dropdown-toggle" data-module-id="<?php echo $modulo['id']; ?>">
                        <i class="<?php echo htmlspecialchars(isset($modulo['icono']) ? $modulo['icono'] : 'fas fa-circle'); ?>"></i>
                        <span class="nav-text"><?php echo htmlspecialchars(isset($modulo['nombre']) ? $modulo['nombre'] : 'Sin nombre'); ?></span>
                        <i class="fas fa-chevron-down nav-arrow"></i>
                    </button>
                    <div class="nav-submenu">
                        <?php foreach ($modulo['submodulos'] as $submodulo): ?>
                            <a href="<?php echo htmlspecialchars(isset($submodulo['url']) ? $submodulo['url'] : '#'); ?>" class="nav-sublink">
                                <i class="fas fa-angle-right"></i>
                                <span><?php echo htmlspecialchars(isset($submodulo['nombre']) ? $submodulo['nombre'] : 'Sin nombre'); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Módulo sin submódulos -->
                <a href="<?php echo htmlspecialchars(isset($modulo['url']) ? $modulo['url'] : '#'); ?>" class="nav-link">
                    <i class="<?php echo htmlspecialchars(isset($modulo['icono']) ? $modulo['icono'] : 'fas fa-circle'); ?>"></i>
                    <span class="nav-text"><?php echo htmlspecialchars(isset($modulo['nombre']) ? $modulo['nombre'] : 'Sin nombre'); ?></span>
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
