<?php
require_once __DIR__ . '/../config/database.php';

class Material {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Listar todos los materiales activos
     * @param array $filtros Filtros opcionales
     * @return array Lista de materiales
     */
    public function listar($filtros = []) {
        try {
            $condiciones = ["activo = 1"];
            $parametros = [];
            $contador = 1;

            // Filtros básicos
            if (!empty($filtros['nombre'])) {
                $condiciones[] = "nombre LIKE ?";
                $parametros[$contador++] = '%' . $filtros['nombre'] . '%';
            }

            $query = "SELECT 
                        id,
                        codigo_material as codigo,
                        nombre,
                        descripcion,
                        cantidad_stock,
                        costo_unitario,
                        precio_venta,
                        stock_minimo,
                        stock_maximo,
                        ubicacion_deposito,
                        unidad_medida_id,
                        activo
                    FROM materiales 
                    WHERE " . implode(' AND ', $condiciones) . 
                    " ORDER BY nombre";
            
            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice, $valor);
            }

            $stmt->execute();
            $materiales = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Agregar campos calculados para la vista
            foreach ($materiales as &$material) {
                $material['stock_actual'] = $material['cantidad_stock'];
                $material['precio_unitario'] = $material['costo_unitario'];
                $material['estado_stock'] = $this->calcularEstadoStock($material);
                $material['categoria'] = 'MATERIAL'; 
                $material['unidad_medida_simbolo'] = 'kg'; // Placeholder
            }
            
            return $materiales;

        } catch (Exception $e) {
            error_log("Error al listar materiales: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calcular estado del stock basado en cantidad actual y mínima
     */
    private function calcularEstadoStock($material) {
        $actual = floatval($material['cantidad_stock'] ?? 0);
        $minimo = floatval($material['stock_minimo'] ?? 1);
        
        if ($actual <= 0) {
            return 'AGOTADO';
        } elseif ($actual <= $minimo) {
            return 'BAJO';
        } else {
            return 'OK';
        }
    }

    /**
     * Obtener un material por ID
     * @param int $id ID del material
     * @return array|false Datos del material o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT 
                        id,
                        codigo_material,
                        nombre,
                        descripcion,
                        cantidad_stock,
                        costo_unitario,
                        precio_venta,
                        stock_minimo,
                        stock_maximo,
                        ubicacion_deposito,
                        unidad_medida_id,
                        activo
                    FROM materiales 
                    WHERE id = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $material = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($material) {
                // Agregar campos calculados
                $material['stock_actual'] = $material['cantidad_stock'];
                $material['estado_stock'] = $this->calcularEstadoStock($material);
            }
            
            return $material;
        } catch (Exception $e) {
            error_log("Error al obtener material por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar material por código
     * @param string $codigo Código del material
     * @return array|false Datos del material o false si no existe
     */
    public function obtenerPorCodigo($codigo) {
        try {
            $query = "SELECT * FROM materiales WHERE codigo_material = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $codigo);
            $stmt->execute();
            $material = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($material) {
                // Agregar campos que espera la vista
                $material['material_id'] = $material['id'];
                $material['stock_actual'] = $material['cantidad_stock'] ?? 0;
                $material['unidad_nombre'] = 'Unidad';
                $material['estado_stock'] = $this->calcularEstadoStock($material);
                $material['categoria'] = 'GENERAL';
            }
            
            return $material;
        } catch (Exception $e) {
            error_log("Error al buscar material por código: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear un nuevo material
     * @param array $datos Datos del material
     * @return int|false ID del material creado o false en caso de error
     */
    public function crear($datos) {
        try {
            // Validar datos obligatorios
            if (empty($datos['nombre'])) {
                throw new Exception("El nombre del material es obligatorio");
            }

            if (empty($datos['unidad_medida_id'])) {
                throw new Exception("La unidad de medida es obligatoria");
            }

            // Verificar que la unidad de medida existe
            if (!$this->verificarUnidadMedida($datos['unidad_medida_id'])) {
                throw new Exception("La unidad de medida especificada no existe");
            }

            $this->db->beginTransaction();

            $query = "INSERT INTO materiales (
                codigo_material, nombre, descripcion, unidad_medida_id,
                cantidad_stock, costo_unitario, precio_venta, stock_minimo,
                stock_maximo, ubicacion_deposito, activo, fecha_creacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";

            $stmt = $this->db->prepare($query);
            
            // Usar bindValue en lugar de bindParam para valores directos
            $stmt->bindValue(1, $datos['codigo_barras'] ?? null);
            $stmt->bindValue(2, $datos['nombre']);
            $stmt->bindValue(3, $datos['descripcion'] ?? null);
            $stmt->bindValue(4, $datos['unidad_medida_id'], PDO::PARAM_INT);
            $stmt->bindValue(5, $datos['cantidad_stock'] ?? 0.0);
            $stmt->bindValue(6, $datos['costo_unitario'] ?? 0.0);
            $stmt->bindValue(7, $datos['precio_venta'] ?? null);
            $stmt->bindValue(8, $datos['stock_minimo'] ?? 0.0);
            $stmt->bindValue(9, $datos['stock_maximo'] ?? null);
            $stmt->bindValue(10, $datos['ubicacion_deposito'] ?? null);

            $stmt->execute();
            $materialId = $this->db->lastInsertId();

            $this->db->commit();
            return $materialId;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear material: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar un material existente
     * @param int $id ID del material
     * @param array $datos Datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datos) {
        try {
            // Verificar que el material existe
            $materialExistente = $this->obtenerMaterialBasico($id);
            if (!$materialExistente) {
                throw new Exception("El material no existe");
            }

            // Validar unidad de medida si se proporciona
            if (!empty($datos['unidad_medida_id']) && !$this->verificarUnidadMedida($datos['unidad_medida_id'])) {
                throw new Exception("La unidad de medida especificada no existe");
            }

            $this->db->beginTransaction();

            // Usar solo campos que realmente existen en la tabla materiales
            $query = "UPDATE materiales SET 
                codigo_material = ?, nombre = ?, descripcion = ?, 
                unidad_medida_id = ?, costo_unitario = ?, 
                stock_minimo = ?, stock_maximo = ?, ubicacion_deposito = ?
                WHERE id = ? AND activo = 1";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(1, $datos['codigo_barras'] ?? $materialExistente['codigo_barras']);
            $stmt->bindValue(2, $datos['nombre'] ?? $materialExistente['nombre']);
            $stmt->bindValue(3, $datos['descripcion'] ?? $materialExistente['descripcion']);
            $stmt->bindValue(4, $datos['unidad_medida_id'] ?? $materialExistente['unidad_medida_id'], PDO::PARAM_INT);
            $stmt->bindValue(5, $datos['costo_unitario'] ?? $materialExistente['costo_unitario']);
            $stmt->bindValue(6, $datos['stock_minimo'] ?? $materialExistente['stock_minimo']);
            $stmt->bindValue(7, $datos['stock_maximo'] ?? $materialExistente['stock_maximo']);
            $stmt->bindValue(8, $datos['ubicacion_deposito'] ?? $materialExistente['ubicacion_deposito']);
            $stmt->bindValue(9, $id, PDO::PARAM_INT);

            $resultado = $stmt->execute();
            $this->db->commit();
            
            return $resultado;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar material: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar (desactivar) un material
     * @param int $id ID del material
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            // Verificar que el material existe
            $material = $this->obtenerPorId($id);
            if (!$material) {
                throw new Exception("El material no existe");
            }

            // Marcar material como inactivo
            $query = "UPDATE materiales SET activo = 0 WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error al eliminar material: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar stock del material
     * @param int $materialId ID del material
     * @param float $cantidad Cantidad del movimiento
     * @param string $tipo 'entrada' o 'salida'
     * @param string $motivo Motivo del movimiento
     * @return bool True si se actualizó correctamente
     */
    public function actualizarStock($materialId, $cantidad, $tipo, $motivo = '') {
        try {
            $this->db->beginTransaction();

            $cantidadMovimiento = ($tipo === 'salida') ? -$cantidad : $cantidad;

            // Actualizar stock del material
            $query = "UPDATE materiales SET 
                cantidad_stock = cantidad_stock + ?
                WHERE id = ? AND activo = 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $cantidadMovimiento);
            $stmt->bindValue(2, $materialId, PDO::PARAM_INT);
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
     * Buscar materiales para AJAX
     * @param string $termino Término de búsqueda
     * @param string $categoria Categoría opcional
     * @return array Lista de materiales encontrados
     */
    public function buscar($termino = '', $categoria = '') {
        try {
            $condiciones = ["material_activo = 1"];
            $parametros = [];
            $contador = 1;

            if (!empty($termino)) {
                $condiciones[] = "(nombre LIKE ? OR codigo_material LIKE ?)";
                $parametros[$contador++] = '%' . $termino . '%';
                $parametros[$contador++] = '%' . $termino . '%';
            }

            if (!empty($categoria)) {
                $condiciones[] = "categoria LIKE ?";
                $parametros[$contador++] = '%' . $categoria . '%';
            }

            $query = "SELECT * FROM vista_materiales_completa WHERE " . implode(' AND ', $condiciones) . 
                    " ORDER BY categoria, nombre LIMIT 50";

            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice, $valor);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al buscar materiales: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener categorías de materiales
     * @return array Lista de categorías predefinidas
     */
    public function obtenerCategorias() {
        try {
            // Por ahora devolvemos categorías predefinidas
            // Más tarde se puede hacer dinámico cuando se agregue la columna categoria
            return [
                'CEMENTO',
                'HIERRO',
                'ARENA',
                'GRAVA',
                'LADRILLOS',
                'BLOQUES',
                'VIGAS',
                'COLUMNAS',
                'ACERO',
                'HERRAMIENTAS',
                'OTROS'
            ];

        } catch (Exception $e) {
            error_log("Error al obtener categorías: " . $e->getMessage());
            return ['GENERAL'];
        }
    }

    /**
     * Obtener lista de unidades de medida disponibles
     * @return array Lista de unidades de medida predefinidas
     */
    public function obtenerUnidadesMedida() {
        try {
            // Verificar si existe tabla unidades_medida
            $query = "SHOW TABLES LIKE 'unidades_medida'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Si existe la tabla, intentar obtener datos (ajustar columnas según estructura real)
                try {
                    $query = "SELECT id, nombre FROM unidades_medida WHERE activo = 1 ORDER BY nombre";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute();
                    $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Agregar símbolo por defecto si no existe
                    foreach ($unidades as &$unidad) {
                        if (!isset($unidad['simbolo'])) {
                            $unidad['simbolo'] = substr($unidad['nombre'], 0, 3);
                        }
                    }
                    
                    return $unidades;
                } catch (Exception $e) {
                    // Si falla, usar unidades predefinidas
                }
            }
            
            // Unidades predefinidas como fallback
            return [
                ['id' => 1, 'nombre' => 'Kilogramos', 'simbolo' => 'kg'],
                ['id' => 2, 'nombre' => 'Gramos', 'simbolo' => 'g'],
                ['id' => 3, 'nombre' => 'Litros', 'simbolo' => 'l'],
                ['id' => 4, 'nombre' => 'Metros', 'simbolo' => 'm'],
                ['id' => 5, 'nombre' => 'Centímetros', 'simbolo' => 'cm'],
                ['id' => 6, 'nombre' => 'Unidades', 'simbolo' => 'un'],
                ['id' => 7, 'nombre' => 'Metros cuadrados', 'simbolo' => 'm²'],
                ['id' => 8, 'nombre' => 'Metros cúbicos', 'simbolo' => 'm³'],
                ['id' => 9, 'nombre' => 'Toneladas', 'simbolo' => 't'],
                ['id' => 10, 'nombre' => 'Bolsas', 'simbolo' => 'bolsa']
            ];

        } catch (Exception $e) {
            error_log("Error al obtener unidades de medida: " . $e->getMessage());
            return [['id' => 1, 'nombre' => 'Unidades', 'simbolo' => 'un']];
        }
    }

    /**
     * Verificar si un material puede ser eliminado
     * @param int $id ID del material
     * @return bool True si puede eliminarse
     */
    public function puedeEliminar($id) {
        try {
            // Por ahora asumimos que siempre puede eliminarse (desactivarse)
            // En el futuro se pueden agregar validaciones de relaciones
            return true;

        } catch (Exception $e) {
            error_log("Error al verificar si puede eliminar material: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si una unidad de medida existe
     * @param int $unidadId ID de la unidad
     * @return bool True si existe
     */
    private function verificarUnidadMedida($unidadId) {
        try {
            $query = "SELECT COUNT(*) FROM unidades_medida WHERE id = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $unidadId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener datos básicos del material (sin vista)
     * @param int $id ID del material
     * @return array|false Datos básicos del material
     */
    private function obtenerMaterialBasico($id) {
        try {
            $query = "SELECT * FROM materiales WHERE id = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>