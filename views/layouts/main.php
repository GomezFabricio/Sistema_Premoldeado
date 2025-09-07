<?php
/**
 * Layout principal del sistema
 * Combina header y footer para crear una plantilla completa
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado (excepto para páginas públicas)
$publicPages = ['login', 'logout'];
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

if (!in_array($currentPage, $publicPages) && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

// Incluir header
include 'header.php';
?>

<!-- Breadcrumb (si está definido) -->
<?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo BASE_URL; ?>dashboard">
                <i class="fas fa-home"></i> Inicio
            </a>
        </li>
        <?php foreach ($breadcrumb as $item): ?>
            <?php if (isset($item['url'])): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
                </li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo $item['title']; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>

<!-- Título de la página -->
<?php if (isset($pageTitle)): ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <?php if (isset($pageIcon)): ?>
            <i class="<?php echo $pageIcon; ?> me-2"></i>
        <?php endif; ?>
        <?php echo $pageTitle; ?>
    </h1>
    
    <!-- Botones de acción (si están definidos) -->
    <?php if (isset($pageButtons)): ?>
        <div class="btn-group" role="group">
            <?php echo $pageButtons; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Contenido de la página -->
<div class="row">
    <div class="col-12">
        <?php
        // Aquí se incluirá el contenido específico de cada página
        // El contenido se define en cada vista individual
        ?>
    </div>
</div>

<?php
// Incluir footer
include 'footer.php';
?>
