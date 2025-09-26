<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Persona.php';

class Cliente {
    private $db;
    private $persona;
    
    public function __construct() {
        // ✅ CORREGIDO: Usar getInstance() en lugar de new Database()
        $this->db = Database::getInstance()->getConnection();
        $this->persona = new Persona();
    }

    /**
     * Listar todos los clientes activos usando vista completa
     * @return array Lista de clientes con datos de persona
     */
    public function listar() {
        try {
            $query = "SELECT * FROM vista_clientes_completa WHERE cliente_activo = 1 ORDER BY nombre_completo";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al listar clientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un cliente por ID usando vista completa
     * @param int $id ID del cliente
     * @return array|false Datos completos del cliente o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM vista_clientes_completa WHERE cliente_id = ? AND cliente_activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener cliente por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar cliente por código
     * @param string $codigo Código del cliente
     * @return array|false Datos del cliente o false si no existe
     */
    public function obtenerPorCodigo($codigo) {
        try {
            $query = "SELECT * FROM vista_clientes_completa WHERE codigo_cliente = ? AND cliente_activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $codigo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al buscar cliente por código: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear un nuevo cliente (con transacción persona + cliente)
     * @param array $datosPersona Datos de la persona
     * @param array $datosCliente Datos específicos del cliente
     * @return int|false ID del cliente creado o false en caso de error
     */
    public function crear($datosPersona, $datosCliente = []) {
        try {
            $this->db->beginTransaction();

            // 1. Crear la persona primero
            $personaId = $this->persona->crear($datosPersona);
            if (!$personaId) {
                throw new Exception("Error al crear la persona base");
            }

            // 2. Validar datos del cliente
            $erroresCliente = $this->validarDatosCliente($datosCliente);
            if (!empty($erroresCliente)) {
                throw new Exception("Errores en datos de cliente: " . implode(', ', $erroresCliente));
            }

            // 3. Crear el registro de cliente
            $query = "INSERT INTO clientes (
                persona_id, tipo_cliente, condicion_iva, limite_credito, 
                descuento_general, dias_credito, fecha_alta, observaciones,
                lista_precios_id, vendedor_asignado_id, usuario_creacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            
            $fechaAlta = $datosCliente['fecha_alta'] ?? date('Y-m-d');
            
            $stmt->bindParam(1, $personaId, PDO::PARAM_INT);
            $stmt->bindParam(2, $datosCliente['tipo_cliente'] ?? 'MINORISTA');
            $stmt->bindParam(3, $datosCliente['condicion_iva'] ?? 'CONSUMIDOR_FINAL');
            $stmt->bindParam(4, $datosCliente['limite_credito'] ?? 0.00);
            $stmt->bindParam(5, $datosCliente['descuento_general'] ?? 0.00);
            $stmt->bindParam(6, $datosCliente['dias_credito'] ?? 0, PDO::PARAM_INT);
            $stmt->bindParam(7, $fechaAlta);
            $stmt->bindParam(8, $datosCliente['observaciones'] ?? null);
            $stmt->bindParam(9, $datosCliente['lista_precios_id'] ?? null, PDO::PARAM_INT);
            $stmt->bindParam(10, $datosCliente['vendedor_asignado_id'] ?? null, PDO::PARAM_INT);
            $stmt->bindParam(11, $datosCliente['usuario_creacion'] ?? null, PDO::PARAM_INT);

            $stmt->execute();
            $clienteId = $this->db->lastInsertId();

            $this->db->commit();
            return $clienteId;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear cliente: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar un cliente existente
     * @param int $id ID del cliente
     * @param array $datosPersona Datos actualizados de la persona
     * @param array $datosCliente Datos actualizados del cliente
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datosPersona = [], $datosCliente = []) {
        try {
            // Verificar que el cliente existe
            $clienteExistente = $this->obtenerPorId($id);
            if (!$clienteExistente) {
                throw new Exception("El cliente no existe");
            }

            $this->db->beginTransaction();

            // 1. Actualizar datos de persona si se proporcionan
            if (!empty($datosPersona)) {
                $resultadoPersona = $this->persona->actualizar($clienteExistente['persona_id'], $datosPersona);
                if (!$resultadoPersona) {
                    throw new Exception("Error al actualizar datos de la persona");
                }
            }

            // 2. Actualizar datos del cliente si se proporcionan
            if (!empty($datosCliente)) {
                // Validar datos del cliente
                $erroresCliente = $this->validarDatosCliente($datosCliente);
                if (!empty($erroresCliente)) {
                    throw new Exception("Errores en datos de cliente: " . implode(', ', $erroresCliente));
                }

                $query = "UPDATE clientes SET 
                    tipo_cliente = ?, condicion_iva = ?, limite_credito = ?, 
                    descuento_general = ?, dias_credito = ?, observaciones = ?,
                    lista_precios_id = ?, vendedor_asignado_id = ?
                    WHERE id = ? AND activo = 1";

                $stmt = $this->db->prepare($query);
                
                $stmt->bindParam(1, $datosCliente['tipo_cliente'] ?? $clienteExistente['tipo_cliente']);
                $stmt->bindParam(2, $datosCliente['condicion_iva'] ?? $clienteExistente['condicion_iva']);
                $stmt->bindParam(3, $datosCliente['limite_credito'] ?? $clienteExistente['limite_credito']);
                $stmt->bindParam(4, $datosCliente['descuento_general'] ?? $clienteExistente['descuento_general']);
                $stmt->bindParam(5, $datosCliente['dias_credito'] ?? $clienteExistente['dias_credito'], PDO::PARAM_INT);
                $stmt->bindParam(6, $datosCliente['observaciones'] ?? $clienteExistente['observaciones']);
                $stmt->bindParam(7, $datosCliente['lista_precios_id'] ?? $clienteExistente['lista_precios_id'], PDO::PARAM_INT);
                $stmt->bindParam(8, $datosCliente['vendedor_asignado_id'] ?? $clienteExistente['vendedor_asignado_id'], PDO::PARAM_INT);
                $stmt->bindParam(9, $id, PDO::PARAM_INT);

                $stmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar cliente: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar (desactivar) un cliente
     * @param int $id ID del cliente
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            // Verificar que el cliente existe
            $cliente = $this->obtenerPorId($id);
            if (!$cliente) {
                throw new Exception("El cliente no existe");
            }

            $this->db->beginTransaction();

            // Verificar si tiene pedidos o ventas activos
            $queryRelacionados = "
                SELECT 
                    (SELECT COUNT(*) FROM pedidos WHERE cliente_id = ?) as pedidos,
                    (SELECT COUNT(*) FROM ventas WHERE cliente_id = ?) as ventas
            ";
            
            $stmt = $this->db->prepare($queryRelacionados);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();
            $relacionados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (($relacionados['pedidos'] ?? 0) > 0 || ($relacionados['ventas'] ?? 0) > 0) {
                throw new Exception("No se puede eliminar el cliente porque tiene pedidos o ventas registrados");
            }

            // Marcar cliente como inactivo
            $query = "UPDATE clientes SET activo = 0 WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            // También marcar la persona como inactiva si no tiene otros roles
            $queryOtrosRoles = "
                SELECT 
                    (SELECT COUNT(*) FROM proveedores WHERE persona_id = ? AND activo = 1) as proveedores,
                    (SELECT COUNT(*) FROM usuarios WHERE persona_id = ? AND activo = 1) as usuarios
            ";
            
            $stmt = $this->db->prepare($queryOtrosRoles);
            $stmt->bindParam(1, $cliente['persona_id'], PDO::PARAM_INT);
            $stmt->bindParam(2, $cliente['persona_id'], PDO::PARAM_INT);
            $stmt->execute();
            $otrosRoles = $stmt->fetch(PDO::FETCH_ASSOC);

            if (($otrosRoles['proveedores'] ?? 0) == 0 && ($otrosRoles['usuarios'] ?? 0) == 0) {
                $query = "UPDATE personas SET activo = 0 WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $cliente['persona_id'], PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar cliente: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar crédito utilizado del cliente
     * @param int $clienteId ID del cliente
     * @param float $monto Monto a sumar al crédito utilizado
     * @return bool True si se actualizó correctamente
     */
    public function actualizarCreditoUtilizado($clienteId, $monto) {
        try {
            $query = "UPDATE clientes SET 
                credito_utilizado = credito_utilizado + ?,
                fecha_ultima_compra = CURRENT_DATE(),
                total_facturado = total_facturado + ?
                WHERE id = ? AND activo = 1";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $monto);
            $stmt->bindParam(2, $monto);
            $stmt->bindParam(3, $clienteId, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error al actualizar crédito utilizado: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener clientes con crédito disponible
     * @param float $montoMinimo Monto mínimo de crédito disponible
     * @return array Lista de clientes con crédito suficiente
     */
    public function obtenerConCreditoDisponible($montoMinimo = 0) {
        try {
            $query = "SELECT * FROM vista_clientes_completa 
                     WHERE cliente_activo = 1 
                     AND credito_disponible >= ? 
                     ORDER BY nombre_completo";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $montoMinimo);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al obtener clientes con crédito disponible: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar clientes por criterios
     * @param array $criterios Criterios de búsqueda
     * @return array Lista de clientes que coinciden
     */
    public function buscar($criterios) {
        try {
            $condiciones = ["cliente_activo = 1"];
            $parametros = [];
            $contador = 1;

            if (!empty($criterios['nombre'])) {
                $condiciones[] = "nombre_completo LIKE ?";
                $parametros[$contador++] = '%' . $criterios['nombre'] . '%';
            }

            if (!empty($criterios['codigo'])) {
                $condiciones[] = "codigo_cliente LIKE ?";
                $parametros[$contador++] = '%' . $criterios['codigo'] . '%';
            }

            if (!empty($criterios['documento'])) {
                $condiciones[] = "numero_documento LIKE ?";
                $parametros[$contador++] = '%' . $criterios['documento'] . '%';
            }

            if (!empty($criterios['tipo_cliente'])) {
                $condiciones[] = "tipo_cliente = ?";
                $parametros[$contador++] = $criterios['tipo_cliente'];
            }

            if (!empty($criterios['localidad'])) {
                $condiciones[] = "localidad LIKE ?";
                $parametros[$contador++] = '%' . $criterios['localidad'] . '%';
            }

            $query = "SELECT * FROM vista_clientes_completa WHERE " . implode(' AND ', $condiciones) . 
                    " ORDER BY nombre_completo LIMIT 50";

            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice, $valor);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al buscar clientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Validar datos específicos del cliente
     * @param array $datos Datos a validar
     * @return array Array con errores encontrados
     */
    public function validarDatosCliente($datos) {
        $errores = [];

        // Validar límite de crédito
        if (isset($datos['limite_credito'])) {
            if (!is_numeric($datos['limite_credito']) || $datos['limite_credito'] < 0) {
                $errores[] = "El límite de crédito debe ser un número positivo";
            }
        }

        // Validar descuento general
        if (isset($datos['descuento_general'])) {
            if (!is_numeric($datos['descuento_general']) || 
                $datos['descuento_general'] < 0 || 
                $datos['descuento_general'] > 100) {
                $errores[] = "El descuento general debe estar entre 0 y 100";
            }
        }

        // Validar días de crédito
        if (isset($datos['dias_credito'])) {
            if (!is_numeric($datos['dias_credito']) || $datos['dias_credito'] < 0 || $datos['dias_credito'] > 365) {
                $errores[] = "Los días de crédito deben estar entre 0 y 365";
            }
        }

        // Validar tipo de cliente
        if (!empty($datos['tipo_cliente'])) {
            $tiposValidos = ['MINORISTA', 'MAYORISTA', 'EMPRESARIAL', 'GOBIERNO', 'CONSTRUCTOR'];
            if (!in_array($datos['tipo_cliente'], $tiposValidos)) {
                $errores[] = "Tipo de cliente no válido";
            }
        }

        // Validar condición IVA
        if (!empty($datos['condicion_iva'])) {
            $condicionesValidas = ['RESPONSABLE_INSCRIPTO', 'MONOTRIBUTISTA', 'CONSUMIDOR_FINAL', 'EXENTO', 'NO_CATEGORIZADO'];
            if (!in_array($datos['condicion_iva'], $condicionesValidas)) {
                $errores[] = "Condición de IVA no válida";
            }
        }

        // Validar fecha de alta
        if (!empty($datos['fecha_alta'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_alta']);
            if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_alta']) {
                $errores[] = "El formato de fecha de alta debe ser YYYY-MM-DD";
            }
        }

        return $errores;
    }

    /**
     * Obtener estadísticas del cliente
     * @param int $clienteId ID del cliente
     * @return array Estadísticas del cliente
     */
    public function obtenerEstadisticas($clienteId) {
        try {
            $query = "SELECT 
                credito_disponible,
                credito_utilizado,
                limite_credito,
                total_facturado,
                fecha_ultima_compra,
                DATEDIFF(CURRENT_DATE(), fecha_alta) as dias_cliente,
                (SELECT COUNT(*) FROM pedidos WHERE cliente_id = ?) as total_pedidos,
                (SELECT COUNT(*) FROM ventas WHERE cliente_id = ?) as total_ventas
                FROM vista_clientes_completa 
                WHERE cliente_id = ? AND cliente_activo = 1";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $clienteId, PDO::PARAM_INT);
            $stmt->bindParam(2, $clienteId, PDO::PARAM_INT);
            $stmt->bindParam(3, $clienteId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        } catch (Exception $e) {
            error_log("Error al obtener estadísticas del cliente: " . $e->getMessage());
            return [];
        }
    }
}
?>
