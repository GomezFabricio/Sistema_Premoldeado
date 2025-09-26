<?php<?php

require_once __DIR__ . '/BaseController.php';/**

require_once __DIR__ . '/../models/Usuario.php'; * Controlador de Usuarios

require_once __DIR__ . '/../config/modules.php'; * Ejemplo de implementación usando BaseController

 */

class UsuarioController extends BaseController {

    private $usuarioModel;require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../models/Usuario.php';

    public function __construct() {require_once __DIR__ . '/../config/modules.php';

        parent::__construct();

        $this->usuarioModel = new Usuario();class UsuarioController extends BaseController {

            

        // Auto-ejecución si hay action en GET    /**

        if (isset($_GET['action'])) {     * Constructor - Auto-ejecuta handleRequest si hay parámetro action

            $this->handleRequest();     */

        }    public function __construct() {

    }        parent::__construct();

        

    /**        // Auto-ejecución si hay action en GET

     * ✅ NUEVO: Método estándar NavigationController para manejar acciones GET        if (isset($_GET['action'])) {

     */            $this->handleRequest();

    public function handleRequest() {        }

        // ✅ OBLIGATORIO: Verificar acceso al módulo de usuarios usando ModuleConfig    }

        $this->verificarAccesoModulo(ModuleConfig::USUARIOS);    

            public function listar() {

        $action = $_GET['action'] ?? 'index';        // Verificar acceso al módulo de usuarios

        $id = $_GET['id'] ?? null;        $this->verificarAccesoModulo(ModuleConfig::USUARIOS);

                

        switch($action) {        try {

            case 'index':            // Obtener usuarios reales desde la base de datos

                $this->index();            $usuarioModel = new Usuario();

                break;            $usuarios = $usuarioModel->listar();

            case 'create':            

                $this->create();            // Datos para la vista

                break;            $datos = [

            case 'store':                'pageTitle' => 'Gestión de Usuarios',

                $this->store();                'usuarios' => $usuarios,

                break;                'usuario' => $this->usuario

            case 'edit':            ];

                if ($id) {            

                    $this->edit($id);            // Renderizar vista

                }            $this->render(__DIR__ . '/../views/pages/usuarios/listado_usuarios.php', $datos);

                break;            

            case 'update':        } catch (Exception $e) {

                if ($id) {            error_log("Error en listado de usuarios: " . $e->getMessage());

                    $this->update($id);            

                }            // En caso de error, mostrar vista con datos vacíos y mensaje de error

                break;            $datos = [

            case 'delete':                'pageTitle' => 'Gestión de Usuarios',

                if ($id) {                'usuarios' => [],

                    $this->delete($id);                'usuario' => $this->usuario,

                }                'error' => 'Error al cargar usuarios. Por favor intente nuevamente.'

                break;            ];

            case 'perfiles':            

                $this->indexPerfiles();            $this->render(__DIR__ . '/../views/pages/usuarios/listado_usuarios.php', $datos);

                break;        }

            default:    }

                $this->index();    

                break;    private function obtenerUsuarios() {

        }        // Método deprecado - usar listar() en su lugar

    }        return $this->listar();

    }

    /**    

     * Método principal - listado de usuarios (compatible con ?action=index)    public function crear() {

     * ✅ BaseController->render() pattern        // Verificar acceso al módulo de usuarios

     */        $this->verificarAccesoModulo(ModuleConfig::USUARIOS);

    public function index() {        

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $items = $this->usuarioModel->listar();            // Procesar creación
                $this->procesarCreacion();
                $data = [
                    'titulo' => 'Gestión de Usuarios',            // Mostrar formulario
                    'items' => $items,
                    'totalUsuarios' => count($items),
                    'usuario' => $this->usuario
                ];
                // ✅ OBLIGATORIO: BaseController->render()            $this->render(__DIR__ . '/../views/pages/usuarios/crear_usuario.php', $datos);
                $this->render('pages/usuarios/listado_usuarios', $data);

                'totalUsuarios' => count($usuarios),                'pageTitle' => 'Crear Usuario',

                'usuario' => $this->usuario                'usuario' => $this->usuario

            ];            ];

            

            // ✅ OBLIGATORIO: BaseController->render()            $this->render(__DIR__ . '/../views/pages/usuarios/crear_usuario.php', $datos);

            $this->render('pages/usuarios/listado_usuarios', $data);        }

    }

        } catch (Exception $e) {    

            $this->manejarError("Error al cargar listado de usuarios: " . $e->getMessage());    private function procesarCreacion() {

        }        // Validar datos con nombres de campos correctos

    }        $reglas = [

            'nombre_usuario' => ['required' => true, 'type' => 'string', 'max_length' => 50],

    /**            'email' => ['required' => true, 'type' => 'email'],

     * Mostrar formulario para crear nuevo usuario (compatible con ?action=create)            'password' => ['required' => true, 'min_length' => 6],

     */            'perfil_id' => ['required' => true, 'type' => 'numeric']

    public function create() {        ];

        try {        

            $data = [        $datos = $this->sanitizarDatos($_POST);

                'titulo' => 'Crear Usuario',        $errores = $this->validarDatos($datos, $reglas);

                'perfiles' => $this->usuarioModel->obtenerPerfiles(),        

                'usuario' => $this->usuario        if (empty($errores)) {

            ];            // TODO: Implementar creación en base de datos

            $this->redirect('listado_usuarios.php', 'Usuario creado exitosamente', 'success');

            $this->render('pages/usuarios/crear_usuario', $data);        } else {

            // Mostrar errores

        } catch (Exception $e) {            $this->redirect('crear_usuario.php', 'Error en los datos: ' . implode(', ', $errores), 'error');

            $this->manejarError("Error al cargar formulario de creación: " . $e->getMessage());        }

        }    }

    }    

    // ============================================================================

    /**    // SUBMÓDULO PERFILES - Métodos del controlador para gestión de perfiles

     * Procesar creación de nuevo usuario - ✅ BaseController sanitización + validación    // ============================================================================

     */    

    public function store() {    /**

        try {     * ✅ NUEVO: Método estándar para manejar acciones GET

            // ✅ OBLIGATORIO: BaseController verificar método POST     */

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {    public function handleRequest() {

                throw new Exception("Método no permitido");        $action = $_GET['action'] ?? 'index';

            }        

        switch($action) {

            // ✅ OBLIGATORIO: BaseController sanitización            case 'index':

            $datos = $this->sanitizarDatos($_POST);                $this->index();

                            break;

            // Validación específica de usuarios            case 'create':

            $reglas = [                $this->create();

                'nombre_usuario' => ['required' => true, 'type' => 'string', 'max_length' => 50],                break;

                'email' => ['required' => true, 'type' => 'email'],            case 'store':

                'password' => ['required' => true, 'min_length' => 6],                $this->store();

                'perfil_id' => ['required' => true, 'type' => 'numeric']                break;

            ];            case 'edit':

                            if (isset($_GET['id'])) {

            $errores = $this->validarDatos($datos, $reglas);                    $this->edit($_GET['id']);

                            }

            if (empty($errores)) {                break;

                // Hash de la contraseña            case 'update':

                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);                if (isset($_GET['id'])) {

                                    $this->update($_GET['id']);

                // Crear usuario usando el modelo                }

                $resultado = $this->usuarioModel->crear($datos);                break;

                            case 'delete':

                if ($resultado) {                if (isset($_GET['id'])) {

                    $this->redirect(                    $this->delete($_GET['id']);

                        'controllers/UsuarioController.php?action=index',                }

                        'Usuario creado exitosamente',                break;

                        'success'            case 'indexPerfiles':

                    );                $this->indexPerfiles();

                } else {                break;

                    throw new Exception("Error al crear usuario en base de datos");            case 'createPerfiles':

                }                $this->createPerfiles();

            } else {                break;

                // Mostrar errores            case 'editPerfiles':

                $this->redirect(                if (isset($_GET['id'])) {

                    'controllers/UsuarioController.php?action=create',                    $this->editPerfiles($_GET['id']);

                    'Error en los datos: ' . implode(', ', $errores),                }

                    'error'                break;

                );            case 'storePerfiles':

            }                $this->storePerfiles();

                break;

        } catch (Exception $e) {            case 'updatePerfiles':

            error_log("Error en UsuarioController::store: " . $e->getMessage());                if (isset($_GET['id'])) {

            $this->redirect(                    $this->updatePerfiles($_GET['id']);

                'controllers/UsuarioController.php?action=create',                }

                'Error interno: ' . $e->getMessage(),                break;

                'error'            case 'deletePerfiles':

            );                if (isset($_GET['id'])) {

        }                    $this->deletePerfiles($_GET['id']);

    }                }

                break;

    /**            case 'getModulosPerfiles':

     * Formulario para editar usuario                if (isset($_GET['id'])) {

     */                    $this->getModulosPerfiles($_GET['id']);

    public function edit($id) {                }

        try {                break;

            $usuario = $this->usuarioModel->obtenerPorId($id);            default:

                            $this->index();

            if (!$usuario) {                break;

                $this->redirect(        }

                    'controllers/UsuarioController.php?action=index',    }

                    'Usuario no encontrado',

                    'error'    /**

                );     * Método principal - listado de usuarios (compatible con ?action=index)

                return;     */

            }    public function index() {

                    $this->listar(); // Usar método existente

            $data = [    }

                'titulo' => 'Editar Usuario',

                'usuario_editar' => $usuario,    /**

                'perfiles' => $this->usuarioModel->obtenerPerfiles(),     * Mostrar formulario de creación (compatible con ?action=create)

                'usuario' => $this->usuario     */

            ];    public function create() {

        $this->crear(); // Usar método existente

            $this->render('pages/usuarios/editar_usuario', $data);    }



        } catch (Exception $e) {    /**

            $this->manejarError("Error al cargar formulario de edición: " . $e->getMessage());     * Procesar creación de usuario (compatible con ?action=store)

        }     */

    }    public function store() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /**            $this->procesarCreacion();

     * Procesar actualización de usuario        } else {

     */            $this->redirect('/controllers/UsuarioController.php?action=create');

    public function update($id) {        }

        try {    }

            // ✅ BaseController verificar método POST

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {    /**

                throw new Exception("Método no permitido");     * Mostrar formulario de edición (compatible con ?action=edit)

            }     */

                public function edit($id) {

            $datos = $this->sanitizarDatos($_POST);        // TODO: Implementar edición

                    $this->redirect('/controllers/UsuarioController.php?action=index', 'Función en desarrollo', 'info');

            $reglas = [    }

                'nombre_usuario' => ['required' => true, 'type' => 'string', 'max_length' => 50],

                'email' => ['required' => true, 'type' => 'email'],    /**

                'perfil_id' => ['required' => true, 'type' => 'numeric']     * Procesar actualización (compatible con ?action=update)

            ];     */

                public function update($id) {

            // Si se proporciona nueva contraseña, validarla        // TODO: Implementar actualización

            if (!empty($datos['password'])) {        $this->redirect('/controllers/UsuarioController.php?action=index', 'Función en desarrollo', 'info');

                $reglas['password'] = ['min_length' => 6];    }

                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);

            } else {    /**

                // Eliminar password vacío para no sobreescribir     * Eliminar usuario (compatible con ?action=delete)

                unset($datos['password']);     */

            }    public function delete($id) {

                    // TODO: Implementar eliminación

            $errores = $this->validarDatos($datos, $reglas);        echo json_encode(['success' => false, 'message' => 'Función en desarrollo']);

                }

            if (empty($errores)) {

                $resultado = $this->usuarioModel->actualizar($id, $datos);    // ============================================================================

                    // SUBMÓDULO PERFILES - Métodos del controlador para gestión de perfiles

                if ($resultado) {    // ============================================================================

                    $this->redirect(

                        'controllers/UsuarioController.php?action=index',    /**

                        'Usuario actualizado exitosamente',     * Lista todos los perfiles del sistema

                        'success'     * 

                    );     * @return void

                } else {     */

                    throw new Exception("Error al actualizar usuario");    public function indexPerfiles() {

                }        require_once __DIR__ . '/NavigationController.php';

            } else {        

                $this->redirect(        try {

                    "controllers/UsuarioController.php?action=edit&id=$id",            // Verificar acceso al módulo de usuarios

                    'Error en los datos: ' . implode(', ', $errores),            $auth = new AuthController();

                    'error'            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {

                );                NavigationController::redirect(

            }                    NavigationController::getDashboardUrl(),

                    'No tienes permisos para acceder a este módulo',

        } catch (Exception $e) {                    'error'

            error_log("Error en UsuarioController::update: " . $e->getMessage());                );

            $this->redirect(            }

                "controllers/UsuarioController.php?action=edit&id=$id",            

                'Error interno: ' . $e->getMessage(),            // Obtener perfiles del modelo

                'error'            $perfiles = Usuario::obtenerTodosPerfiles();

            );            

        }            // Preparar datos para la vista

    }            $pageTitle = 'Gestión de Perfiles';

            $usuario = $auth->getUsuarioLogueado();

    /**            

     * Eliminar usuario (soft delete)            // Renderizar la vista usando los archivos reales

     */            include __DIR__ . '/../views/layouts/header.php';

    public function delete($id) {            include __DIR__ . '/../views/pages/usuarios/perfiles/listado_perfiles.php';

        try {            include __DIR__ . '/../views/layouts/footer.php';

            $resultado = $this->usuarioModel->eliminar($id);            

                    } catch (Exception $e) {

            if ($resultado) {            error_log("Error en indexPerfiles: " . $e->getMessage());

                $this->redirect(            NavigationController::redirect(

                    'controllers/UsuarioController.php?action=index',                NavigationController::getDashboardUrl(),

                    'Usuario eliminado exitosamente',                'Error interno del servidor',

                    'success'                'error'

                );            );

            } else {        }

                throw new Exception("Error al eliminar usuario");    }

            }    

    /**

        } catch (Exception $e) {     * Muestra el formulario de creación de perfil

            error_log("Error en UsuarioController::delete: " . $e->getMessage());     * 

            $this->redirect(     * @return void

                'controllers/UsuarioController.php?action=index',     */

                'Error interno: ' . $e->getMessage(),    public function createPerfiles() {

                'error'        require_once __DIR__ . '/NavigationController.php';

            );        

        }        try {

    }            // Verificar acceso al módulo de usuarios

            $auth = new AuthController();

    /**            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {

     * Gestión de perfiles (submódulo)                NavigationController::redirect(

     */                    NavigationController::getDashboardUrl(),

    public function indexPerfiles() {                    'No tienes permisos para acceder a este módulo',

        try {                    'error'

            $perfiles = $this->usuarioModel->listarPerfiles();                );

                        }

            $data = [            

                'titulo' => 'Gestión de Perfiles',            // Obtener todos los módulos para el formulario

                'perfiles' => $perfiles,            $modulos = Usuario::obtenerTodosModulos();

                'usuario' => $this->usuario            

            ];            // Preparar datos para la vista

            $pageTitle = 'Crear Perfil';

            $this->render('pages/usuarios/perfiles/listado_perfiles', $data);            $usuario = $auth->getUsuarioLogueado();

            

        } catch (Exception $e) {            // Renderizar la vista usando los archivos reales

            $this->manejarError("Error al cargar perfiles: " . $e->getMessage());            include __DIR__ . '/../views/layouts/header.php';

        }            include __DIR__ . '/../views/pages/usuarios/perfiles/crear_perfil.php';

    }            include __DIR__ . '/../views/layouts/footer.php';

            

    /**        } catch (Exception $e) {

     * ✅ Método de BaseController para manejo de errores            error_log("Error en createPerfiles: " . $e->getMessage());

     */            NavigationController::redirect(

    private function manejarError($mensaje) {                NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),

        error_log($mensaje);                'Error interno del servidor',

        $this->redirect(                'error'

            'controllers/UsuarioController.php?action=index',            );

            $mensaje,        }

            'error'    }

        );    

    }    /**

}     * Procesa la creación de un nuevo perfil

     * 

// ✅ Auto-ejecución para compatibilidad con NavigationController     * @return void

if (basename($_SERVER['PHP_SELF']) === 'UsuarioController.php') {     */

    $controller = new UsuarioController();    public function storePerfiles() {

}        try {

?>            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                exit;
            }
            
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Sanitizar datos de entrada
            $datos = [
                'nombre' => isset($_POST['nombre']) ? trim(htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8')) : ''
            ];
            
            // Validar datos
            $reglas = [
                'nombre' => [
                    'required' => true,
                    'type' => 'string',
                    'max_length' => 45,
                    'min_length' => 2
                ]
            ];
            
            $errores = [];
            foreach ($reglas as $campo => $regla) {
                $valor = $datos[$campo] ?? null;
                
                // Campo requerido
                if (isset($regla['required']) && $regla['required'] && empty($valor)) {
                    $errores[$campo] = "El campo {$campo} es requerido";
                    continue;
                }
                
                if (!empty($valor)) {
                    // Longitud mínima
                    if (isset($regla['min_length']) && strlen($valor) < $regla['min_length']) {
                        $errores[$campo] = "El campo {$campo} debe tener al menos {$regla['min_length']} caracteres";
                    }
                    
                    // Longitud máxima
                    if (isset($regla['max_length']) && strlen($valor) > $regla['max_length']) {
                        $errores[$campo] = "El campo {$campo} no puede tener más de {$regla['max_length']} caracteres";
                    }
                }
            }
            
            if (!empty($errores)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errores)]);
                exit;
            }
            
            // Crear perfil
            $resultado = Usuario::crearPerfil($datos);
            
            // Asignar módulos si se seleccionaron
            if ($resultado['success'] && isset($_POST['modulos']) && is_array($_POST['modulos'])) {
                $modulosAsignados = Usuario::asignarModulosAPerfil($resultado['id'], $_POST['modulos']);
                if (!$modulosAsignados['success']) {
                    error_log("Error al asignar módulos al perfil: " . $modulosAsignados['message']);
                }
            }
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en storePerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
    
    /**
     * Muestra el formulario de edición de perfil
     * 
     * @param int $id ID del perfil a editar
     * @return void
     */
    public function editPerfiles($id) {
        require_once __DIR__ . '/NavigationController.php';
        
        try {
            // Verificar acceso al módulo de usuarios
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                NavigationController::redirect(
                    NavigationController::getDashboardUrl(),
                    'No tienes permisos para acceder a este módulo',
                    'error'
                );
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                NavigationController::redirect(
                    NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),
                    'ID de perfil inválido',
                    'error'
                );
            }
            
            // Obtener datos del perfil
            $perfil = Usuario::obtenerPerfilPorId($id);
            if (!$perfil) {
                NavigationController::redirect(
                    NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),
                    'El perfil especificado no existe',
                    'error'
                );
            }
            
            // Obtener módulos disponibles y asignados
            $modulos = Usuario::obtenerTodosModulos();
            $modulosAsignados = Usuario::obtenerModulosAsignadosPorPerfil($id);
            $modulosAsignadosIds = array_column($modulosAsignados, 'id');
            
            // Preparar datos para la vista
            $pageTitle = 'Editar Perfil';
            $usuario = $auth->getUsuarioLogueado();
            
            // Renderizar la vista usando los archivos reales
            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/pages/usuarios/perfiles/editar_perfil.php';
            include __DIR__ . '/../views/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en editPerfiles: " . $e->getMessage());
            NavigationController::redirect(
                NavigationController::buildControllerUrl('Usuario', 'indexPerfiles'),
                'Error interno del servidor',
                'error'
            );
        }
    }
    
    /**
     * Procesa la actualización de un perfil
     * 
     * @param int $id ID del perfil a actualizar
     * @return void
     */
    public function updatePerfiles($id) {
        try {
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                exit;
            }
            
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de perfil inválido']);
                exit;
            }
            
            // Sanitizar datos de entrada
            $datos = [
                'nombre' => isset($_POST['nombre']) ? trim(htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8')) : ''
            ];
            
            // Validar datos
            $reglas = [
                'nombre' => [
                    'required' => true,
                    'type' => 'string',
                    'max_length' => 45,
                    'min_length' => 2
                ]
            ];
            
            $errores = [];
            foreach ($reglas as $campo => $regla) {
                $valor = $datos[$campo] ?? null;
                
                // Campo requerido
                if (isset($regla['required']) && $regla['required'] && empty($valor)) {
                    $errores[$campo] = "El campo {$campo} es requerido";
                    continue;
                }
                
                if (!empty($valor)) {
                    // Longitud mínima
                    if (isset($regla['min_length']) && strlen($valor) < $regla['min_length']) {
                        $errores[$campo] = "El campo {$campo} debe tener al menos {$regla['min_length']} caracteres";
                    }
                    
                    // Longitud máxima
                    if (isset($regla['max_length']) && strlen($valor) > $regla['max_length']) {
                        $errores[$campo] = "El campo {$campo} no puede tener más de {$regla['max_length']} caracteres";
                    }
                }
            }
            
            if (!empty($errores)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errores)]);
                exit;
            }
            
            // Actualizar perfil
            $resultado = Usuario::actualizarPerfil($id, $datos);
            
            // Asignar módulos si se actualizaron
            if ($resultado['success'] && isset($_POST['modulos'])) {
                $modulosSeleccionados = is_array($_POST['modulos']) ? $_POST['modulos'] : [];
                $modulosAsignados = Usuario::asignarModulosAPerfil($id, $modulosSeleccionados);
                if (!$modulosAsignados['success']) {
                    error_log("Error al asignar módulos al perfil: " . $modulosAsignados['message']);
                }
            }
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en updatePerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
    
    /**
     * Elimina un perfil del sistema
     * 
     * @param int $id ID del perfil a eliminar
     * @return void
     */
    public function deletePerfiles($id) {
        try {
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                exit;
            }
            
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de perfil inválido']);
                exit;
            }
            
            // Eliminar perfil
            $resultado = Usuario::eliminarPerfil($id);
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en deletePerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
    
    /**
     * Obtiene los módulos asignados a un perfil específico (para AJAX)
     * 
     * @param int $id ID del perfil
     * @return void
     */
    public function getModulosPerfiles($id) {
        try {
            // Verificar acceso al módulo de usuarios (módulo ID 1)
            $auth = new AuthController();
            if (!$auth->verificarAccesoModulo(ModuleConfig::USUARIOS)) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de perfil inválido']);
                exit;
            }
            
            // Obtener módulos del perfil
            $modulos = Usuario::obtenerModulosAsignadosPorPerfil($id);
            
            // Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'modulos' => $modulos]);
            
        } catch (Exception $e) {
            error_log("Error en getModulosPerfiles: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }
}

// ✅ Auto-ejecución siguiendo patrón estándar
new UsuarioController();
?>
