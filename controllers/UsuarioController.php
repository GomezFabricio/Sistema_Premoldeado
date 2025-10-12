<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/modules.php';

/**
 * Controlador de Usuarios
 * Maneja el CRUD completo de usuarios
 */
class UsuarioController extends BaseController {
    private $usuarioModel;
    
    /**
     * Constructor - Inicializa el controlador
     */
    public function __construct() {
        parent::__construct();                           // Llama al constructor del BaseController
        $this->verificarAccesoModulo(ModuleConfig::USUARIOS); // Verifica acceso al módulo
        $this->usuarioModel = new Usuario();             // Instancia el modelo Usuario
    }
    
    /**
     * Método principal - Maneja las peticiones
     */
    public function handleRequest() {
        $action = $_GET['a'] ?? 'index';
        $id = $_GET['id'] ?? null;
        
        switch ($action) {
            case 'list':
            case 'index':
                $this->index();
                break;
            case 'crear':
                $this->crear();
                break;
            case 'guardarNuevoUsuario':
                $this->guardarNuevoUsuario();
                break;
            case 'edit':
            case 'editar':
                if ($id) {
                    $this->editarUsuario($id);
                } else {
                    $this->redirectToDashboard('ID de usuario requerido', 'error');
                }
                break;
            case 'update':
            case 'actualizar':
                if ($id) {
                    $this->actualizarUsuario($id);
                } else {
                    $this->redirectToDashboard('ID de usuario requerido', 'error');
                }
                break;
            case 'delete':
            case 'eliminar':
                if ($id) {
                    $this->eliminarUsuario($id);
                } else {
                    $this->redirectToDashboard('ID de usuario requerido', 'error');
                }
                break;
            case 'reactivar':
                if ($id) {
                    $this->reactivarUsuario($id);
                } else {
                    $this->redirectToDashboard('ID de usuario requerido', 'error');
                }
                break;
            // Gestión de Perfiles
            case 'perfiles':
                $this->listarPerfiles();
                break;
            case 'crear_perfil':
                $this->crearPerfil();
                break;
            case 'guardar_perfil':
                $this->guardarPerfil();
                break;
            case 'editar_perfil':
                if ($id) {
                    $this->editarPerfil($id);
                } else {
                    $this->redirectToDashboard('ID de perfil requerido', 'error');
                }
                break;
            case 'actualizar_perfil':
                if ($id) {
                    $this->actualizarPerfil($id);
                } else {
                    $this->redirectToDashboard('ID de perfil requerido', 'error');
                }
                break;
            case 'eliminar_perfil':
                if ($id) {
                    $this->eliminarPerfil($id);
                } else {
                    $this->redirectToDashboard('ID de perfil requerido', 'error');
                }
                break;
            case 'reactivar_perfil':
                if ($id) {
                    $this->reactivarPerfil($id);
                } else {
                    $this->redirectToDashboard('ID de perfil requerido', 'error');
                }
                break;
            default:
                $this->index();
        }
    }
    
    /**
     * Mostrar lista de usuarios
     */
    public function index() {
        try {
            $usuarios = $this->usuarioModel->listar();
            
            $data = [
                'usuarios' => $usuarios ?? [],
                'titulo' => 'Gestión de Usuarios',
                'usuario_logueado' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/usuarios/listado_usuarios.php', $data);
            
        } catch (Exception $e) {
            $this->redirectToDashboard('Error al cargar usuarios: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Mostrar formulario de creación de usuario
     */
    public function crear() {
        $data = [
            'titulo' => 'Crear Usuario',
            'usuario_logueado' => $this->usuario
        ];
        
        $this->render(__DIR__ . '/../views/pages/usuarios/crear_usuario.php', $data);
    }
    
    /**
     * Procesar creación de nuevo usuario
     */
    public function guardarNuevoUsuario() {
        try {
            // Usar función del BaseController
            $this->verificarMetodo('POST');
            
            $datos = $this->sanitizarDatos([
                'nombre_usuario' => $_POST['nombre_usuario'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'domicilio' => $_POST['domicilio'] ?? '',
                'perfil_id' => (int)($_POST['perfil_id'] ?? 1),
                'activo' => 1
            ]);
            
            // Usar función del BaseController para validación
            $reglas = [
                'nombre_usuario' => ['required' => true, 'min_length' => 3, 'max_length' => 50],
                'email' => ['required' => true, 'type' => 'email', 'max_length' => 150],
                'password' => ['required' => true, 'min_length' => 6, 'max_length' => 255],
                'domicilio' => ['max_length' => 200],
                'perfil_id' => ['required' => true, 'type' => 'numeric']
            ];
            
            $errores = $this->validarDatos($datos, $reglas);
            if (!empty($errores)) {
                $this->establecerMensaje('Errores en el formulario: ' . implode(', ', $errores), 'error');
                $this->crear();
                return;
            }
            
            // Crear usuario usando el modelo
            $resultado = $this->usuarioModel->crear($datos);
            
            if ($resultado['success']) {
                $this->redirectToController('Usuario', 'index', [], 'Usuario creado exitosamente', 'success');
            } else {
                $this->establecerMensaje($resultado['message'], 'error');
                $this->crear();
            }
            
        } catch (Exception $e) {
            $this->establecerMensaje('Error al crear usuario: ' . $e->getMessage(), 'error');
            $this->crear();
        }
    }
    
    /**
     * Mostrar formulario de edición
     */
    private function editarUsuario($id) {
        try {
            $usuario = $this->usuarioModel->obtenerPorId($id);
            
            if (!$usuario) {
                $this->redirectToController('Usuario', 'index', [], 'Usuario no encontrado', 'error');
                return;
            }
            
            $data = [
                'usuario' => $usuario,
                'titulo' => 'Editar Usuario',
                'usuario_logueado' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/usuarios/editar_usuario.php', $data);
            
        } catch (Exception $e) {
            $this->redirectToController('Usuario', 'index', [], 'Error al cargar usuario: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Procesar actualización de usuario
     */
    private function actualizarUsuario($id) {
        try {
            // Usar función del BaseController
            $this->verificarMetodo('POST');
            
            $datos = [
                'nombre_usuario' => trim($_POST['nombre_usuario'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'domicilio' => trim($_POST['domicilio'] ?? ''),
                'perfil_id' => (int)($_POST['perfil_id'] ?? 1),
                'activo' => (int)($_POST['activo'] ?? 1)
            ];
            
            // Usar función del BaseController para validación
            $reglas = [
                'nombre_usuario' => ['required' => true, 'min_length' => 3, 'max_length' => 50],
                'email' => ['required' => true, 'type' => 'email', 'max_length' => 150],
                'domicilio' => ['max_length' => 200],
                'perfil_id' => ['required' => true, 'type' => 'numeric']
            ];
            
            if (!empty($datos['password'])) {
                $reglas['password'] = ['min_length' => 6, 'max_length' => 255];
            }
            
            $errores = $this->validarDatos($datos, $reglas);
            if (!empty($errores)) {
                $this->establecerMensaje('Errores en el formulario: ' . implode(', ', $errores), 'error');
                $this->editarUsuario($id);
                return;
            }
            
            // Actualizar usuario
            $resultado = $this->usuarioModel->actualizar($id, $datos);
            
            if ($resultado['success']) {
                $this->redirectToController('Usuario', 'index', [], 'Usuario actualizado exitosamente', 'success');
            } else {
                $this->establecerMensaje($resultado['message'], 'error');
                $this->editarUsuario($id);
            }
            
        } catch (Exception $e) {
            $this->establecerMensaje('Error al actualizar usuario: ' . $e->getMessage(), 'error');
            $this->editarUsuario($id);
        }
    }
    
    /**
     * Eliminar usuario (baja lógica)
     */
    private function eliminarUsuario($id) {
        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id) || $id <= 0) {
                $this->redirectToController('Usuario', 'index', [], 'ID de usuario inválido', 'error');
                return;
            }
            
            $resultado = $this->usuarioModel->darDeBaja($id);
            
            if ($resultado['success']) {
                $this->redirectToController('Usuario', 'index', [], 'Usuario desactivado exitosamente', 'success');
            } else {
                $this->redirectToController('Usuario', 'index', [], $resultado['message'], 'error');
            }
            
        } catch (Exception $e) {
            $this->redirectToController('Usuario', 'index', [], 'Error al desactivar usuario: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Reactivar usuario
     */
    private function reactivarUsuario($id) {
        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id) || $id <= 0) {
                $this->redirectToController('Usuario', 'index', [], 'ID de usuario inválido', 'error');
                return;
            }
            
            $resultado = $this->usuarioModel->reactivar($id);
            
            if ($resultado['success']) {
                $this->redirectToController('Usuario', 'index', [], 'Usuario reactivado exitosamente', 'success');
            } else {
                $this->redirectToController('Usuario', 'index', [], $resultado['message'], 'error');
            }
            
        } catch (Exception $e) {
            $this->redirectToController('Usuario', 'index', [], 'Error al reactivar usuario: ' . $e->getMessage(), 'error');
        }
    }
    

    
    // ============================================================================
    // GESTIÓN DE PERFILES
    // ============================================================================
    
    /**
     * Mostrar lista de perfiles
     */
    public function listarPerfiles() {
        try {
            $perfiles = $this->usuarioModel->obtenerTodosPerfiles();
            
            $data = [
                'perfiles' => $perfiles ?? [],
                'titulo' => 'Gestión de Perfiles',
                'usuario_logueado' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/usuarios/perfiles/listado_perfiles.php', $data);
            
        } catch (Exception $e) {
            $this->redirectToDashboard('Error al cargar perfiles: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Mostrar formulario de creación de perfil
     */
    public function crearPerfil() {
        try {
            $modulos = $this->usuarioModel->obtenerTodosModulos();
            
            $data = [
                'modulos' => $modulos ?? [],
                'titulo' => 'Crear Perfil',
                'usuario_logueado' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/usuarios/perfiles/crear_perfil.php', $data);
            
        } catch (Exception $e) {
            $this->redirectToDashboard('Error al cargar módulos: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Procesar creación de nuevo perfil
     */
    public function guardarPerfil() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirectToDashboard('Método no permitido', 'error');
                return;
            }
            
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? '')
            ];
            
            $modulos = $_POST['modulos'] ?? [];
            
            // Debug: Log para verificar qué datos llegan
            error_log("DEBUG - Crear Perfil: Nombre = " . $datos['nombre']);
            error_log("DEBUG - Crear Perfil: Módulos recibidos = " . print_r($modulos, true));
            
            // Validaciones
            if (empty($datos['nombre'])) {
                $this->establecerMensaje('El nombre del perfil es requerido', 'error');
                $this->crearPerfil();
                return;
            }
            
            // Crear perfil usando el modelo
            $resultado = $this->usuarioModel->crearPerfil($datos);
            
            if ($resultado['success']) {
                error_log("DEBUG - Perfil creado con ID: " . $resultado['id']);
                
                // Asignar módulos al perfil
                if (!empty($modulos)) {
                    error_log("DEBUG - Asignando " . count($modulos) . " módulos al perfil ID " . $resultado['id']);
                    error_log("DEBUG - Módulos: " . print_r($modulos, true));
                    
                    $resultadoModulos = $this->usuarioModel->asignarModulosAPerfil($resultado['id'], $modulos);
                    error_log("DEBUG - Resultado asignación módulos: " . print_r($resultadoModulos, true));
                    
                    if (!$resultadoModulos['success']) {
                        $this->establecerMensaje('Perfil creado, pero error al asignar módulos: ' . $resultadoModulos['message'], 'error');
                        $this->crearPerfil();
                        return;
                    }
                } else {
                    error_log("DEBUG - No hay módulos para asignar - array vacío o null");
                }
                
                $this->redirectToController('Usuario', 'perfiles', [], 'Perfil creado exitosamente', 'success');
            } else {
                $this->establecerMensaje($resultado['message'], 'error');
                $this->crearPerfil();
            }
            
        } catch (Exception $e) {
            $this->establecerMensaje('Error al crear perfil: ' . $e->getMessage(), 'error');
            $this->crearPerfil();
        }
    }
    
    /**
     * Mostrar formulario de edición de perfil
     */
    public function editarPerfil($id) {
        try {
            $perfil = $this->usuarioModel->obtenerPerfilPorId($id);
            
            if (!$perfil) {
                $this->redirectToController('Usuario', 'perfiles', [], 'Perfil no encontrado', 'error');
                return;
            }
            
            $modulos = $this->usuarioModel->obtenerTodosModulos();
            $modulosAsignados = $this->usuarioModel->obtenerModulosAsignadosPorPerfil($id);
            
            // Crear array de IDs de módulos asignados para facilitar el marcado en la vista
            $modulosAsignadosIds = array_column($modulosAsignados, 'id');
            
            $data = [
                'perfil' => $perfil,
                'modulos' => $modulos ?? [],
                'modulosAsignadosIds' => $modulosAsignadosIds,
                'titulo' => 'Editar Perfil',
                'usuario_logueado' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/usuarios/perfiles/editar_perfil.php', $data);
            
        } catch (Exception $e) {
            $this->redirectToController('Usuario', 'perfiles', [], 'Error al cargar perfil: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Procesar actualización de perfil
     */
    public function actualizarPerfil($id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirectToDashboard('Método no permitido', 'error');
                return;
            }
            
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? '')
            ];
            
            $modulos = $_POST['modulos'] ?? [];
            
            // Validaciones
            if (empty($datos['nombre'])) {
                $this->establecerMensaje('El nombre del perfil es requerido', 'error');
                $this->editarPerfil($id);
                return;
            }
            
            // Actualizar perfil
            $resultado = $this->usuarioModel->actualizarPerfil($id, $datos);
            
            if ($resultado['success']) {
                // Actualizar módulos asignados al perfil
                $this->usuarioModel->asignarModulosAPerfil($id, $modulos);
                
                $this->redirectToController('Usuario', 'perfiles', [], 'Perfil actualizado exitosamente', 'success');
            } else {
                $this->establecerMensaje($resultado['message'], 'error');
                $this->editarPerfil($id);
            }
            
        } catch (Exception $e) {
            $this->establecerMensaje('Error al actualizar perfil: ' . $e->getMessage(), 'error');
            $this->editarPerfil($id);
        }
    }
    
    /**
     * Eliminar perfil (baja lógica)
     */
    public function eliminarPerfil($id) {
        try {
            $resultado = $this->usuarioModel->eliminarPerfil($id);
            
            if ($resultado['success']) {
                $this->redirectToController('Usuario', 'perfiles', [], 'Perfil desactivado exitosamente', 'success');
            } else {
                $this->redirectToController('Usuario', 'perfiles', [], $resultado['message'], 'error');
            }
            
        } catch (Exception $e) {
            $this->redirectToController('Usuario', 'perfiles', [], 'Error al desactivar perfil: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Reactivar perfil
     */
    public function reactivarPerfil($id) {
        try {
            $resultado = $this->usuarioModel->reactivarPerfil($id);
            
            if ($resultado['success']) {
                $this->redirectToController('Usuario', 'perfiles', [], 'Perfil reactivado exitosamente', 'success');
            } else {
                $this->redirectToController('Usuario', 'perfiles', [], $resultado['message'], 'error');
            }
            
        } catch (Exception $e) {
            $this->redirectToController('Usuario', 'perfiles', [], 'Error al reactivar perfil: ' . $e->getMessage(), 'error');
        }
    }
}

// Ejecutar el controlador si es llamado directamente
if (basename($_SERVER['PHP_SELF']) === 'UsuarioController.php') {
    $controller = new UsuarioController();
    $controller->handleRequest();
}
?>