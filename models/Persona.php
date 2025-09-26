<?php
require_once __DIR__ . '/../config/database.php';

class Persona {
    private $db;
    
    public function __construct() {
    $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Listar todas las personas activas
     * @return array Lista de personas
     */
    public function listar() {
        try {
            $query = "SELECT * FROM personas WHERE activo = 1 ORDER BY apellidos, nombres";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al listar personas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener una persona por ID
     * @param int $id ID de la persona
     * @return array|false Datos de la persona o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM personas WHERE id = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener persona por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar persona por documento
     * @param string $tipoDocumento Tipo de documento
     * @param string $numeroDocumento Número de documento
     * @return array|false Datos de la persona o false si no existe
     */
    public function obtenerPorDocumento($tipoDocumento, $numeroDocumento) {
        try {
            $query = "SELECT * FROM personas WHERE tipo_documento = ? AND numero_documento = ? AND activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $tipoDocumento);
            $stmt->bindParam(2, $numeroDocumento);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al buscar persona por documento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear una nueva persona
     * @param array $datos Datos de la persona
     * @return int|false ID de la persona creada o false en caso de error
     */
    public function crear($datos) {
        try {
            // Validar datos obligatorios
            if (empty($datos['apellidos']) || empty($datos['nombres'])) {
                throw new Exception("Apellidos y nombres son obligatorios");
            }

            // Verificar que el documento no esté duplicado
            if (!empty($datos['numero_documento'])) {
                $existente = $this->obtenerPorDocumento(
                    $datos['tipo_documento'] ?? 'DNI',
                    $datos['numero_documento']
                );
                if ($existente) {
                    throw new Exception("Ya existe una persona con el documento {$datos['numero_documento']}");
                }
            }

            $this->db->beginTransaction();

            $query = "INSERT INTO personas (
                tipo_documento, numero_documento, apellidos, nombres, razon_social,
                telefono, telefono_alternativo, email, direccion, localidad, 
                provincia, codigo_postal, fecha_nacimiento, usuario_creacion, observaciones
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $datos['tipo_documento'] ?? 'DNI');
            $stmt->bindParam(2, $datos['numero_documento']);
            $stmt->bindParam(3, $datos['apellidos']);
            $stmt->bindParam(4, $datos['nombres']);
            $stmt->bindParam(5, $datos['razon_social'] ?? null);
            $stmt->bindParam(6, $datos['telefono'] ?? null);
            $stmt->bindParam(7, $datos['telefono_alternativo'] ?? null);
            $stmt->bindParam(8, $datos['email'] ?? null);
            $stmt->bindParam(9, $datos['direccion'] ?? null);
            $stmt->bindParam(10, $datos['localidad'] ?? null);
            $stmt->bindParam(11, $datos['provincia'] ?? null);
            $stmt->bindParam(12, $datos['codigo_postal'] ?? null);
            $stmt->bindParam(13, $datos['fecha_nacimiento'] ?? null);
            $stmt->bindParam(14, $datos['usuario_creacion'] ?? null, PDO::PARAM_INT);
            $stmt->bindParam(15, $datos['observaciones'] ?? null);

            $stmt->execute();
            $personaId = $this->db->lastInsertId();

            $this->db->commit();
            return $personaId;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear persona: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar una persona existente
     * @param int $id ID de la persona
     * @param array $datos Datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datos) {
        try {
            // Verificar que la persona existe
            $personaExistente = $this->obtenerPorId($id);
            if (!$personaExistente) {
                throw new Exception("La persona no existe");
            }

            // Verificar duplicados de documento (excluyendo el registro actual)
            if (!empty($datos['numero_documento'])) {
                $query = "SELECT id FROM personas 
                         WHERE tipo_documento = ? AND numero_documento = ? 
                         AND id != ? AND activo = 1";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $datos['tipo_documento'] ?? $personaExistente['tipo_documento']);
                $stmt->bindParam(2, $datos['numero_documento']);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);
                $stmt->execute();
                
                if ($stmt->fetch()) {
                    throw new Exception("Ya existe otra persona con el documento {$datos['numero_documento']}");
                }
            }

            $this->db->beginTransaction();

            $query = "UPDATE personas SET 
                tipo_documento = ?, numero_documento = ?, apellidos = ?, nombres = ?, 
                razon_social = ?, telefono = ?, telefono_alternativo = ?, email = ?, 
                direccion = ?, localidad = ?, provincia = ?, codigo_postal = ?, 
                fecha_nacimiento = ?, observaciones = ?
                WHERE id = ? AND activo = 1";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $datos['tipo_documento'] ?? $personaExistente['tipo_documento']);
            $stmt->bindParam(2, $datos['numero_documento'] ?? $personaExistente['numero_documento']);
            $stmt->bindParam(3, $datos['apellidos'] ?? $personaExistente['apellidos']);
            $stmt->bindParam(4, $datos['nombres'] ?? $personaExistente['nombres']);
            $stmt->bindParam(5, $datos['razon_social'] ?? $personaExistente['razon_social']);
            $stmt->bindParam(6, $datos['telefono'] ?? $personaExistente['telefono']);
            $stmt->bindParam(7, $datos['telefono_alternativo'] ?? $personaExistente['telefono_alternativo']);
            $stmt->bindParam(8, $datos['email'] ?? $personaExistente['email']);
            $stmt->bindParam(9, $datos['direccion'] ?? $personaExistente['direccion']);
            $stmt->bindParam(10, $datos['localidad'] ?? $personaExistente['localidad']);
            $stmt->bindParam(11, $datos['provincia'] ?? $personaExistente['provincia']);
            $stmt->bindParam(12, $datos['codigo_postal'] ?? $personaExistente['codigo_postal']);
            $stmt->bindParam(13, $datos['fecha_nacimiento'] ?? $personaExistente['fecha_nacimiento']);
            $stmt->bindParam(14, $datos['observaciones'] ?? $personaExistente['observaciones']);
            $stmt->bindParam(15, $id, PDO::PARAM_INT);

            $resultado = $stmt->execute();
            $this->db->commit();
            
            return $resultado;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar persona: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar (desactivar) una persona
     * @param int $id ID de la persona
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            // Verificar que la persona existe
            $persona = $this->obtenerPorId($id);
            if (!$persona) {
                throw new Exception("La persona no existe");
            }

            // Verificar si tiene registros relacionados (clientes, proveedores, usuarios)
            $queryRelacionados = "
                SELECT 
                    (SELECT COUNT(*) FROM clientes WHERE persona_id = ? AND activo = 1) as clientes,
                    (SELECT COUNT(*) FROM proveedores WHERE persona_id = ? AND activo = 1) as proveedores,
                    (SELECT COUNT(*) FROM usuarios WHERE persona_id = ? AND activo = 1) as usuarios
            ";
            
            $stmt = $this->db->prepare($queryRelacionados);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->bindParam(3, $id, PDO::PARAM_INT);
            $stmt->execute();
            $relacionados = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($relacionados['clientes'] > 0 || $relacionados['proveedores'] > 0 || $relacionados['usuarios'] > 0) {
                throw new Exception("No se puede eliminar la persona porque tiene registros relacionados activos");
            }

            // Marcar como inactivo
            $query = "UPDATE personas SET activo = 0 WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error al eliminar persona: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validar datos de persona
     * @param array $datos Datos a validar
     * @return array Array con errores encontrados
     */
    public function validarDatos($datos) {
        $errores = [];

        // Validaciones obligatorias
        if (empty($datos['apellidos'])) {
            $errores[] = "Los apellidos son obligatorios";
        }

        if (empty($datos['nombres'])) {
            $errores[] = "Los nombres son obligatorios";
        }

        // Validar formato de email
        if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        }

        // Validar documento
        if (!empty($datos['numero_documento'])) {
            $tipoDoc = $datos['tipo_documento'] ?? 'DNI';
            $numeroDoc = $datos['numero_documento'];

            switch ($tipoDoc) {
                case 'DNI':
                    if (!preg_match('/^\d{7,8}$/', $numeroDoc)) {
                        $errores[] = "El DNI debe tener entre 7 y 8 dígitos";
                    }
                    break;
                case 'CUIL':
                case 'CUIT':
                    if (!preg_match('/^\d{2}-\d{8}-\d{1}$/', $numeroDoc)) {
                        $errores[] = "El formato de $tipoDoc debe ser XX-XXXXXXXX-X";
                    }
                    break;
            }
        }

        // Validar fecha de nacimiento
        if (!empty($datos['fecha_nacimiento'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_nacimiento']);
            if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_nacimiento']) {
                $errores[] = "El formato de fecha de nacimiento debe ser YYYY-MM-DD";
            } elseif ($fecha > new DateTime()) {
                $errores[] = "La fecha de nacimiento no puede ser futura";
            }
        }

        return $errores;
    }

    /**
     * Sanitizar datos de entrada
     * @param array $datos Datos a sanitizar
     * @return array Datos sanitizados
     */
    public function sanitizarDatos($datos) {
        $datosSanitizados = [];

        foreach ($datos as $campo => $valor) {
            if (is_string($valor)) {
                $datosSanitizados[$campo] = trim($valor) ?: null;
            } else {
                $datosSanitizados[$campo] = $valor;
            }
        }

        return $datosSanitizados;
    }

    /**
     * Buscar personas por criterios
     * @param array $criterios Criterios de búsqueda
     * @return array Lista de personas que coinciden
     */
    public function buscar($criterios) {
        try {
            $condiciones = ["activo = 1"];
            $parametros = [];
            $contador = 1;

            if (!empty($criterios['nombre'])) {
                $condiciones[] = "(CONCAT(nombres, ' ', apellidos) LIKE ? OR razon_social LIKE ?)";
                $busqueda = '%' . $criterios['nombre'] . '%';
                $parametros[$contador++] = $busqueda;
                $parametros[$contador++] = $busqueda;
            }

            if (!empty($criterios['documento'])) {
                $condiciones[] = "numero_documento LIKE ?";
                $parametros[$contador++] = '%' . $criterios['documento'] . '%';
            }

            if (!empty($criterios['email'])) {
                $condiciones[] = "email LIKE ?";
                $parametros[$contador++] = '%' . $criterios['email'] . '%';
            }

            if (!empty($criterios['localidad'])) {
                $condiciones[] = "localidad LIKE ?";
                $parametros[$contador++] = '%' . $criterios['localidad'] . '%';
            }

            $query = "SELECT * FROM personas WHERE " . implode(' AND ', $condiciones) . 
                    " ORDER BY apellidos, nombres LIMIT 50";

            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice, $valor);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al buscar personas: " . $e->getMessage());
            return [];
        }
    }
}
?>
