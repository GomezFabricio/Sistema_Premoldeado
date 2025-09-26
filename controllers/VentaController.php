<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../config/modules.php';

class VentaController extends BaseController {
    private $ventaModel;

    public function __construct() {
        parent::__construct();
                $this->ventaModel = new Venta();
        
        // Auto-ejecución si hay action en GET
        if (isset($_GET['action'])) {
            $this->handleRequest();
        }
    }

    /**
     * ✅ NUEVO: Método estándar para manejar acciones GET
     */
    public function handleRequest() {
        // Verificar acceso al módulo de proveedores
        $this->verificarAccesoModulo(ModuleConfig::VENTAS);
        
        $action = $_GET['action'] ?? 'index';
        
        switch($action) {
            case 'index':
                $this->index();
                break;
            case 'create':
                $this->create();
                break;
            case 'store':
                $this->store();
                break;
            case 'edit':
                if (isset($_GET['id'])) {
                    $this->edit($_GET['id']);
                }
                break;
            case 'update':
                if (isset($_GET['id'])) {
                    $this->update($_GET['id']);
                }
                break;
            case 'delete':
                if (isset($_GET['id'])) {
                    $this->delete($_GET['id']);
                }
                break;
        }
    }

    /**
     * Página principal de listado de ventas
     */
    public function index() {
        try {
            $items = $this->ventaModel->listar();
            $data = [
                'titulo' => 'Gestión de Ventas',
                'items' => $items,
                'totalVentas' => count($items)
            ];
            $this->render('pages/ventas/listado_ventas', $data);
        } catch (Exception $e) {
            $this->manejarError("Error al cargar listado de ventas: " . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar proveedor
     */
    public function editar() {
        try {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de venta no válido");
            }

            $venta = $this->ventaModel->obtenerPorId($id);
            if (!$venta) {
                throw new Exception("Venta no encontrada");
            }

            $data = [
                'titulo' => 'Editar Venta',
                'venta' => $venta,
                // Agregar otros datos necesarios para la edición de venta
            ];

            $this->render('pages/ventas/editar_venta', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al cargar formulario de edición: " . $e->getMessage());
        }
    }

    /**
     * Procesar actualización de proveedor
     */
    public function procesarActualizacion() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de proveedor no válido");
            }

            // Sanitizar datos de entrada
            $datosPersona = $this->sanitizarDatos([
                'tipo_documento' => $_POST['tipo_documento'] ?? null,
                'numero_documento' => $_POST['numero_documento'] ?? null,
                'apellidos' => $_POST['apellidos'] ?? null,
                'nombres' => $_POST['nombres'] ?? null,
                'razon_social' => $_POST['razon_social'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'telefono_alternativo' => $_POST['telefono_alternativo'] ?? null,
                'email' => $_POST['email'] ?? null,
                'direccion' => $_POST['direccion'] ?? null,
                'localidad' => $_POST['localidad'] ?? null,
                'provincia' => $_POST['provincia'] ?? null,
                'codigo_postal' => $_POST['codigo_postal'] ?? null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'observaciones' => $_POST['observaciones_persona'] ?? null
            ]);

            $datosProveedor = $this->sanitizarDatos([
                'cuit' => $_POST['cuit'] ?? null,
                'condicion_iva' => $_POST['condicion_iva'] ?? null,
                'tipo_proveedor' => $_POST['tipo_proveedor'] ?? null,
                'condicion_pago' => $_POST['condicion_pago'] ?? null,
                'descuento_pronto_pago' => isset($_POST['descuento_pronto_pago']) ? floatval($_POST['descuento_pronto_pago']) : null,
                'recargo_financiacion' => isset($_POST['recargo_financiacion']) ? floatval($_POST['recargo_financiacion']) : null,
                'plazo_entrega_dias' => isset($_POST['plazo_entrega_dias']) ? intval($_POST['plazo_entrega_dias']) : null,
                'calificacion' => $_POST['calificacion'] ?? null,
                'contacto_comercial' => $_POST['contacto_comercial'] ?? null,
                'telefono_comercial' => $_POST['telefono_comercial'] ?? null,
                'email_comercial' => $_POST['email_comercial'] ?? null,
                'observaciones' => $_POST['observaciones'] ?? null
            ]);

            // Filtrar solo los campos que tienen valor
            $datosPersona = array_filter($datosPersona, function($valor) {
                return $valor !== null && $valor !== '';
            });

            $datosProveedor = array_filter($datosProveedor, function($valor) {
                return $valor !== null && $valor !== '';
            });

            // Actualizar proveedor
            $resultado = $this->proveedor->actualizar($id, $datosPersona, $datosProveedor);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Proveedor actualizado exitosamente";
            } else {
                throw new Exception("No se pudo actualizar el proveedor");
            }

            header('Location: /controllers/ProveedorController.php?accion=index');

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            header('Location: /controllers/ProveedorController.php?accion=editar&id=' . ($id ?? 0));
        }
        exit;
    }

    /**
     * Eliminar (desactivar) proveedor
     */
    public function eliminar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de proveedor no válido");
            }

            $resultado = $this->proveedor->eliminar($id);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Proveedor eliminado exitosamente";
            } else {
                throw new Exception("No se pudo eliminar el proveedor");
            }

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
        }

        header('Location: /controllers/ProveedorController.php?accion=index');
        exit;
    }

    /**
     * Buscar proveedores por criterios
     */
    public function buscar() {
        try {
            $criterios = [
                'nombre' => $_GET['nombre'] ?? '',
                'codigo' => $_GET['codigo'] ?? '',
                'cuit' => $_GET['cuit'] ?? '',
                'tipo_proveedor' => $_GET['tipo_proveedor'] ?? '',
                'calificacion' => $_GET['calificacion'] ?? '',
                'localidad' => $_GET['localidad'] ?? ''
            ];

            // Filtrar criterios vacíos
            $criterios = array_filter($criterios, function($valor) {
                return $valor !== '' && $valor !== null;
            });

            if (empty($criterios)) {
                $proveedores = $this->proveedor->listar();
            } else {
                $proveedores = $this->proveedor->buscar($criterios);
            }

            $data = [
                'titulo' => 'Búsqueda de Proveedores',
                'proveedores' => $proveedores,
                'criterios' => $criterios,
                'totalProveedores' => count($proveedores)
            ];

            $this->render('pages/proveedores/listado_proveedores', $data);

        } catch (Exception $e) {
            $this->manejarError("Error en búsqueda de proveedores: " . $e->getMessage());
        }
    }

    /**
     * Ver detalles de un proveedor
     */
    public function ver() {
        try {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                throw new Exception("ID de proveedor no válido");
            }

            $proveedor = $this->proveedor->obtenerPorId($id);
            if (!$proveedor) {
                throw new Exception("Proveedor no encontrado");
            }

            $estadisticas = $this->proveedor->obtenerEstadisticas($id);

            $data = [
                'titulo' => 'Detalles del Proveedor',
                'proveedor' => $proveedor,
                'estadisticas' => $estadisticas
            ];

            $this->render('pages/proveedores/ver_proveedor', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al ver proveedor: " . $e->getMessage());
        }
    }

    /**
     * Filtrar proveedores por tipo
     */
    public function filtrarPorTipo() {
        try {
            $tipo = $_GET['tipo'] ?? '';
            if (empty($tipo)) {
                throw new Exception("Tipo de proveedor requerido");
            }

            $proveedores = $this->proveedor->obtenerPorTipo($tipo);

            $data = [
                'titulo' => "Proveedores - $tipo",
                'proveedores' => $proveedores,
                'filtroActivo' => $tipo,
                'totalProveedores' => count($proveedores)
            ];

            $this->cargarVista('pages/proveedores/listado_proveedores', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al filtrar proveedores: " . $e->getMessage());
        }
    }

    /**
     * Filtrar proveedores por calificación
     */
    public function filtrarPorCalificacion() {
        try {
            $calificacion = $_GET['calificacion'] ?? 'C';
            
            $proveedores = $this->proveedor->obtenerPorCalificacion($calificacion);

            $data = [
                'titulo' => "Proveedores - Calificación $calificacion o superior",
                'proveedores' => $proveedores,
                'filtroActivo' => $calificacion,
                'totalProveedores' => count($proveedores)
            ];

            $this->render('pages/proveedores/listado_proveedores', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al filtrar proveedores por calificación: " . $e->getMessage());
        }
    }

    /**
     * API para obtener proveedores (JSON)
     */
    public function api() {
        try {
            header('Content-Type: application/json');
            
            $accion = $_GET['accion'] ?? 'listar';
            
            switch ($accion) {
                case 'listar':
                    $tipo = $_GET['tipo'] ?? null;
                    $proveedores = $tipo ? $this->proveedor->obtenerPorTipo($tipo) : $this->proveedor->listar();
                    echo json_encode(['success' => true, 'data' => $proveedores]);
                    break;
                    
                case 'buscar':
                    $criterios = [
                        'nombre' => $_GET['nombre'] ?? '',
                        'codigo' => $_GET['codigo'] ?? '',
                        'cuit' => $_GET['cuit'] ?? ''
                    ];
                    
                    $criterios = array_filter($criterios);
                    $proveedores = empty($criterios) ? [] : $this->proveedor->buscar($criterios);
                    echo json_encode(['success' => true, 'data' => $proveedores]);
                    break;
                    
                case 'obtener':
                    $id = intval($_GET['id'] ?? 0);
                    if (!$id) {
                        throw new Exception("ID requerido");
                    }
                    
                    $proveedor = $this->proveedor->obtenerPorId($id);
                    if (!$proveedor) {
                        throw new Exception("Proveedor no encontrado");
                    }
                    
                    echo json_encode(['success' => true, 'data' => $proveedor]);
                    break;
                    
                case 'select':
                    $tipo = $_GET['tipo'] ?? null;
                    $proveedores = $this->proveedor->obtenerParaSelect($tipo);
                    echo json_encode(['success' => true, 'data' => $proveedores]);
                    break;
                    
                default:
                    throw new Exception("Acción no válida");
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Obtener tipos de proveedor para select
     */
    private function obtenerTiposProveedor() {
        return [
            'MATERIALES' => 'Materiales',
            'SERVICIOS' => 'Servicios',
            'TRANSPORTE' => 'Transporte',
            'EQUIPOS' => 'Equipos',
            'HERRAMIENTAS' => 'Herramientas',
            'ENERGIA' => 'Energía',
            'OTROS' => 'Otros'
        ];
    }

    /**
     * Obtener condiciones de IVA para proveedores
     */
    private function obtenerCondicionesIVAProveedor() {
        return [
            'RESPONSABLE_INSCRIPTO' => 'Responsable Inscripto',
            'MONOTRIBUTISTA' => 'Monotributista',
            'EXENTO' => 'Exento',
            'NO_INSCRIPTO' => 'No Inscripto'
        ];
    }

    /**
     * Obtener condiciones de pago para select
     */
    private function obtenerCondicionesPago() {
        return [
            'CONTADO' => 'Contado',
            '7_DIAS' => '7 días',
            '15_DIAS' => '15 días',
            '30_DIAS' => '30 días',
            '45_DIAS' => '45 días',
            '60_DIAS' => '60 días',
            '90_DIAS' => '90 días',
            '120_DIAS' => '120 días'
        ];
    }

    /**
     * Obtener calificaciones para select
     */
    private function obtenerCalificaciones() {
        return [
            'A' => 'A - Excelente',
            'B' => 'B - Muy Bueno',
            'C' => 'C - Bueno',
            'D' => 'D - Regular',
            'E' => 'E - Deficiente'
        ];
    }

    /**
     * Obtener tipos de documento para select
     */
    private function obtenerTiposDocumento() {
        return [
            'CUIT' => 'CUIT',
            'DNI' => 'DNI',
            'CUIL' => 'CUIL',
            'PASAPORTE' => 'Pasaporte',
            'CEDULA' => 'Cédula'
        ];
    }
}

?>
