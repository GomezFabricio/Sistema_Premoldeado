<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Persona.php';

class Proveedor {
    private $db;
    private $persona;
    
    public function __construct() {
        // ✅ CORREGIDO: Usar getInstance() en lugar de new Database()
        $this->db = Database::getInstance()->getConnection();
        $this->persona = new Persona();
    }

    /**
     * Listar todos los proveedores activos usando vista completa
     * @return array Lista de proveedores con datos de persona
     */
    public function listar() {
        try {
            $query = "SELECT * FROM vista_proveedores_completa WHERE proveedor_activo = 1 ORDER BY nombre_completo";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al listar proveedores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un proveedor por ID usando vista completa
     * @param int $id ID del proveedor
     * @return array|false Datos completos del proveedor o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM vista_proveedores_completa WHERE proveedor_id = ? AND proveedor_activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener proveedor por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar proveedor por código
     * @param string $codigo Código del proveedor
     * @return array|false Datos del proveedor o false si no existe
     */
    public function obtenerPorCodigo($codigo) {
        try {
            $query = "SELECT * FROM vista_proveedores_completa WHERE codigo_proveedor = ? AND proveedor_activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $codigo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al buscar proveedor por código: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar proveedor por CUIT
     * @param string $cuit CUIT del proveedor
     * @return array|false Datos del proveedor o false si no existe
     */
    public function obtenerPorCUIT($cuit) {
        try {
            $query = "SELECT * FROM vista_proveedores_completa WHERE cuit = ? AND proveedor_activo = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $cuit);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al buscar proveedor por CUIT: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear un nuevo proveedor (con transacción persona + proveedor)
     * @param array $datosPersona Datos de la persona
     * @param array $datosProveedor Datos específicos del proveedor
     * @return int|false ID del proveedor creado o false en caso de error
     */
    public function crear($datosPersona, $datosProveedor = []) {
        try {
            $this->db->beginTransaction();

            // 1. Crear la persona primero
            $personaId = $this->persona->crear($datosPersona);
            if (!$personaId) {
                throw new Exception("Error al crear la persona base");
            }

            // 2. Validar datos del proveedor
            $erroresProveedor = $this->validarDatosProveedor($datosProveedor);
            if (!empty($erroresProveedor)) {
                throw new Exception("Errores en datos de proveedor: " . implode(', ', $erroresProveedor));
            }

            // 3. Verificar que el CUIT no esté duplicado
            if (!empty($datosProveedor['cuit'])) {
                $existente = $this->obtenerPorCUIT($datosProveedor['cuit']);
                if ($existente) {
                    throw new Exception("Ya existe un proveedor con el CUIT {$datosProveedor['cuit']}");
                }
            }

            // 4. Crear el registro de proveedor
            $query = "INSERT INTO proveedores (
                persona_id, cuit, condicion_iva, tipo_proveedor, condicion_pago,
                descuento_pronto_pago, recargo_financiacion, plazo_entrega_dias,
                calificacion, fecha_registro, contacto_comercial, telefono_comercial,
                email_comercial, observaciones, usuario_creacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            
            $fechaRegistro = $datosProveedor['fecha_registro'] ?? date('Y-m-d');
            
            $stmt->bindParam(1, $personaId, PDO::PARAM_INT);
            $stmt->bindParam(2, $datosProveedor['cuit'] ?? null);
            $stmt->bindParam(3, $datosProveedor['condicion_iva'] ?? 'RESPONSABLE_INSCRIPTO');
            $stmt->bindParam(4, $datosProveedor['tipo_proveedor'] ?? 'MATERIALES');
            $stmt->bindParam(5, $datosProveedor['condicion_pago'] ?? '30_DIAS');
            $stmt->bindParam(6, $datosProveedor['descuento_pronto_pago'] ?? 0.00);
            $stmt->bindParam(7, $datosProveedor['recargo_financiacion'] ?? 0.00);
            $stmt->bindParam(8, $datosProveedor['plazo_entrega_dias'] ?? null, PDO::PARAM_INT);
            $stmt->bindParam(9, $datosProveedor['calificacion'] ?? 'C');
            $stmt->bindParam(10, $fechaRegistro);
            $stmt->bindParam(11, $datosProveedor['contacto_comercial'] ?? null);
            $stmt->bindParam(12, $datosProveedor['telefono_comercial'] ?? null);
            $stmt->bindParam(13, $datosProveedor['email_comercial'] ?? null);
            $stmt->bindParam(14, $datosProveedor['observaciones'] ?? null);
            $stmt->bindParam(15, $datosProveedor['usuario_creacion'] ?? null, PDO::PARAM_INT);

            $stmt->execute();
            $proveedorId = $this->db->lastInsertId();

            $this->db->commit();
            return $proveedorId;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear proveedor: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar un proveedor existente
     * @param int $id ID del proveedor
     * @param array $datosPersona Datos actualizados de la persona
     * @param array $datosProveedor Datos actualizados del proveedor
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datosPersona = [], $datosProveedor = []) {
        try {
            // Verificar que el proveedor existe
            $proveedorExistente = $this->obtenerPorId($id);
            if (!$proveedorExistente) {
                throw new Exception("El proveedor no existe");
            }

            $this->db->beginTransaction();

            // 1. Actualizar datos de persona si se proporcionan
            if (!empty($datosPersona)) {
                $resultadoPersona = $this->persona->actualizar($proveedorExistente['persona_id'], $datosPersona);
                if (!$resultadoPersona) {
                    throw new Exception("Error al actualizar datos de la persona");
                }
            }

            // 2. Actualizar datos del proveedor si se proporcionan
            if (!empty($datosProveedor)) {
                // Validar datos del proveedor
                $erroresProveedor = $this->validarDatosProveedor($datosProveedor);
                if (!empty($erroresProveedor)) {
                    throw new Exception("Errores en datos de proveedor: " . implode(', ', $erroresProveedor));
                }

                // Verificar CUIT duplicado (excluyendo el registro actual)
                if (!empty($datosProveedor['cuit'])) {
                    $query = "SELECT id FROM proveedores 
                             WHERE cuit = ? AND id != ? AND activo = 1";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $datosProveedor['cuit']);
                    $stmt->bindParam(2, $id, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    if ($stmt->fetch()) {
                        throw new Exception("Ya existe otro proveedor con el CUIT {$datosProveedor['cuit']}");
                    }
                }

                $query = "UPDATE proveedores SET 
                    cuit = ?, condicion_iva = ?, tipo_proveedor = ?, condicion_pago = ?,
                    descuento_pronto_pago = ?, recargo_financiacion = ?, plazo_entrega_dias = ?,
                    calificacion = ?, contacto_comercial = ?, telefono_comercial = ?,
                    email_comercial = ?, observaciones = ?
                    WHERE id = ? AND activo = 1";

                $stmt = $this->db->prepare($query);
                
                $stmt->bindParam(1, $datosProveedor['cuit'] ?? $proveedorExistente['cuit']);
                $stmt->bindParam(2, $datosProveedor['condicion_iva'] ?? $proveedorExistente['condicion_iva']);
                $stmt->bindParam(3, $datosProveedor['tipo_proveedor'] ?? $proveedorExistente['tipo_proveedor']);
                $stmt->bindParam(4, $datosProveedor['condicion_pago'] ?? $proveedorExistente['condicion_pago']);
                $stmt->bindParam(5, $datosProveedor['descuento_pronto_pago'] ?? $proveedorExistente['descuento_pronto_pago']);
                $stmt->bindParam(6, $datosProveedor['recargo_financiacion'] ?? $proveedorExistente['recargo_financiacion']);
                $stmt->bindParam(7, $datosProveedor['plazo_entrega_dias'] ?? $proveedorExistente['plazo_entrega_dias'], PDO::PARAM_INT);
                $stmt->bindParam(8, $datosProveedor['calificacion'] ?? $proveedorExistente['calificacion']);
                $stmt->bindParam(9, $datosProveedor['contacto_comercial'] ?? $proveedorExistente['contacto_comercial']);
                $stmt->bindParam(10, $datosProveedor['telefono_comercial'] ?? $proveedorExistente['telefono_comercial']);
                $stmt->bindParam(11, $datosProveedor['email_comercial'] ?? $proveedorExistente['email_comercial']);
                $stmt->bindParam(12, $datosProveedor['observaciones'] ?? $proveedorExistente['observaciones']);
                $stmt->bindParam(13, $id, PDO::PARAM_INT);

                $stmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar proveedor: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar (desactivar) un proveedor
     * @param int $id ID del proveedor
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            // Verificar que el proveedor existe
            $proveedor = $this->obtenerPorId($id);
            if (!$proveedor) {
                throw new Exception("El proveedor no existe");
            }

            $this->db->beginTransaction();

            // Verificar si tiene compras o materiales activos
            $queryRelacionados = "
                SELECT 
                    (SELECT COUNT(*) FROM compras WHERE proveedor_id = ?) as compras,
                    (SELECT COUNT(*) FROM materiales WHERE proveedor_preferido_id = ?) as materiales
            ";
            
            $stmt = $this->db->prepare($queryRelacionados);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();
            $relacionados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (($relacionados['compras'] ?? 0) > 0) {
                throw new Exception("No se puede eliminar el proveedor porque tiene compras registradas");
            }

            if (($relacionados['materiales'] ?? 0) > 0) {
                // Si hay materiales asociados, solo advertir pero permitir la eliminación
                error_log("Advertencia: Proveedor tiene materiales asociados como preferido");
            }

            // Marcar proveedor como inactivo
            $query = "UPDATE proveedores SET activo = 0 WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            // También marcar la persona como inactiva si no tiene otros roles
            $queryOtrosRoles = "
                SELECT 
                    (SELECT COUNT(*) FROM clientes WHERE persona_id = ? AND activo = 1) as clientes,
                    (SELECT COUNT(*) FROM usuarios WHERE persona_id = ? AND activo = 1) as usuarios
            ";
            
            $stmt = $this->db->prepare($queryOtrosRoles);
            $stmt->bindParam(1, $proveedor['persona_id'], PDO::PARAM_INT);
            $stmt->bindParam(2, $proveedor['persona_id'], PDO::PARAM_INT);
            $stmt->execute();
            $otrosRoles = $stmt->fetch(PDO::FETCH_ASSOC);

            if (($otrosRoles['clientes'] ?? 0) == 0 && ($otrosRoles['usuarios'] ?? 0) == 0) {
                $query = "UPDATE personas SET activo = 0 WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $proveedor['persona_id'], PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar proveedor: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar totales de compras del proveedor
     * @param int $proveedorId ID del proveedor
     * @param float $monto Monto a sumar al total comprado
     * @return bool True si se actualizó correctamente
     */
    public function actualizarTotalComprado($proveedorId, $monto) {
        try {
            $query = "UPDATE proveedores SET 
                total_comprado = total_comprado + ?,
                fecha_ultima_compra = CURRENT_DATE()
                WHERE id = ? AND activo = 1";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $monto);
            $stmt->bindParam(2, $proveedorId, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error al actualizar total comprado: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener proveedores por tipo
     * @param string $tipo Tipo de proveedor
     * @return array Lista de proveedores del tipo especificado
     */
    public function obtenerPorTipo($tipo) {
        try {
            $query = "SELECT * FROM vista_proveedores_completa 
                     WHERE tipo_proveedor = ? AND proveedor_activo = 1 
                     ORDER BY nombre_completo";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $tipo);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al obtener proveedores por tipo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener proveedores por calificación
     * @param string $calificacion Calificación mínima (A, B, C, D, E)
     * @return array Lista de proveedores con calificación igual o superior
     */
    public function obtenerPorCalificacion($calificacion = 'C') {
        try {
            $ordenCalificacion = ['A' => 5, 'B' => 4, 'C' => 3, 'D' => 2, 'E' => 1];
            $valorMinimo = $ordenCalificacion[$calificacion] ?? 3;
            
            $query = "SELECT * FROM vista_proveedores_completa 
                     WHERE proveedor_activo = 1 
                     AND CASE calificacion 
                         WHEN 'A' THEN 5 
                         WHEN 'B' THEN 4 
                         WHEN 'C' THEN 3 
                         WHEN 'D' THEN 2 
                         WHEN 'E' THEN 1 
                         ELSE 0 
                     END >= ?
                     ORDER BY calificacion, nombre_completo";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $valorMinimo, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al obtener proveedores por calificación: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar proveedores por criterios
     * @param array $criterios Criterios de búsqueda
     * @return array Lista de proveedores que coinciden
     */
    public function buscar($criterios) {
        try {
            $condiciones = ["proveedor_activo = 1"];
            $parametros = [];
            $contador = 1;

            if (!empty($criterios['nombre'])) {
                $condiciones[] = "nombre_completo LIKE ?";
                $parametros[$contador++] = '%' . $criterios['nombre'] . '%';
            }

            if (!empty($criterios['codigo'])) {
                $condiciones[] = "codigo_proveedor LIKE ?";
                $parametros[$contador++] = '%' . $criterios['codigo'] . '%';
            }

            if (!empty($criterios['cuit'])) {
                $condiciones[] = "cuit LIKE ?";
                $parametros[$contador++] = '%' . $criterios['cuit'] . '%';
            }

            if (!empty($criterios['tipo_proveedor'])) {
                $condiciones[] = "tipo_proveedor = ?";
                $parametros[$contador++] = $criterios['tipo_proveedor'];
            }

            if (!empty($criterios['calificacion'])) {
                $condiciones[] = "calificacion = ?";
                $parametros[$contador++] = $criterios['calificacion'];
            }

            if (!empty($criterios['localidad'])) {
                $condiciones[] = "localidad LIKE ?";
                $parametros[$contador++] = '%' . $criterios['localidad'] . '%';
            }

            $query = "SELECT * FROM vista_proveedores_completa WHERE " . implode(' AND ', $condiciones) . 
                    " ORDER BY calificacion, nombre_completo LIMIT 50";

            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice, $valor);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al buscar proveedores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Validar datos específicos del proveedor
     * @param array $datos Datos a validar
     * @return array Array con errores encontrados
     */
    public function validarDatosProveedor($datos) {
        $errores = [];

        // Validar CUIT
        if (!empty($datos['cuit'])) {
            if (!preg_match('/^\d{2}-\d{8}-\d{1}$/', $datos['cuit'])) {
                $errores[] = "El formato de CUIT debe ser XX-XXXXXXXX-X";
            }
        }

        // Validar descuento pronto pago
        if (isset($datos['descuento_pronto_pago'])) {
            if (!is_numeric($datos['descuento_pronto_pago']) || 
                $datos['descuento_pronto_pago'] < 0 || 
                $datos['descuento_pronto_pago'] > 100) {
                $errores[] = "El descuento pronto pago debe estar entre 0 y 100";
            }
        }

        // Validar recargo financiación
        if (isset($datos['recargo_financiacion'])) {
            if (!is_numeric($datos['recargo_financiacion']) || 
                $datos['recargo_financiacion'] < 0 || 
                $datos['recargo_financiacion'] > 100) {
                $errores[] = "El recargo por financiación debe estar entre 0 y 100";
            }
        }

        // Validar plazo entrega
        if (isset($datos['plazo_entrega_dias'])) {
            if (!is_numeric($datos['plazo_entrega_dias']) || $datos['plazo_entrega_dias'] < 0 || $datos['plazo_entrega_dias'] > 365) {
                $errores[] = "El plazo de entrega debe estar entre 0 y 365 días";
            }
        }

        // Validar calificación
        if (!empty($datos['calificacion'])) {
            $calificacionesValidas = ['A', 'B', 'C', 'D', 'E'];
            if (!in_array($datos['calificacion'], $calificacionesValidas)) {
                $errores[] = "La calificación debe ser A, B, C, D o E";
            }
        }

        // Validar tipo de proveedor
        if (!empty($datos['tipo_proveedor'])) {
            $tiposValidos = ['MATERIALES', 'SERVICIOS', 'TRANSPORTE', 'EQUIPOS', 'HERRAMIENTAS', 'ENERGIA', 'OTROS'];
            if (!in_array($datos['tipo_proveedor'], $tiposValidos)) {
                $errores[] = "Tipo de proveedor no válido";
            }
        }

        // Validar condición IVA
        if (!empty($datos['condicion_iva'])) {
            $condicionesValidas = ['RESPONSABLE_INSCRIPTO', 'MONOTRIBUTISTA', 'EXENTO', 'NO_INSCRIPTO'];
            if (!in_array($datos['condicion_iva'], $condicionesValidas)) {
                $errores[] = "Condición de IVA no válida";
            }
        }

        // Validar condición de pago
        if (!empty($datos['condicion_pago'])) {
            $condicionesValidas = ['CONTADO', '7_DIAS', '15_DIAS', '30_DIAS', '45_DIAS', '60_DIAS', '90_DIAS', '120_DIAS'];
            if (!in_array($datos['condicion_pago'], $condicionesValidas)) {
                $errores[] = "Condición de pago no válida";
            }
        }

        // Validar email comercial
        if (!empty($datos['email_comercial']) && !filter_var($datos['email_comercial'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email comercial no es válido";
        }

        // Validar fecha de registro
        if (!empty($datos['fecha_registro'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_registro']);
            if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_registro']) {
                $errores[] = "El formato de fecha de registro debe ser YYYY-MM-DD";
            }
        }

        return $errores;
    }

    /**
     * Obtener estadísticas del proveedor
     * @param int $proveedorId ID del proveedor
     * @return array Estadísticas del proveedor
     */
    public function obtenerEstadisticas($proveedorId) {
        try {
            $query = "SELECT 
                total_comprado,
                fecha_ultima_compra,
                calificacion,
                plazo_entrega_dias,
                DATEDIFF(CURRENT_DATE(), fecha_registro) as dias_proveedor,
                (SELECT COUNT(*) FROM compras WHERE proveedor_id = ?) as total_compras,
                (SELECT COUNT(*) FROM materiales WHERE proveedor_preferido_id = ?) as materiales_suministrados
                FROM vista_proveedores_completa 
                WHERE proveedor_id = ? AND proveedor_activo = 1";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $proveedorId, PDO::PARAM_INT);
            $stmt->bindParam(2, $proveedorId, PDO::PARAM_INT);
            $stmt->bindParam(3, $proveedorId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        } catch (Exception $e) {
            error_log("Error al obtener estadísticas del proveedor: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener proveedores para dropdown/select
     * @param string $tipo Filtrar por tipo (opcional)
     * @return array Lista simplificada para selects
     */
    public function obtenerParaSelect($tipo = null) {
        try {
            $query = "SELECT proveedor_id as id, codigo_proveedor, nombre_completo 
                     FROM vista_proveedores_completa 
                     WHERE proveedor_activo = 1";
            
            $parametros = [];
            if ($tipo) {
                $query .= " AND tipo_proveedor = ?";
                $parametros[] = $tipo;
            }
            
            $query .= " ORDER BY nombre_completo";

            $stmt = $this->db->prepare($query);
            
            foreach ($parametros as $indice => $valor) {
                $stmt->bindValue($indice + 1, $valor);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error al obtener proveedores para select: " . $e->getMessage());
            return [];
        }
    }
}
?>
