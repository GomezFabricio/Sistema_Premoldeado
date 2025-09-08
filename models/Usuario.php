<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function autenticar($email, $password) {
        $sql = "SELECT u.id, u.nombre_usuarios, u.email, u.password, u.activo, u.perfiles_id, p.nombre as perfil_nombre 
                FROM usuarios u 
                INNER JOIN perfiles p ON u.perfiles_id = p.id 
                WHERE u.email = ? AND u.activo = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }
    
    /**
     * Método de instancia para obtener módulos por perfil - SOLO DATOS
     * El controlador se encarga de agregar URLs y configuración
     * 
     * @param int $perfil_id ID del perfil
     * @return array Array de módulos básicos del perfil
     */
    public function obtenerModulosPorPerfil($perfil_id) {
        // Solo devolver los módulos básicos de la base de datos
        return self::obtenerModulosAsignadosPorPerfil($perfil_id);
    }

    // ============================================================================
    // SUBMÓDULO PERFILES - Gestión de perfiles de usuario
    // ============================================================================
    
    /**
     * Obtiene todos los perfiles activos
     * 
     * @return array Array de perfiles activos
     */
    public static function obtenerTodosPerfiles() {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT 
                        p.id, 
                        p.nombre,
                        COUNT(DISTINCT pm.modulos_id) as total_modulos,
                        COUNT(DISTINCT u.id) as total_usuarios
                    FROM perfiles p
                    LEFT JOIN perfiles_modulos pm ON p.id = pm.perfiles_id
                    LEFT JOIN usuarios u ON p.id = u.perfiles_id
                    GROUP BY p.id, p.nombre
                    ORDER BY p.nombre ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Agregar campos faltantes con valores por defecto
            foreach ($perfiles as &$perfil) {
                $perfil['descripcion'] = 'Perfil de ' . $perfil['nombre'];
                $perfil['activo'] = 1; // Todos activos por defecto
                $perfil['fecha_creacion'] = '2024-01-01'; // Fecha por defecto
            }
            
            return $perfiles;
        } catch (PDOException $e) {
            error_log("Error al obtener perfiles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un perfil específico por su ID
     * 
     * @param int $id ID del perfil
     * @return array|false Datos del perfil o false si no existe
     */
    public static function obtenerPerfilPorId($id) {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT id, nombre FROM perfiles WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener perfil por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea un nuevo perfil
     * 
     * @param array $datos Array con los datos del perfil ['nombre' => 'valor']
     * @return array Resultado de la operación
     */
    public static function crearPerfil($datos) {
        try {
            // Validar datos obligatorios
            if (empty($datos['nombre'])) {
                return [
                    'success' => false,
                    'message' => 'El nombre del perfil es obligatorio'
                ];
            }
            
            // Sanitizar datos
            $nombre = trim($datos['nombre']);
            
            // Validar longitud del nombre
            if (strlen($nombre) > 45) {
                return [
                    'success' => false,
                    'message' => 'El nombre del perfil no puede exceder 45 caracteres'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Verificar que no exista un perfil con el mismo nombre
            $sqlVerificar = "SELECT id FROM perfiles WHERE nombre = ?";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$nombre]);
            
            if ($stmtVerificar->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ya existe un perfil con este nombre'
                ];
            }
            
            // Insertar nuevo perfil
            $sql = "INSERT INTO perfiles (nombre) VALUES (?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nombre]);
            
            $perfilId = $db->lastInsertId();
            
            return [
                'success' => true,
                'message' => 'Perfil creado exitosamente',
                'id' => $perfilId
            ];
            
        } catch (PDOException $e) {
            error_log("Error al crear perfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
    
    /**
     * Actualiza un perfil existente
     * 
     * @param int $id ID del perfil a actualizar
     * @param array $datos Array con los datos a actualizar
     * @return array Resultado de la operación
     */
    public static function actualizarPerfil($id, $datos) {
        try {
            // Validar que el perfil existe
            if (!self::obtenerPerfilPorId($id)) {
                return [
                    'success' => false,
                    'message' => 'El perfil especificado no existe'
                ];
            }
            
            // Validar datos obligatorios
            if (empty($datos['nombre'])) {
                return [
                    'success' => false,
                    'message' => 'El nombre del perfil es obligatorio'
                ];
            }
            
            // Sanitizar datos
            $nombre = trim($datos['nombre']);
            
            // Validar longitud del nombre
            if (strlen($nombre) > 45) {
                return [
                    'success' => false,
                    'message' => 'El nombre del perfil no puede exceder 45 caracteres'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Verificar que no exista otro perfil con el mismo nombre
            $sqlVerificar = "SELECT id FROM perfiles WHERE nombre = ? AND id != ?";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$nombre, $id]);
            
            if ($stmtVerificar->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ya existe otro perfil con este nombre'
                ];
            }
            
            // Actualizar perfil
            $sql = "UPDATE perfiles SET nombre = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nombre, $id]);
            
            return [
                'success' => true,
                'message' => 'Perfil actualizado exitosamente'
            ];
            
        } catch (PDOException $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
    
    /**
     * Elimina un perfil (eliminación física debido a que la tabla no tiene campo 'activo')
     * NOTA: Solo se permite eliminar si no hay usuarios asociados al perfil
     * 
     * @param int $id ID del perfil a eliminar
     * @return array Resultado de la operación
     */
    public static function eliminarPerfil($id) {
        try {
            // Validar que el perfil existe
            if (!self::obtenerPerfilPorId($id)) {
                return [
                    'success' => false,
                    'message' => 'El perfil especificado no existe'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Verificar si hay usuarios asociados a este perfil
            $sqlVerificar = "SELECT COUNT(*) as total FROM usuarios WHERE perfiles_id = ?";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$id]);
            $resultado = $stmtVerificar->fetch();
            
            if ($resultado['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el perfil porque tiene usuarios asociados'
                ];
            }
            
            // Verificar si hay módulos asociados a este perfil
            $sqlVerificarModulos = "SELECT COUNT(*) as total FROM perfiles_modulos WHERE perfiles_id = ?";
            $stmtVerificarModulos = $db->prepare($sqlVerificarModulos);
            $stmtVerificarModulos->execute([$id]);
            $resultadoModulos = $stmtVerificarModulos->fetch();
            
            if ($resultadoModulos['total'] > 0) {
                // Eliminar primero las asociaciones con módulos
                $sqlEliminarModulos = "DELETE FROM perfiles_modulos WHERE perfiles_id = ?";
                $stmtEliminarModulos = $db->prepare($sqlEliminarModulos);
                $stmtEliminarModulos->execute([$id]);
            }
            
            // Eliminar perfil
            $sql = "DELETE FROM perfiles WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            
            return [
                'success' => true,
                'message' => 'Perfil eliminado exitosamente'
            ];
            
        } catch (PDOException $e) {
            error_log("Error al eliminar perfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
    
    /**
     * Obtiene los módulos asignados a un perfil específico
     * 
     * @param int $perfilId ID del perfil
     * @return array Array de módulos asignados al perfil
     */
    public static function obtenerModulosAsignadosPorPerfil($perfilId) {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT m.id, m.nombre 
                    FROM modulos m 
                    INNER JOIN perfiles_modulos pm ON m.id = pm.modulos_id 
                    WHERE pm.perfiles_id = ? 
                    ORDER BY m.nombre ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$perfilId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener módulos por perfil: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene todos los módulos disponibles para asignar a perfiles
     * 
     * @return array Array de todos los módulos disponibles
     */
    public static function obtenerTodosModulos() {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT id, nombre FROM modulos ORDER BY nombre ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener módulos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Asigna módulos a un perfil específico
     * 
     * @param int $perfilId ID del perfil
     * @param array $modulosIds Array de IDs de módulos a asignar
     * @return array Resultado de la operación
     */
    public static function asignarModulosAPerfil($perfilId, $modulosIds) {
        try {
            // Validar que el perfil existe
            if (!self::obtenerPerfilPorId($perfilId)) {
                return [
                    'success' => false,
                    'message' => 'El perfil especificado no existe'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Iniciar transacción
            $db->beginTransaction();
            
            // Eliminar asignaciones previas
            $sqlEliminar = "DELETE FROM perfiles_modulos WHERE perfiles_id = ?";
            $stmtEliminar = $db->prepare($sqlEliminar);
            $stmtEliminar->execute([$perfilId]);
            
            // Asignar nuevos módulos si se proporcionaron
            if (!empty($modulosIds) && is_array($modulosIds)) {
                $sqlInsertar = "INSERT INTO perfiles_modulos (perfiles_id, modulos_id) VALUES (?, ?)";
                $stmtInsertar = $db->prepare($sqlInsertar);
                
                foreach ($modulosIds as $moduloId) {
                    $stmtInsertar->execute([$perfilId, $moduloId]);
                }
            }
            
            // Confirmar transacción
            $db->commit();
            
            return [
                'success' => true,
                'message' => 'Módulos asignados exitosamente al perfil'
            ];
            
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error al asignar módulos a perfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
}
?>
