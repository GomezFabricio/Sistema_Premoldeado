<?php
/**
 * Controlador Pedidos - Sistema Premoldeados
 * 
 * Gestiona todas las operaciones CRUD de pedidos e items.
 * Incluye gestión de estados, validaciones y generación de reportes.
 * 
 * @version 1.0 - Fase 4 Desbloqueada
 * @date 2025-09-15
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../config/modules.php';

class PedidoController extends BaseController {
    private $pedidoModel;
    private $clienteModel;
    private $materialModel;
    
    public function __construct() {
        parent::__construct();
        $this->pedidoModel = new Pedido();
        $this->clienteModel = new Cliente();
        $this->materialModel = new Material();
        
        // Auto-ejecución si hay action en GET
        if (isset($_GET['action'])) {
            $this->handleRequest();
        }
    }

    /**
     * ✅ NUEVO: Método estándar para manejar acciones GET
     */
    public function handleRequest() {
        // Verificar acceso al módulo de pedidos
        $this->verificarAccesoModulo(ModuleConfig::PEDIDOS);
        
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
            // Submódulos de PedidoController según NavigationController
            case 'indexEstados':
                $this->indexEstados();
                break;
            case 'indexFormasEntrega':
                $this->indexFormasEntrega();
                break;
            case 'indexReservas':
                $this->indexReservas();
                break;
            case 'indexEstadosReserva':
                $this->indexEstadosReserva();
                break;
            case 'indexDevoluciones':
                $this->indexDevoluciones();
                break;
            case 'indexEstadosDevoluciones':
                $this->indexEstadosDevoluciones();
                break;
            default:
                $this->index();
                break;
        }
    }
    
    /**
     * Mostrar listado de pedidos con filtros
     */
    public function index() {
        try {
            // Obtener parámetros de filtros
            $filtros = [
                'cliente_id' => $_GET['cliente_id'] ?? '',
                'estado_pedido_id' => $_GET['estado_pedido_id'] ?? '',
                'fecha_desde' => $_GET['fecha_desde'] ?? '',
                'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
                'numero_pedido' => $_GET['numero_pedido'] ?? '',
                'prioridad' => $_GET['prioridad'] ?? ''
            ];
            
            // Filtrar valores vacíos
            $filtros = array_filter($filtros, function($value) {
                return $value !== '' && $value !== null;
            });
            
            // Paginación
            $pagina = (int)($_GET['pagina'] ?? 1);
            $por_pagina = 20;
            $offset = ($pagina - 1) * $por_pagina;
            
            // Obtener pedidos
            $pedidos = $this->pedidoModel->listar($filtros, $por_pagina, $offset);
            
            // Obtener datos para filtros
            $clientes = $this->clienteModel->listar();
            $estados = $this->pedidoModel->obtenerEstados();
            
            // Obtener estadísticas
            $estadisticas = $this->pedidoModel->obtenerEstadisticas(
                $filtros['fecha_desde'] ?? null,
                $filtros['fecha_hasta'] ?? null
            );
            
            $this->datos = [
                'titulo' => 'Gestión de Pedidos',
                'items' => $pedidos,
                'clientes' => $clientes,
                'estados' => $estados,
                'filtros' => $filtros,
                'estadisticas' => $estadisticas,
                'pagina_actual' => $pagina
            ];
            $this->render('pedidos/listado_pedidos', $this->datos);
            
        } catch (Exception $e) {
            $this->manejarError("Error al cargar pedidos", $e);
        }
    }
    
    /**
     * Mostrar formulario para crear pedido
     */
    public function crear() {
        try {
            $clientes = $this->clienteModel->listar();
            $estados = $this->pedidoModel->obtenerEstados();
            $materiales = $this->materialModel->listar();
            
            // Obtener formas de entrega
            $formasEntrega = $this->obtenerFormasEntrega();
            
            $this->datos = [
                'titulo' => 'Crear Nuevo Pedido',
                'clientes' => $clientes,
                'estados' => $estados,
                'materiales' => $materiales,
                'formas_entrega' => $formasEntrega,
                'accion' => 'crear'
            ];
            
            $this->render('pedidos/crear_pedido', $this->datos);
            
        } catch (Exception $e) {
            $this->manejarError("Error al cargar formulario de pedido", $e);
        }
    }
    
    /**
     * Procesar creación de pedido
     */
    public function store() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }
            
            $datos = [
                'cliente_id' => $_POST['cliente_id'] ?? '',
                'fecha_pedido' => $_POST['fecha_pedido'] ?? date('Y-m-d'),
                'fecha_entrega_solicitada' => $_POST['fecha_entrega_solicitada'] ?? null,
                'estado_pedido_id' => $_POST['estado_pedido_id'] ?? Pedido::ESTADO_BORRADOR,
                'forma_entrega_id' => $_POST['forma_entrega_id'] ?? null,
                'observaciones' => $_POST['observaciones'] ?? null
            ];
            
            // Procesar items del pedido
            $items = $this->procesarItems($_POST);
            
            $pedido_id = $this->pedidoModel->crear($datos, $items);
            
            $this->establecerMensaje("Pedido creado exitosamente con código: PED" . sprintf("%04d", $pedido_id), 'success');
            header("Location: /controllers/PedidoController.php?action=ver&id=$pedido_id");
            exit;
            
        } catch (Exception $e) {
            $this->manejarError("Error al crear pedido", $e);
            $this->crear(); // Volver a mostrar el formulario
        }
    }
    
    /**
     * Mostrar detalle de pedido específico
     */
    public function ver() {
        try {
            $id = $_GET['id'] ?? '';
            
            if (empty($id)) {
                throw new Exception("ID de pedido no proporcionado");
            }
            
            $pedido = $this->pedidoModel->obtenerPorId($id);
            
            if (!$pedido) {
                throw new Exception("Pedido no encontrado");
            }
            
            $this->datos = [
                'titulo' => "Pedido {$pedido['numero_pedido']}",
                'pedido' => $pedido
            ];
            
            $this->render('pedidos/detalle_pedido', $this->datos);
            
        } catch (Exception $e) {
            $this->manejarError("Error al cargar pedido", $e);
        }
    }
    
    /**
     * Mostrar formulario para editar pedido
     */
    public function editar() {
        try {
            $id = $_GET['id'] ?? '';
            
            if (empty($id)) {
                throw new Exception("ID de pedido no proporcionado");
            }
            
            $pedido = $this->pedidoModel->obtenerPorId($id);
            
            if (!$pedido) {
                throw new Exception("Pedido no encontrado");
            }
            
            $clientes = $this->clienteModel->listar();
            $estados = $this->pedidoModel->obtenerEstados();
            $materiales = $this->materialModel->listar();
            $formasEntrega = $this->obtenerFormasEntrega();
            
            $this->datos = [
                'titulo' => "Editar Pedido {$pedido['numero_pedido']}",
                'pedido' => $pedido,
                'clientes' => $clientes,
                'estados' => $estados,
                'materiales' => $materiales,
                'formas_entrega' => $formasEntrega,
                'accion' => 'editar'
            ];
            
            $this->render('pedidos/editar_pedido', $this->datos);
            
        } catch (Exception $e) {
            $this->manejarError("Error al cargar pedido para edición", $e);
        }
    }
    
    /**
     * Procesar actualización de pedido
     */
    public function update() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }
            
            $id = $_POST['id'] ?? '';
            
            if (empty($id)) {
                throw new Exception("ID de pedido no proporcionado");
            }
            
            $datos = [
                'cliente_id' => $_POST['cliente_id'] ?? '',
                'fecha_pedido' => $_POST['fecha_pedido'] ?? '',
                'fecha_entrega_solicitada' => $_POST['fecha_entrega_solicitada'] ?? null,
                'estado_pedido_id' => $_POST['estado_pedido_id'] ?? '',
                'forma_entrega_id' => $_POST['forma_entrega_id'] ?? null,
                'observaciones' => $_POST['observaciones'] ?? null
            ];
            
            // Procesar items del pedido si se enviaron
            $items = null;
            if (isset($_POST['actualizar_items'])) {
                $items = $this->procesarItems($_POST);
            }
            
            $this->pedidoModel->actualizar($id, $datos, $items);
            
            $this->establecerMensaje("Pedido actualizado exitosamente", 'success');
            header("Location: /controllers/PedidoController.php?action=ver&id=$id");
            exit;
            
        } catch (Exception $e) {
            $this->manejarError("Error al actualizar pedido", $e);
            $this->editar(); // Volver a mostrar el formulario
        }
    }
    
    /**
     * Eliminar pedido (soft delete)
     */
    public function eliminar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }
            
            $id = $_POST['id'] ?? '';
            
            if (empty($id)) {
                throw new Exception("ID de pedido no proporcionado");
            }
            
            $this->pedidoModel->eliminar($id);
            
            $this->establecerMensaje("Pedido eliminado exitosamente", 'success');
            header("Location: /controllers/PedidoController.php");
            exit;
            
        } catch (Exception $e) {
            $this->manejarError("Error al eliminar pedido", $e);
        }
    }
    
    /**
     * Cambiar estado de pedido
     */
    public function cambiarEstado() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }
            
            $id = $_POST['id'] ?? '';
            $nuevo_estado = $_POST['nuevo_estado'] ?? '';
            $observaciones = $_POST['observaciones'] ?? null;
            
            if (empty($id) || empty($nuevo_estado)) {
                throw new Exception("Datos incompletos para cambiar estado");
            }
            
            $this->pedidoModel->cambiarEstado($id, $nuevo_estado, $observaciones);
            
            // Respuesta AJAX
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'mensaje' => 'Estado actualizado exitosamente']);
                exit;
            }
            
            $this->establecerMensaje("Estado del pedido actualizado exitosamente", 'success');
            header("Location: /controllers/PedidoController.php?action=ver&id=$id");
            exit;
            
        } catch (Exception $e) {
            // Respuesta AJAX para errores
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
            
            $this->manejarError("Error al cambiar estado", $e);
        }
    }
    
    /**
     * Generar reporte PDF de pedido
     */
    public function generarPDF() {
        try {
            $id = $_GET['id'] ?? '';
            
            if (empty($id)) {
                throw new Exception("ID de pedido no proporcionado");
            }
            
            $pedido = $this->pedidoModel->obtenerPorId($id);
            
            if (!$pedido) {
                throw new Exception("Pedido no encontrado");
            }
            
            // Configurar headers para PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Pedido_' . $pedido['numero_pedido'] . '.pdf"');
            
            // Aquí iría la lógica para generar PDF
            // Por ahora, retornamos un placeholder
            echo "PDF del pedido " . $pedido['numero_pedido'] . " se generaría aquí";
            
        } catch (Exception $e) {
            $this->manejarError("Error al generar PDF", $e);
        }
    }
    
    /**
     * API para buscar materiales/productos (AJAX)
     */
    public function buscarItems() {
        try {
            $termino = $_GET['q'] ?? '';
            $tipo = $_GET['tipo'] ?? 'material'; // material o producto
            
            if (strlen($termino) < 2) {
                echo json_encode([]);
                exit;
            }
            
            $items = [];
            
            if ($tipo === 'material') {
                $materiales = $this->materialModel->buscar($termino);
                foreach ($materiales as $material) {
                    $items[] = [
                        'id' => $material['id'],
                        'tipo' => 'material',
                        'nombre' => $material['nombre'],
                        'descripcion' => $material['descripcion'],
                        'stock' => $material['stock_actual'],
                        'precio' => $material['precio_unitario'] ?? 0,
                        'unidad' => $material['unidad_medida']
                    ];
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode($items);
            exit;
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * Procesar items del formulario
     */
    private function procesarItems($post) {
        $items = [];
        
        if (!isset($post['item_tipo']) || !is_array($post['item_tipo'])) {
            return $items;
        }
        
        foreach ($post['item_tipo'] as $index => $tipo) {
            $item = [
                'material_id' => null,
                'producto_id' => null,
                'cantidad' => $post['item_cantidad'][$index] ?? 0,
                'precio_unitario' => $post['item_precio'][$index] ?? 0,
                'descuento_porcentaje' => $post['item_descuento'][$index] ?? 0,
                'observaciones' => $post['item_observaciones'][$index] ?? null
            ];
            
            if ($tipo === 'material') {
                $item['material_id'] = $post['item_id'][$index] ?? null;
            } else {
                $item['producto_id'] = $post['item_id'][$index] ?? null;
            }
            
            if ($item['cantidad'] > 0 && $item['precio_unitario'] > 0 && 
                ($item['material_id'] || $item['producto_id'])) {
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
    /**
     * Obtener formas de entrega disponibles
     */
    private function obtenerFormasEntrega() {
        try {
            $sql = "SELECT id, nombre FROM forma_entrega WHERE 1=1 ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener formas de entrega: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Manejar errores específicos del controlador
     */
    protected function manejarError($mensaje, $exception) {
        error_log("PedidoController Error: $mensaje - " . $exception->getMessage());
        $this->establecerMensaje($mensaje . ": " . $exception->getMessage(), 'error');
        
        // Si es una petición AJAX, devolver JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $exception->getMessage()]);
            exit;
        }
        
        // Redirigir al listado si no estamos ya ahí
        if (!isset($_GET['action']) || $_GET['action'] !== 'index') {
            header("Location: /controllers/PedidoController.php");
            exit;
        }
    }

    /**
     * ✅ NUEVOS: Métodos estándar CRUD compatibles con ?action=
     */
    
    /**
     * Mostrar formulario de creación (compatible con ?action=create)
     */
    public function create() {
        $this->crear(); // Usar método existente
    }

    /**
     * Procesar creación (compatible con ?action=store) - MÉTODO YA EXISTE
     */

    /**
     * Mostrar formulario de edición (compatible con ?action=edit)
     */
    public function edit($id) {
        $_GET['id'] = $id;
        if (method_exists($this, 'editar')) {
            $this->editar();
        } else {
            $this->redirect('/controllers/PedidoController.php?action=index', 'Función en desarrollo', 'info');
        }
    }

    /**
     * Procesar actualización (compatible con ?action=update) - MÉTODO YA EXISTE
     */

    /**
     * Eliminar pedido (compatible con ?action=delete)
     */
    public function delete($id) {
        if (method_exists($this, 'eliminar')) {
            $_GET['id'] = $id;
            $this->eliminar();
        } else {
            echo json_encode(['success' => false, 'message' => 'Función en desarrollo']);
        }
    }

    /**
     * ✅ SUBMÓDULOS según NavigationController URLs (métodos placeholder)
     */
    public function indexEstados() {
        $this->redirect('/controllers/PedidoController.php?action=index', 'Estados de Pedido - En desarrollo', 'info');
    }

    public function indexFormasEntrega() {
        $this->redirect('/controllers/PedidoController.php?action=index', 'Formas de Entrega - En desarrollo', 'info');
    }

    public function indexReservas() {
        $this->redirect('/controllers/PedidoController.php?action=index', 'Reservas - En desarrollo', 'info');
    }

    public function indexEstadosReserva() {
        $this->redirect('/controllers/PedidoController.php?action=index', 'Estados de Reserva - En desarrollo', 'info');
    }

    public function indexDevoluciones() {
        $this->redirect('/controllers/PedidoController.php?action=index', 'Devoluciones - En desarrollo', 'info');
    }

    public function indexEstadosDevoluciones() {
        $this->redirect('/controllers/PedidoController.php?action=index', 'Estados de Devolución - En desarrollo', 'info');
    }
}

// ✅ Auto-ejecución siguiendo patrón estándar
new PedidoController();
?>
