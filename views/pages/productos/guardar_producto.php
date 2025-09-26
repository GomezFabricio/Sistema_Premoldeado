<?php
session_start();
require_once '../../../models/Producto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $producto->crear($data);
        $_SESSION['mensaje_exito'] = 'Producto guardado correctamente.';
        header('Location: crear_producto.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = 'Error al guardar el producto: ' . $e->getMessage();
        header('Location: crear_producto.php');
        exit;
    }
} else {
    header('Location: crear_producto.php');
    exit;
}
