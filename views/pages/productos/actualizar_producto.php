<?php
session_start();
require_once '../../../models/Producto.php';

$id = $_POST['id'] ?? null;
if ($id) {
    try {
        $producto = new Producto();
        $data = [
            'ancho' => $_POST['ancho'] ?? '',
            'largo' => $_POST['largo'] ?? '',
            'cantidad_disponible' => $_POST['cantidad_disponible'] ?? 0,
            'stock_minimo' => $_POST['stock_minimo'] ?? 1,
            'precio_unitario' => $_POST['precio_unitario'] ?? 0.00,
            'tipo_producto_id' => $_POST['tipo_producto_id'] ?? '',
        ];
        $producto->actualizar($id, $data);
        $_SESSION['mensaje_exito'] = 'Producto actualizado correctamente.';
        header('Location: editar_producto.php?id=' . urlencode($id));
        exit;
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = 'Error al actualizar el producto: ' . $e->getMessage();
        header('Location: editar_producto.php?id=' . urlencode($id));
        exit;
    }
} else {
    $_SESSION['mensaje_error'] = 'ID de producto no válido.';
    header('Location: listado_productos.php');
    exit;
}
