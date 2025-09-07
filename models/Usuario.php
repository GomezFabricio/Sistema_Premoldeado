<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function autenticar($email, $password) {
        $sql = "SELECT u.id, u.nombre_usuarios, u.email, u.password, u.activo, u.perfiles_id, p.nombre as perfil_nombre 
                FROM usuarios u 
                INNER JOIN perfiles p ON u.perfiles_id = p.id 
                WHERE u.email = ? AND u.activo = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }
    
    public function obtenerModulosPorPerfil($perfil_id) {
        $sql = "SELECT m.id, m.nombre 
                FROM modulos m 
                INNER JOIN perfiles_modulos pm ON m.id = pm.modulos_id 
                WHERE pm.perfiles_id = ? 
                ORDER BY m.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perfil_id]);
        $modulos = $stmt->fetchAll();
        
        // Mapear módulos a estructura del menú con iconos y URLs
        $menuModulos = [];
        $configuracionModulos = $this->getConfiguracionModulos();
        
        foreach ($modulos as $modulo) {
            if (isset($configuracionModulos[$modulo['id']])) {
                $config = $configuracionModulos[$modulo['id']];
                $menuModulos[] = [
                    'id' => $modulo['id'],
                    'nombre' => $modulo['nombre'],
                    'icono' => $config['icono'],
                    'url' => $config['url'],
                    'submodulos' => $config['submodulos']
                ];
            }
        }
        
        return $menuModulos;
    }
    
    private function getConfiguracionModulos() {
        // Configuración de módulos con iconos, URLs y submódulos
        return [
            1 => [ // Usuarios
                'icono' => 'fas fa-users',
                'url' => '../usuarios/listado_usuarios.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Usuarios', 'url' => '../usuarios/listado_usuarios.php'],
                    ['nombre' => 'Crear Usuario', 'url' => '../usuarios/crear_usuario.php'],
                    ['nombre' => 'Perfiles', 'url' => '../usuarios/perfiles/listado_perfiles.php']
                ]
            ],
            2 => [ // Clientes
                'icono' => 'fas fa-user-friends',
                'url' => '../clientes/listado_clientes.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Clientes', 'url' => '../clientes/listado_clientes.php'],
                    ['nombre' => 'Crear Cliente', 'url' => '../clientes/crear_cliente.php']
                ]
            ],
            3 => [ // Productos
                'icono' => 'fas fa-boxes',
                'url' => '../productos/listado_productos.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Productos', 'url' => '../productos/listado_productos.php'],
                    ['nombre' => 'Crear Producto', 'url' => '../productos/crear_producto.php'],
                    ['nombre' => 'Tipos de Producto', 'url' => '../productos/tipos/listado_tipos_producto.php']
                ]
            ],
            4 => [ // Materiales
                'icono' => 'fas fa-industry',
                'url' => '../materiales/listado_materiales.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Materiales', 'url' => '../materiales/listado_materiales.php'],
                    ['nombre' => 'Crear Material', 'url' => '../materiales/crear_material.php']
                ]
            ],
            5 => [ // Pedidos y reservas
                'icono' => 'fas fa-shopping-cart',
                'url' => '../pedidos/listado_pedidos.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Pedidos', 'url' => '../pedidos/listado_pedidos.php'],
                    ['nombre' => 'Crear Pedido', 'url' => '../pedidos/crear_pedido.php'],
                    ['nombre' => 'Reservas', 'url' => '../pedidos/reservas/listado_reservas.php'],
                    ['nombre' => 'Devoluciones', 'url' => '../pedidos/devoluciones/listado_devoluciones.php'],
                    ['nombre' => 'Estados de Pedido', 'url' => '../pedidos/estados/listado_estados_pedido.php'],
                    ['nombre' => 'Formas de Entrega', 'url' => '../pedidos/formas_entrega/listado_formas_entrega.php'],
                    ['nombre' => 'Estados de Reserva', 'url' => '../pedidos/reservas/estados/listado_estados_reserva.php']
                ]
            ],
            6 => [ // Producción
                'icono' => 'fas fa-cogs',
                'url' => '../produccion/listado_produccion.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Producción', 'url' => '../produccion/listado_produccion.php'],
                    ['nombre' => 'Crear Producción', 'url' => '../produccion/crear_produccion.php'],
                    ['nombre' => 'Estados de Producción', 'url' => '../produccion/estados/listado_estados_produccion.php']
                ]
            ],
            7 => [ // Proveedores
                'icono' => 'fas fa-truck',
                'url' => '../proveedores/listado_proveedores.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Proveedores', 'url' => '../proveedores/listado_proveedores.php'],
                    ['nombre' => 'Crear Proveedor', 'url' => '../proveedores/crear_proveedor.php']
                ]
            ],
            8 => [ // Compras
                'icono' => 'fas fa-shopping-bag',
                'url' => '../compras/listado_compras.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Compras', 'url' => '../compras/listado_compras.php']
                ]
            ],
            9 => [ // Ventas
                'icono' => 'fas fa-receipt',
                'url' => '../ventas/listado_ventas.php',
                'submodulos' => [
                    ['nombre' => 'Listado de Ventas', 'url' => '../ventas/listado_ventas.php'],
                    ['nombre' => 'Crear Venta', 'url' => '../ventas/crear_venta.php'],
                    ['nombre' => 'Métodos de Pago', 'url' => '../ventas/metodos_pago/listado_metodos_pago.php']
                ]
            ],
            10 => [ // Modulos
                'icono' => 'fas fa-th-large',
                'url' => '#',
                'submodulos' => []
            ],
            20 => [ // Parámetros
                'icono' => 'fas fa-cog',
                'url' => '#',
                'submodulos' => []
            ]
        ];
    }
}
?>
