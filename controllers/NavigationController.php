<?php
/**
 * Controlador de Navegación
 * Maneja las rutas y URLs del sistema
 */

class NavigationController {
    
    private static $baseUrl = 'http://localhost/Sistema_Premoldeado';
    
    /**
     * Prepara los módulos con URLs, iconos y submódulos para el menú
     * 
     * @param array $modulosUsuario Módulos básicos del usuario desde el modelo
     * @return array Módulos preparados para renderizar en el menú
     */
    public static function prepararModulosParaMenu($modulosUsuario) {
        $configuracionModulos = self::getConfiguracionModulos();
        $menuModulos = [];
        
        foreach ($modulosUsuario as $modulo) {
            if (isset($configuracionModulos[$modulo['id']])) {
                $config = $configuracionModulos[$modulo['id']];
                $menuModulos[] = [
                    'id' => $modulo['id'],
                    'nombre' => $modulo['nombre'],               // Del modelo
                    'icono' => $config['icono'] ?? 'fas fa-circle',
                    'url' => $config['url'] ?? '#',             // Del NavigationController
                    'submodulos' => $config['submodulos'] ?? []  // Del NavigationController
                ];
            } else {
                // Módulo sin configuración específica
                $menuModulos[] = [
                    'id' => $modulo['id'],
                    'nombre' => $modulo['nombre'],
                    'icono' => 'fas fa-circle',
                    'url' => '#',
                    'submodulos' => []
                ];
            }
        }
        
        return $menuModulos;
    }

    /**
     * Obtiene la configuración de módulos con iconos, URLs y submódulos
     * 
     * @return array Configuración completa de navegación
     */
    public static function getConfiguracionModulos() {
        return [
            1 => [ // Usuarios
                'icono' => 'fas fa-users',
                'url' => self::$baseUrl . '/controllers/UsuarioController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Usuarios', 'url' => self::$baseUrl . '/controllers/UsuarioController.php?action=index'],
                    ['nombre' => 'Crear Usuario', 'url' => self::$baseUrl . '/controllers/UsuarioController.php?action=create'],
                    ['nombre' => 'Perfiles', 'url' => self::$baseUrl . '/controllers/UsuarioController.php?action=indexPerfiles']
                ]
            ],
            2 => [ // Clientes
                'icono' => 'fas fa-user-friends',
                'url' => self::$baseUrl . '/controllers/ClienteController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Clientes', 'url' => self::$baseUrl . '/controllers/ClienteController.php?action=index'],
                    ['nombre' => 'Crear Cliente', 'url' => self::$baseUrl . '/controllers/ClienteController.php?action=create']
                ]
            ],
            3 => [ // Productos
                'icono' => 'fas fa-boxes',
                'url' => self::$baseUrl . '/controllers/ProductoController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Productos', 'url' => self::$baseUrl . '/controllers/ProductoController.php?action=index'],
                    ['nombre' => 'Crear Producto', 'url' => self::$baseUrl . '/controllers/ProductoController.php?action=create'],
                    ['nombre' => 'Tipos de Producto', 'url' => self::$baseUrl . '/controllers/ProductoController.php?action=indexTipos']
                ]
            ],
            4 => [ // Materiales
                'icono' => 'fas fa-cubes',
                'url' => self::$baseUrl . '/controllers/MaterialController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Materiales', 'url' => self::$baseUrl . '/controllers/MaterialController.php?action=index'],
                    ['nombre' => 'Crear Material', 'url' => self::$baseUrl . '/controllers/MaterialController.php?action=create']
                ]
            ],
            5 => [ // Pedidos y reservas
                'icono' => 'fas fa-shopping-cart',
                'url' => self::$baseUrl . '/controllers/PedidoController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Pedidos', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=index'],
                    ['nombre' => 'Crear Pedido', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=create'],
                    ['nombre' => 'Estados de Pedido', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=indexEstados'],
                    ['nombre' => 'Formas de Entrega', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=indexFormasEntrega'],
                    ['nombre' => 'Reservas', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=indexReservas'],
                    ['nombre' => 'Estados de Reserva', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=indexEstadosReserva'],
                    ['nombre' => 'Devoluciones', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=indexDevoluciones'],
                    ['nombre' => 'Estados de Devolución', 'url' => self::$baseUrl . '/controllers/PedidoController.php?action=indexEstadosDevoluciones']
                ]
            ],
            6 => [ // Producción
                'icono' => 'fas fa-industry',
                'url' => self::$baseUrl . '/controllers/ProduccionController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Producción', 'url' => self::$baseUrl . '/controllers/ProduccionController.php?action=index'],
                    ['nombre' => 'Crear Producción', 'url' => self::$baseUrl . '/controllers/ProduccionController.php?action=create'],
                    ['nombre' => 'Estados de Producción', 'url' => self::$baseUrl . '/controllers/ProduccionController.php?action=indexEstados']
                ]
            ],
            7 => [ // Proveedores
                'icono' => 'fas fa-truck',
                'url' => self::$baseUrl . '/controllers/ProveedorController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Proveedores', 'url' => self::$baseUrl . '/controllers/ProveedorController.php?action=index'],
                    ['nombre' => 'Crear Proveedor', 'url' => self::$baseUrl . '/controllers/ProveedorController.php?action=create']
                ]
            ],
            8 => [ // Compras
                'icono' => 'fas fa-receipt',
                'url' => self::$baseUrl . '/controllers/CompraController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Compras', 'url' => self::$baseUrl . '/controllers/CompraController.php?action=index']
                ]
            ],
            9 => [ // Ventas
                'icono' => 'fas fa-cash-register',
                'url' => self::$baseUrl . '/controllers/VentaController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Listado de Ventas', 'url' => self::$baseUrl . '/controllers/VentaController.php?action=index'],
                    ['nombre' => 'Crear Venta', 'url' => self::$baseUrl . '/controllers/VentaController.php?action=create'],
                    ['nombre' => 'Métodos de Pago', 'url' => self::$baseUrl . '/controllers/VentaController.php?action=indexMetodosPago']
                ]
            ],
            10 => [ // Módulos
                'icono' => 'fas fa-puzzle-piece',
                'url' => self::$baseUrl . '/controllers/ModuloController.php?action=index',
                'submodulos' => []
            ],
            20 => [ // Parámetros
                'icono' => 'fas fa-cog',
                'url' => self::$baseUrl . '/controllers/ParametroController.php?action=index',
                'submodulos' => [
                    ['nombre' => 'Configuración General', 'url' => self::$baseUrl . '/controllers/ParametroController.php?action=index']
                ]
            ]
        ];
    }
    
    /**
     * Redirige a una URL específica con mensaje opcional
     * 
     * @param string $url URL de destino
     * @param string|null $message Mensaje flash opcional
     * @param string $type Tipo de mensaje (success, error, warning, info)
     */
    public static function redirect($url, $message = null, $type = 'info') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($message) {
            $_SESSION['flash_message'] = [
                'message' => $message,
                'type' => $type
            ];
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Obtiene la URL del dashboard
     * 
     * @return string URL del dashboard
     */
    public static function getDashboardUrl() {
        return self::$baseUrl . '/views/pages/dashboard.php';
    }
    
    /**
     * Obtiene la URL de login
     * 
     * @return string URL de login
     */
    public static function getLoginUrl() {
        return self::$baseUrl . '/views/pages/auth/login.php';
    }
    
    /**
     * Obtiene la URL de logout
     * 
     * @return string URL de logout
     */
    public static function getLogoutUrl() {
        return self::$baseUrl . '/views/pages/auth/logout.php';
    }
    
    /**
     * Construye una URL para un controlador específico
     * 
     * @param string $controller Nombre del controlador (sin 'Controller')
     * @param string $action Acción a ejecutar
     * @param array $params Parámetros adicionales
     * @return string URL construida
     */
    public static function buildControllerUrl($controller, $action, $params = []) {
        $url = self::$baseUrl . '/controllers/' . $controller . 'Controller.php?action=' . $action;
        
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        
        return $url;
    }
}
?>
