<?php
/**
 * Common Styles Component - Sistema Premoldeado
 * Incluye CSS y JavaScript comunes para todas las páginas del sistema
 * 
 * Uso: include_once __DIR__ . '/common-styles.php';
 */

// Función para obtener la URL base del proyecto
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    
    // Limpiar el path para obtener la ruta base del proyecto
    $pathParts = explode('/', trim($path, '/'));
    $basePath = '';
    
    // Buscar 'Sistema_Premoldeado' en el path
    foreach ($pathParts as $part) {
        if (strpos($part, 'Sistema_Premoldeado') !== false) {
            $basePath = '/' . $part;
            break;
        }
    }
    
    return $protocol . $host . $basePath;
}

$baseUrl = getBaseUrl();
?>

<!-- ===== CSS EXTERNOS ===== -->
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- DataTables Bootstrap 5 -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<!-- Google Fonts - Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- ===== CSS PERSONALIZADOS ===== -->
<!-- Estilos comunes del sistema -->
<link href="<?= $baseUrl ?>/assets/css/datatables-custom.css" rel="stylesheet">

<!-- ===== SCRIPTS EXTERNOS ===== -->
<!-- jQuery (Debe cargarse antes que Bootstrap y DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- ===== SCRIPTS PERSONALIZADOS ===== -->
<!-- Inicialización común de DataTables -->
<script src="<?= $baseUrl ?>/assets/js/datatables-init.js"></script>

<!-- ===== CONFIGURACIÓN GLOBAL ===== -->
<script>
// Variables globales del sistema
window.SISTEMA_CONFIG = {
    baseUrl: '<?= $baseUrl ?>',
    appName: 'Sistema Premoldeado',
    version: '1.0.0',
    debug: <?= (defined('DEBUG_MODE') && DEBUG_MODE) ? 'true' : 'false' ?>
};

// Configuración de tooltips globales
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips en elementos que se agreguen dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
                // Reinicializar tooltips para nuevos elementos
                DataTableManager.initTooltips();
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
</script>

<!-- ===== META TAGS ADICIONALES ===== -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Sistema de Gestión de Premoldeados">
<meta name="author" content="Sistema Premoldeado">
<meta name="robots" content="noindex, nofollow">

<style>
/* ===== ESTILOS ADICIONALES INLINE (solo si es necesario) ===== */
:root {
    --primary-color: #007bff;
    --secondary-color: #6f42c1;
    --success-color: #198754;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #0dcaf0;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    
    --border-radius: 10px;
    --box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    --transition: all 0.2s ease;
}

/* Prevenir FOUC (Flash of Unstyled Content) */
body {
    visibility: visible !important;
}

/* Loader básico para cargas */
.sistema-loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
}

.sistema-loader.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.sistema-loader .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Clases de utilidad adicionales */
.fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Accesibilidad */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Focus visible para mejor accesibilidad */
.btn:focus-visible,
.form-control:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>

<!-- ===== LOADER HTML ===== -->
<div class="sistema-loader" id="sistemaLoader">
    <div class="spinner"></div>
</div>

<!-- ===== FUNCIONES JAVASCRIPT GLOBALES ===== -->
<script>
// Funciones de utilidad globales
window.SistemaUtils = {
    
    // Mostrar loader
    showLoader: function() {
        document.getElementById('sistemaLoader').classList.add('show');
    },
    
    // Ocultar loader
    hideLoader: function() {
        document.getElementById('sistemaLoader').classList.remove('show');
    },
    
    // Scroll suave a elemento
    scrollTo: function(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    },
    
    // Copiar texto al portapapeles
    copyToClipboard: function(text) {
        navigator.clipboard.writeText(text).then(function() {
            console.log('Texto copiado: ' + text);
        });
    },
    
    // Formatear números
    formatNumber: function(number, decimals = 0) {
        return new Intl.NumberFormat('es-ES', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    },
    
    // Formatear moneda
    formatCurrency: function(amount, currency = 'USD') {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },
    
    // Validar email
    isValidEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Generar ID único
    generateId: function() {
        return 'id_' + Math.random().toString(36).substr(2, 9);
    }
};

// Debug helper (solo en modo desarrollo)
if (window.SISTEMA_CONFIG.debug) {
    console.log('🚀 Sistema Premoldeado - Modo Debug Activado');
    console.log('📍 Base URL:', window.SISTEMA_CONFIG.baseUrl);
    console.log('📋 Configuración:', window.SISTEMA_CONFIG);
}
</script>