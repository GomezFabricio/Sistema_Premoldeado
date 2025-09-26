<?php
/**
 * Modelo de Ventas
 * Sistema Premoldeado - Patrón Database::getInstance()
 */

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/ProduccionMaterial.php';

class Venta {
    /**
     * Registrar venta y descontar stock de producción y materiales
     */
    public static function registrarVenta($produccion_id, $cantidad_vendida, $otros_campos = []) {
        $db = Database::getInstance()->getConnection();
        // Registrar la venta
        $stmt = $db->prepare("INSERT INTO venta (produccion_id, cantidad, fecha_venta) VALUES (?, ?, NOW())");
        $stmt->execute([$produccion_id, $cantidad_vendida]);
        // Descontar del stock de producción
        $stmt2 = $db->prepare("UPDATE produccion SET cantidad_disponible = cantidad_disponible - ? WHERE id = ?");
        $stmt2->execute([$cantidad_vendida, $produccion_id]);
        // Descontar materiales asociados
        ProduccionMaterial::descontarMaterialesPorVenta($produccion_id, $cantidad_vendida);
    }
    private $db;

    public function __construct() {
        // ✅ OBLIGATORIO: Usar getInstance() NO new Database()
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Listar todas las ventas activas
     * PATRÓN: Compatible con BaseController->render()
     */
    public function listar($filtros = []) {
        try {
            $condiciones = ["v.activo = 1"];
            $parametros = [];
            $contador = 1;

            // Filtros específicos
            if (!empty($filtros['fecha_desde'])) {
                $condiciones[] = "v.fecha_venta >= ?";
                $parametros[$contador++] = $filtros['fecha_desde'];
            }

            if (!empty($filtros['fecha_hasta'])) {
                $condiciones[] = "v.fecha_venta <= ?";
                $parametros[$contador++] = $filtros['fecha_hasta'];
            }

            if (!empty($filtros['cliente_id'])) {
                $condiciones[] = "v.cliente_id = ?";
                $parametros[$contador++] = $filtros['cliente_id'];
            }

            if (!empty($filtros['metodo_pago'])) {
                $condiciones[] = "v.metodo_pago = ?";
                $parametros[$contador++] = $filtros['metodo_pago'];
            }

            $query = "SELECT 
                        v.id,
                        v.numero_venta,
                        v.fecha_venta,
                        v.cliente_id,
                        CONCAT(p.nombres, ' ', p.apellidos) as nombre_cliente,
                        v.subtotal,
                        v.impuestos,
                        v.total,
                        v.metodo_pago,
                        v.estado,
                        v.observaciones,
                        v.fecha_creacion
                      FROM ventas v
                      LEFT JOIN clientes c ON v.cliente_id = c.id
                      LEFT JOIN personas p ON c.persona_id = p.id
                      WHERE " . implode(' AND ', $condiciones) . "
                      ORDER BY v.fecha_venta DESC";

            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice, $valor);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al listar ventas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener una venta por ID
     * PATRÓN: Compatible con BaseController edit/update
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT 
                        v.*,
                        CONCAT(p.nombres, ' ', p.apellidos) as nombre_cliente,
                        c.email as email_cliente
                      FROM ventas v
                      LEFT JOIN clientes c ON v.cliente_id = c.id
                      LEFT JOIN personas p ON c.persona_id = p.id
                      WHERE v.id = ? AND v.activo = 1";
                      
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener venta por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear nueva venta
     * PATRÓN: Compatible con BaseController store()
     */
    public function crear($datos) {
        try {
            // Validar datos obligatorios
            if (empty($datos['cliente_id']) || empty($datos['total'])) {
                throw new Exception("Cliente y total son obligatorios");
            }

            $this->db->beginTransaction();

            // Generar número de venta automático
            $numeroVenta = $this->generarNumeroVenta();

            $query = "INSERT INTO ventas (
                        numero_venta, fecha_venta, cliente_id, subtotal, 
                        impuestos, total, metodo_pago, estado, observaciones, 
                        activo, fecha_creacion
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(1, $numeroVenta);
            $stmt->bindValue(2, $datos['fecha_venta'] ?? date('Y-m-d'));
            $stmt->bindValue(3, $datos['cliente_id']);
            $stmt->bindValue(4, $datos['subtotal'] ?? $datos['total']);
            $stmt->bindValue(5, $datos['impuestos'] ?? 0);
            $stmt->bindValue(6, $datos['total']);
            $stmt->bindValue(7, $datos['metodo_pago'] ?? 'Efectivo');
            $stmt->bindValue(8, $datos['estado'] ?? 'Pendiente');
            $stmt->bindValue(9, $datos['observaciones'] ?? '');

            $resultado = $stmt->execute();
            $ventaId = $this->db->lastInsertId();

            // Si se proporcionan items de venta, insertarlos
            if (!empty($datos['items']) && is_array($datos['items'])) {
                $this->insertarItemsVenta($ventaId, $datos['items']);
            }

            $this->db->commit();
            return $ventaId;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al crear venta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar venta existente
     * PATRÓN: Compatible con BaseController update()
     */
    public function actualizar($id, $datos) {
        try {
            $this->db->beginTransaction();

            $query = "UPDATE ventas SET 
                        fecha_venta = ?, cliente_id = ?, subtotal = ?, 
                        impuestos = ?, total = ?, metodo_pago = ?, 
                        estado = ?, observaciones = ?, fecha_modificacion = NOW()
                      WHERE id = ?";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(1, $datos['fecha_venta']);
            $stmt->bindValue(2, $datos['cliente_id']);
            $stmt->bindValue(3, $datos['subtotal']);
            $stmt->bindValue(4, $datos['impuestos']);
            $stmt->bindValue(5, $datos['total']);
            $stmt->bindValue(6, $datos['metodo_pago']);
            $stmt->bindValue(7, $datos['estado']);
            $stmt->bindValue(8, $datos['observaciones']);
            $stmt->bindValue(9, $id);

            $resultado = $stmt->execute();

            $this->db->commit();
            return $resultado;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al actualizar venta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar venta (soft delete)
     */
    public function eliminar($id) {
        try {
            $query = "UPDATE ventas SET activo = 0, fecha_eliminacion = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al eliminar venta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function obtenerMetodosPago() {
        return [
            'Efectivo' => 'Efectivo',
            'Tarjeta_Credito' => 'Tarjeta de Crédito',
            'Tarjeta_Debito' => 'Tarjeta de Débito',
            'Transferencia' => 'Transferencia Bancaria',
            'Cheque' => 'Cheque',
            'Credito' => 'Crédito'
        ];
    }

    /**
     * Obtener estados de venta disponibles
     */
    public function obtenerEstados() {
        return [
            'Pendiente' => 'Pendiente',
            'Confirmada' => 'Confirmada',
            'En_Preparacion' => 'En Preparación',
            'Lista' => 'Lista para Entrega',
            'Entregada' => 'Entregada',
            'Cancelada' => 'Cancelada'
        ];
    }

    /**
     * Obtener clientes activos para formularios
     */
    public function obtenerClientes() {
        try {
            $query = "SELECT 
                        c.id, 
                        CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo,
                        p.numero_documento,
                        c.email
                      FROM clientes c
                      INNER JOIN personas p ON c.persona_id = p.id
                      WHERE c.activo = 1
                      ORDER BY p.apellidos, p.nombres";
                      
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generar número de venta automático
     */
    private function generarNumeroVenta() {
        try {
            $query = "SELECT MAX(CAST(SUBSTRING(numero_venta, 3) AS UNSIGNED)) as max_num 
                      FROM ventas 
                      WHERE numero_venta LIKE 'V-%'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $siguiente = ($resultado['max_num'] ?? 0) + 1;
            return 'V-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            error_log("Error al generar número de venta: " . $e->getMessage());
            return 'V-' . date('YmdHis');
        }
    }

    /**
     * Insertar items de venta (detalle)
     */
    private function insertarItemsVenta($ventaId, $items) {
        try {
            $query = "INSERT INTO venta_items (
                        venta_id, producto_id, cantidad, precio_unitario, subtotal
                      ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            
            foreach ($items as $item) {
                $stmt->bindValue(1, $ventaId);
                $stmt->bindValue(2, $item['producto_id']);
                $stmt->bindValue(3, $item['cantidad']);
                $stmt->bindValue(4, $item['precio_unitario']);
                $stmt->bindValue(5, $item['cantidad'] * $item['precio_unitario']);
                $stmt->execute();
            }
            
        } catch (Exception $e) {
            error_log("Error al insertar items de venta: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verificar si existe número de venta
     */
    public function existeNumeroVenta($numeroVenta, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM ventas WHERE numero_venta = ?";
            $parametros = [$numeroVenta];
            
            if ($excludeId) {
                $query .= " AND id != ?";
                $parametros[] = $excludeId;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($parametros);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Error al verificar número de venta: " . $e->getMessage());
            return false;
        }
    }
}
?>