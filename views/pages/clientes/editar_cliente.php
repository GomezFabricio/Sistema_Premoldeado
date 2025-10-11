<?php
// Las variables vienen del controlador: $cliente, $pageTitle, $usuario

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

// Validar que tenemos los datos del cliente
if (!isset($cliente) || empty($cliente)) {
    echo '<div class="alert alert-danger">Error: No se encontraron los datos del cliente.</div>';
    return;
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-user-edit text-primary me-2"></i><?= htmlspecialchars($pageTitle ?? 'Editar Cliente') ?>
            </h1>
            <a href="/Sistema_Premoldeado/controllers/ClienteController.php?action=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Información del Cliente #<?= htmlspecialchars($cliente['cliente_id'] ?? $cliente['id']) ?>
                    <small class="ms-3 opacity-75">
                        <?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?>
                    </small>
                </h5>
            </div>
            <div class="card-body">
                <form action="/Sistema_Premoldeado/controllers/ClienteController.php?action=update&id=<?= urlencode($cliente['cliente_id'] ?? $cliente['id']) ?>" method="POST" id="editarClienteForm">
                    
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
                                   value="<?= htmlspecialchars($cliente['nombres'] ?? '') ?>" 
                                   placeholder="Ej: Juan Carlos" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="apellidos" class="form-label fw-bold">
                                <i class="fas fa-user text-primary me-1"></i>Apellidos *
                            </label>
                            <input type="text" class="form-control" name="apellidos" id="apellidos" 
                                   value="<?= htmlspecialchars($cliente['apellidos'] ?? '') ?>" 
                                   placeholder="Ej: González Pérez" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_documento" class="form-label fw-bold">
                                <i class="fas fa-id-badge text-info me-1"></i>Tipo de Documento
                            </label>
                            <select class="form-select" name="tipo_documento" id="tipo_documento" required>
                                <option value="DNI" <?= ($cliente['tipo_documento'] ?? 'DNI') === 'DNI' ? 'selected' : '' ?>>DNI - Documento Nacional de Identidad</option>
                                <option value="CUIL" <?= ($cliente['tipo_documento'] ?? '') === 'CUIL' ? 'selected' : '' ?>>CUIL - Código Único de Identificación Laboral</option>
                                <option value="CUIT" <?= ($cliente['tipo_documento'] ?? '') === 'CUIT' ? 'selected' : '' ?>>CUIT - Código Único de Identificación Tributaria</option>
                                <option value="PASAPORTE" <?= ($cliente['tipo_documento'] ?? '') === 'PASAPORTE' ? 'selected' : '' ?>>Pasaporte</option>
                                <option value="CEDULA" <?= ($cliente['tipo_documento'] ?? '') === 'CEDULA' ? 'selected' : '' ?>>Cédula de Identidad</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="numero_documento" class="form-label fw-bold">
                                <i class="fas fa-id-card-alt text-info me-1"></i>Número de Documento *
                            </label>
                            <input type="text" class="form-control" name="numero_documento" id="numero_documento" 
                                   value="<?= htmlspecialchars($cliente['numero_documento'] ?? '') ?>" 
                                   placeholder="Ej: 12345678" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="razon_social" class="form-label fw-bold">
                                <i class="fas fa-building text-secondary me-1"></i>Razón Social (Opcional)
                            </label>
                            <input type="text" class="form-control" name="razon_social" id="razon_social" 
                                   value="<?= htmlspecialchars($cliente['razon_social'] ?? '') ?>" 
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
                                   value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" 
                                   placeholder="cliente@email.com">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-bold">
                                <i class="fas fa-phone text-success me-1"></i>Teléfono *
                            </label>
                            <input type="tel" class="form-control" name="telefono" id="telefono" 
                                   value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>" 
                                   placeholder="Ej: 3001234567" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="direccion" class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>Dirección
                            </label>
                            <input type="text" class="form-control" name="direccion" id="direccion" 
                                   value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>" 
                                   placeholder="Ej: Calle 123 #45-67">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="localidad" class="form-label fw-bold">
                                <i class="fas fa-city text-info me-1"></i>Localidad
                            </label>
                            <input type="text" class="form-control" name="localidad" id="localidad" 
                                   value="<?= htmlspecialchars($cliente['localidad'] ?? '') ?>" 
                                   placeholder="Ej: Bogotá">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="provincia" class="form-label fw-bold">
                                <i class="fas fa-map text-primary me-1"></i>Provincia
                            </label>
                            <input type="text" class="form-control" name="provincia" id="provincia" 
                                   value="<?= htmlspecialchars($cliente['provincia'] ?? '') ?>" 
                                   placeholder="Ej: Cundinamarca">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="codigo_postal" class="form-label fw-bold">
                                <i class="fas fa-mail-bulk text-secondary me-1"></i>Código Postal
                            </label>
                            <input type="text" class="form-control" name="codigo_postal" id="codigo_postal" 
                                   value="<?= htmlspecialchars($cliente['codigo_postal'] ?? '') ?>" 
                                   placeholder="Ej: 110111">
                        </div>
                    </div>
                    
                    <!-- Información Comercial -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-handshake me-2"></i>Información Comercial
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_cliente" class="form-label fw-bold">
                                <i class="fas fa-user-tag text-purple me-1"></i>Tipo de Cliente
                            </label>
                            <select class="form-select" name="tipo_cliente" id="tipo_cliente">
                                <option value="MINORISTA" <?= ($cliente['tipo_cliente'] ?? 'MINORISTA') === 'MINORISTA' ? 'selected' : '' ?>>Minorista</option>
                                <option value="MAYORISTA" <?= ($cliente['tipo_cliente'] ?? '') === 'MAYORISTA' ? 'selected' : '' ?>>Mayorista</option>
                                <option value="EMPRESARIAL" <?= ($cliente['tipo_cliente'] ?? '') === 'EMPRESARIAL' ? 'selected' : '' ?>>Empresarial</option>
                                <option value="CONSTRUCTOR" <?= ($cliente['tipo_cliente'] ?? '') === 'CONSTRUCTOR' ? 'selected' : '' ?>>Constructor</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="condicion_iva" class="form-label fw-bold">
                                <i class="fas fa-receipt text-success me-1"></i>Condición IVA
                            </label>
                            <select class="form-select" name="condicion_iva" id="condicion_iva">
                                <option value="CONSUMIDOR_FINAL" <?= ($cliente['condicion_iva'] ?? 'CONSUMIDOR_FINAL') === 'CONSUMIDOR_FINAL' ? 'selected' : '' ?>>Consumidor Final</option>
                                <option value="RESPONSABLE_INSCRIPTO" <?= ($cliente['condicion_iva'] ?? '') === 'RESPONSABLE_INSCRIPTO' ? 'selected' : '' ?>>Responsable Inscripto</option>
                                <option value="MONOTRIBUTISTA" <?= ($cliente['condicion_iva'] ?? '') === 'MONOTRIBUTISTA' ? 'selected' : '' ?>>Monotributista</option>
                                <option value="EXENTO" <?= ($cliente['condicion_iva'] ?? '') === 'EXENTO' ? 'selected' : '' ?>>Exento</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="limite_credito" class="form-label fw-bold">
                                <i class="fas fa-dollar-sign text-warning me-1"></i>Límite de Crédito
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="limite_credito" id="limite_credito" 
                                       min="0" step="0.01" value="<?= htmlspecialchars($cliente['limite_credito'] ?? '0') ?>" 
                                       placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="descuento_general" class="form-label fw-bold">
                                <i class="fas fa-percentage text-info me-1"></i>Descuento General
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="descuento_general" id="descuento_general" 
                                       min="0" max="100" step="0.01" value="<?= htmlspecialchars($cliente['descuento_general'] ?? '0') ?>" 
                                       placeholder="0.00">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="dias_credito" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>Días de Crédito
                            </label>
                            <input type="number" class="form-control" name="dias_credito" id="dias_credito" 
                                   min="0" max="365" value="<?= htmlspecialchars($cliente['dias_credito'] ?? '0') ?>" 
                                   placeholder="0">
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
                                      placeholder="Ingrese cualquier información adicional relevante sobre el cliente..."><?= htmlspecialchars($cliente['observaciones'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Estado del Cliente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-toggle-on me-2"></i>Estado del Cliente
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="cliente_activo" id="cliente_activo" 
                                       value="1" <?= ($cliente['cliente_activo'] ?? $cliente['activo'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="cliente_activo">
                                    <i class="fas fa-user-check text-success me-1"></i>Cliente Activo
                                </label>
                                <div class="form-text">Desactivar el cliente impedirá crear nuevos pedidos</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del Sistema -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-light border">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle text-info me-2"></i>Información del Sistema
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>ID del Cliente:</strong> <?= htmlspecialchars($cliente['cliente_id'] ?? $cliente['id']) ?><br>
                                            <strong>Fecha de Alta:</strong> <?= htmlspecialchars($cliente['fecha_alta'] ?? 'No especificada') ?>
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Última Modificación:</strong> <?= htmlspecialchars($cliente['fecha_modificacion'] ?? 'No disponible') ?><br>
                                            <strong>Estado:</strong> 
                                            <?php 
                                            $activo = $cliente['cliente_activo'] ?? $cliente['activo'] ?? true;
                                            echo $activo ? '<span class="text-success">Activo</span>' : '<span class="text-danger">Inactivo</span>';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-flex gap-3 pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Actualizar Cliente
                        </button>
                        <a href="/Sistema_Premoldeado/controllers/ClienteController.php?action=index" 
                           class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar Cambios
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-lg ms-auto" 
                                onclick="eliminarCliente(<?= $cliente['cliente_id'] ?? $cliente['id'] ?>)">
                            <i class="fas fa-trash me-2"></i>Eliminar Cliente
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validación del formulario en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editarClienteForm');
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

// Función para eliminar cliente
function eliminarCliente(id) {
    if (confirm('¿Estás seguro de que deseas ELIMINAR este cliente?\n\nATENCIÓN: Esta acción no se puede deshacer y se eliminarán todos los datos relacionados.\n\nSi solo quieres desactivar el cliente, desmarca la casilla "Cliente Activo" y guarda los cambios.')) {
        // Crear formulario para enviar petición DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/Sistema_Premoldeado/controllers/ClienteController.php?action=delete&id=' + id;
        
        // Añadir campo oculto para confirmar la eliminación
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'confirmar_eliminacion';
        input.value = '1';
        form.appendChild(input);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>