<?php
/**
 * Controlador de Usuarios
 * Ejemplo de implementación usando BaseController
 */

require_once __DIR__ . '/BaseController.php';

class UsuarioController extends BaseController {
    
    public function listar() {
        // Verificar acceso al módulo de usuarios (módulo ID 1)
        if (!$this->auth->verificarAccesoModulo(1)) {
            $this->redirect('../dashboard.php', 'No tienes permisos para acceder a este módulo', 'error');
        }
        
        // Datos para la vista
        $datos = [
            'pageTitle' => 'Gestión de Usuarios',
            'usuarios' => $this->obtenerUsuarios(), // Aquí irían los datos reales
            'usuario' => $this->usuario
        ];
        
        // Renderizar vista
        $this->render(__DIR__ . '/../views/pages/usuarios/listado_usuarios.php', $datos);
    }
    
    public function crear() {
        // Verificar acceso
        if (!$this->auth->verificarAccesoModulo(1)) {
            $this->redirect('../dashboard.php', 'No tienes permisos para acceder a este módulo', 'error');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar creación
            $this->procesarCreacion();
        } else {
            // Mostrar formulario
            $datos = [
                'pageTitle' => 'Crear Usuario',
                'usuario' => $this->usuario
            ];
            
            $this->render(__DIR__ . '/../views/pages/usuarios/crear_usuario.php', $datos);
        }
    }
    
    private function obtenerUsuarios() {
        // TODO: Implementar consulta real a la base de datos
        return [
            [
                'id' => 1,
                'nombre' => 'Administrador',
                'email' => 'admin@sistema.com',
                'perfil' => 'Administrador',
                'activo' => 1
            ],
            [
                'id' => 2,
                'nombre' => 'Usuario Demo',
                'email' => 'usuario@sistema.com',
                'perfil' => 'Usuario',
                'activo' => 1
            ]
        ];
    }
    
    private function procesarCreacion() {
        // Validar datos
        $reglas = [
            'nombre' => ['required' => true, 'type' => 'string', 'max_length' => 100],
            'email' => ['required' => true, 'type' => 'email'],
            'password' => ['required' => true, 'min_length' => 6],
            'perfil_id' => ['required' => true, 'type' => 'numeric']
        ];
        
        $datos = $this->sanitizarDatos($_POST);
        $errores = $this->validarDatos($datos, $reglas);
        
        if (empty($errores)) {
            // TODO: Implementar creación en base de datos
            $this->redirect('listado_usuarios.php', 'Usuario creado exitosamente', 'success');
        } else {
            // Mostrar errores
            $this->redirect('crear_usuario.php', 'Error en los datos: ' . implode(', ', $errores), 'error');
        }
    }
}
?>
