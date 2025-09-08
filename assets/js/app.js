/**
 * JavaScript principal del Sistema Premoldeado
 * Manejo de navegación responsiva y componentes interactivos
 */

// ===========================
// MENÚ RESPONSIVO
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initDropdownMenus();
    initUtilities();
});

/**
 * Inicializar menú móvil
 */
function initMobileMenu() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    console.log('Inicializando menú móvil...', {navToggle, navMenu}); // Debug
    
    if (navToggle && navMenu) {
        // Toggle del menú
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Toggle clicked'); // Debug
            
            const isActive = navMenu.classList.contains('active');
            
            navToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            console.log('Menu activo:', !isActive); // Debug
            
            // Prevenir scroll del body cuando el menú está abierto
            if (!isActive) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    } else {
        console.error('No se encontraron elementos navToggle o navMenu'); // Debug
    }
    
    // Botón de cerrar del menú móvil
    const navClose = document.getElementById('navClose');
    if (navClose) {
        navClose.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Close button clicked'); // Debug
            closeMenu();
        });
    }
    
    // Cerrar menú al hacer clic en el área vacía del menú (no en los enlaces)
    navMenu?.addEventListener('click', function(e) {
        // Solo cerrar si se hace clic en el fondo del menú, no en enlaces o dropdowns
        if (e.target === navMenu || e.target.classList.contains('nav-menu-container')) {
            closeMenu();
        }
    });
    
    // Cerrar menú al hacer clic en un enlace directo
    document.addEventListener('click', function(e) {
        if (e.target.matches('.nav-sublink')) {
            closeMenu();
        }
    });
    
    // Cerrar menú con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMenu();
        }
    });
    
    // Cerrar menú al cambiar tamaño de ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            closeMenu();
        }
    });
}

/**
 * Inicializar dropdowns del menú
 */
function initDropdownMenus() {
    const dropdownToggles = document.querySelectorAll('.nav-link.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = this.closest('.nav-dropdown');
            const isActive = dropdown.classList.contains('active');
            
            // Cerrar otros dropdowns
            document.querySelectorAll('.nav-dropdown.active').forEach(activeDropdown => {
                if (activeDropdown !== dropdown) {
                    activeDropdown.classList.remove('active');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('active', !isActive);
        });
    });
}

/**
 * Cerrar menú móvil
 */
function closeMenu() {
    const navMenu = document.getElementById('navMenu');
    const navToggle = document.getElementById('navToggle');
    
    console.log('Cerrando menú'); // Debug
    
    navMenu?.classList.remove('active');
    navToggle?.classList.remove('active');
    document.body.style.overflow = '';
    
    // Cerrar todos los dropdowns
    document.querySelectorAll('.nav-dropdown.active').forEach(dropdown => {
        dropdown.classList.remove('active');
    });
}

/**
 * Inicializar utilidades
 */
function initUtilities() {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-dismiss de alertas de Bootstrap después de 5 segundos
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Limpiar validaciones al escribir
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('is-invalid')) {
            e.target.classList.remove('is-invalid');
        }
    });
}

// ===========================
// UTILIDADES GLOBALES
// ===========================

/**
 * Mostrar mensaje con SweetAlert2
 */
function showAlert(title, text, icon = 'info') {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonColor: '#3498db'
    });
}

/**
 * Confirmar acción con SweetAlert2
 */
function confirmAction(title, text, callback) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#95a5a6',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}
