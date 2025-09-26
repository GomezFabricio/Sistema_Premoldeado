
<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Produccion.php';

class ProduccionController {
    public function listado() {
        require_once __DIR__ . '/../models/Produccion.php';
        $items = Produccion::listar();
        require_once __DIR__ . '/../views/pages/produccion/listado_produccion.php';
    }
}
