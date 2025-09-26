<?php
class ProduccionMaterial {
    public static function registrarMaterial($produccion_id, $material_id, $cantidad, $costo_unitario) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO produccion_materiales (produccion_id, material_id, cantidad, costo_unitario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$produccion_id, $material_id, $cantidad, $costo_unitario]);
    }

    public static function obtenerMaterialesPorProduccion($produccion_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT pm.*, m.nombre AS material_nombre FROM produccion_materiales pm JOIN material m ON pm.material_id = m.id WHERE pm.produccion_id = ?");
        $stmt->execute([$produccion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function descontarMaterialesPorVenta($produccion_id, $cantidad_vendida) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT material_id, cantidad FROM produccion_materiales WHERE produccion_id = ?");
        $stmt->execute([$produccion_id]);
        $materiales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($materiales as $mat) {
            $cantidad_a_descontar = $mat['cantidad'] * $cantidad_vendida;
            $stmt2 = $db->prepare("UPDATE material SET stock = stock - ? WHERE id = ?");
            $stmt2->execute([$cantidad_a_descontar, $mat['material_id']]);
        }
    }
}
