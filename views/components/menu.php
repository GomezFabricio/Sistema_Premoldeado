<?php
/**
 * Componente de menú simple usando Bootstrap
 * Renderiza el menú basado en los permisos del usuario
 */

// Verificar que el usuario esté logueado y tenga módulos
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['modulos'])) {
    return;
}

// Menú principal limpio y ordenado
$menuModulos = [
    [
        'nombre' => 'Dashboard',
        'url' => '/Sistema_Premoldeado/views/pages/dashboard.php',
        'icono' => 'fas fa-tachometer-alt',
        'submodulos' => []
    ],
    [
        'nombre' => 'Productos',
        'url' => '/Sistema_Premoldeado/views/pages/productos/listado_productos.php',
        'icono' => 'fas fa-boxes',
        'submodulos' => []
    ],
    [
        'nombre' => 'Producción',
        'url' => '/Sistema_Premoldeado/views/pages/produccion/listado_producciones.php',
        'icono' => 'fas fa-industry',
        'submodulos' => []
    ],
    [
        'nombre' => 'Materiales',
        'url' => '/Sistema_Premoldeado/views/pages/materiales/listado_materiales.php',
        'icono' => 'fas fa-cubes',
        'submodulos' => []
    ],
    [
        'nombre' => 'Clientes',
        'url' => '/Sistema_Premoldeado/views/pages/clientes/listado_clientes.php',
        'icono' => 'fas fa-user-tie',
        'submodulos' => []
    ],
    [
        'nombre' => 'Proveedores',
        'url' => '/Sistema_Premoldeado/views/pages/proveedores/listado_proveedores.php',
        'icono' => 'fas fa-truck',
        'submodulos' => []
    ],
    [
        'nombre' => 'Pedidos',
        'url' => '/Sistema_Premoldeado/views/pages/pedidos/listado_pedidos.php',
        'icono' => 'fas fa-shopping-cart',
        'submodulos' => []
    ],
    [
        'nombre' => 'Ventas',
        'url' => '/Sistema_Premoldeado/views/pages/ventas/listado_ventas.php',
        'icono' => 'fas fa-cash-register',
        'submodulos' => []
    ],
    [
        'nombre' => 'Usuarios',
        'url' => '/Sistema_Premoldeado/views/pages/usuarios/listado_usuarios.php',
        'icono' => 'fas fa-users',
        'submodulos' => []
    ],
    [
        'nombre' => 'Perfiles',
        'url' => '/Sistema_Premoldeado/views/pages/usuarios/perfiles/listado_perfiles.php',
        'icono' => 'fas fa-user-shield',
        'submodulos' => []
    ]
];
?>

<!-- Menú Simple con Bootstrap -->
<ul class="nav d-none d-lg-flex" id="desktopMenu">
    <?php foreach ($menuModulos as $index => $modulo): ?>
        <?php if (!empty($modulo['submodulos']) && is_array($modulo['submodulos'])): ?>
            <!-- Módulo con submódulos -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-module-index="<?php echo $index; ?>">
                    <i class="<?php echo htmlspecialchars(isset($modulo['icono']) ? $modulo['icono'] : 'fas fa-circle'); ?> me-1"></i>
                    <?php echo htmlspecialchars(isset($modulo['nombre']) ? $modulo['nombre'] : 'Sin nombre'); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach ($modulo['submodulos'] as $submodulo): ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo htmlspecialchars(isset($submodulo['url']) ? $submodulo['url'] : '#'); ?>">
                                <?php echo htmlspecialchars(isset($submodulo['nombre']) ? $submodulo['nombre'] : 'Sin nombre'); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php else: ?>
            <!-- Módulo sin submódulos -->
            <li class="nav-item">
                <a class="nav-link text-white px-3" href="<?php echo htmlspecialchars(isset($modulo['url']) ? $modulo['url'] : '#'); ?>">
                    <i class="<?php echo htmlspecialchars(isset($modulo['icono']) ? $modulo['icono'] : 'fas fa-circle'); ?> me-1"></i>
                    <?php echo htmlspecialchars(isset($modulo['nombre']) ? $modulo['nombre'] : 'Sin nombre'); ?>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<!-- Menú Móvil/Tablet con Acordeón -->
<div class="d-lg-none">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileMenuLabel">
                <i class="fas fa-bars me-2"></i>
                Menú
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="accordion accordion-flush" id="mobileMenuAccordion">
                <?php foreach ($menuModulos as $index => $modulo): ?>
                    <?php if (!empty($modulo['submodulos']) && is_array($modulo['submodulos'])): ?>
                        <!-- Módulo con submódulos - Acordeón -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                    <i class="<?php echo htmlspecialchars(isset($modulo['icono']) ? $modulo['icono'] : 'fas fa-circle'); ?> me-2"></i>
                                    <?php echo htmlspecialchars(isset($modulo['nombre']) ? $modulo['nombre'] : 'Sin nombre'); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#mobileMenuAccordion">
                                <div class="accordion-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($modulo['submodulos'] as $submodulo): ?>
                                            <a href="<?php echo htmlspecialchars(isset($submodulo['url']) ? $submodulo['url'] : '#'); ?>" 
                                               class="list-group-item list-group-item-action border-0 ps-4">
                                                <i class="fas fa-angle-right me-2 text-muted"></i>
                                                <?php echo htmlspecialchars(isset($submodulo['nombre']) ? $submodulo['nombre'] : 'Sin nombre'); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Módulo sin submódulos - Enlace directo -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <a href="<?php echo htmlspecialchars(isset($modulo['url']) ? $modulo['url'] : '#'); ?>" 
                                   class="accordion-button text-decoration-none collapsed" style="pointer-events: auto;">
                                    <i class="<?php echo htmlspecialchars(isset($modulo['icono']) ? $modulo['icono'] : 'fas fa-circle'); ?> me-2"></i>
                                    <?php echo htmlspecialchars(isset($modulo['nombre']) ? $modulo['nombre'] : 'Sin nombre'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar otros dropdowns cuando se abre uno nuevo (Desktop)
    const desktopMenu = document.getElementById('desktopMenu');
    if (desktopMenu) {
        desktopMenu.addEventListener('show.bs.dropdown', function(e) {
            // Cerrar todos los otros dropdowns abiertos
            const openDropdowns = desktopMenu.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                if (dropdown !== e.target.nextElementSibling) {
                    const toggleButton = dropdown.previousElementSibling;
                    if (toggleButton) {
                        bootstrap.Dropdown.getInstance(toggleButton)?.hide();
                    }
                }
            });
        });
    }
});
</script>
