<?php
/**
 * Controlador de Productos
 * Ejemplo de implementación usando BaseController
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../config/modules.php';

class ProductoController extends BaseController {
    private $productoModel;

    /**
     * Constructor - Auto-ejecuta handleRequest si hay parámetro action
     */
    public function __construct() {
        // parent::__construct(); // TEMPORALMENTE DESHABILITADO PARA PRUEBAS
        
        // Simular usuario para las vistas
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['usuario'] = [
                'id' => 1,
                'nombre' => 'Usuario Temporal',
                'rol' => 'admin'
            ];
        }
        
        $this->productoModel = new Producto();
        
        // Auto-ejecución si hay action en GET
        if (isset($_GET['action'])) {
            $this->handleRequest();
        }
    }

    /**
     * Maneja las peticiones GET con acciones
     */
    public function handleRequest() {
        // Verificar acceso al módulo de productos - TEMPORALMENTE DESHABILITADO
        // $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);
        
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
                        $this->redirectToController('Producto', 'index', [], 'ID de producto requerido', 'error');
                    }
                    break;
                case 'update':
                    if ($id) {
                        $this->update($id);
                    } else {
                        $this->redirectToController('Producto', 'index', [], 'ID de producto requerido', 'error');
                    }
                    break;
                case 'delete':
                    if ($id) {
                        $this->delete($id);
                    } else {
                        $this->redirectToController('Producto', 'index', [], 'ID de producto requerido', 'error');
                    }
                    break;
                case 'indexTipos':
                    $this->indexTipos();
                    break;
                default:
                    $this->redirectToController('Producto', 'index');
                    break;
            }
        } catch (Exception $e) {
            error_log("Error en ProductoController: " . $e->getMessage());
            $this->redirectToController('Producto', 'index', [], 'Error interno del servidor', 'error');
        }
    }

    /**
     * Método principal - listado de productos (compatible con ?action=index)
     */
    public function index() {
        try {
            // Verificar acceso al módulo de productos
            $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);

            $productos = $this->productoModel->listar();
            
            $data = [
                'pageTitle' => 'Listado de Productos',
                'productos' => $productos,
                'usuario' => $this->usuario
            ];

            // Renderizar la vista usando BaseController
            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/pages/productos/listado_productos.php';
            include __DIR__ . '/../views/layouts/footer.php';

        } catch (Exception $e) {
            error_log("Error en ProductoController::index(): " . $e->getMessage());
            $this->redirectToDashboard('Error al cargar productos', 'error');
        }
    }

    /**
     * Mostrar formulario para crear nuevo producto (compatible con ?action=create)
     */
    public function create() {
        try {
            // Verificar acceso al módulo de productos
            $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);

            $tiposProducto = $this->productoModel->obtenerTiposProducto();

            $datos = [
                'pageTitle' => 'Crear Producto',
                'tiposProducto' => $tiposProducto,
                'usuario' => $this->usuario
            ];

            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/pages/productos/crear_producto.php';
            include __DIR__ . '/../views/layouts/footer.php';

        } catch (Exception $e) {
            error_log("Error en ProductoController::create(): " . $e->getMessage());
            $this->redirectToController('Producto', 'index', [], 'Error al mostrar formulario', 'error');
        }
    }

    /**
     * Procesar creación de nuevo producto
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarCreacion();
        } else {
            $this->redirectToController('Producto', 'create');
        }
    }

    /**
     * Formulario para editar producto
     */
    public function edit($id) {
        try {
            // Verificar acceso al módulo de productos
            $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);

            $producto = $this->productoModel->obtenerPorId($id);
            if (!$producto) {
                $this->redirectToController('Producto', 'index', [], 'Producto no encontrado', 'error');
                return;
            }

            $tiposProducto = $this->productoModel->obtenerTiposProducto();

            $datos = [
                'pageTitle' => 'Editar Producto',
                'producto' => $producto,
                'tiposProducto' => $tiposProducto,
                'usuario' => $this->usuario
            ];

            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/pages/productos/editar_producto.php';
            include __DIR__ . '/../views/layouts/footer.php';

        } catch (Exception $e) {
            error_log("Error en ProductoController::edit(): " . $e->getMessage());
            $this->redirectToController('Producto', 'index', [], 'Error al cargar producto', 'error');
        }
    }

    /**
     * Procesar actualización de producto
     */
    public function update($id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect("/controllers/ProductoController.php?action=edit&id=$id", 'Método no permitido', 'error');
                return;
            }

            // Verificar acceso al módulo de productos
            $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);

            $datos = [
                'ancho' => $_POST['ancho'] ?? '',
                'largo' => $_POST['largo'] ?? '',
                'cantidad_disponible' => $_POST['cantidad_disponible'] ?? '',
                'stock_minimo' => $_POST['stock_minimo'] ?? '',
                'precio_unitario' => $_POST['precio_unitario'] ?? '',
                'tipo_producto_id' => $_POST['tipo_producto_id'] ?? '',
            ];

            // Validar datos básicos
            $errores = [];
            if (empty($datos['ancho']) || !is_numeric($datos['ancho'])) {
                $errores[] = 'El ancho es requerido y debe ser numérico';
            }
            if (empty($datos['largo']) || !is_numeric($datos['largo'])) {
                $errores[] = 'El largo es requerido y debe ser numérico';
            }
            if (empty($datos['precio_unitario']) || !is_numeric($datos['precio_unitario']) || $datos['precio_unitario'] <= 0) {
                $errores[] = 'El precio unitario es requerido y debe ser mayor a 0';
            }
            if (empty($datos['tipo_producto_id']) || !is_numeric($datos['tipo_producto_id'])) {
                $errores[] = 'El tipo de producto es requerido';
            }

            if (!empty($errores)) {
                $this->redirect("/controllers/ProductoController.php?action=edit&id=$id", 'Error en los datos: ' . implode(', ', $errores), 'error');
                return;
            }

            $resultado = $this->productoModel->actualizar($id, $datos);
            
            if ($resultado) {
                $this->redirect("/controllers/ProductoController.php?action=edit&id=$id", 'Producto actualizado correctamente', 'success');
            } else {
                $this->redirect("/controllers/ProductoController.php?action=edit&id=$id", 'Error al actualizar producto', 'error');
            }

        } catch (Exception $e) {
            error_log("Error en ProductoController::update(): " . $e->getMessage());
            $this->redirect("/controllers/ProductoController.php?action=edit&id=$id", 'Error interno: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Eliminar producto (soft delete)
     */
    public function delete($id) {
        try {
            // Verificar acceso al módulo de productos
            $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);

            $resultado = $this->productoModel->eliminar($id);
            
            if ($resultado) {
                $this->redirect('/controllers/ProductoController.php?action=index', 'Producto eliminado correctamente', 'success');
            } else {
                $this->redirect('/controllers/ProductoController.php?action=index', 'Error al eliminar producto', 'error');
            }

        } catch (Exception $e) {
            error_log("Error en ProductoController::delete(): " . $e->getMessage());
            $this->redirect('/controllers/ProductoController.php?action=index', 'Error interno del servidor', 'error');
        }
    }

    /**
     * Gestión de tipos de productos (submódulo)
     */
    public function indexTipos() {
        // Verificar acceso al módulo de productos
        $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);
        
        $tiposProducto = $this->productoModel->obtenerTiposProducto();
        
        $datos = [
            'pageTitle' => 'Tipos de Producto',
            'tiposProducto' => $tiposProducto,
            'usuario' => $this->usuario
        ];

        // Por ahora redirigir al listado principal, en el futuro crear vista específica
        $this->redirect('/controllers/ProductoController.php?action=index', 'Función en desarrollo', 'info');
    }

    /**
     * Validar datos - Extiende la validación de BaseController para productos
     */
    protected function validarDatos($datos, $reglas) {
        // Usar la validación base del padre
        $errores = parent::validarDatos($datos, $reglas);
        
        // Agregar validaciones específicas para productos
        foreach ($reglas as $campo => $regla) {
            $valor = $datos[$campo] ?? null;
            
            // Validar valor mínimo para campos numéricos
            if (!empty($valor) && isset($regla['min_value']) && is_numeric($valor)) {
                if (floatval($valor) < $regla['min_value']) {
                    $errores[$campo] = "El campo {$campo} debe ser mayor a {$regla['min_value']}";
                }
            }
        }
        
        return $errores;
    }

    /**
     * Procesar creación de producto - método privado
     */
    private function procesarCreacion() {
        try {
            // Verificar acceso al módulo de productos
            $this->verificarAccesoModulo(ModuleConfig::PRODUCTOS);

            $datos = [
                'ancho' => $_POST['ancho'] ?? '',
                'largo' => $_POST['largo'] ?? '',
                'cantidad_disponible' => $_POST['cantidad_disponible'] ?? 0,
                'stock_minimo' => $_POST['stock_minimo'] ?? 1,
                'precio_unitario' => $_POST['precio_unitario'] ?? '',
                'tipo_producto_id' => $_POST['tipo_producto_id'] ?? '',
            ];

            // Validar datos con nombres de campos correctos
            $reglas = [
                'ancho' => ['required' => true, 'type' => 'numeric'],
                'largo' => ['required' => true, 'type' => 'numeric'],
                'precio_unitario' => ['required' => true, 'type' => 'numeric', 'min_value' => 0.01],
                'tipo_producto_id' => ['required' => true, 'type' => 'numeric']
            ];

            $errores = $this->validarDatos($datos, $reglas);
            if (!empty($errores)) {
                // BaseController devuelve array asociativo, convertir a string
                $mensajesError = array_values($errores); // Obtener solo los mensajes de error
                $this->redirect('/controllers/ProductoController.php?action=create', 'Error en los datos: ' . implode(', ', $mensajesError), 'error');
                return;
            }

            $productoId = $this->productoModel->crear($datos);
            
            if ($productoId) {
                $this->redirect('/controllers/ProductoController.php?action=index', 'Producto creado correctamente', 'success');
            } else {
                $this->redirect('/controllers/ProductoController.php?action=create', 'Error al crear producto', 'error');
            }

        } catch (Exception $e) {
            error_log("Error en ProductoController::procesarCreacion(): " . $e->getMessage());
            $this->redirect('/controllers/ProductoController.php?action=create', 'Error interno: ' . $e->getMessage(), 'error');
        }
    }
}

// Auto-ejecución siguiendo patrón estándar
new ProductoController();
?>