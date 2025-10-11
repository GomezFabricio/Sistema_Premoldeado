<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../config/modules.php';

class MaterialController extends BaseController {
    private $material;

    public function __construct() {
        parent::__construct();
        $this->material = new Material();
        
        // Auto-ejecución siempre
        $this->handleRequest();
    }

    /**
     * ✅ NUEVO: Método estándar para manejar acciones GET
     */
    public function handleRequest() {
        // Verificar acceso al módulo de materiales
        $this->verificarAccesoModulo(ModuleConfig::MATERIALES);
        
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
            default:
                $this->index();
                break;
        }
    }

    /**
     * Página principal de listado de materiales (compatible con ?action=index)
     */
    public function index() {
        try {
            $items = $this->material->listar();
            $data = [
                'titulo' => 'Gestión de Materiales',
                'items' => $items,
                'totalMateriales' => count($items)
            ];
            $this->render(__DIR__ . '/../views/pages/materiales/listado_materiales.php', $data);
        } catch (Exception $e) {
            $this->manejarError("Error al cargar listado de materiales: " . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para crear nuevo material (compatible con ?action=create)
     */
    public function create() {
        try {
            $data = [
                'titulo' => 'Nuevo Material',
                'categorias' => $this->material->obtenerCategorias(),
                'unidadesMedida' => $this->material->obtenerUnidadesMedida()
            ];

            $this->render(__DIR__ . '/../views/pages/materiales/crear_material.php', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al cargar formulario de creación: " . $e->getMessage());
        }
    }

    /**
     * Procesar creación de nuevo material (compatible con ?action=store)
     */
    public function store() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            // Sanitizar datos de entrada
            $datosMaterial = $this->sanitizarDatos([
                'codigo_barras' => $_POST['codigo_barras'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'unidad_medida_id' => intval($_POST['unidad_medida_id'] ?? 0),
                'cantidad_stock' => floatval($_POST['cantidad_stock'] ?? 0),
                'costo_unitario' => floatval($_POST['costo_unitario'] ?? 0),
                'precio_venta' => floatval($_POST['precio_venta'] ?? 0) ?: null,
                'stock_minimo' => floatval($_POST['stock_minimo'] ?? 0),
                'stock_maximo' => floatval($_POST['stock_maximo'] ?? 0) ?: null,
                'ubicacion_deposito' => $_POST['ubicacion_deposito'] ?? null
            ]);

            // Validación básica
            if (empty($datosMaterial['nombre'])) {
                throw new Exception("El nombre del material es obligatorio");
            }

            if ($datosMaterial['unidad_medida_id'] <= 0) {
                throw new Exception("Debe seleccionar una unidad de medida válida");
            }

            // Crear material
            $materialId = $this->material->crear($datosMaterial);
            
            if ($materialId) {
                $_SESSION['mensaje_exito'] = "Material creado exitosamente";
                $this->redirect('?action=index');
            } else {
                throw new Exception("No se pudo crear el material");
            }

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            $_SESSION['datos_formulario'] = $datosMaterial ?? [];
            $this->redirect('?action=create');
        }
    }

    /**
     * Mostrar formulario para editar material (compatible con ?action=edit)
     */
    public function edit($id) {
        try {
            $id = intval($id);
            if (!$id) {
                throw new Exception("ID de material no válido");
            }

            $material = $this->material->obtenerPorId($id);
            if (!$material) {
                throw new Exception("Material no encontrado");
            }

            $data = [
                'titulo' => 'Editar Material',
                'material' => $material,
                'categorias' => $this->material->obtenerCategorias(),
                'unidadesMedida' => $this->material->obtenerUnidadesMedida()
            ];

            $this->render(__DIR__ . '/../views/pages/materiales/editar_material.php', $data);

        } catch (Exception $e) {
            $this->manejarError("Error al cargar formulario de edición: " . $e->getMessage());
        }
    }

    /**
     * Procesar actualización de material (compatible con ?action=update)
     */
    public function update($id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $id = intval($id);
            if (!$id) {
                throw new Exception("ID de material no válido");
            }

            // Sanitizar datos de entrada
            $datosMaterial = $this->sanitizarDatos([
                'codigo_barras' => $_POST['codigo_barras'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'unidad_medida_id' => intval($_POST['unidad_medida_id'] ?? 0),
                'cantidad_stock' => floatval($_POST['cantidad_stock'] ?? 0),
                'costo_unitario' => floatval($_POST['costo_unitario'] ?? 0),
                'precio_venta' => floatval($_POST['precio_venta'] ?? 0) ?: null,
                'stock_minimo' => floatval($_POST['stock_minimo'] ?? 0),
                'stock_maximo' => floatval($_POST['stock_maximo'] ?? 0) ?: null,
                'ubicacion_deposito' => $_POST['ubicacion_deposito'] ?? null
            ]);

            // Validación básica
            if (empty($datosMaterial['nombre'])) {
                throw new Exception("El nombre del material es obligatorio");
            }

            if ($datosMaterial['unidad_medida_id'] <= 0) {
                throw new Exception("Debe seleccionar una unidad de medida válida");
            }

            // Actualizar material
            $resultado = $this->material->actualizar($id, $datosMaterial);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Material actualizado exitosamente";
                $this->redirect('?action=index');
            } else {
                throw new Exception("No se pudo actualizar el material");
            }

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
            $_SESSION['datos_formulario'] = $datosMaterial ?? [];
            $this->redirect('?action=edit&id=' . $id);
        }
    }

    /**
     * Eliminar material (compatible con ?action=delete)
     */
    public function delete($id) {
        try {
            $id = intval($id);
            if (!$id) {
                throw new Exception("ID de material no válido");
            }

            // Verificar si puede eliminar
            if (!$this->material->puedeEliminar($id)) {
                throw new Exception("No se puede eliminar este material");
            }

            $resultado = $this->material->eliminar($id);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Material eliminado exitosamente";
            } else {
                throw new Exception("No se pudo eliminar el material");
            }

        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = $e->getMessage();
        }

        $this->redirect('?action=index');
    }
}
?>