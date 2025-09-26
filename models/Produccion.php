<?php
class Produccion {
    public static function listar() {
        require_once __DIR__ . '/../config/database.php';
        $db = Database::getInstance()->getConnection();
        // Si existe la vista, usarla para obtener el nombre del estado
        try {
            $stmt = $db->prepare('SELECT p.id, p.fecha_inicio, p.fecha_entrega, p.cantidad, ep.nombre AS estado, p.reserva_id FROM produccion p INNER JOIN estado_produccion ep ON p.estado_produccion_id = ep.id');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Si falla el join, usar el id y mapear en la vista
            $stmt = $db->prepare('SELECT id, fecha_inicio, fecha_entrega, cantidad, estado_produccion_id, reserva_id FROM produccion');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}