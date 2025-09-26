<?php
/**
 * Template para Controladores del Sistema Premoldeado
 * 
 * USAR ESTE TEMPLATE para migrar controladores existentes al patrón estándar:
 * - BaseController (autenticación, permisos, render, validación)
 * - NavigationController (URLs centralizadas ?action=)  
 * - ModuleConfig (constantes de permisos)
 *
 * REEMPLAZAR:
 * - [MODULO] por el nombre del módulo (ej: Usuario, Cliente, Pedido)
 * - [MODULO_CONSTANT] por la constante en ModuleConfig (ej: USUARIOS, CLIENTES)
 * - [modulo] por nombre en minúscula (ej: usuario, cliente, pedido)
 * - [modulos] por plural en minúscula (ej: usuarios, clientes, pedidos)
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/[MODULO].php';
require_once __DIR__ . '/../config/modules.php';

class [MODULO]Controller extends BaseController {
    private $[modulo]Model;
    
    public function __construct() {
        parent::__construct();
        $this->[modulo]Model = new [MODULO]();
    }

    /**
     * Punto de entrada principal - maneja las acciones según parámetro ?action
     * ESTE MÉTODO ES OBLIGATORIO - Patrón NavigationController
     */
    public function handleRequest() {
        $action = $_GET['action'] ?? 'index';
        $id = $_GET['id'] ?? null;

        switch ($action) {
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
                $this->edit($id);
                break;
            case 'update':
                $this->update($id);
                break;
            case 'delete':
                $this->delete($id);
                break;
            // Agregar acciones específicas del módulo aquí
            default:
                $this->index();
                break;
        }
    }

    /**
     * Mostrar listado de [modulos]
     * PATRÓN: BaseController->verificarAccesoModulo + render
     */
    public function index() {
        // ✅ OBLIGATORIO: Verificar acceso usando ModuleConfig
        if (!$this->verificarAccesoModulo(ModuleConfig::[MODULO_CONSTANT])) {
            return; // BaseController redirige automáticamente
        }

        try {
            // Obtener datos del modelo
            $filtros = [];
            // Agregar filtros específicos del módulo aquí
            
            $[modulos] = $this->[modulo]Model->listar($filtros);

            // Preparar datos para la vista
            $data = [
                'title' => 'Gestión de [MODULO]s',
                '[modulos]' => $[modulos],
                'flash_message' => $this->getFlashMessage() // BaseController método
            ];

            // ✅ OBLIGATORIO: Usar BaseController->render()
            $this->render(__DIR__ . '/../views/pages/[modulos]/listado_[modulos].php', $data);

        } catch (Exception $e) {
            error_log("Error en [MODULO]Controller::index: " . $e->getMessage());
            // ✅ OBLIGATORIO: Usar BaseController->redirect()
            $this->redirect(
                'index.php',
                'Error al cargar [modulos]: ' . $e->getMessage(),
                'error'
            );
        }
    }

    /**
     * Mostrar formulario para crear [modulo]
     * PATRÓN: BaseController verificación + render
     */
    public function create() {
        if (!$this->verificarAccesoModulo(ModuleConfig::[MODULO_CONSTANT])) {
            return;
        }

        try {
            // Obtener datos necesarios para formularios (ej: categorías, estados)
            // $categorias = $this->[modulo]Model->obtenerCategorias();
            
            $data = [
                'title' => 'Crear [MODULO]',
                'flash_message' => $this->getFlashMessage()
                // Agregar datos específicos del módulo
            ];

            $this->render(__DIR__ . '/../views/pages/[modulos]/crear_[modulo].php', $data);

        } catch (Exception $e) {
            error_log("Error en [MODULO]Controller::create: " . $e->getMessage());
            $this->redirect(
                'controllers/[MODULO]Controller.php?action=index',
                'Error al cargar formulario: ' . $e->getMessage(),
                'error'
            );
        }
    }

    /**
     * Procesar creación de [modulo]
     * PATRÓN: BaseController verificación + validación + sanitización
     */
    public function store() {
        if (!$this->verificarAccesoModulo(ModuleConfig::[MODULO_CONSTANT])) {
            return;
        }

        // ✅ OBLIGATORIO: Verificar método POST
        $this->verificarMetodo('POST');

        try {
            // ✅ OBLIGATORIO: Sanitizar datos con BaseController
            $datos = $this->sanitizarDatos($_POST);

            // Validar datos específicos del módulo
            $errores = $this->validarDatos[MODULO]($datos);
            
            if (!empty($errores)) {
                $this->redirect(
                    'controllers/[MODULO]Controller.php?action=create',
                    'Errores: ' . implode(', ', $errores),
                    'error'
                );
                return;
            }

            // Crear registro
            $[modulo]Id = $this->[modulo]Model->crear($datos);

            if ($[modulo]Id) {
                $this->redirect(
                    'controllers/[MODULO]Controller.php?action=index',
                    '[MODULO] creado exitosamente',
                    'success'
                );
            } else {
                $this->redirect(
                    'controllers/[MODULO]Controller.php?action=create',
                    'Error al crear [modulo]',
                    'error'
                );
            }

        } catch (Exception $e) {
            error_log("Error en [MODULO]Controller::store: " . $e->getMessage());
            $this->redirect(
                'controllers/[MODULO]Controller.php?action=create',
                'Error: ' . $e->getMessage(),
                'error'
            );
        }
    }

    /**
     * Mostrar formulario para editar [modulo]
     */
    public function edit($id) {
        if (!$this->verificarAccesoModulo(ModuleConfig::[MODULO_CONSTANT])) {
            return;
        }

        try {
            $[modulo] = $this->[modulo]Model->obtenerPorId($id);
            
            if (!$[modulo]) {
                $this->redirect(
                    'controllers/[MODULO]Controller.php?action=index',
                    '[MODULO] no encontrado',
                    'error'
                );
                return;
            }

            $data = [
                'title' => 'Editar [MODULO]',
                '[modulo]' => $[modulo],
                'flash_message' => $this->getFlashMessage()
            ];

            $this->render(__DIR__ . '/../views/pages/[modulos]/editar_[modulo].php', $data);

        } catch (Exception $e) {
            error_log("Error en [MODULO]Controller::edit: " . $e->getMessage());
            $this->redirect(
                'controllers/[MODULO]Controller.php?action=index',
                'Error: ' . $e->getMessage(),
                'error'
            );
        }
    }

    /**
     * Procesar actualización de [modulo]
     */
    public function update($id) {
        if (!$this->verificarAccesoModulo(ModuleConfig::[MODULO_CONSTANT])) {
            return;
        }

        $this->verificarMetodo('POST');

        try {
            $datos = $this->sanitizarDatos($_POST);
            $errores = $this->validarDatos[MODULO]($datos, $id);
            
            if (!empty($errores)) {
                $this->redirect(
                    'controllers/[MODULO]Controller.php?action=edit&id=' . $id,
                    'Errores: ' . implode(', ', $errores),
                    'error'
                );
                return;
            }

            if ($this->[modulo]Model->actualizar($id, $datos)) {
                $this->redirect(
                    'controllers/[MODULO]Controller.php?action=index',
                    '[MODULO] actualizado exitosamente',
                    'success'
                );
            } else {
                $this->redirect(
                    'controllers/[MODULO]Controller.php?action=edit&id=' . $id,
                    'Error al actualizar',
                    'error'
                );
            }

        } catch (Exception $e) {
            error_log("Error en [MODULO]Controller::update: " . $e->getMessage());
            $this->redirect(
                'controllers/[MODULO]Controller.php?action=edit&id=' . $id,
                'Error: ' . $e->getMessage(),
                'error'
            );
        }
    }

    /**
     * Eliminar [modulo] - respuesta AJAX
     * PATRÓN: BaseController->jsonResponse
     */
    public function delete($id) {
        // ✅ OBLIGATORIO: Verificar permisos sin redirect para AJAX
        if (!$this->verificarAccesoModulo(ModuleConfig::[MODULO_CONSTANT], false)) {
            $this->jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
            return;
        }

        try {
            if ($this->[modulo]Model->eliminar($id)) {
                // ✅ OBLIGATORIO: Usar BaseController->jsonResponse
                $this->jsonResponse([
                    'success' => true, 
                    'message' => '[MODULO] eliminado exitosamente'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false, 
                    'message' => 'Error al eliminar'
                ], 500);
            }

        } catch (Exception $e) {
            error_log("Error en [MODULO]Controller::delete: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar datos específicos del módulo
     * USAR BaseController->validarDatos() como base
     */
    private function validarDatos[MODULO]($datos, $idExcluir = null) {
        $reglas = [
            // Definir reglas específicas del módulo
            'nombre' => ['required' => true, 'max_length' => 100],
            // Agregar más reglas según necesidad
        ];

        // ✅ OBLIGATORIO: Usar BaseController->validarDatos()
        $errores = $this->validarDatos($datos, $reglas);

        // Agregar validaciones específicas del módulo aquí
        
        return array_values($errores);
    }
}

// ✅ OBLIGATORIO: Auto-ejecución para compatibilidad con NavigationController
if (basename($_SERVER['PHP_SELF']) === '[MODULO]Controller.php') {
    $controller = new [MODULO]Controller();
    $controller->handleRequest();
}
?>