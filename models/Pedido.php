<?php
/**
 * Modelo Pedido - Sistema Premoldeados
 * 
 * Gestiona la entidad pedidos con sus items asociados.
 * Incluye validaciones, cálculo de totales y gestión de estados.
 * 
 * Tabla principal: pedidos_new
 * Tabla relacionada: pedidos_items_new
 * Vista optimizada: vista_pedidos_completa
 * 
 * @version 1.0 - Fase 4 Desbloqueada
 * @date 2025-09-15
 */

require_once __DIR__ . '/../config/database.php';

class Pedido {
    private $db;
    
    // Propiedades del pedido
    public $id;
    public $numero_pedido;
    public $cliente_id;
    public $fecha_pedido;
    public $fecha_entrega_solicitada;
    public $fecha_entrega_real;
    public $estado_pedido_id;
    public $forma_entrega_id;
    public $subtotal;
    public $descuentos;
    public $impuestos;
    public $total;
    public $observaciones;
    public $usuario_creacion;
    public $usuario_modificacion;
    public $fecha_creacion;
    public $fecha_modificacion;
    public $activo;
    
    // Estados de pedido comunes
    const ESTADO_BORRADOR = 1;
    const ESTADO_CONFIRMADO = 2;
    const ESTADO_EN_PRODUCCION = 3;
    const ESTADO_LISTO = 4;
    const ESTADO_ENTREGADO = 5;
    const ESTADO_CANCELADO = 6;
    
    public function __construct($db = null) {
        $this->db = $db ?: Database::getInstance()->getConnection();
    }
    
    /**
     * Listar pedidos con información completa
     * Utiliza la vista optimizada vista_pedidos_completa
     */
    public function listar($filtros = [], $limit = null, $offset = 0) {
        try {
            $sql = "SELECT * FROM vista_pedidos_completa WHERE 1=1";
            $params = [];
            
            // Aplicar filtros
            if (!empty($filtros['cliente_id'])) {
                $sql .= " AND cliente_id = ?";
                $params[] = $filtros['cliente_id'];
            }
            
            if (!empty($filtros['estado_pedido_id'])) {
                $sql .= " AND estado_pedido_id = ?";
                $params[] = $filtros['estado_pedido_id'];
            }
            
            if (!empty($filtros['fecha_desde'])) {
                $sql .= " AND fecha_pedido >= ?";
                $params[] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $sql .= " AND fecha_pedido <= ?";
                $params[] = $filtros['fecha_hasta'];
            }
            
            if (!empty($filtros['numero_pedido'])) {
                $sql .= " AND numero_pedido LIKE ?";
                $params[] = "%{$filtros['numero_pedido']}%";
            }
            
            if (!empty($filtros['prioridad'])) {
                $sql .= " AND prioridad = ?";
                $params[] = $filtros['prioridad'];
            }
            
            // Ordenar por fecha más reciente
            $sql .= " ORDER BY fecha_pedido DESC, id DESC";
            
            // Aplicar limit y offset si se especifican
            if ($limit) {
                $sql .= " LIMIT " . (int)$limit;
                if ($offset) {
                    $sql .= " OFFSET " . (int)$offset;
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Pedido::listar: " . $e->getMessage());
            throw new Exception("Error al obtener pedidos: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener pedido por ID con información completa
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM vista_pedidos_completa WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($pedido) {
                // Cargar items del pedido
                $pedido['items'] = $this->obtenerItemsPorPedidoId($id);
            }
            
            return $pedido;
            
        } catch (Exception $e) {
            error_log("Error en Pedido::obtenerPorId: " . $e->getMessage());
            throw new Exception("Error al obtener pedido: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener items de un pedido
     */
    public function obtenerItemsPorPedidoId($pedido_id) {
        try {
            $sql = "SELECT 
                        pi.*,
                        COALESCE(m.nombre, p.nombre) as item_nombre,
                        COALESCE(m.descripcion, p.descripcion) as item_descripcion,
                        COALESCE(um.nombre, 'Unidad') as unidad_medida
                    FROM pedidos_items_new pi
                        LEFT JOIN materiales_new m ON pi.material_id = m.id
                        LEFT JOIN productos p ON pi.producto_id = p.id
                        LEFT JOIN unidades_medida um ON m.unidad_medida_id = um.id
                    WHERE pi.pedido_id = ? AND pi.activo = 1
                    ORDER BY pi.id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$pedido_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Pedido::obtenerItemsPorPedidoId: " . $e->getMessage());
            throw new Exception("Error al obtener items del pedido: " . $e->getMessage());
        }
    }
    
    /**
     * Crear nuevo pedido con transacción
     */
    public function crear($datos, $items = []) {
        try {
            $this->db->beginTransaction();
            
            // Validar datos del pedido
            $this->validarDatos($datos);
            
            // Insertar pedido principal
            $sql = "INSERT INTO pedidos_new (
                        cliente_id, fecha_pedido, fecha_entrega_solicitada, 
                        estado_pedido_id, forma_entrega_id, observaciones, 
                        usuario_creacion, activo
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $datos['cliente_id'],
                $datos['fecha_pedido'],
                $datos['fecha_entrega_solicitada'] ?? null,
                $datos['estado_pedido_id'] ?? self::ESTADO_BORRADOR,
                $datos['forma_entrega_id'] ?? null,
                $datos['observaciones'] ?? null,
                $_SESSION['usuario_id'] ?? null
            ]);
            
            $pedido_id = $this->db->lastInsertId();
            
            // Insertar items si existen
            if (!empty($items)) {
                $this->insertarItems($pedido_id, $items);
            }
            
            // Actualizar totales del pedido
            $this->actualizarTotales($pedido_id);
            
            $this->db->commit();
            
            return $pedido_id;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en Pedido::crear: " . $e->getMessage());
            throw new Exception("Error al crear pedido: " . $e->getMessage());
        }
    }
    
    /**
     * Actualizar pedido existente
     */
    public function actualizar($id, $datos, $items = null) {
        try {
            $this->db->beginTransaction();
            
            // Validar datos del pedido
            $this->validarDatos($datos);
            
            // Actualizar pedido principal
            $sql = "UPDATE pedidos_new SET 
                        cliente_id = ?, fecha_pedido = ?, fecha_entrega_solicitada = ?,
                        estado_pedido_id = ?, forma_entrega_id = ?, observaciones = ?,
                        usuario_modificacion = ?, fecha_modificacion = NOW()
                    WHERE id = ? AND activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $datos['cliente_id'],
                $datos['fecha_pedido'],
                $datos['fecha_entrega_solicitada'] ?? null,
                $datos['estado_pedido_id'],
                $datos['forma_entrega_id'] ?? null,
                $datos['observaciones'] ?? null,
                $_SESSION['usuario_id'] ?? null,
                $id
            ]);
            
            if (!$resultado) {
                throw new Exception("No se pudo actualizar el pedido");
            }
            
            // Actualizar items si se proporcionan
            if ($items !== null) {
                // Eliminar items existentes
                $this->eliminarItems($id);
                
                // Insertar nuevos items
                if (!empty($items)) {
                    $this->insertarItems($id, $items);
                }
            }
            
            // Actualizar totales del pedido
            $this->actualizarTotales($id);
            
            $this->db->commit();
            
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en Pedido::actualizar: " . $e->getMessage());
            throw new Exception("Error al actualizar pedido: " . $e->getMessage());
        }
    }
    
    /**
     * Eliminar pedido (soft delete)
     */
    public function eliminar($id) {
        try {
            $this->db->beginTransaction();
            
            // Soft delete del pedido
            $sql = "UPDATE pedidos_new SET activo = 0, usuario_modificacion = ?, fecha_modificacion = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $_SESSION['usuario_id'] ?? null,
                $id
            ]);
            
            if (!$resultado) {
                throw new Exception("No se pudo eliminar el pedido");
            }
            
            // Soft delete de los items
            $sql = "UPDATE pedidos_items_new SET activo = 0 WHERE pedido_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            $this->db->commit();
            
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en Pedido::eliminar: " . $e->getMessage());
            throw new Exception("Error al eliminar pedido: " . $e->getMessage());
        }
    }
    
    /**
     * Cambiar estado del pedido
     */
    public function cambiarEstado($id, $nuevo_estado_id, $observaciones = null) {
        try {
            // Validar que el estado existe
            if (!$this->validarEstado($nuevo_estado_id)) {
                throw new Exception("Estado de pedido no válido");
            }
            
            $sql = "UPDATE pedidos_new SET 
                        estado_pedido_id = ?, 
                        observaciones = CASE 
                            WHEN ? IS NOT NULL THEN CONCAT(COALESCE(observaciones, ''), '\n[', NOW(), '] Estado cambiado: ', ?)
                            ELSE observaciones 
                        END,
                        usuario_modificacion = ?, 
                        fecha_modificacion = NOW()
                    WHERE id = ? AND activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $nuevo_estado_id,
                $observaciones,
                $observaciones,
                $_SESSION['usuario_id'] ?? null,
                $id
            ]);
            
            if (!$resultado) {
                throw new Exception("No se pudo cambiar el estado del pedido");
            }
            
            // Si se marca como entregado, actualizar fecha de entrega real
            if ($nuevo_estado_id == self::ESTADO_ENTREGADO) {
                $sql = "UPDATE pedidos_new SET fecha_entrega_real = CURDATE() WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error en Pedido::cambiarEstado: " . $e->getMessage());
            throw new Exception("Error al cambiar estado: " . $e->getMessage());
        }
    }
    
    /**
     * Insertar items del pedido
     */
    private function insertarItems($pedido_id, $items) {
        foreach ($items as $item) {
            $this->validarItem($item);
            
            $sql = "INSERT INTO pedidos_items_new (
                        pedido_id, material_id, producto_id, cantidad, 
                        precio_unitario, descuento_porcentaje, descuento_monto, 
                        observaciones, activo
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $pedido_id,
                $item['material_id'] ?? null,
                $item['producto_id'] ?? null,
                $item['cantidad'],
                $item['precio_unitario'],
                $item['descuento_porcentaje'] ?? 0,
                $item['descuento_monto'] ?? 0,
                $item['observaciones'] ?? null
            ]);
        }
    }
    
    /**
     * Eliminar items del pedido
     */
    private function eliminarItems($pedido_id) {
        $sql = "UPDATE pedidos_items_new SET activo = 0 WHERE pedido_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pedido_id]);
    }
    
    /**
     * Actualizar totales del pedido basado en sus items
     */
    private function actualizarTotales($pedido_id) {
        $sql = "UPDATE pedidos_new SET 
                    subtotal = (
                        SELECT COALESCE(SUM(subtotal), 0) 
                        FROM pedidos_items_new 
                        WHERE pedido_id = ? AND activo = 1
                    ),
                    total = (
                        SELECT COALESCE(SUM(subtotal), 0) 
                        FROM pedidos_items_new 
                        WHERE pedido_id = ? AND activo = 1
                    ) - descuentos + impuestos
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pedido_id, $pedido_id, $pedido_id]);
    }
    
    /**
     * Validar datos del pedido
     */
    private function validarDatos($datos) {
        $errores = [];
        
        if (empty($datos['cliente_id'])) {
            $errores[] = "El cliente es obligatorio";
        }
        
        if (empty($datos['fecha_pedido'])) {
            $errores[] = "La fecha del pedido es obligatoria";
        }
        
        if (!empty($datos['fecha_entrega_solicitada']) && $datos['fecha_entrega_solicitada'] < $datos['fecha_pedido']) {
            $errores[] = "La fecha de entrega no puede ser anterior a la fecha del pedido";
        }
        
        if (!empty($errores)) {
            throw new Exception("Errores de validación: " . implode(", ", $errores));
        }
    }
    
    /**
     * Validar item del pedido
     */
    private function validarItem($item) {
        $errores = [];
        
        if (empty($item['material_id']) && empty($item['producto_id'])) {
            $errores[] = "El item debe referenciar un material o producto";
        }
        
        if (!empty($item['material_id']) && !empty($item['producto_id'])) {
            $errores[] = "El item no puede referenciar material y producto a la vez";
        }
        
        if (empty($item['cantidad']) || $item['cantidad'] <= 0) {
            $errores[] = "La cantidad debe ser mayor a cero";
        }
        
        if (empty($item['precio_unitario']) || $item['precio_unitario'] <= 0) {
            $errores[] = "El precio unitario debe ser mayor a cero";
        }
        
        if (!empty($errores)) {
            throw new Exception("Errores de validación en item: " . implode(", ", $errores));
        }
    }
    
    /**
     * Validar que existe el estado
     */
    private function validarEstado($estado_id) {
        $sql = "SELECT COUNT(*) FROM estado_pedido WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estado_id]);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtener estados de pedido disponibles
     */
    public function obtenerEstados() {
        try {
            $sql = "SELECT id, nombre FROM estado_pedido ORDER BY id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Pedido::obtenerEstados: " . $e->getMessage());
            throw new Exception("Error al obtener estados: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener estadísticas de pedidos
     */
    public function obtenerEstadisticas($fecha_desde = null, $fecha_hasta = null) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_pedidos,
                        SUM(total) as monto_total,
                        AVG(total) as monto_promedio,
                        COUNT(CASE WHEN prioridad = 'URGENTE' THEN 1 END) as pedidos_urgentes,
                        COUNT(CASE WHEN prioridad = 'VENCIDO' THEN 1 END) as pedidos_vencidos
                    FROM vista_pedidos_completa
                    WHERE 1=1";
            
            $params = [];
            
            if ($fecha_desde) {
                $sql .= " AND fecha_pedido >= ?";
                $params[] = $fecha_desde;
            }
            
            if ($fecha_hasta) {
                $sql .= " AND fecha_pedido <= ?";
                $params[] = $fecha_hasta;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en Pedido::obtenerEstadisticas: " . $e->getMessage());
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }
}
?>
