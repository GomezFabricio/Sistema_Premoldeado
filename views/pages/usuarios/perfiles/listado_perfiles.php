<?php
/**
 * Vista de Listado de Perfiles
 * Submódulo del módulo Usuarios
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar dependencias
require_once __DIR__ . '/../../../../controllers/AuthController.php';
require_once __DIR__ . '/../../../../models/Usuario.php';

try {
    // Verificar acceso al módulo de usuarios (módulo ID 1)
    $auth = new AuthController();
    if (!$auth->verificarAccesoModulo(1)) {
        header('Location: http://localhost/Sistema_Premoldeado/views/pages/dashboard.php');
        $_SESSION['flash_message'] = [
            'message' => 'No tienes permisos para acceder a este módulo',
            'type' => 'error'
        ];
        exit;
    }
    
    // Obtener perfiles del modelo
    $perfiles = Usuario::obtenerTodosPerfiles();
    
    // Preparar datos para la vista
    $pageTitle = 'Gestión de Perfiles';
    $usuario = $auth->getUsuarioLogueado();
    ?>
    
    <!-- Contenido de Gestión de Perfiles -->
    <div class="row">
        <div class="col-12">
            <!-- Título de la página -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-user-shield me-2"></i>
                    Gestión de Perfiles
                </h1>
                <a href="/Sistema_Premoldeado/controllers/UsuarioController.php?action=createPerfiles" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Perfil
                </a>
            </div>
            
            <?php
            // Configuración para el componente table
            $data = $perfiles;
            $config = [
                'title' => 'Perfiles Registrados',
                'id' => 'perfiles_table',
                'columns' => [
                    'id' => [
                        'label' => 'ID',
                        'type' => 'badge',
                        'width' => '80px',
                        'class' => 'text-center'
                    ],
                    'nombre' => [
                        'label' => 'Nombre',
                        'type' => 'text',
                        'formatter' => function($value, $row) {
                            return '<strong>' . htmlspecialchars($value) . '</strong>';
                        }
                    ],
                    'descripcion' => [
                        'label' => 'Descripción',
                        'type' => 'text',
                        'formatter' => function($value, $row) {
                            return htmlspecialchars($value ?: 'Sin descripción');
                        }
                    ],
                    'total_modulos' => [
                        'label' => 'Módulos',
                        'type' => 'custom',
                        'width' => '120px',
                        'class' => 'text-center',
                        'formatter' => function($value, $row) {
                            return '<span class="badge bg-info">' . intval($value ?: 0) . ' módulos</span>';
                        }
                    ],
                    'total_usuarios' => [
                        'label' => 'Usuarios',
                        'type' => 'custom',
                        'width' => '120px',
                        'class' => 'text-center',
                        'formatter' => function($value, $row) {
                            return '<span class="badge bg-secondary">' . intval($value ?: 0) . ' usuarios</span>';
                        }
                    ],
                    'activo' => [
                        'label' => 'Estado',
                        'type' => 'status',
                        'width' => '100px',
                        'class' => 'text-center'
                    ],
                    'fecha_creacion' => [
                        'label' => 'Fecha Creación',
                        'type' => 'date',
                        'width' => '140px',
                        'class' => 'text-center'
                    ]
                ],
                'actions' => [
                    'edit' => true,
                    'delete' => true
                ],
                'datatables' => [
                    'pageLength' => 25,
                    'ordering' => true,
                    'searching' => true,
                    'paging' => true,
                    'info' => true,
                    'responsive' => true,
                    'language' => 'es'
                ],
                'empty' => [
                    'message' => 'No hay perfiles registrados',
                    'icon' => 'fas fa-user-shield',
                    'subtext' => 'Comience creando el primer perfil del sistema.',
                    'action' => '<a href="/Sistema_Premoldeado/controllers/UsuarioController.php?action=createPerfiles" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Crear Primer Perfil</a>'
                ]
            ];
            
            // Incluir el componente de tabla
            include __DIR__ . '/../../../components/table.php';
            ?>
        </div>
    </div>

    <script>
    // Sobrescribir las funciones globales del componente table
    function editRecord(id) {
        window.location.href = '/Sistema_Premoldeado/controllers/UsuarioController.php?action=editPerfiles&id=' + id;
    }
    
    function deleteRecord(id) {
        // Obtener el nombre del perfil para la confirmación
        const row = $('#perfiles_table').DataTable().row($('button[data-id="' + id + '"].btn-delete').closest('tr')).data();
        const nombre = $('button[data-id="' + id + '"].btn-delete').closest('tr').find('td:eq(1)').text().trim();
        
        if (confirm('¿Estás seguro de que deseas eliminar el perfil "' + nombre + '"?')) {
            // Crear formulario para enviar la eliminación
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/Sistema_Premoldeado/controllers/UsuarioController.php?action=deletePerfiles&id=' + id;
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>

    <?php
    
} catch (Exception $e) {
    error_log("Error en listado de perfiles: " . $e->getMessage());
    header('Location: http://localhost/Sistema_Premoldeado/views/pages/dashboard.php');
    $_SESSION['flash_message'] = [
        'message' => 'Error interno del servidor',
        'type' => 'error'
    ];
    exit;
}
?>
