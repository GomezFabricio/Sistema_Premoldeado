<?php
require_once __DIR__ . '/../models/Producto.php';


class ProductoController {
    private $producto;

    public function __construct() {
        $this->producto = new Producto();
    }

    public function listado() {
        $items = $this->producto->listar();
        include __DIR__ . '/../views/pages/productos/listado_productos.php';
    }

    // Acción para actualizar producto
    public function actualizar() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $datos = [
                'ancho' => $_POST['ancho'] ?? '',
                'largo' => $_POST['largo'] ?? '',
                'cantidad_disponible' => $_POST['cantidad_disponible'] ?? '',
                'stock_minimo' => $_POST['stock_minimo'] ?? '',
                'precio_unitario' => $_POST['precio_unitario'] ?? '',
                'tipo_producto_id' => $_POST['tipo_producto_id'] ?? '',
            ];
            try {
                $resultado = $this->producto->actualizar($id, $datos);
                $_SESSION['mensaje_exito'] = 'Producto actualizado correctamente.';
                header('Location: /Sistema_Premoldeado/views/pages/productos/editar_producto.php?id=' . urlencode($id));
                exit;
            } catch (Exception $e) {
                $_SESSION['mensaje_error'] = 'Error al actualizar el producto: ' . $e->getMessage();
                header('Location: /Sistema_Premoldeado/views/pages/productos/editar_producto.php?id=' . urlencode($id));
                exit;
            }
        } else {
            header('Location: /Sistema_Premoldeado/views/pages/productos/listado_productos.php');
            exit;
        }
    }
}

// Enrutamiento simple por parámetro 'action'
if (isset($_GET['action'])) {
    $controller = new ProductoController();
    $action = $_GET['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Location: /Sistema_Premoldeado/views/pages/productos/listado_productos.php');
        exit;
    }
}
?>