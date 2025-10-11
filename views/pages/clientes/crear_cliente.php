<?php
// Las variables vienen del controlador: $pageTitle, $usuario

// Incluir header del layout
include_once __DIR__ . '/../../layouts/header.php';

// Mostrar mensajes flash si existen
if (isset($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                ($flash['type'] === 'error' ? 'alert-danger' : 'alert-info');
    echo '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($flash['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['flash_message']);
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-user-plus text-success me-2"></i><?= htmlspecialchars($pageTitle ?? 'Crear Nuevo Cliente') ?>
            </h1>
            <a href="/Sistema_Premoldeado/controllers/ClienteController.php?action=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-friends me-2"></i>Información del Nuevo Cliente
                </h5>
            </div>
            <div class="card-body">
                <form action="/Sistema_Premoldeado/controllers/ClienteController.php?action=store" method="POST" id="crearClienteForm">
                    
                    <!-- Información Personal -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-id-card me-2"></i>Información Personal
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nombres" class="form-label fw-bold">
                                <i class="fas fa-user text-primary me-1"></i>Nombres *
                            </label>
                            <input type="text" class="form-control" name="nombres" id="nombres" 
                                   placeholder="Ej: Juan Carlos" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="apellidos" class="form-label fw-bold">
                                <i class="fas fa-user text-primary me-1"></i>Apellidos *
                            </label>
                            <input type="text" class="form-control" name="apellidos" id="apellidos" 
                                   placeholder="Ej: González Pérez" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_documento" class="form-label fw-bold">
                                <i class="fas fa-id-badge text-info me-1"></i>Tipo de Documento
                            </label>
                            <select class="form-select" name="tipo_documento" id="tipo_documento" required>
                                <option value="DNI" selected>DNI - Documento Nacional de Identidad</option>
                                <option value="CUIL">CUIL - Código Único de Identificación Laboral</option>
                                <option value="CUIT">CUIT - Código Único de Identificación Tributaria</option>
                                <option value="PASAPORTE">Pasaporte</option>
                                <option value="CEDULA">Cédula de Identidad</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="numero_documento" class="form-label fw-bold">
                                <i class="fas fa-id-card-alt text-info me-1"></i>Número de Documento *
                            </label>
                            <input type="text" class="form-control" name="numero_documento" id="numero_documento" 
                                   placeholder="Ej: 12345678" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="razon_social" class="form-label fw-bold">
                                <i class="fas fa-building text-secondary me-1"></i>Razón Social (Opcional)
                            </label>
                            <input type="text" class="form-control" name="razon_social" id="razon_social" 
                                   placeholder="Solo para personas jurídicas">
                        </div>
                    </div>
                    
                    <!-- Información de Contacto -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-address-book me-2"></i>Información de Contacto
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope text-warning me-1"></i>Email
                            </label>
                            <input type="email" class="form-control" name="email" id="email" 
                                   placeholder="cliente@email.com">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-bold">
                                <i class="fas fa-phone text-success me-1"></i>Teléfono *
                            </label>
                            <input type="tel" class="form-control" name="telefono" id="telefono" 
                                   placeholder="Ej: 3001234567" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="direccion" class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>Dirección
                            </label>
                            <input type="text" class="form-control" name="direccion" id="direccion" 
                                   placeholder="Ej: Calle 123 #45-67">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="localidad" class="form-label fw-bold">
                                <i class="fas fa-city text-info me-1"></i>Localidad
                            </label>
                            <input type="text" class="form-control" name="localidad" id="localidad" 
                                   placeholder="Ej: Bogotá">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="provincia" class="form-label fw-bold">
                                <i class="fas fa-map text-primary me-1"></i>Provincia
                            </label>
                            <input type="text" class="form-control" name="provincia" id="provincia" 
                                   placeholder="Ej: Cundinamarca">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="codigo_postal" class="form-label fw-bold">
                                <i class="fas fa-mail-bulk text-secondary me-1"></i>Código Postal
                            </label>
                            <input type="text" class="form-control" name="codigo_postal" id="codigo_postal" 
                                   placeholder="Ej: 110111">
                        </div>
                    </div>
                    
                    <!-- Información Comercial -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-handshake me-2"></i>Información Comercial (Opcional)
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_cliente" class="form-label fw-bold">
                                <i class="fas fa-user-tag text-purple me-1"></i>Tipo de Cliente
                            </label>
                            <select class="form-select" name="tipo_cliente" id="tipo_cliente">
                                <option value="MINORISTA" selected>Minorista</option>
                                <option value="MAYORISTA">Mayorista</option>
                                <option value="EMPRESARIAL">Empresarial</option>
                                <option value="CONSTRUCTOR">Constructor</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="condicion_iva" class="form-label fw-bold">
                                <i class="fas fa-receipt text-success me-1"></i>Condición IVA
                            </label>
                            <select class="form-select" name="condicion_iva" id="condicion_iva">
                                <option value="CONSUMIDOR_FINAL" selected>Consumidor Final</option>
                                <option value="RESPONSABLE_INSCRIPTO">Responsable Inscripto</option>
                                <option value="MONOTRIBUTISTA">Monotributista</option>
                                <option value="EXENTO">Exento</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="limite_credito" class="form-label fw-bold">
                                <i class="fas fa-dollar-sign text-warning me-1"></i>Límite de Crédito
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="limite_credito" id="limite_credito" 
                                       min="0" step="0.01" value="0" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="descuento_general" class="form-label fw-bold">
                                <i class="fas fa-percentage text-info me-1"></i>Descuento General
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="descuento_general" id="descuento_general" 
                                       min="0" max="100" step="0.01" value="0" placeholder="0.00">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="dias_credito" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>Días de Crédito
                            </label>
                            <input type="number" class="form-control" name="dias_credito" id="dias_credito" 
                                   min="0" max="365" value="0" placeholder="0">
                        </div>
                    </div>
                    
                    <!-- Observaciones -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Observaciones Adicionales
                            </h6>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="observaciones" class="form-label fw-bold">
                                <i class="fas fa-comment text-secondary me-1"></i>Notas y Comentarios
                            </label>
                            <textarea class="form-control" name="observaciones" id="observaciones" rows="4" 
                                      placeholder="Ingrese cualquier información adicional relevante sobre el cliente..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-flex gap-3 pt-3 border-top">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Crear Cliente
                        </button>
                        <a href="/Sistema_Premoldeado/controllers/ClienteController.php?action=index" 
                           class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validación del formulario en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('crearClienteForm');
    const numeroDocumento = document.getElementById('numero_documento');
    const telefono = document.getElementById('telefono');
    const email = document.getElementById('email');
    
    // Validar número de documento
    numeroDocumento.addEventListener('input', function() {
        const valor = this.value.replace(/\D/g, ''); // Solo números
        this.value = valor;
        
        if (valor.length < 7 || valor.length > 11) {
            this.setCustomValidity('El documento debe tener entre 7 y 11 dígitos');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validar teléfono
    telefono.addEventListener('input', function() {
        const valor = this.value.replace(/\D/g, ''); // Solo números
        this.value = valor;
        
        if (valor.length < 7) {
            this.setCustomValidity('El teléfono debe tener al menos 7 dígitos');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validar email si se ingresa
    email.addEventListener('input', function() {
        if (this.value && !this.validity.valid) {
            this.setCustomValidity('Por favor ingrese un email válido');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validación al enviar
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Mostrar primer campo con error
            const invalidField = form.querySelector(':invalid');
            if (invalidField) {
                invalidField.focus();
                invalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>