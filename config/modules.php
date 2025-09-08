<?php
/**
 * Configuración de Módulos del Sistema
 * Define los IDs de módulos para usar en los controladores
 */

class ModuleConfig {
    const USUARIOS = 1;
    const CLIENTES = 2;
    const PRODUCTOS = 3;
    const MATERIALES = 4;
    const PEDIDOS_RESERVAS = 5;
    const PRODUCCION = 6;
    const PROVEEDORES = 7;
    const COMPRAS = 8;
    const VENTAS = 9;
    const MODULOS = 10;
    const PARAMETROS = 20;
    
    /**
     * Obtiene el nombre del módulo por ID
     */
    public static function getNombreModulo($id) {
        $modulos = [
            self::USUARIOS => 'Usuarios',
            self::CLIENTES => 'Clientes',
            self::PRODUCTOS => 'Productos',
            self::MATERIALES => 'Materiales',
            self::PEDIDOS_RESERVAS => 'Pedidos y Reservas',
            self::PRODUCCION => 'Producción',
            self::PROVEEDORES => 'Proveedores',
            self::COMPRAS => 'Compras',
            self::VENTAS => 'Ventas',
            self::MODULOS => 'Módulos',
            self::PARAMETROS => 'Parámetros'
        ];
        
        return $modulos[$id] ?? 'Módulo Desconocido';
    }
}
?>
