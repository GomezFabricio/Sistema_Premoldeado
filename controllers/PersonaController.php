<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Persona.php';

class PersonaController extends BaseController {
    private $personaModel;

    public function __construct() {
        parent::__construct();
        $this->personaModel = new Persona();
        if (isset($_GET['action'])) {
            $this->handleRequest();
        }
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'index';
        switch($action) {
            case 'index':
                $this->index();
                break;
            case 'create':
                $this->create();
                break;
            case 'store':
                $this->store();
                break;
            case 'edit':
                if (isset($_GET['id'])) {
                    $this->edit($_GET['id']);
                }
                break;
            case 'update':
                if (isset($_GET['id'])) {
                    $this->update($_GET['id']);
                }
                break;
            case 'delete':
                if (isset($_GET['id'])) {
                    $this->delete($_GET['id']);
                }
                break;
        }
    }

    public function index() {
        $items = $this->personaModel->listar();
        $data = [
            'titulo' => 'Listado de Personas',
            'items' => $items
        ];
        $this->render('pages/personas/listado_personas', $data);
    }

    public function create() {
        $this->render('pages/personas/crear_persona');
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'apellidos' => $_POST['apellidos'] ?? '',
                'nombres' => $_POST['nombres'] ?? '',
                'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
                'numero_documento' => $_POST['numero_documento'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'localidad' => $_POST['localidad'] ?? '',
                'provincia' => $_POST['provincia'] ?? '',
                'codigo_postal' => $_POST['codigo_postal'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'observaciones' => $_POST['observaciones'] ?? ''
            ];
            $id = $this->personaModel->crear($datos);
            if ($id) {
                $_SESSION['mensaje_exito'] = 'Persona creada correctamente';
                header('Location: ?action=index');
            } else {
                $_SESSION['mensaje_error'] = 'Error al crear persona';
                header('Location: ?action=create');
            }
        }
    }

    public function edit($id) {
        $persona = $this->personaModel->obtenerPorId($id);
        $data = [
            'titulo' => 'Editar Persona',
            'persona' => $persona
        ];
        $this->render('pages/personas/editar_persona', $data);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'apellidos' => $_POST['apellidos'] ?? '',
                'nombres' => $_POST['nombres'] ?? '',
                'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
                'numero_documento' => $_POST['numero_documento'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'localidad' => $_POST['localidad'] ?? '',
                'provincia' => $_POST['provincia'] ?? '',
                'codigo_postal' => $_POST['codigo_postal'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'observaciones' => $_POST['observaciones'] ?? ''
            ];
            $resultado = $this->personaModel->actualizar($id, $datos);
            if ($resultado) {
                $_SESSION['mensaje_exito'] = 'Persona actualizada correctamente';
                header('Location: ?action=index');
            } else {
                $_SESSION['mensaje_error'] = 'Error al actualizar persona';
                header('Location: ?action=edit&id=' . $id);
            }
        }
    }

    public function delete($id) {
        $resultado = $this->personaModel->eliminar($id);
        if ($resultado) {
            $_SESSION['mensaje_exito'] = 'Persona eliminada correctamente';
        } else {
            $_SESSION['mensaje_error'] = 'Error al eliminar persona';
        }
        header('Location: ?action=index');
    }
}
