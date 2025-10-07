<?php
class AuthController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function login($usuario, $contrasena) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM usuarios 
                WHERE usuario = :usuario AND estado = 'Activo' 
                LIMIT 1
            ");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && $contrasena === $user['contrasena']) {
                // Cargar permisos del usuario
                $permisoModel = new Permiso($this->db);
                $permisos = $permisoModel->obtenerPermisosPorRol($user['rol']);

                $_SESSION['usuario_logged_in'] = true;
                $_SESSION['usuario_id'] = $user['id_usuario'];
                $_SESSION['usuario_nombre'] = $user['nombre_completo'];
                $_SESSION['usuario_rol'] = $user['rol'];
                $_SESSION['usuario_correo'] = $user['correo'];
                $_SESSION['permisos'] = $permisos;

                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }

    public function registrarUsuario($datos) {
        try {
            // Verificar si el usuario ya existe
            $stmt = $this->db->prepare("SELECT id_usuario FROM usuarios WHERE usuario = :usuario OR correo = :correo");
            $stmt->execute([
                ':usuario' => $datos['usuario'],
                ':correo' => $datos['correo']
            ]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El usuario o email ya existe'];
            }

            // Crear usuario
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (nombre_completo, correo, usuario, contrasena, rol, estado) 
                VALUES (:nombre, :correo, :usuario, :contrasena, :rol, :estado)
            ");
            
            $result = $stmt->execute([
                ':nombre' => $datos['nombre_completo'],
                ':correo' => $datos['correo'],
                ':usuario' => $datos['usuario'],
                ':contrasena' => $datos['contrasena'], // En producción usar password_hash
                ':rol' => $datos['rol'],
                ':estado' => $datos['estado'] ?? 'Activo'
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Error al registrar usuario'];
            }

        } catch (PDOException $e) {
            error_log("Error en registrarUsuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema al registrar usuario'];
        }
    }

    public function verificarPermiso($accion, $modulo = null) {
        if (!isset($_SESSION['usuario_rol'])) {
            return false;
        }

        if ($_SESSION['usuario_rol'] === 'Administrador') {
            return true; // Los administradores tienen acceso total
        }

        if (!$modulo) {
            $modulo = $_GET['page'] ?? 'dashboard';
        }

        $permisoModel = new Permiso($this->db);
        return $permisoModel->verificarPermiso($_SESSION['usuario_rol'], $modulo, $accion);
    }
}
?>