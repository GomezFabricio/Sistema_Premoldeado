<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../config/modules.php';
require_once __DIR__ . '/../config/database.php';

class ClienteController extends BaseController {
    private $clienteModel;

    public function __construct() {
        echo "<h3>🔍 Debug: Constructor ClienteController iniciado</h3>";
        
        // Verificar si hay sesión activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        echo "<p>Sesión iniciada. Estado: " . session_status() . "</p>";
        
        // Verificar autenticación
        if (!isset($_SESSION['usuario_id'])) {
            echo "<p>❌ No hay usuario_id en sesión. Redirigiendo...</p>";
            // Redirigir al login si no está autenticado
            header('Location: views/pages/auth/login.php');
            exit;
        }
        
        echo "<p>✅ Usuario logueado: " . ($_SESSION['usuario_nombre'] ?? 'Sin nombre') . "</p>";
        
        // Cargar datos del usuario desde la sesión
        $this->usuario = [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'] ?? 'Usuario',
            'email' => $_SESSION['usuario_email'] ?? '',
            'perfil_id' => $_SESSION['perfil_id'] ?? 1,
            'perfil_nombre' => $_SESSION['perfil_nombre'] ?? 'Usuario'
        ];
        
        // Verificar permisos del módulo (comentado temporalmente)
        // $this->verificarModulo(ModuleConfig::CLIENTES);
        
        echo "<p>Inicializando modelo Cliente...</p>";
        $this->clienteModel = new Cliente();
        $this->moduleTitle = 'Clientes';
        echo "<p>✅ Constructor completado</p>";
        
        // Auto-ejecución si hay action en GET (comentado para debug)
        // if (isset($_GET['action'])) {
        //     echo "<p>Action detectada: " . $_GET['action'] . "</p>";
        //     $this->handleRequest();
        // }
    }

    /**
     * ✅ NUEVO: Método estándar para manejar acciones GET
     */
    public function handleRequest() {
        // Temporalmente sin verificación de módulos
        // $this->verificarAccesoModulo(ModuleConfig::CLIENTES);
        
        try {
            $action = $_GET['action'] ?? 'index';
            $id = $_GET['id'] ?? null;

            switch($action) {
                case 'index':
                    $this->index();
                    break;
                case 'create':
                    $this->create();
                    break;
                case 'store':
                    $this->store();
                    break;
                case 'edit':
                    if ($id) {
                        $this->edit($id);
                    } else {
                        $this->redirectToController('Cliente', 'index', [], 'ID de cliente requerido', 'error');
                    }
                    break;
                case 'update':
                    if ($id) {
                        $this->update($id);
                    } else {
                        $this->redirectToController('Cliente', 'index', [], 'ID de cliente requerido', 'error');
                    }
                    break;
                case 'delete':
                    if ($id) {
                        $this->delete($id);
                    } else {
                        $this->redirectToController('Cliente', 'index', [], 'ID de cliente requerido', 'error');
                    }
                    break;
                case 'crear_usuario':
                    if ($id) {
                        $this->crearUsuarioParaCliente($id);
                    } else {
                        $this->redirect('/controllers/ClienteController.php?action=index', 'ID de cliente requerido', 'error');
                    }
                    break;
                case 'procesar_usuario':
                    $this->procesarCreacionUsuario();
                    break;
                case 'activate':
                    if ($id) {
                        $this->activate($id);
                    } else {
                        $this->redirectToController('Cliente', 'index', [], 'ID de cliente requerido', 'error');
                    }
                    break;
                default:
                    $this->redirectToController('Cliente', 'index');
                    break;
            }
        } catch (Exception $e) {
            error_log("Error en ClienteController: " . $e->getMessage());
            $this->redirectToController('Cliente', 'index', [], 'Error interno del servidor', 'error');
        }
    }

    /**
     * Método principal - listado de clientes (compatible con ?action=index)
     */
    public function index() {
        echo "<h3>🔍 Debug: Listado de Clientes</h3>";
        
        try {
            $cliente = new Cliente();
            $clientes = $cliente->listar();
            
            echo "<p>Total de clientes encontrados: " . count($clientes) . "</p>";
            
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Listado de Clientes</title>
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>
                            <i class="fas fa-users text-primary me-2"></i>
                            Gestión de Clientes (<?= count($clientes) ?>)
                        </h1>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?action=create" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Nuevo Cliente
                        </a>
                    </div>
                    
                    <?php if (empty($clientes)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay clientes registrados. <a href="<?= $_SERVER['PHP_SELF'] ?>?action=create">Crea el primero</a>
                        </div>
                    <?php else: ?>
                        <div class="card shadow">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th>ID</th>
                                                <th>Documento</th>
                                                <th>Nombre Completo</th>
                                                <th>Teléfono</th>
                                                <th>Email</th>
                                                <th>Estado</th>
                                                <th width="200">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($cliente['id']) ?></td>
                                                    <td><?= htmlspecialchars($cliente['numero_documento'] ?? $cliente['documento'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($cliente['nombres']) ?> <?= htmlspecialchars($cliente['apellidos']) ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($cliente['telefono'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($cliente['email'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if ($cliente['activo'] ?? 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?= $_SERVER['PHP_SELF'] ?>?action=edit&id=<?= $cliente['id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            
                                                            <?php if ($cliente['activo'] ?? 1): ?>
                                                                <a href="<?= $_SERVER['PHP_SELF'] ?>?action=delete&id=<?= $cliente['id'] ?>" 
                                                                   class="btn btn-sm btn-outline-danger" 
                                                                   title="Dar de baja"
                                                                   onclick="return confirm('¿Estás seguro de dar de baja este cliente?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="<?= $_SERVER['PHP_SELF'] ?>?action=activate&id=<?= $cliente['id'] ?>" 
                                                                   class="btn btn-sm btn-outline-success" 
                                                                   title="Reactivar"
                                                                   onclick="return confirm('¿Estás seguro de reactivar este cliente?')">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            
                                                            <a href="<?= $_SERVER['PHP_SELF'] ?>?action=crear_usuario_para_cliente&cliente_id=<?= $cliente['id'] ?>" 
                                                               class="btn btn-sm btn-outline-info" 
                                                               title="Crear Usuario">
                                                                <i class="fas fa-user-plus"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </body>
            </html>
            <?php
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h5>❌ Error al cargar clientes</h5>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
    }

    /**
     * Procesar la creación de un nuevo cliente
     */
    public function store() {
        echo "<h3>🔍 Debug: Procesando store()</h3>";
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?action=create');
            exit;
        }
        
        try {
            // Obtener datos del formulario
            $nombres = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $documento = trim($_POST['documento'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            echo "<p>Datos recibidos:</p>";
            echo "<ul>";
            echo "<li>Nombres: " . htmlspecialchars($nombres) . "</li>";
            echo "<li>Apellidos: " . htmlspecialchars($apellidos) . "</li>";
            echo "<li>Documento: " . htmlspecialchars($documento) . "</li>";
            echo "<li>Telefono: " . htmlspecialchars($telefono) . "</li>";
            echo "<li>Email: " . htmlspecialchars($email) . "</li>";
            echo "</ul>";
            
            // Validaciones básicas
            $errores = [];
            
            if (empty($nombres)) {
                $errores[] = "Los nombres son obligatorios";
            }
            
            if (empty($apellidos)) {
                $errores[] = "Los apellidos son obligatorios";
            }
            
            if (empty($documento)) {
                $errores[] = "El documento es obligatorio";
            }
            
            // Email opcional pero si se proporciona debe ser válido
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El email no tiene un formato válido";
            }
            
            if (!empty($errores)) {
                echo "<div class='alert alert-danger'>";
                echo "<h5>Errores de validación:</h5>";
                echo "<ul>";
                foreach ($errores as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                echo "</ul>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=create' class='btn btn-secondary'>Volver al formulario</a>";
                echo "</div>";
                return;
            }
            
            // Crear el cliente
            $cliente = new Cliente();
            
            // Datos para la tabla personas (todos los campos requeridos)
            $datosPersona = [
                'tipo_documento' => 'DNI', // Valor por defecto
                'numero_documento' => $documento,
                'apellidos' => $apellidos,
                'nombres' => $nombres,
                'razon_social' => null, // Para personas físicas
                'telefono' => $telefono ?: null,
                'email' => $email ?: null,
                'direccion' => null, // Por ahora opcional
                'localidad' => null, // Por ahora opcional
                'provincia' => null, // Por ahora opcional
                'codigo_postal' => null // Por ahora opcional
            ];
            
            // Datos específicos del cliente
            $datosCliente = [
                'observaciones' => '' // Cadena vacía en lugar de null
            ];
            
            echo "<p>Intentando crear cliente...</p>";
            
            $resultado = $cliente->crear($datosPersona, $datosCliente);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>";
                echo "<h5>✅ Cliente creado exitosamente!</h5>";
                echo "<p>ID del cliente: " . $resultado . "</p>";
                echo "<div class='mt-3'>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-primary'>Ver Listado de Clientes</a> ";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=create' class='btn btn-success'>Crear Otro Cliente</a>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-danger'>";
                echo "<h5>❌ Error al crear cliente</h5>";
                echo "<p>No se pudo guardar el cliente en la base de datos.</p>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=create' class='btn btn-secondary'>Volver al formulario</a>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h5>❌ Error del sistema</h5>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=create' class='btn btn-secondary'>Volver al formulario</a>";
            echo "</div>";
        }
    }

    /**
     * Mostrar formulario para crear nuevo cliente
     */
    public function create() {
        echo "<h3>🔍 Debug: Iniciando método create()</h3>";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<p>Procesando POST...</p>";
            // Procesar creación
            $this->procesarCreacion();
            return;
        }

        echo "<p>Mostrando formulario GET...</p>";
        
        // Usar variables directas en lugar del método render
        $pageTitle = 'Crear Cliente';
        $usuario = $this->usuario;
        
        echo "<p>Verificando vista...</p>";
        $rutaVista = __DIR__ . '/../views/pages/clientes/crear_cliente.php';
        echo "<p>Ruta vista: " . $rutaVista . "</p>";
        echo "<p>Archivo existe: " . (file_exists($rutaVista) ? 'SÍ' : 'NO') . "</p>";
        
        if (file_exists($rutaVista)) {
            echo "<p>Incluyendo vista directamente...</p>";
            
            // Crear una versión simple sin layout para debugging
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Crear Cliente - Debug</title>
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <h1>✅ Crear Nuevo Cliente - FUNCIONANDO!</h1>
                    <p><strong>Usuario:</strong> <?= isset($usuario['nombres']) ? htmlspecialchars($usuario['nombres']) : 'Sin sesión' ?></p>
                    
                    <form action="<?= $_SERVER['PHP_SELF'] ?>?action=store" method="POST" class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="documento" class="form-label">Documento *</label>
                                <input type="text" class="form-control" id="documento" name="documento" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="email" class="form-label">Email (opcional)</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com">
                                <div class="form-text">
                                    El email es opcional. Si lo proporcionas, debe tener un formato válido.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Cliente
                            </button>
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?action=index" class="btn btn-secondary ms-2">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </body>
            </html>
            <?php
        } else {
            echo "<div class='alert alert-danger'>Vista no encontrada: " . $rutaVista . "</div>";
        }
    }

    /**
     * Procesar creación de cliente
     */
    private function procesarCreacion() {
        try {
            $reglas = [
                'numero_documento' => ['required' => true, 'max_length' => 20],
                'apellidos' => ['required' => false, 'max_length' => 100],
                'nombres' => ['required' => false, 'max_length' => 100],
                'razon_social' => ['required' => false, 'max_length' => 200],
                'email' => ['required' => false, 'type' => 'email', 'max_length' => 150],
                'telefono' => ['required' => false, 'max_length' => 20]
            ];

            $errores = $this->validarDatos($_POST, $reglas);
            
            if (!empty($errores)) {
                $this->establecerMensaje('Por favor corrija los errores en el formulario', 'error');
                $this->create();
                return;
            }

            $datosPersona = [
                'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
                'numero_documento' => $_POST['numero_documento'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'nombres' => $_POST['nombres'] ?? '',
                'razon_social' => $_POST['razon_social'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'localidad' => $_POST['localidad'] ?? '',
                'provincia' => $_POST['provincia'] ?? '',
                'codigo_postal' => $_POST['codigo_postal'] ?? ''
            ];
            
            $datosCliente = [
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            $this->clienteModel->crear($datosPersona, $datosCliente);
            $this->redirect('/controllers/ClienteController.php?action=index', 'Cliente creado correctamente', 'success');
            
        } catch (Exception $e) {
            error_log("Error al crear cliente: " . $e->getMessage());
            $this->establecerMensaje('Error al crear cliente: ' . $e->getMessage(), 'error');
            $this->create();
        }
    }

    /**
     * Mostrar formulario para editar cliente
     */
    public function edit($id) {
        echo "<h3>🔍 Debug: Editando Cliente ID: " . htmlspecialchars($id) . "</h3>";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<p>Procesando POST para actualizar...</p>";
            $this->update($id);
            return;
        }
        
        try {
            $cliente = new Cliente();
            $datosCliente = $cliente->obtenerPorId($id);
            
            if (!$datosCliente) {
                echo "<div class='alert alert-danger'>Cliente no encontrado</div>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
                return;
            }
            
            echo "<p>Cliente encontrado: " . htmlspecialchars($datosCliente['nombres']) . " " . htmlspecialchars($datosCliente['apellidos']) . "</p>";
            
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Editar Cliente</title>
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>
                            <i class="fas fa-edit text-warning me-2"></i>
                            Editar Cliente
                        </h1>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                    </div>
                    
                    <div class="card shadow">
                        <div class="card-header bg-warning text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-edit me-2"></i>Información del Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= $_SERVER['PHP_SELF'] ?>?action=edit&id=<?= $id ?>" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombres" class="form-label fw-bold">Nombres *</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" 
                                               value="<?= htmlspecialchars($datosCliente['nombres']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="apellidos" class="form-label fw-bold">Apellidos *</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                               value="<?= htmlspecialchars($datosCliente['apellidos']) ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="documento" class="form-label fw-bold">Documento *</label>
                                        <input type="text" class="form-control" id="documento" name="documento" 
                                               value="<?= htmlspecialchars($datosCliente['numero_documento'] ?? $datosCliente['documento'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" 
                                               value="<?= htmlspecialchars($datosCliente['telefono'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="email" class="form-label">Email (opcional)</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($datosCliente['email'] ?? '') ?>"
                                               placeholder="ejemplo@correo.com">
                                        <div class="form-text">
                                            El email es opcional. Si lo proporcionas, debe tener un formato válido.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-2"></i>Actualizar Cliente
                                    </button>
                                    <a href="<?= $_SERVER['PHP_SELF'] ?>?action=index" class="btn btn-secondary ms-2">
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            <?php
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h5>❌ Error al cargar cliente</h5>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
            echo "</div>";
        }
    }

    /**
     * Procesar actualización de cliente
     */
    private function procesarActualizacion($id) {
        try {
            $reglas = [
                'numero_documento' => ['required' => true, 'max_length' => 20],
                'apellidos' => ['required' => false, 'max_length' => 100],
                'nombres' => ['required' => false, 'max_length' => 100],
                'razon_social' => ['required' => false, 'max_length' => 200],
                'email' => ['required' => false, 'type' => 'email', 'max_length' => 150],
                'telefono' => ['required' => false, 'max_length' => 20]
            ];

            $errores = $this->validarDatos($_POST, $reglas);
            
            if (!empty($errores)) {
                $this->establecerMensaje('Por favor corrija los errores en el formulario', 'error');
                $this->edit($id);
                return;
            }

            $datosPersona = [
                'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
                'numero_documento' => $_POST['numero_documento'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'nombres' => $_POST['nombres'] ?? '',
                'razon_social' => $_POST['razon_social'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'localidad' => $_POST['localidad'] ?? '',
                'provincia' => $_POST['provincia'] ?? '',
                'codigo_postal' => $_POST['codigo_postal'] ?? ''
            ];
            
            $datosCliente = [
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            $this->clienteModel->actualizar($id, $datosPersona, $datosCliente);
            $this->redirect('/controllers/ClienteController.php?action=index', 'Cliente actualizado correctamente', 'success');
            
        } catch (Exception $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            $this->establecerMensaje('Error al actualizar cliente: ' . $e->getMessage(), 'error');
            $this->edit($id);
        }
    }

    /**
     * Procesar actualización de cliente
     */
    public function update($id) {
        echo "<h3>🔍 Debug: Actualizando Cliente ID: " . htmlspecialchars($id) . "</h3>";
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "<div class='alert alert-warning'>Método no permitido</div>";
            echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
            return;
        }
        
        try {
            // Obtener datos del formulario
            $nombres = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $documento = trim($_POST['documento'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            echo "<p>Datos recibidos para actualización:</p>";
            echo "<ul>";
            echo "<li>Nombres: " . htmlspecialchars($nombres) . "</li>";
            echo "<li>Apellidos: " . htmlspecialchars($apellidos) . "</li>";
            echo "<li>Documento: " . htmlspecialchars($documento) . "</li>";
            echo "<li>Telefono: " . htmlspecialchars($telefono) . "</li>";
            echo "<li>Email: " . htmlspecialchars($email) . "</li>";
            echo "</ul>";
            
            // Validaciones básicas
            $errores = [];
            
            if (empty($nombres)) {
                $errores[] = "Los nombres son obligatorios";
            }
            
            if (empty($apellidos)) {
                $errores[] = "Los apellidos son obligatorios";
            }
            
            if (empty($documento)) {
                $errores[] = "El documento es obligatorio";
            }
            
            // Email opcional pero si se proporciona debe ser válido
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El email no tiene un formato válido";
            }
            
            if (!empty($errores)) {
                echo "<div class='alert alert-danger'>";
                echo "<h5>Errores de validación:</h5>";
                echo "<ul>";
                foreach ($errores as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                echo "</ul>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $id . "' class='btn btn-secondary'>Volver al formulario</a>";
                echo "</div>";
                return;
            }
            
            // Actualizar el cliente
            $cliente = new Cliente();
            
            // Datos para actualizar en personas
            $datosPersona = [
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'numero_documento' => $documento,
                'telefono' => $telefono ?: null,
                'email' => $email ?: null
            ];
            
            echo "<p>Intentando actualizar cliente...</p>";
            
            $resultado = $cliente->actualizar($id, $datosPersona, []);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>";
                echo "<h5>✅ Cliente actualizado exitosamente!</h5>";
                echo "<div class='mt-3'>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-primary'>Ver Listado de Clientes</a> ";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $id . "' class='btn btn-warning'>Seguir Editando</a>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-danger'>";
                echo "<h5>❌ Error al actualizar cliente</h5>";
                echo "<p>No se pudo actualizar el cliente en la base de datos.</p>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $id . "' class='btn btn-secondary'>Volver al formulario</a>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h5>❌ Error del sistema</h5>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=edit&id=" . $id . "' class='btn btn-secondary'>Volver al formulario</a>";
            echo "</div>";
        }
    }

    /**
     * Dar de baja cliente (baja lógica)
     */
    public function delete($id) {
        echo "<h3>🔍 Debug: Dando de baja Cliente ID: " . htmlspecialchars($id) . "</h3>";
        
        try {
            $cliente = new Cliente();
            
            // Verificar que el cliente existe
            $datosCliente = $cliente->obtenerPorId($id);
            if (!$datosCliente) {
                echo "<div class='alert alert-danger'>Cliente no encontrado</div>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
                return;
            }
            
            echo "<p>Cliente encontrado: " . htmlspecialchars($datosCliente['nombres']) . " " . htmlspecialchars($datosCliente['apellidos']) . "</p>";
            
            // Realizar baja lógica (marcar como inactivo)
            $resultado = $cliente->darDeBaja($id);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>";
                echo "<h5>✅ Cliente dado de baja correctamente!</h5>";
                echo "<p>El cliente <strong>" . htmlspecialchars($datosCliente['nombres']) . " " . htmlspecialchars($datosCliente['apellidos']) . "</strong> ha sido marcado como inactivo.</p>";
                echo "<div class='mt-3'>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-primary'>Volver al Listado</a> ";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=activate&id=" . $id . "' class='btn btn-success'>Reactivar Cliente</a>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-danger'>";
                echo "<h5>❌ Error al dar de baja</h5>";
                echo "<p>No se pudo dar de baja el cliente en la base de datos.</p>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h5>❌ Error del sistema</h5>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
            echo "</div>";
        }
    }

    /**
     * Reactivar cliente
     */
    public function activate($id) {
        echo "<h3>🔍 Debug: Reactivando Cliente ID: " . htmlspecialchars($id) . "</h3>";
        
        try {
            $cliente = new Cliente();
            
            // Verificar que el cliente existe
            $datosCliente = $cliente->obtenerPorId($id);
            if (!$datosCliente) {
                echo "<div class='alert alert-danger'>Cliente no encontrado</div>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
                return;
            }
            
            echo "<p>Cliente encontrado: " . htmlspecialchars($datosCliente['nombres']) . " " . htmlspecialchars($datosCliente['apellidos']) . "</p>";
            
            // Reactivar cliente
            $resultado = $cliente->reactivar($id);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>";
                echo "<h5>✅ Cliente reactivado correctamente!</h5>";
                echo "<p>El cliente <strong>" . htmlspecialchars($datosCliente['nombres']) . " " . htmlspecialchars($datosCliente['apellidos']) . "</strong> ha sido marcado como activo nuevamente.</p>";
                echo "<div class='mt-3'>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-primary'>Volver al Listado</a>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-danger'>";
                echo "<h5>❌ Error al reactivar</h5>";
                echo "<p>No se pudo reactivar el cliente en la base de datos.</p>";
                echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h5>❌ Error del sistema</h5>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<a href='" . $_SERVER['PHP_SELF'] . "?action=index' class='btn btn-secondary'>Volver al listado</a>";
            echo "</div>";
        }
    }
    
    /**
     * Método render simplificado para evitar dependencias del BaseController
     */
    private function renderSimple($vista, $datos = []) {
        // Hacer disponibles las variables en la vista
        extract($datos);
        
        // Incluir la vista
        include __DIR__ . '/../views/pages/' . $vista;
    }
    
    /**
     * Redirección simplificada
     */
    private function redirectSimple($url, $message = '', $type = 'info') {
        if ($message) {
            $_SESSION['flash_message'] = [
                'message' => $message,
                'type' => $type
            ];
        }
        header("Location: $url");
        exit;
    }

    /**
     * Crear usuario para un cliente existente (flujo correcto)
     */
    public function crearUsuarioParaCliente($clienteId) {
        try {
            // Obtener datos del cliente
            $cliente = $this->clienteModel->obtenerPorId($clienteId);
            
            if (!$cliente) {
                $this->redirect('/controllers/ClienteController.php?action=index', 'Cliente no encontrado', 'error');
                return;
            }

            // Verificar si ya tiene usuario
            if ($this->clienteYaTieneUsuario($clienteId)) {
                $this->redirect('/controllers/ClienteController.php?action=index', 
                              'Este cliente ya tiene acceso al sistema', 'warning');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->procesarCreacionUsuario();
                return;
            }

            // Mostrar formulario
            $datos = [
                'pageTitle' => 'Crear Acceso Web para Cliente',
                'cliente' => $cliente,
                'usuario' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/clientes/crear_usuario_cliente.php', $datos);
            
        } catch (Exception $e) {
            error_log("Error al crear usuario para cliente: " . $e->getMessage());
            $this->redirect('/controllers/ClienteController.php?action=index', 'Error al procesar la solicitud', 'error');
        }
    }

    /**
     * Procesar creación de usuario para cliente existente
     */
    public function procesarCreacionUsuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/controllers/ClienteController.php?action=index', 'Método no permitido', 'error');
            return;
        }

        try {
            $clienteId = $_POST['cliente_id'] ?? null;
            $nombreUsuario = $_POST['nombre_usuario'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmarPassword = $_POST['confirmar_password'] ?? '';

            if (!$clienteId || !$nombreUsuario || !$password) {
                $this->establecerMensaje('Todos los campos son obligatorios', 'error');
                $this->crearUsuarioParaCliente($clienteId);
                return;
            }

            if ($password !== $confirmarPassword) {
                $this->establecerMensaje('Las contraseñas no coinciden', 'error');
                $this->crearUsuarioParaCliente($clienteId);
                return;
            }

            // Obtener datos del cliente
            $cliente = $this->clienteModel->obtenerPorId($clienteId);
            
            // Crear usuario usando el modelo Usuario
            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();
            
            $datosUsuario = [
                'nombre_usuario' => $nombreUsuario,
                'email' => $cliente['email'],
                'password' => $password,
                'perfil_id' => 2, // ID de perfil cliente (ajustar según tu sistema)
                'activo' => 1,
                'persona_id' => $clienteId // Usar el mismo ID de la persona
            ];

            $resultado = $usuarioModel->crearDesdeClienteExistente($datosUsuario);
            
            if (is_numeric($resultado) && $resultado > 0) {
                $this->redirect('/controllers/ClienteController.php?action=index', 
                              'Acceso web creado exitosamente para el cliente', 'success');
            } else {
                $this->establecerMensaje('Error al crear usuario: ' . $resultado['message'], 'error');
                $this->crearUsuarioParaCliente($clienteId);
            }

        } catch (Exception $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            $this->establecerMensaje('Error al crear usuario: ' . $e->getMessage(), 'error');
            $this->crearUsuarioParaCliente($_POST['cliente_id'] ?? null);
        }
    }

    /**
     * Verificar si un cliente ya tiene usuario
     */
    private function clienteYaTieneUsuario($clienteId) {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT COUNT(*) as count FROM usuarios WHERE persona_id = ? AND activo = 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([$clienteId]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Auto-ejecución cuando se accede directamente al archivo
if (basename($_SERVER['PHP_SELF']) === 'ClienteController.php') {
    try {
        echo "<h2>🚀 Ejecutando ClienteController directamente</h2>";
        $controller = new ClienteController();
        $controller->handleRequest();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error crítico: " . $e->getMessage() . "</div>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
?>
