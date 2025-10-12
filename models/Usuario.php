<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Método para crear usuario con validaciones adicionales
     */
    public function crear($datos) {
        // Validar email único
        $db = $this->db;
        $sqlEmail = "SELECT id FROM usuarios WHERE email = ? AND activo = 1";
        $stmtEmail = $db->prepare($sqlEmail);
        $stmtEmail->execute([$datos['email']]);
        if ($stmtEmail->fetch()) {
            return [
                'success' => false,
                'message' => 'El email ya está registrado para otro usuario activo.'
            ];
        }

        // Validar fortaleza de contraseña
        $password = $datos['password'] ?? '';
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 8 caracteres, incluir letras y números.'
            ];
        }

        try {
            // Iniciar transacción para crear persona y usuario
            $this->db->beginTransaction();
            
            // 1. Crear registro en tabla personas primero
            // Generar número de documento único basado en timestamp
            $numeroDocumento = 'AUTO' . time() . rand(100, 999);
            
            $sqlPersona = "INSERT INTO personas (nombres, apellidos, email, tipo_documento, numero_documento, activo, fecha_creacion) 
                          VALUES (?, ?, ?, 'DNI', ?, 1, NOW())";
            
            // Usar el nombre de usuario como nombre por defecto
            $nombres = $datos['nombre_usuario'];
            $apellidos = 'Usuario'; // Por defecto
            
            $stmtPersona = $this->db->prepare($sqlPersona);
            $resultadoPersona = $stmtPersona->execute([
                $nombres,
                $apellidos, 
                $datos['email'],
                $numeroDocumento
            ]);
            
            if (!$resultadoPersona) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Error al crear el registro de persona.'
                ];
            }
            
            $personaId = $this->db->lastInsertId();
            
            // 2. Hashear la contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // 3. Insertar usuario SIN perfil_id (ya no existe esa columna)
            $sql = "INSERT INTO usuarios (persona_id, nombre_usuario, email, password, domicilio, activo) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $personaId,
                $datos['nombre_usuario'],
                $datos['email'],
                $passwordHash,
                $datos['domicilio'] ?? null,
                $datos['activo'] ?? 1
            ]);
            
            if (!$resultado) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Error al crear el usuario.'
                ];
            }

            $usuarioId = $this->db->lastInsertId();

            // 4. Asignar perfiles si se proporcionaron
            if (!empty($datos['perfiles_ids']) && is_array($datos['perfiles_ids'])) {
                $sqlPerfil = "INSERT INTO usuarios_perfiles (usuario_id, perfil_id, activo) VALUES (?, ?, 1)";
                $stmtPerfil = $this->db->prepare($sqlPerfil);
                
                foreach ($datos['perfiles_ids'] as $perfil_id) {
                    $stmtPerfil->execute([$usuarioId, $perfil_id]);
                }
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'id' => $usuarioId
            ];
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al crear usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Desactiva (baja lógica) un usuario
     * @param int $id ID del usuario
     * @return array Resultado de la operación
     */
    public static function desactivarUsuario($id) {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "UPDATE usuarios SET activo = 0 WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Usuario desactivado correctamente'];
        } catch (PDOException $e) {
            error_log("Error al desactivar usuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    public function autenticar($email, $password) {
        $sql = "SELECT u.persona_id as id, u.nombre_usuario, u.email, u.password, u.activo,
                       -- Obtener primer perfil como perfil principal (retrocompatibilidad)
                       (SELECT p.id FROM perfiles p 
                        INNER JOIN usuarios_perfiles up ON p.id = up.perfil_id
                        WHERE up.usuario_id = u.id AND up.activo = 1 AND p.estado = 1
                        ORDER BY p.nombre LIMIT 1) as perfil_id,
                       -- Concatenar nombres de perfiles
                       (SELECT GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ', ')
                        FROM perfiles p 
                        INNER JOIN usuarios_perfiles up ON p.id = up.perfil_id
                        WHERE up.usuario_id = u.id AND up.activo = 1 AND p.estado = 1) as perfil_nombre
                FROM usuarios u 
                WHERE u.email = ? AND u.activo = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Si no tiene perfiles, asignar valores por defecto
            if (empty($usuario['perfil_id'])) {
                $usuario['perfil_id'] = null;
                $usuario['perfil_nombre'] = 'Sin perfiles asignados';
            }
            return $usuario;
        }
        return false;
    }
    
    /**
     * Listar todos los usuarios usando vista completa
     * 
     * @param int $limite Número máximo de registros (opcional)
     * @param int $offset Desplazamiento para paginación (opcional)
     * @return array Array de usuarios con datos completos
     */
    public function listar($limite = null, $offset = 0) {
        try {
            // ✅ CORREGIDO: Usar nueva estructura con múltiples perfiles
            $sql = "SELECT 
                        u.persona_id as id,
                        u.nombre_usuario,
                        u.email,
                        u.domicilio,
                        u.ultimo_acceso,
                        u.fecha_creacion,
                        CASE WHEN u.activo = 1 THEN 'Activo' ELSE 'Inactivo' END as estado,
                        -- Concatenar todos los perfiles del usuario
                        COALESCE(
                            (SELECT GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ', ')
                             FROM perfiles p 
                             INNER JOIN usuarios_perfiles up ON p.id = up.perfil_id
                             WHERE up.usuario_id = u.id AND up.activo = 1 AND p.estado = 1),
                            'Sin perfiles'
                        ) as perfil_nombre
                    FROM usuarios u
                    ORDER BY u.activo DESC, u.nombre_usuario ASC";
            
            if ($limite !== null) {
                $sql .= " LIMIT ? OFFSET ?";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($limite !== null) {
                $stmt->execute([$limite, $offset]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al listar usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ✅ NUEVO: Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        try {
            // ✅ CORREGIDO: Usar nueva estructura sin perfil_id
            $sql = "SELECT 
                        u.persona_id as id,
                        u.nombre_usuario,
                        u.email,
                        u.domicilio,
                        u.activo,
                        u.ultimo_acceso,
                        u.fecha_creacion,
                        -- Obtener todos los perfiles del usuario
                        COALESCE(
                            (SELECT GROUP_CONCAT(p.nombre ORDER BY p.nombre SEPARATOR ', ')
                             FROM perfiles p 
                             INNER JOIN usuarios_perfiles up ON p.id = up.perfil_id
                             WHERE up.usuario_id = u.id AND up.activo = 1 AND p.estado = 1),
                            'Sin perfiles'
                        ) as perfil_nombre
                    FROM usuarios u
                    WHERE u.persona_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ✅ NUEVO: Actualizar usuario
     */
    public function actualizar($id, $datos) {
        try {
            // Construir la query dinámicamente según los campos proporcionados
            $campos = [];
            $valores = [];
            
            if (isset($datos['nombre_usuario'])) {
                $campos[] = "nombre_usuario = ?";
                $valores[] = $datos['nombre_usuario'];
            }
            
            if (isset($datos['email'])) {
                $campos[] = "email = ?";
                $valores[] = $datos['email'];
            }
            
            if (isset($datos['password']) && !empty($datos['password'])) {
                $campos[] = "password = ?";
                $valores[] = password_hash($datos['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($datos['domicilio'])) {
                $campos[] = "domicilio = ?";
                $valores[] = $datos['domicilio'];
            }
            
            // ✅ CORREGIDO: Los perfiles ahora se manejan por separado con asignarPerfilesAUsuario()
            
            if (isset($datos['activo'])) {
                $campos[] = "activo = ?";
                $valores[] = $datos['activo'];
            }
            
            if (empty($campos)) {
                return ['success' => false, 'message' => 'No hay campos para actualizar'];
            }
            
            $valores[] = $id; // Para la condición WHERE
            
            $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE persona_id = ?";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute($valores);
            
            if ($resultado && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
            } elseif ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar el usuario'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * ✅ NUEVO: Eliminar usuario (baja lógica)
     */
    public function eliminar($id) {
        try {
            $sql = "UPDATE usuarios SET activo = 0 WHERE persona_id = ?";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([$id]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Usuario desactivado correctamente'];
            } elseif ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            } else {
                return ['success' => false, 'message' => 'Error al desactivar el usuario'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }

    /**
     * ✅ NUEVO: Dar de baja usuario (baja lógica)
     */
    public function darDeBaja($id) {
        try {
            $sql = "UPDATE usuarios SET activo = 0 WHERE persona_id = ?";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([$id]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Usuario dado de baja correctamente'];
            } elseif ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            } else {
                return ['success' => false, 'message' => 'Error al dar de baja el usuario'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al dar de baja usuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }

    /**
     * ✅ NUEVO: Reactivar usuario
     */
    public function reactivar($id) {
        try {
            $sql = "UPDATE usuarios SET activo = 1 WHERE persona_id = ?";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([$id]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Usuario reactivado correctamente'];
            } elseif ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            } else {
                return ['success' => false, 'message' => 'Error al reactivar el usuario'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al reactivar usuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * ✅ NUEVO: Obtener perfiles disponibles
     */
    public function obtenerPerfiles() {
        try {
            $sql = "SELECT id, nombre FROM perfiles WHERE estado = 1 ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener perfiles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ✅ NUEVO: Validar que el nombre de usuario sea único
     * 
     * @param string $nombreUsuario Nombre de usuario a validar
     * @param int $excluirId ID del usuario a excluir de la validación (para edición)
     * @return bool true si ya existe, false si es único
     */
    public function validarNombreUsuarioUnico($nombreUsuario, $excluirId = null) {
        try {
            if ($excluirId) {
                // Para edición - excluir el usuario actual
                $sql = "SELECT persona_id FROM usuarios WHERE nombre_usuario = ? AND persona_id != ? AND activo = 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$nombreUsuario, $excluirId]);
            } else {
                // Para creación - verificar si existe
                $sql = "SELECT persona_id FROM usuarios WHERE nombre_usuario = ? AND activo = 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$nombreUsuario]);
            }
            
            return $stmt->fetch() !== false; // true si existe, false si no existe
            
        } catch (PDOException $e) {
            error_log("Error al validar nombre de usuario único: " . $e->getMessage());
            return false; // En caso de error, permitir continuar
        }
    }
    
    /**
     * ✅ NUEVO: Validar que el email sea único
     * 
     * @param string $email Email a validar
     * @param int $excluirId ID del usuario a excluir de la validación (para edición)
     * @return bool true si ya existe, false si es único
     */
    public function validarEmailUnico($email, $excluirId = null) {
        try {
            if ($excluirId) {
                // Para edición - excluir el usuario actual
                $sql = "SELECT persona_id FROM usuarios WHERE email = ? AND persona_id != ? AND activo = 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$email, $excluirId]);
            } else {
                // Para creación - verificar si existe
                $sql = "SELECT persona_id FROM usuarios WHERE email = ? AND activo = 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$email]);
            }
            
            return $stmt->fetch() !== false; // true si existe, false si no existe
            
        } catch (PDOException $e) {
            error_log("Error al validar email único: " . $e->getMessage());
            return false; // En caso de error, permitir continuar
        }
    }
    
    /**
     * ✅ NUEVO: Obtener módulos de TODOS los perfiles de un usuario (sin duplicados)
     * 
     * @param int $usuario_id ID del usuario
     * @return array Array de módulos únicos del usuario
     */
    public function obtenerModulosPorUsuario($usuario_id) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // ✅ CORREGIDO: Obtener el usuarios.id correcto basado en persona_id
            $sqlUsuarioId = "SELECT id FROM usuarios WHERE persona_id = ?";
            $stmtUsuarioId = $db->prepare($sqlUsuarioId);
            $stmtUsuarioId->execute([$usuario_id]);
            $usuario_real_id = $stmtUsuarioId->fetchColumn();
            
            if (!$usuario_real_id) {
                return [];
            }
            
            $sql = "SELECT DISTINCT m.id, m.nombre 
                    FROM modulos m 
                    INNER JOIN perfiles_modulos pm ON m.id = pm.modulos_id 
                    INNER JOIN usuarios_perfiles up ON pm.perfiles_id = up.perfil_id
                    WHERE up.usuario_id = ? 
                    AND up.activo = 1
                    ORDER BY m.nombre ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$usuario_real_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener módulos por usuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NUEVO: Obtener perfiles de un usuario específico
     * 
     * @param int $usuario_id ID del usuario
     * @return array Array de perfiles del usuario
     */
    public function obtenerPerfilesDelUsuario($usuario_id) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // ✅ CORREGIDO: Obtener el usuarios.id correcto basado en persona_id
            $sqlUsuarioId = "SELECT id FROM usuarios WHERE persona_id = ?";
            $stmtUsuarioId = $db->prepare($sqlUsuarioId);
            $stmtUsuarioId->execute([$usuario_id]);
            $usuario_real_id = $stmtUsuarioId->fetchColumn();
            
            if (!$usuario_real_id) {
                return [];
            }
            
            $sql = "SELECT p.id, p.nombre, up.fecha_asignacion
                    FROM perfiles p 
                    INNER JOIN usuarios_perfiles up ON p.id = up.perfil_id
                    WHERE up.usuario_id = ? 
                    AND up.activo = 1 
                    AND p.estado = 1
                    ORDER BY p.nombre ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$usuario_real_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener perfiles del usuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NUEVO: Asignar múltiples perfiles a un usuario
     * 
     * @param int $usuario_id ID del usuario
     * @param array $perfiles_ids Array de IDs de perfiles
     * @param int $asignado_por ID del usuario que asigna
     * @return array Resultado de la operación
     */
    public function asignarPerfilesAUsuario($usuario_id, $perfiles_ids, $asignado_por = null) {
        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // ✅ CORREGIDO: Obtener el usuarios.id correcto basado en persona_id
            $sqlUsuarioId = "SELECT id FROM usuarios WHERE persona_id = ?";
            $stmtUsuarioId = $db->prepare($sqlUsuarioId);
            $stmtUsuarioId->execute([$usuario_id]);
            $usuario_real_id = $stmtUsuarioId->fetchColumn();
            
            if (!$usuario_real_id) {
                throw new PDOException("Usuario no encontrado con persona_id: " . $usuario_id);
            }

            // Primero desactivar todas las asignaciones actuales
            $sqlDesactivar = "UPDATE usuarios_perfiles SET activo = 0 WHERE usuario_id = ?";
            $stmtDesactivar = $db->prepare($sqlDesactivar);
            $stmtDesactivar->execute([$usuario_real_id]);

            // Luego asignar los nuevos perfiles
            $sqlAsignar = "INSERT INTO usuarios_perfiles (usuario_id, perfil_id, asignado_por, activo) 
                          VALUES (?, ?, ?, 1)
                          ON DUPLICATE KEY UPDATE activo = 1, asignado_por = ?, fecha_asignacion = CURRENT_TIMESTAMP";

            $stmtAsignar = $db->prepare($sqlAsignar);

            foreach ($perfiles_ids as $perfil_id) {
                $stmtAsignar->execute([$usuario_real_id, $perfil_id, $asignado_por, $asignado_por]);
            }

            $db->commit();
            
            return [
                'success' => true,
                'message' => 'Perfiles asignados correctamente'
            ];

        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error al asignar perfiles a usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al asignar perfiles: ' . $e->getMessage()
            ];
        }
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
            // ✅ CORREGIDO: Usar nueva estructura usuarios_perfiles
            $sql = "SELECT 
                        p.id, 
                        p.nombre,
                        p.estado,
                        COUNT(DISTINCT pm.modulos_id) as total_modulos,
                        COUNT(DISTINCT up.usuario_id) as total_usuarios
                    FROM perfiles p
                    LEFT JOIN perfiles_modulos pm ON p.id = pm.perfiles_id
                    LEFT JOIN usuarios_perfiles up ON p.id = up.perfil_id AND up.activo = 1
                    GROUP BY p.id, p.nombre, p.estado
                    ORDER BY p.estado DESC, p.nombre ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener los nombres de módulos para cada perfil
            foreach ($perfiles as &$perfil) {
                $perfil['descripcion'] = 'Perfil de ' . $perfil['nombre'];
                $perfil['activo'] = 1; // Todos activos por defecto
                $perfil['fecha_creacion'] = '2024-01-01'; // Fecha por defecto
                
                // Obtener nombres de módulos asignados
                $sqlModulos = "SELECT m.nombre 
                              FROM modulos m 
                              INNER JOIN perfiles_modulos pm ON m.id = pm.modulos_id 
                              WHERE pm.perfiles_id = ? 
                              ORDER BY m.nombre ASC";
                $stmtModulos = $db->prepare($sqlModulos);
                $stmtModulos->execute([$perfil['id']]);
                $modulosNombres = $stmtModulos->fetchAll(PDO::FETCH_COLUMN);
                
                $perfil['modulos_nombres'] = $modulosNombres;
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
            $sql = "SELECT id, nombre, estado FROM perfiles WHERE id = ?";
            
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
            $estado = isset($datos['estado']) ? (int)$datos['estado'] : 1; // Por defecto activo
            
            // Validar longitud del nombre
            if (strlen($nombre) > 45) {
                return [
                    'success' => false,
                    'message' => 'El nombre del perfil no puede exceder 45 caracteres'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Verificar que no exista un perfil activo con el mismo nombre
            $sqlVerificar = "SELECT id FROM perfiles WHERE nombre = ? AND estado = 1";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$nombre]);
            
            if ($stmtVerificar->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ya existe un perfil activo con este nombre'
                ];
            }
            
            // Insertar nuevo perfil
            $sql = "INSERT INTO perfiles (nombre, estado) VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nombre, $estado]);
            
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
            $estado = isset($datos['estado']) ? (int)$datos['estado'] : 1;
            
            // Validar longitud del nombre
            if (strlen($nombre) > 45) {
                return [
                    'success' => false,
                    'message' => 'El nombre del perfil no puede exceder 45 caracteres'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Verificar que no exista otro perfil activo con el mismo nombre
            $sqlVerificar = "SELECT id FROM perfiles WHERE nombre = ? AND id != ? AND estado = 1";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$nombre, $id]);
            
            if ($stmtVerificar->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ya existe otro perfil activo con este nombre'
                ];
            }
            
            // Actualizar perfil
            $sql = "UPDATE perfiles SET nombre = ?, estado = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nombre, $estado, $id]);
            
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
            $perfil = self::obtenerPerfilPorId($id);
            if (!$perfil) {
                return [
                    'success' => false,
                    'message' => 'El perfil especificado no existe'
                ];
            }
            
            // Verificar si es un perfil crítico del sistema
            $perfilesCriticos = ['administrador', 'admin'];
            if (in_array(strtolower($perfil['nombre']), $perfilesCriticos)) {
                return [
                    'success' => false,
                    'message' => 'No se puede desactivar el perfil Administrador ya que es crítico para el funcionamiento del sistema'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // ✅ CORREGIDO: Verificar usuarios usando nueva tabla usuarios_perfiles
            $sqlVerificar = "SELECT COUNT(DISTINCT up.usuario_id) as total 
                           FROM usuarios_perfiles up 
                           INNER JOIN usuarios u ON up.usuario_id = u.id 
                           WHERE up.perfil_id = ? AND up.activo = 1 AND u.activo = 1";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$id]);
            $resultado = $stmtVerificar->fetch();
            
            if ($resultado['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede desactivar el perfil porque tiene usuarios activos asociados. Desactive primero los usuarios.'
                ];
            }
            
            // Realizar baja lógica del perfil (cambiar estado a 0)
            $sql = "UPDATE perfiles SET estado = 0 WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            
            return [
                'success' => true,
                'message' => 'Perfil desactivado exitosamente'
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
     * Reactivar perfil (baja lógica inversa)
     * 
     * @param int $id ID del perfil a reactivar
     * @return array Resultado de la operación
     */
    public static function reactivarPerfil($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return [
                    'success' => false,
                    'message' => 'ID de perfil inválido'
                ];
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Verificar que el perfil existe y está inactivo
            $sqlVerificar = "SELECT estado FROM perfiles WHERE id = ?";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$id]);
            $perfil = $stmtVerificar->fetch();
            
            if (!$perfil) {
                return [
                    'success' => false,
                    'message' => 'El perfil no existe'
                ];
            }
            
            if ($perfil['estado'] == 1) {
                return [
                    'success' => false,
                    'message' => 'El perfil ya está activo'
                ];
            }
            
            // Reactivar perfil (cambiar estado a 1)
            $sql = "UPDATE perfiles SET estado = 1 WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            
            return [
                'success' => true,
                'message' => 'Perfil reactivado exitosamente'
            ];
            
        } catch (PDOException $e) {
            error_log("Error al reactivar perfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al reactivar perfil: ' . $e->getMessage()
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

    /**
     * Crear usuario para cliente existente (flujo correcto de negocio)
     */
    public function crearDesdeClienteExistente($datos) {
        try {
            // Validar que la persona/cliente existe
            $sqlVerificar = "SELECT p.id, p.email, p.nombres, p.apellidos 
                           FROM personas p 
                           INNER JOIN clientes c ON p.id = c.id 
                           WHERE p.id = ? AND p.activo = 1 AND c.activo = 1";
            $stmtVerificar = $this->db->prepare($sqlVerificar);
            $stmtVerificar->execute([$datos['persona_id']]);
            $persona = $stmtVerificar->fetch();

            if (!$persona) {
                return ['success' => false, 'message' => 'Cliente no encontrado o inactivo'];
            }

            // Validar que no tenga ya un usuario
            $sqlUsuarioExiste = "SELECT id FROM usuarios WHERE persona_id = ? AND activo = 1";
            $stmtUsuarioExiste = $this->db->prepare($sqlUsuarioExiste);
            $stmtUsuarioExiste->execute([$datos['persona_id']]);
            
            if ($stmtUsuarioExiste->fetch()) {
                return ['success' => false, 'message' => 'Este cliente ya tiene acceso al sistema'];
            }

            // Validar nombre de usuario único
            $sqlNombreExiste = "SELECT id FROM usuarios WHERE nombre_usuario = ? AND activo = 1";
            $stmtNombreExiste = $this->db->prepare($sqlNombreExiste);
            $stmtNombreExiste->execute([$datos['nombre_usuario']]);
            
            if ($stmtNombreExiste->fetch()) {
                return ['success' => false, 'message' => 'El nombre de usuario ya está en uso'];
            }

            $this->db->beginTransaction();

            // Hashear contraseña
            $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);

            // Crear usuario SIN perfil_id
            $sql = "INSERT INTO usuarios (persona_id, nombre_usuario, email, password, activo) 
                   VALUES (?, ?, ?, ?, 1)";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $datos['persona_id'],
                $datos['nombre_usuario'],
                $persona['email'], // Usar email del cliente
                $passwordHash
            ]);

            if (!$resultado) {
                throw new Exception('Error al crear usuario en la base de datos');
            }

            $usuarioId = $this->db->lastInsertId();

            // Asignar perfil por defecto (cliente)
            $perfilPorDefecto = $datos['perfil_id'] ?? 2; // Perfil cliente por defecto
            $sqlPerfil = "INSERT INTO usuarios_perfiles (usuario_id, perfil_id, activo) VALUES (?, ?, 1)";
            $stmtPerfil = $this->db->prepare($sqlPerfil);
            $stmtPerfil->execute([$usuarioId, $perfilPorDefecto]);
            $this->db->commit();

            return $usuarioId;

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al crear usuario: ' . $e->getMessage()];
        }
    }
}
?>
