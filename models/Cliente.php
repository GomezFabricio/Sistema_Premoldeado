<?php
require_once __DIR__ . '/../config/database.php';

class Cliente {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Listar todos los clientes activos
    public function listar() {
        $sql = "SELECT 
                    c.id as cliente_id,
                    p.nombres,
                    p.apellidos,
                    p.razon_social,
                    p.tipo_documento,
                    p.numero_documento,
                    p.email,
                    p.telefono,
                    p.direccion,
                    p.localidad,
                    p.provincia,
                    p.codigo_postal,
                    c.fecha_alta,
                    c.observaciones,
                    c.activo as cliente_activo,
                    CONCAT(COALESCE(p.nombres, ''), ' ', COALESCE(p.apellidos, '')) as nombre_completo
                FROM clientes c
                LEFT JOIN personas p ON c.persona_id = p.id
                WHERE c.activo = 1
                ORDER BY p.apellidos, p.nombres";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo cliente y persona asociada (primero persona, luego cliente, ambos con el mismo id)
    public function crear($datosPersona, $datosCliente) {
        try {
            $this->db->beginTransaction();
            // Insertar persona
            $sqlPersona = "INSERT INTO personas (tipo_documento, numero_documento, apellidos, nombres, razon_social, telefono, email, direccion, localidad, provincia, codigo_postal, activo, fecha_creacion, fecha_modificacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmtPersona = $this->db->prepare($sqlPersona);
            $stmtPersona->execute([
                $datosPersona['tipo_documento'],
                $datosPersona['numero_documento'],
                $datosPersona['apellidos'],
                $datosPersona['nombres'],
                $datosPersona['razon_social'],
                $datosPersona['telefono'],
                $datosPersona['email'],
                $datosPersona['direccion'],
                $datosPersona['localidad'],
                $datosPersona['provincia'],
                $datosPersona['codigo_postal']
            ]);
            $personaId = $this->db->lastInsertId();
            // Insertar cliente con persona_id
            $sqlCliente = "INSERT INTO clientes (persona_id, fecha_alta, observaciones, activo) VALUES (?, CURDATE(), ?, 1)";
            $stmtCliente = $this->db->prepare($sqlCliente);
            $stmtCliente->execute([
                $personaId,
                $datosCliente['observaciones']
            ]);
            $this->db->commit();
            return $personaId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Obtener un cliente por ID
    public function obtenerPorId($id) {
        $sql = "SELECT 
                    c.id as cliente_id,
                    p.nombres,
                    p.apellidos,
                    p.razon_social,
                    p.tipo_documento,
                    p.numero_documento,
                    p.email,
                    p.telefono,
                    p.direccion,
                    p.localidad,
                    p.provincia,
                    p.codigo_postal,
                    c.fecha_alta,
                    c.observaciones,
                    c.activo as cliente_activo,
                    CONCAT(COALESCE(p.nombres, ''), ' ', COALESCE(p.apellidos, '')) as nombre_completo
                FROM clientes c
                LEFT JOIN personas p ON c.persona_id = p.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            return null;
        }
        
        return $cliente;
    }

    // Actualizar cliente y persona asociada
    public function actualizar($id, $datosPersona, $datosCliente) {
        try {
            $this->db->beginTransaction();
            
            // Actualizar persona
            $camposPersona = [];
            $valoresPersona = [];
            
            foreach ($datosPersona as $campo => $valor) {
                if ($valor !== null && $valor !== '') {
                    $camposPersona[] = "$campo = ?";
                    $valoresPersona[] = $valor;
                }
            }
            
            if (!empty($camposPersona)) {
                $camposPersona[] = "fecha_modificacion = NOW()";
                $valoresPersona[] = $id; // Para WHERE
                
                $sqlPersona = "UPDATE personas SET " . implode(', ', $camposPersona) . " WHERE id = ?";
                $stmtPersona = $this->db->prepare($sqlPersona);
                $stmtPersona->execute($valoresPersona);
            }
            
            // Actualizar cliente
            $camposCliente = [];
            $valoresCliente = [];
            
            foreach ($datosCliente as $campo => $valor) {
                if ($valor !== null && $valor !== '') {
                    $camposCliente[] = "$campo = ?";
                    $valoresCliente[] = $valor;
                }
            }
            
            if (!empty($camposCliente)) {
                $valoresCliente[] = $id; // Para WHERE
                
                $sqlCliente = "UPDATE clientes SET " . implode(', ', $camposCliente) . " WHERE id = ?";
                $stmtCliente = $this->db->prepare($sqlCliente);
                $stmtCliente->execute($valoresCliente);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Eliminar (desactivar) cliente
    public function eliminar($id) {
        try {
            $this->db->beginTransaction();
            
            // Desactivar persona
            $sqlPersona = "UPDATE personas SET activo = 0, fecha_modificacion = NOW() WHERE id = ?";
            $stmtPersona = $this->db->prepare($sqlPersona);
            $stmtPersona->execute([$id]);
            
            // Desactivar cliente
            $sqlCliente = "UPDATE clientes SET activo = 0 WHERE id = ?";
            $stmtCliente = $this->db->prepare($sqlCliente);
            $stmtCliente->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Dar de baja cliente (baja lógica)
    public function darDeBaja($id) {
        try {
            $this->db->beginTransaction();
            
            // Obtener la persona_id del cliente
            $sqlGetPersonaId = "SELECT persona_id FROM clientes WHERE id = ?";
            $stmtGetPersonaId = $this->db->prepare($sqlGetPersonaId);
            $stmtGetPersonaId->execute([$id]);
            $cliente = $stmtGetPersonaId->fetch(PDO::FETCH_ASSOC);
            
            if (!$cliente) {
                throw new Exception("Cliente no encontrado");
            }
            
            $personaId = $cliente['persona_id'];
            
            // Desactivar persona
            $sqlPersona = "UPDATE personas SET activo = 0, fecha_modificacion = NOW() WHERE id = ?";
            $stmtPersona = $this->db->prepare($sqlPersona);
            $stmtPersona->execute([$personaId]);
            
            // Desactivar cliente
            $sqlCliente = "UPDATE clientes SET activo = 0 WHERE id = ?";
            $stmtCliente = $this->db->prepare($sqlCliente);
            $stmtCliente->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Reactivar cliente
    public function reactivar($id) {
        try {
            $this->db->beginTransaction();
            
            // Obtener la persona_id del cliente
            $sqlGetPersonaId = "SELECT persona_id FROM clientes WHERE id = ?";
            $stmtGetPersonaId = $this->db->prepare($sqlGetPersonaId);
            $stmtGetPersonaId->execute([$id]);
            $cliente = $stmtGetPersonaId->fetch(PDO::FETCH_ASSOC);
            
            if (!$cliente) {
                throw new Exception("Cliente no encontrado");
            }
            
            $personaId = $cliente['persona_id'];
            
            // Reactivar persona
            $sqlPersona = "UPDATE personas SET activo = 1, fecha_modificacion = NOW() WHERE id = ?";
            $stmtPersona = $this->db->prepare($sqlPersona);
            $stmtPersona->execute([$personaId]);
            
            // Reactivar cliente
            $sqlCliente = "UPDATE clientes SET activo = 1 WHERE id = ?";
            $stmtCliente = $this->db->prepare($sqlCliente);
            $stmtCliente->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Buscar clientes por criterios
    public function buscar($criterios = []) {
        $condiciones = [];
        $parametros = [];
        
        if (!empty($criterios['nombre'])) {
            $condiciones[] = "(nombres LIKE ? OR apellidos LIKE ? OR razon_social LIKE ?)";
            $parametros[] = '%' . $criterios['nombre'] . '%';
            $parametros[] = '%' . $criterios['nombre'] . '%';
            $parametros[] = '%' . $criterios['nombre'] . '%';
        }
        
        if (!empty($criterios['documento'])) {
            $condiciones[] = "numero_documento LIKE ?";
            $parametros[] = '%' . $criterios['documento'] . '%';
        }
        
        if (!empty($criterios['codigo'])) {
            $condiciones[] = "cliente_id = ?";
            $parametros[] = $criterios['codigo'];
        }
        
        if (!empty($criterios['localidad'])) {
            $condiciones[] = "localidad LIKE ?";
            $parametros[] = '%' . $criterios['localidad'] . '%';
        }
        
        if (!empty($criterios['tipo_cliente'])) {
            $condiciones[] = "tipo_cliente = ?";
            $parametros[] = $criterios['tipo_cliente'];
        }
        
        $sql = "SELECT 
                    c.id as cliente_id,
                    p.nombres,
                    p.apellidos,
                    p.razon_social,
                    p.tipo_documento,
                    p.numero_documento,
                    p.email,
                    p.telefono,
                    p.direccion,
                    p.localidad,
                    p.provincia,
                    p.codigo_postal,
                    c.fecha_alta,
                    c.observaciones,
                    c.activo as cliente_activo,
                    CONCAT(COALESCE(p.nombres, ''), ' ', COALESCE(p.apellidos, '')) as nombre_completo
                FROM clientes c
                INNER JOIN personas p ON c.id = p.id
                WHERE c.activo = 1 AND p.activo = 1";
        
        if (!empty($condiciones)) {
            $sql .= " AND " . implode(' AND ', $condiciones);
        }
        $sql .= " ORDER BY nombre_completo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas del cliente
    public function obtenerEstadisticas($id) {
        $sql = "SELECT 
                    COUNT(p.id) as total_pedidos,
                    COALESCE(SUM(p.total), 0) as total_compras,
                    MAX(p.fecha_pedido) as ultima_compra
                FROM pedidos p 
                WHERE p.cliente_id = ? AND p.activo = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Validar datos del cliente
    public function validarDatos($datos) {
        $errores = [];
        
        // Validaciones básicas
        if (empty($datos['nombres'])) {
            $errores[] = "El nombre es requerido";
        }
        
        if (empty($datos['apellidos'])) {
            $errores[] = "Los apellidos son requeridos";
        }
        
        if (empty($datos['numero_documento'])) {
            $errores[] = "El número de documento es requerido";
        } else {
            // Verificar si el documento ya existe para otro cliente
            $sql = "SELECT id FROM personas WHERE numero_documento = ? AND id != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$datos['numero_documento'], $datos['id'] ?? 0]);
            if ($stmt->fetch()) {
                $errores[] = "Ya existe otro cliente con este número de documento";
            }
        }
        
        if (!empty($datos['email'])) {
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El formato del email no es válido";
            }
        }
        
        return $errores;
    }

    /**
     * Verificar si un cliente ya tiene usuario asociado
     */
    public function clienteTieneUsuario($clienteId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM usuarios WHERE persona_id = ? AND activo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$clienteId]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Convertir una persona existente en cliente
     * @param int $personaId ID de la persona existente
     * @param array $datosCliente Datos específicos del cliente
     * @return mixed ID del cliente creado o array con error
     */
    public function crearDesdePersonaExistente($personaId, $datosCliente = []) {
        try {
            // Verificar que la persona existe y está activa
            $sqlVerificar = "SELECT id, nombres, apellidos, email FROM personas WHERE id = ? AND activo = 1";
            $stmtVerificar = $this->db->prepare($sqlVerificar);
            $stmtVerificar->execute([$personaId]);
            $persona = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if (!$persona) {
                throw new Exception("La persona especificada no existe o no está activa");
            }
            
            // Verificar que no sea ya un cliente
            $sqlClienteExiste = "SELECT id FROM clientes WHERE id = ? AND activo = 1";
            $stmtClienteExiste = $this->db->prepare($sqlClienteExiste);
            $stmtClienteExiste->execute([$personaId]);
            
            if ($stmtClienteExiste->fetch()) {
                throw new Exception("Esta persona ya es un cliente registrado");
            }
            
            $this->db->beginTransaction();
            
            // Crear registro de cliente usando el mismo ID de la persona
            $sqlCliente = "INSERT INTO clientes (id, fecha_alta, observaciones, activo) VALUES (?, CURDATE(), ?, 1)";
            $stmtCliente = $this->db->prepare($sqlCliente);
            $stmtCliente->execute([
                $personaId,
                $datosCliente['observaciones'] ?? 'Convertido desde usuario existente'
            ]);
            
            $this->db->commit();
            return $personaId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener personas que son usuarios pero no clientes (candidatos a convertir)
     * @return array Lista de personas que pueden convertirse en clientes
     */
    public function obtenerPersonasUsuariosNoClientes() {
        try {
            $sql = "SELECT 
                        p.id,
                        p.nombres,
                        p.apellidos,
                        p.email,
                        p.numero_documento,
                        p.tipo_documento,
                        u.nombre_usuario,
                        CONCAT(COALESCE(p.nombres, ''), ' ', COALESCE(p.apellidos, '')) as nombre_completo
                    FROM personas p
                    INNER JOIN usuarios u ON p.id = u.persona_id
                    LEFT JOIN clientes c ON p.id = c.id
                    WHERE p.activo = 1 
                      AND u.activo = 1 
                      AND c.id IS NULL
                    ORDER BY p.apellidos, p.nombres";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}
