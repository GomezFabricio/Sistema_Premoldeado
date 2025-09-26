<?php
session_start();
require_once '../../../models/Producto.php';

$id = $_POST['id'] ?? null;
if ($id) {
    try {
        $producto = new Producto();
        if ($producto->eliminar($id)) {
            $_SESSION['mensaje_exito'] = 'Producto dado de baja correctamente.';
        } else {
            $_SESSION['mensaje_error'] = 'Error al dar de baja.';
        }
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = 'Error al dar de baja: ' . $e->getMessage();
    }
}
header('Location: listado_productos.php');
exit;
