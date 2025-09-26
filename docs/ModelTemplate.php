<?php
/**
 * Template para Modelos del Sistema Premoldeado
 * 
 * PATRÓN OBLIGATORIO para todos los modelos:
 * - Database::getInstance()->getConnection() (NO new Database())
 * - Métodos CRUD estándar
 * - Manejo de errores con try/catch
 * - Compatibilidad con BaseController
 *
 * REEMPLAZAR:
 * - [MODULO] por el nombre del módulo (ej: Usuario, Cliente)
 * - [tabla] por nombre de tabla principal
 * - [vista_completa] por nombre de vista si existe
 */

require_once __DIR__ . '/../config/database.php';

class [MODULO] {
    private $db;

    public function __construct() {
        // ✅ OBLIGATORIO: Usar getInstance() NO new Database()
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Listar todos los registros activos
     * PATRÓN: Compatible con BaseController->render()
     */
    public function listar($filtros = []) {
        try {
            $condiciones = ["activo = 1"]; // Asumir campo activo estándar
            $parametros = [];
            $contador = 1;

            // Agregar filtros específicos del módulo
            if (!empty($filtros['nombre'])) {
                $condiciones[] = "nombre LIKE ?";
                $parametros[$contador++] = '%' . $filtros['nombre'] . '%';
            }

            // Usar vista completa si existe, sino tabla principal
            $tabla = "[vista_completa]"; // o "[tabla]" si no hay vista
            $query = "SELECT * FROM {$tabla} WHERE " . implode(' AND ', $condiciones) . 
                    " ORDER BY nombre"; // Ajustar ORDER BY según necesidad

            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice, $valor);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al listar [tabla]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un registro por ID
     * PATRÓN: Compatible con BaseController edit/update
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM [vista_completa] WHERE id = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear nuevo registro
     * PATRÓN: Compatible con BaseController store()
     */
    public function crear($datos) {
        try {
            // Validar datos obligatorios específicos del módulo
            if (empty($datos['nombre'])) {
                throw new Exception("El nombre es obligatorio");
            }

            $this->db->beginTransaction();

            $query = "INSERT INTO [tabla] (
                nombre, descripcion, activo, fecha_creacion
                -- Ajustar campos según tabla real
            ) VALUES (?, ?, 1, NOW())";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(1, $datos['nombre']);
            $stmt->bindValue(2, $datos['descripcion'] ?? null);
            // Ajustar bindValue según campos reales

            $stmt->execute();
            $id = $this->db->lastInsertId();

            $this->db->commit();
            return $id;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar registro existente
     * PATRÓN: Compatible con BaseController update()
     */
    public function actualizar($id, $datos) {
        try {
            // Verificar que el registro existe
            $existente = $this->obtenerPorId($id);
            if (!$existente) {
                throw new Exception("Registro no encontrado");
            }

            $this->db->beginTransaction();

            $query = "UPDATE [tabla] SET 
                nombre = ?, descripcion = ?
                -- Ajustar campos según tabla real
                WHERE id = ? AND activo = 1";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindValue(1, $datos['nombre'] ?? $existente['nombre']);
            $stmt->bindValue(2, $datos['descripcion'] ?? $existente['descripcion']);
            $stmt->bindValue(3, $id, PDO::PARAM_INT);
            // Ajustar bindValue según campos reales

            $resultado = $stmt->execute();
            $this->db->commit();
            
            return $resultado;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar (desactivar) registro
     * PATRÓN: Compatible con BaseController delete() AJAX
     */
    public function eliminar($id) {
        try {
            // Verificar que existe
            $registro = $this->obtenerPorId($id);
            if (!$registro) {
                throw new Exception("Registro no encontrado");
            }

            // Desactivar en lugar de eliminar físicamente
            $query = "UPDATE [tabla] SET activo = 0 WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error al eliminar: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verificar si un registro puede ser eliminado
     * PATRÓN: Verificaciones de integridad referencial
     */
    public function puedeEliminar($id) {
        try {
            // Agregar verificaciones específicas del módulo
            // Ejemplo: verificar si está en uso en otras tablas
            
            return true; // Ajustar según lógica del módulo

        } catch (Exception $e) {
            error_log("Error al verificar eliminación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar registros (para AJAX)
     * PATRÓN: Compatible con BaseController búsquedas
     */
    public function buscar($termino = '') {
        try {
            if (empty($termino)) {
                return $this->listar();
            }

            $query = "SELECT * FROM [vista_completa] WHERE activo = 1 
                     AND (nombre LIKE ? OR descripcion LIKE ?)
                     ORDER BY nombre LIMIT 50";

            $stmt = $this->db->prepare($query);
            $terminoBusqueda = '%' . $termino . '%';
            $stmt->bindValue(1, $terminoBusqueda);
            $stmt->bindValue(2, $terminoBusqueda);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al buscar: " . $e->getMessage());
            return [];
        }
    }
}
?>