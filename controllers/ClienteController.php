<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../config/modules.php';

class ClienteController extends BaseController {
    private $cliente;

    public function __construct() {
        parent::__construct();
        $this->cliente = new Cliente();
        
        // Auto-ejecución si hay action en GET
        if (isset($_GET['action'])) {
            $this->handleRequest();
        }
    }

    /**
     * ✅ NUEVO: Método estándar para manejar acciones GET
     */
    public function handleRequest() {
        // Verificar acceso al módulo de clientes
        $this->verificarAccesoModulo(ModuleConfig::CLIENTES);
        
        $action = $_GET['action'] ?? 'index';
        
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
                if (isset($_GET['id'])) {
                    $this->edit($_GET['id']);
                }
                break;
            case 'update':
                if (isset($_GET['id'])) {
                    $this->update($_GET['id']);
                }
                break;
            case 'delete':
                if (isset($_GET['id'])) {
                    $this->delete($_GET['id']);
                }
                break;
            default:
                $this->index();
                break;
        }
    }

    /**
     * Método principal - listado de clientes (compatible con ?action=index)
     */
    public function index() {
        try {
            $clientes = $this->cliente->listar();
            
            $data = [
                'titulo' => 'Gestión de Clientes',
                'clientes' => $clientes,
                'totalClientes' => count($clientes)
            ];

            $this->render('pages/clientes/listado_clientes', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al cargar listado de clientes: " . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para crear nuevo cliente (compatible con ?action=create)
     */
    public function create() {
        try {
            $data = [
                'titulo' => 'Nuevo Cliente',
                'tiposCliente' => $this->obtenerTiposCliente(),
                'condicionesIVA' => $this->obtenerCondicionesIVA(),
                'tiposDocumento' => $this->obtenerTiposDocumento()
            ];

            $this->render('pages/clientes/crear_cliente', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al cargar formulario de creación: " . $e->getMessage());
        }
    }

    /**
     * Procesar creación de nuevo cliente
     */
    public function procesarCreacion() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            // Sanitizar datos de entrada
            $datosPersona = $this->sanitizarDatos([
                'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
                'numero_documento' => $_POST['numero_documento'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'nombres' => $_POST['nombres'] ?? '',
                'razon_social' => $_POST['razon_social'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'telefono_alternativo' => $_POST['telefono_alternativo'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'localidad' => $_POST['localidad'] ?? '',
                'provincia' => $_POST['provincia'] ?? '',
                'codigo_postal' => $_POST['codigo_postal'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'observaciones' => $_POST['observaciones_persona'] ?? '',
                'usuario_creacion' => $_SESSION['usuario_id'] ?? null
            ]);

            $datosCliente = $this->sanitizarDatos([
                'tipo_cliente' => $_POST['tipo_cliente'] ?? 'MINORISTA',
                'condicion_iva' => $_POST['condicion_iva'] ?? 'CONSUMIDOR_FINAL',
                'limite_credito' => floatval($_POST['limite_credito'] ?? 0),
                'descuento_general' => floatval($_POST['descuento_general'] ?? 0),
                'dias_credito' => intval($_POST['dias_credito'] ?? 0),
                'fecha_alta' => $_POST['fecha_alta'] ?? date('Y-m-d'),
                'observaciones' => $_POST['observaciones'] ?? '',
                'usuario_creacion' => $_SESSION['usuario_id'] ?? null
            ]);

            // Validar datos
            $erroresPersona = (new Persona())->validarDatos($datosPersona);
            $erroresCliente = $this->cliente->validarDatosCliente($datosCliente);
            
            $errores = array_merge($erroresPersona, $erroresCliente);
            
            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['datos_formulario'] = array_merge($datosPersona, $datosCliente);
                header('Location: /controllers/UsuarioController.php?accion=crear');
                exit;
            }

            // Crear cliente
            $clienteId = $this->cliente->crear($datosPersona, $datosCliente);
            
            if ($clienteId) {
                $_SESSION['mensaje_exito'] = "Cliente creado exitosamente con ID: $clienteId";
                header('Location: /controllers/ClienteController.php?accion=index');
            } else {
                throw new Exception("No se pudo crear el cliente");
            }

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            $_SESSION['datos_formulario'] = array_merge($datosPersona ?? [], $datosCliente ?? []);
            header('Location: /controllers/ClienteController.php?accion=crear');
        }
        exit;
    }

    /**
     * Mostrar formulario para editar cliente
     */
    public function editar() {
        try {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de cliente no válido");
            }

            $cliente = $this->cliente->obtenerPorId($id);
            if (!$cliente) {
                throw new Exception("Cliente no encontrado");
            }

            $data = [
                'titulo' => 'Editar Cliente',
                'cliente' => $cliente,
                'tiposCliente' => $this->obtenerTiposCliente(),
                'condicionesIVA' => $this->obtenerCondicionesIVA(),
                'tiposDocumento' => $this->obtenerTiposDocumento()
            ];

            $this->render('pages/clientes/editar_cliente', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al cargar formulario de edición: " . $e->getMessage());
        }
    }

    /**
     * Procesar actualización de cliente
     */
    public function procesarActualizacion() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de cliente no válido");
            }

            // Sanitizar datos de entrada
            $datosPersona = $this->sanitizarDatos([
                'tipo_documento' => $_POST['tipo_documento'] ?? null,
                'numero_documento' => $_POST['numero_documento'] ?? null,
                'apellidos' => $_POST['apellidos'] ?? null,
                'nombres' => $_POST['nombres'] ?? null,
                'razon_social' => $_POST['razon_social'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'telefono_alternativo' => $_POST['telefono_alternativo'] ?? null,
                'email' => $_POST['email'] ?? null,
                'direccion' => $_POST['direccion'] ?? null,
                'localidad' => $_POST['localidad'] ?? null,
                'provincia' => $_POST['provincia'] ?? null,
                'codigo_postal' => $_POST['codigo_postal'] ?? null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'observaciones' => $_POST['observaciones_persona'] ?? null
            ]);

            $datosCliente = $this->sanitizarDatos([
                'tipo_cliente' => $_POST['tipo_cliente'] ?? null,
                'condicion_iva' => $_POST['condicion_iva'] ?? null,
                'limite_credito' => isset($_POST['limite_credito']) ? floatval($_POST['limite_credito']) : null,
                'descuento_general' => isset($_POST['descuento_general']) ? floatval($_POST['descuento_general']) : null,
                'dias_credito' => isset($_POST['dias_credito']) ? intval($_POST['dias_credito']) : null,
                'observaciones' => $_POST['observaciones'] ?? null
            ]);

            // Filtrar solo los campos que tienen valor
            $datosPersona = array_filter($datosPersona, function($valor) {
                return $valor !== null && $valor !== '';
            });

            $datosCliente = array_filter($datosCliente, function($valor) {
                return $valor !== null && $valor !== '';
            });

            // Actualizar cliente
            $resultado = $this->cliente->actualizar($id, $datosPersona, $datosCliente);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Cliente actualizado exitosamente";
            } else {
                throw new Exception("No se pudo actualizar el cliente");
            }

            header('Location: /controllers/ClienteController.php?accion=index');

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header('Location: /controllers/ClienteController.php?accion=editar&id=' . ($id ?? 0));
        }
        exit;
    }

    /**
     * Eliminar (desactivar) cliente
     */
    public function eliminar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de cliente no válido");
            }

            $resultado = $this->cliente->eliminar($id);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Cliente eliminado exitosamente";
            } else {
                throw new Exception("No se pudo eliminar el cliente");
            }

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
        }

        header('Location: /controllers/ClienteController.php?accion=index');
        exit;
    }

    /**
     * Buscar clientes por criterios
     */
    public function buscar() {
        try {
            $criterios = [
                'nombre' => $_GET['nombre'] ?? '',
                'codigo' => $_GET['codigo'] ?? '',
                'documento' => $_GET['documento'] ?? '',
                'tipo_cliente' => $_GET['tipo_cliente'] ?? '',
                'localidad' => $_GET['localidad'] ?? ''
            ];

            // Filtrar criterios vacíos
            $criterios = array_filter($criterios, function($valor) {
                return $valor !== '' && $valor !== null;
            });

            if (empty($criterios)) {
                $clientes = $this->cliente->listar();
            } else {
                $clientes = $this->cliente->buscar($criterios);
            }

            $data = [
                'titulo' => 'Búsqueda de Clientes',
                'clientes' => $clientes,
                'criterios' => $criterios,
                'totalClientes' => count($clientes)
            ];

            $this->render('pages/clientes/listado_clientes', $data);

        } catch (Exception $e) {
            $this->manejarError("Error en búsqueda de clientes: " . $e->getMessage());
        }
    }

    /**
     * Ver detalles de un cliente
     */
    public function ver() {
        try {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de cliente no válido");
            }

            $cliente = $this->cliente->obtenerPorId($id);
            if (!$cliente) {
                throw new Exception("Cliente no encontrado");
            }

            $estadisticas = $this->cliente->obtenerEstadisticas($id);

            $data = [
                'titulo' => 'Detalles del Cliente',
                'cliente' => $cliente,
                'estadisticas' => $estadisticas
            ];

            $this->render('pages/clientes/ver_cliente', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al ver cliente: " . $e->getMessage());
        }
    }

    /**
     * API para obtener clientes (JSON)
     */
    public function api() {
        try {
            header('Content-Type: application/json');
            
            $accion = $_GET['accion'] ?? 'listar';
            
            switch ($accion) {
                case 'listar':
                    $clientes = $this->cliente->listar();
                    echo json_encode(['success' => true, 'data' => $clientes]);
                    break;
                    
                case 'buscar':
                    $criterios = [
                        'nombre' => $_GET['nombre'] ?? '',
                        'codigo' => $_GET['codigo'] ?? '',
                        'documento' => $_GET['documento'] ?? ''
                    ];
                    
                    $criterios = array_filter($criterios);
                    $clientes = empty($criterios) ? [] : $this->cliente->buscar($criterios);
                    echo json_encode(['success' => true, 'data' => $clientes]);
                    break;
                    
                case 'obtener':
                    $id = intval($_GET['id'] ?? 0);
                    if (!$id) {
                        throw new Exception("ID requerido");
                    }
                    
                    $cliente = $this->cliente->obtenerPorId($id);
                    if (!$cliente) {
                        throw new Exception("Cliente no encontrado");
                    }
                    
                    echo json_encode(['success' => true, 'data' => $cliente]);
                    break;
                    
                default:
                    throw new Exception("Acción no válida");
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Obtener tipos de cliente para select
     */
    private function obtenerTiposCliente() {
        return [
            'MINORISTA' => 'Minorista',
            'MAYORISTA' => 'Mayorista',
            'EMPRESARIAL' => 'Empresarial',
            'GOBIERNO' => 'Gobierno',
            'CONSTRUCTOR' => 'Constructor'
        ];
    }

    /**
     * Obtener condiciones de IVA para select
     */
    private function obtenerCondicionesIVA() {
        return [
            'CONSUMIDOR_FINAL' => 'Consumidor Final',
            'RESPONSABLE_INSCRIPTO' => 'Responsable Inscripto',
            'MONOTRIBUTISTA' => 'Monotributista',
            'EXENTO' => 'Exento',
            'NO_CATEGORIZADO' => 'No Categorizado'
        ];
    }

    /**
     * Obtener tipos de documento para select
     */
    private function obtenerTiposDocumento() {
        return [
            'DNI' => 'DNI',
            'CUIL' => 'CUIL',
            'CUIT' => 'CUIT',
            'PASAPORTE' => 'Pasaporte',
            'CEDULA' => 'Cédula',
            'LE' => 'LE',
            'LC' => 'LC'
        ];
    }

    /**
     * ✅ NUEVOS: Métodos estándar CRUD compatibles con ?action=
     */
    
    /**
     * Procesar creación (compatible con ?action=store)
     */
    public function store() {
        $this->procesarCreacion();
    }

    /**
     * Mostrar formulario de edición (compatible con ?action=edit)
     */
    public function edit($id) {
        // Usar método existente editar() que ya maneja $_GET['id']
        $_GET['id'] = $id;
        $this->editar();
    }

    /**
     * Procesar actualización (compatible con ?action=update)
     */
    public function update($id) {
        // TODO: Implementar o usar método existente si lo hay
        $this->redirect('/controllers/ClienteController.php?action=index', 'Función en desarrollo', 'info');
    }

    /**
     * Eliminar cliente (compatible con ?action=delete)
     */
    public function delete($id) {
        // Usar método existente eliminar() 
        $_GET['id'] = $id;
        $this->eliminar();
    }
}
// ✅ Auto-ejecución siguiendo patrón estándar
new ClienteController();
?>
