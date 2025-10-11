<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Listado') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../components/common-styles.php';
    ?>
    
    <!-- Estilos específicos de esta página (solo si son necesarios) -->
    <style>
        /* Aquí van solo estilos específicos que no están en common-styles */
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <?php
        // Mostrar mensajes flash si existen
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                        ($flash['type'] === 'error' ? 'alert-danger' : 'alert-info');
            echo '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <!-- Header de la página -->
        <div class="page-header text-center">
            <h1 class="mb-3">
                <i class="fas fa-list me-2"></i>
                <?= htmlspecialchars($titulo ?? 'Listado') ?>
            </h1>
            <p class="mb-0 opacity-75">Descripción de la funcionalidad</p>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="#" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Nuevo Elemento
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/Sistema_Premoldeado/dashboard.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
                
                <!-- Tarjeta de la tabla -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2"></i>
                            Lista de Elementos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <!-- 
                                INSTRUCCIONES DE USO:
                                1. Cambiar 'elementos_table' por '[modulo]_table' (ej: clientes_table, productos_table)
                                2. Cambiar 'data-table-type' por el módulo correspondiente:
                                   - usuarios, perfiles, clientes, productos, materiales, pedidos, ventas, proveedores, produccion
                                   - O usar: simple (3-4 cols), extended (7+ cols), common (genérico)
                                3. Ajustar el colspan en el mensaje "No hay elementos" según número de columnas
                                4. Personalizar las columnas del thead según tus necesidades
                                5. Ajustar el loop del tbody con los datos reales
                            -->
                            <table class="table table-hover" id="elementos_table" data-table-type="simple">
                                <thead>
                                    <tr>
                                        <!-- PERSONALIZAR ESTAS COLUMNAS SEGÚN TU MÓDULO -->
                                        <th class="text-center">ID</th>
                                        <th>Nombre</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Acciones</th>
                                        <!-- FIN PERSONALIZACIÓN -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($elementos)): ?>
                                        <tr>
                                            <!-- AJUSTAR COLSPAN SEGÚN NÚMERO DE COLUMNAS -->
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                No hay elementos registrados
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($elementos as $elemento): ?>
                                            <tr>
                                                <!-- PERSONALIZAR ESTAS CELDAS SEGÚN TUS COLUMNAS -->
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?= htmlspecialchars($elemento['id']) ?></span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($elemento['nombre']) ?></strong>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (($elemento['estado'] ?? $elemento['activo'] ?? 1) == 1): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Activo
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i>Inactivo
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= htmlspecialchars($elemento['fecha'] ?? $elemento['fecha_creacion'] ?? '-') ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <!-- PERSONALIZAR ESTOS ENLACES SEGÚN TU CONTROLADOR -->
                                                        <a href="/Sistema_Premoldeado/controllers/TuController.php?a=editar&id=<?= $elemento['id'] ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="/Sistema_Premoldeado/controllers/TuController.php?a=eliminar&id=<?= $elemento['id'] ?>" 
                                                           class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                           onclick="return confirm('¿Estás seguro de eliminar este elemento?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <!-- FIN PERSONALIZACIÓN -->
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script personalizado para esta página (opcional) -->
    <script>
    $(document).ready(function() {
        // La inicialización de DataTable se hace automáticamente por el data-table-type
        // Los tooltips se inicializan automáticamente por common-styles.php
        
        // Aquí puedes agregar JavaScript específico para esta página
        console.log('✅ Página de elementos cargada correctamente');
        
        // Ejemplo: Manejar eventos específicos
        $('#elementos_table tbody').on('click', 'tr', function() {
            // Hacer algo cuando se hace clic en una fila
            console.log('Fila seleccionada:', this);
        });
    });
    </script>
</body>
</html>

<!--
============================================================================
                        GUÍA DE USO DEL TEMPLATE
============================================================================

PASOS PARA USAR ESTE TEMPLATE:

1. COPIAR EL ARCHIVO
   - Copia este archivo a tu carpeta de vistas
   - Renómbralo según tu módulo (ej: listado_clientes.php)

2. PERSONALIZAR VARIABLES
   - Cambia $elementos por el nombre de tu array de datos
   - Ajusta $titulo según tu módulo

3. CONFIGURAR LA TABLA
   - ID de tabla: Cambia 'elementos_table' por '[modulo]_table'
   - data-table-type: Elige el tipo apropiado:
     * Específicos: usuarios, perfiles, clientes, productos, materiales, pedidos, ventas, proveedores, produccion
     * Genéricos: simple (3-4 cols), extended (7+ cols), common (básico)

4. PERSONALIZAR COLUMNAS
   - Ajusta las columnas del <thead> según tus necesidades
   - Modifica las celdas del <tbody> con tus datos reales
   - Actualiza el colspan en el mensaje "No hay elementos"

5. CONFIGURAR ACCIONES
   - Cambia los enlaces de los botones por tus controladores reales
   - Ajusta los parámetros según tu lógica de negocio

EJEMPLO PARA MÓDULO CLIENTES:
-----------------------------
- Archivo: listado_clientes.php
- ID tabla: clientes_table  
- data-table-type: clientes
- Variable: $clientes
- Controlador: ClienteController.php

TIPOS DE DATA-TABLE DISPONIBLES:
--------------------------------
- usuarios: Para listado de usuarios (6 columnas típicas)
- perfiles: Para listado de perfiles (5 columnas)
- clientes: Para listado de clientes (6 columnas)
- productos: Para listado de productos (6 columnas)
- materiales: Para listado de materiales (6 columnas)
- pedidos: Para listado de pedidos (7 columnas)
- ventas: Para listado de ventas (7 columnas)
- proveedores: Para listado de proveedores (6 columnas)
- produccion: Para listado de producción (7 columnas)
- simple: Para tablas simples (3-4 columnas)
- extended: Para tablas extensas (7+ columnas)
- common: Configuración genérica básica

El sistema es completamente automático - solo necesitas agregar el data-table-type correcto.

============================================================================
-->