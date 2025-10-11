
<?php
/**
 * Controlador de Producción
 * Ejemplo de implementación usando BaseController
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Produccion.php';
require_once __DIR__ . '/../config/modules.php';

class ProduccionController extends BaseController {
    private $produccionModel;

    /**
     * Constructor - Auto-ejecuta handleRequest si hay parámetro action
     */
    public function __construct() {
        parent::__construct();
        $this->produccionModel = new Produccion();
        
        // Auto-ejecución si hay action en GET
        if (isset($_GET['action'])) {
            $this->handleRequest();
        }
    }

    /**
     * Maneja las peticiones GET con acciones
     */
    public function handleRequest() {
        // Verificar acceso al módulo de producción
        $this->verificarAccesoModulo(ModuleConfig::PRODUCCION);
        
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
                        $this->redirect('/controllers/ProduccionController.php?action=index', 'ID de producción requerido', 'error');
                    }
                    break;
                case 'update':
                    if ($id) {
                        $this->update($id);
                    } else {
                        $this->redirect('/controllers/ProduccionController.php?action=index', 'ID de producción requerido', 'error');
                    }
                    break;
                case 'delete':
                    if ($id) {
                        $this->delete($id);
                    } else {
                        $this->redirect('/controllers/ProduccionController.php?action=index', 'ID de producción requerido', 'error');
                    }
                    break;
                default:
                    $this->redirect('/controllers/ProduccionController.php?action=index');
                    break;
            }
        } catch (Exception $e) {
            error_log("Error en ProduccionController: " . $e->getMessage());
            $this->redirect('/controllers/ProduccionController.php?action=index', 'Error interno del servidor', 'error');
        }
    }

    /**
     * Método principal - listado de producciones
     */
    public function index() {
        try {
            $producciones = $this->produccionModel->listar();
            
            $datos = [
                'pageTitle' => 'Gestión de Producción',
                'producciones' => $producciones,
                'usuario' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/produccion/listado_produccion.php', $datos);
            
        } catch (Exception $e) {
            error_log("Error en listado de producción: " . $e->getMessage());
            
            $datos = [
                'pageTitle' => 'Gestión de Producción',
                'producciones' => [],
                'usuario' => $this->usuario,
                'error' => 'Error al cargar producción. Por favor intente nuevamente.'
            ];
            
            $this->render(__DIR__ . '/../views/pages/produccion/listado_produccion.php', $datos);
        }
    }

    /**
     * Mostrar formulario para crear nueva producción
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar creación
            $this->procesarCreacion();
            return;
        }

        // Mostrar formulario
        $datos = [
            'pageTitle' => 'Crear Producción',
            'usuario' => $this->usuario
        ];
        
        $this->render(__DIR__ . '/../views/pages/produccion/crear_produccion.php', $datos);
    }

    /**
     * Procesar creación de producción
     */
    private function procesarCreacion() {
        try {
            $reglas = [
                'descripcion' => ['required' => true, 'max_length' => 200],
                'cantidad' => ['required' => true, 'type' => 'numeric', 'min_value' => 1],
                'fecha_inicio' => ['required' => true],
            ];

            $errores = $this->validarDatos($_POST, $reglas);
            
            if (!empty($errores)) {
                $this->establecerMensaje('Por favor corrija los errores en el formulario', 'error');
                $this->create();
                return;
            }

            $datos = [
                'descripcion' => $_POST['descripcion'],
                'cantidad' => (int)$_POST['cantidad'],
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin_estimada' => $_POST['fecha_fin_estimada'] ?? null,
                'estado' => $_POST['estado'] ?? 'PLANIFICADO',
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            $this->produccionModel->crear($datos);
            $this->redirect('/controllers/ProduccionController.php?action=index', 'Producción creada correctamente', 'success');
            
        } catch (Exception $e) {
            error_log("Error al crear producción: " . $e->getMessage());
            $this->establecerMensaje('Error al crear producción: ' . $e->getMessage(), 'error');
            $this->create();
        }
    }

    /**
     * Método store - redirige a procesarCreacion para mantener compatibilidad
     */
    public function store() {
        $this->procesarCreacion();
    }

    /**
     * Mostrar formulario para editar producción
     */
    public function edit($id) {
        try {
            $produccion = $this->produccionModel->obtenerPorId($id);
            
            if (!$produccion) {
                $this->redirect('/controllers/ProduccionController.php?action=index', 'Producción no encontrada', 'error');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->procesarActualizacion($id);
                return;
            }

            $datos = [
                'pageTitle' => 'Editar Producción',
                'produccion' => $produccion,
                'usuario' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/produccion/editar_produccion.php', $datos);
            
        } catch (Exception $e) {
            error_log("Error al cargar producción: " . $e->getMessage());
            $this->redirect('/controllers/ProduccionController.php?action=index', 'Error al cargar producción', 'error');
        }
    }

    /**
     * Procesar actualización de producción
     */
    private function procesarActualizacion($id) {
        try {
            $reglas = [
                'descripcion' => ['required' => true, 'max_length' => 200],
                'cantidad' => ['required' => true, 'type' => 'numeric', 'min_value' => 1],
                'fecha_inicio' => ['required' => true],
            ];

            $errores = $this->validarDatos($_POST, $reglas);
            
            if (!empty($errores)) {
                $this->establecerMensaje('Por favor corrija los errores en el formulario', 'error');
                $this->edit($id);
                return;
            }

            $datos = [
                'descripcion' => $_POST['descripcion'],
                'cantidad' => (int)$_POST['cantidad'],
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin_estimada' => $_POST['fecha_fin_estimada'] ?? null,
                'estado' => $_POST['estado'] ?? 'PLANIFICADO',
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            $this->produccionModel->actualizar($id, $datos);
            $this->redirect('/controllers/ProduccionController.php?action=index', 'Producción actualizada correctamente', 'success');
            
        } catch (Exception $e) {
            error_log("Error al actualizar producción: " . $e->getMessage());
            $this->establecerMensaje('Error al actualizar producción: ' . $e->getMessage(), 'error');
            $this->edit($id);
        }
    }

    /**
     * Método update - redirige a procesarActualizacion para mantener compatibilidad
     */
    public function update($id) {
        $this->procesarActualizacion($id);
    }

    /**
     * Eliminar producción
     */
    public function delete($id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/controllers/ProduccionController.php?action=index', 'Método no permitido', 'error');
                return;
            }

            $resultado = $this->produccionModel->eliminar($id);
            
            if ($resultado) {
                $this->redirect('/controllers/ProduccionController.php?action=index', 'Producción eliminada correctamente', 'success');
            } else {
                $this->redirect('/controllers/ProduccionController.php?action=index', 'No se pudo eliminar la producción', 'error');
            }
            
        } catch (Exception $e) {
            error_log("Error al eliminar producción: " . $e->getMessage());
            $this->redirect('/controllers/ProduccionController.php?action=index', 'Error al eliminar producción: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Método legacy para compatibilidad hacia atrás
     */
    public function listado() {
        $this->index();
    }
}
