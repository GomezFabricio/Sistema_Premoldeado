
<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener tipos de productos desde la base de datos
     * @return array Lista de tipos de productos activos
     */
    public function obtenerTiposProducto() {
        try {
            $sql = "SELECT id, nombre FROM tipo_producto WHERE activo = '1' ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener tipos de producto: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar productos con filtros opcionales
     * @param array $filtros Filtros opcionales
     * @return array Lista de productos
     */
    public function listar($filtros = []) {
        $sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un producto por ID
     * @param int $id ID del producto
     * @return array|false Datos del producto o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM productos WHERE id = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto) {
                $producto['estado_stock'] = $this->calcularEstadoStock($producto);
                $producto['margen_beneficio'] = $this->calcularMargen($producto);
            }
            
            return $producto;
        } catch (Exception $e) {
            error_log("Error al obtener producto por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar producto por código
     * @param string $codigo Código del producto
     * @return array|false Datos del producto o false si no existe
     */
    public function obtenerPorCodigo($codigo) {
        try {
            $query = "SELECT * FROM productos WHERE codigo = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $codigo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al buscar producto por código: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear un nuevo producto
     * @param array $datos Datos del producto
     * @return int|false ID del producto creado o false en caso de error
     */
    public function crear($datos) {
        try {
            // Validar datos obligatorios según tu estructura real
            if (empty($datos['tipo_producto_id'])) {
                throw new Exception("El tipo de producto es obligatorio");
            }

            if (empty($datos['ancho']) || empty($datos['largo'])) {
                throw new Exception("Las dimensiones (ancho y largo) son obligatorias");
            }

            if ($datos['precio_unitario'] <= 0) {
                throw new Exception("El precio unitario debe ser mayor a 0");
            }

            $this->db->beginTransaction();

            // Insertar en la tabla productos con la estructura real
            $query = "INSERT INTO productos (
                ancho, largo, cantidad_disponible, stock_minimo, 
                precio_unitario, activo, tipo_producto_id
            ) VALUES (?, ?, ?, ?, ?, 1, ?)";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(1, $datos['ancho']);
            $stmt->bindValue(2, $datos['largo']);
            $stmt->bindValue(3, intval($datos['cantidad_disponible'] ?? 0));
            $stmt->bindValue(4, intval($datos['stock_minimo'] ?? 1));
            $stmt->bindValue(5, floatval($datos['precio_unitario']));
            $stmt->bindValue(6, intval($datos['tipo_producto_id']));

            if (!$stmt->execute()) {
                throw new Exception("Error al insertar producto: " . implode(", ", $stmt->errorInfo()));
            }

            $productoId = $this->db->lastInsertId();
            $this->db->commit();

            return $productoId;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en Producto::crear(): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar un producto existente
     * @param int $id ID del producto
     * @param array $datos Datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datos) {
        try {
            // Validar que el producto existe
            $producto = $this->obtenerPorId($id);
            if (!$producto) {
                throw new Exception("El producto no existe");
            }

            // Validar datos obligatorios según tu estructura real
            if (empty($datos['tipo_producto_id'])) {
                throw new Exception("El tipo de producto es obligatorio");
            }

            if (empty($datos['ancho']) || empty($datos['largo'])) {
                throw new Exception("Las dimensiones (ancho y largo) son obligatorias");
            }

            if ($datos['precio_unitario'] <= 0) {
                throw new Exception("El precio unitario debe ser mayor a 0");
            }

            $this->db->beginTransaction();

            // Actualizar en la tabla productos con la estructura real
            $query = "UPDATE productos SET 
                ancho = ?, largo = ?, cantidad_disponible = ?, stock_minimo = ?, 
                precio_unitario = ?, tipo_producto_id = ?
                WHERE id = ?";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(1, $datos['ancho']);
            $stmt->bindValue(2, $datos['largo']);
            $stmt->bindValue(3, intval($datos['cantidad_disponible'] ?? 0));
            $stmt->bindValue(4, intval($datos['stock_minimo'] ?? 1));
            $stmt->bindValue(5, floatval($datos['precio_unitario']));
            $stmt->bindValue(6, intval($datos['tipo_producto_id']));
            $stmt->bindValue(7, $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar producto: " . implode(", ", $stmt->errorInfo()));
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en Producto::actualizar(): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar un producto (marcar como inactivo)
     * @param int $id ID del producto
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            // Verificar que el producto existe
            $producto = $this->obtenerPorId($id);
            if (!$producto) {
                throw new Exception("El producto no existe");
            }

            // Marcar producto como inactivo
            $query = "UPDATE productos SET activo = '0' WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar stock del producto
     * @param int $productoId ID del producto
     * @param float $cantidad Cantidad del movimiento
     * @param string $tipo 'entrada' o 'salida'
     * @param string $motivo Motivo del movimiento
     * @return bool True si se actualizó correctamente
     */
    public function actualizarStock($productoId, $cantidad, $tipo, $motivo = '') {
        try {
            $this->db->beginTransaction();

            $cantidadMovimiento = ($tipo === 'salida') ? -$cantidad : $cantidad;

            // Actualizar stock del producto
            $query = "UPDATE productos SET 
                stock_actual = stock_actual + ?,
                fecha_modificacion = NOW()
                WHERE id = ? AND activo = 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $cantidadMovimiento);
            $stmt->bindValue(2, $productoId, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener unidades de medida predefinidas para productos
     * @return array Lista de unidades de medida
     */
    public function obtenerUnidadesMedida() {
        return [
            'UNIDADES',
            'METROS',
            'METROS_CUADRADOS',
            'METROS_CUBICOS',
            'PIEZAS',
            'JUEGOS',
            'LOTES'
        ];
    }

    /**
     * Verificar si un producto puede ser eliminado
     * @param int $id ID del producto
     * @return bool True si puede eliminarse
     */
    public function puedeEliminar($id) {
        try {
            // Por ahora asumimos que siempre puede eliminarse (desactivarse)
            // En el futuro se pueden agregar validaciones de relaciones
            return true;

        } catch (Exception $e) {
            error_log("Error al verificar si puede eliminar producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcular estado del stock basado en cantidad actual y mínima - ADAPTADO
     */
    private function calcularEstadoStock($producto) {
        $actual = floatval($producto['cantidad_disponible'] ?? 0);
        $minimo = floatval($producto['stock_minimo'] ?? 1);
        
        if ($actual <= 0) {
            return 'AGOTADO';
        } elseif ($actual <= $minimo) {
            return 'BAJO';
        } else {
            return 'OK';
        }
    }

    /**
     * Calcular margen de beneficio
     */
    private function calcularMargen($producto) {
        $costo = floatval($producto['precio_costo'] ?? 0);
        $venta = floatval($producto['precio_venta'] ?? 0);
        
        if ($costo > 0 && $venta > 0) {
            return round((($venta - $costo) / $venta) * 100, 2);
        }
        return 0;
    }

    /**
     * Obtener materiales estándar requeridos para un producto
     */
    public function obtenerMaterialesPorProducto($producto_id) {
        try {
            $sql = "SELECT pm.material_id, m.nombre, pm.cantidad_estandar, m.unidad_medida_id, m.costo_unitario
                    FROM producto_materiales pm
                    INNER JOIN materiales_new m ON pm.material_id = m.id
                    WHERE pm.producto_id = ? AND pm.activo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$producto_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error al obtener materiales por producto: ' . $e->getMessage());
            return [];
        }
    }
}
?>