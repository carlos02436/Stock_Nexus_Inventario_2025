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
                // Cargar permisos del usuario - CORREGIDO
                $permisoModel = new PermisoModel($this->db); // ← Cambiado a PermisoModel
                
                // Obtener el ID del rol para los permisos
                $id_rol = $this->obtenerIdRol($user['rol']);
                $permisos = $permisoModel->obtenerPorRol($id_rol); // ← Método correcto

                $_SESSION['usuario_logged_in'] = true;
                $_SESSION['usuario_id'] = $user['id_usuario'];
                $_SESSION['usuario_nombre'] = $user['nombre_completo'];
                $_SESSION['usuario_rol'] = $user['rol'];
                $_SESSION['usuario_correo'] = $user['correo'];
                $_SESSION['id_rol'] = $id_rol; // ← IMPORTANTE para permisos
                $_SESSION['permisos'] = $permisos;

                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener el ID del rol basado en el nombre del rol
     */
    private function obtenerIdRol($nombreRol) {
        // Mapeo de nombres de rol a IDs (igual que en AuthMiddleware)
        $rolesMap = [
            'Administrador' => 1,
            'Vendedor' => 2, 
            'Contador' => 3,
            'Comprador' => 4,
            'Bodeguero' => 5
        ];
        
        return $rolesMap[$nombreRol] ?? null;
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

        // CORREGIDO - Usar PermisoModel y pasar id_rol
        $permisoModel = new PermisoModel($this->db);
        $id_rol = $_SESSION['id_rol'] ?? $this->obtenerIdRol($_SESSION['usuario_rol']);
        
        return $permisoModel->verificarPermiso($id_rol, $modulo, $accion);
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function isLoggedIn() {
        return isset($_SESSION['usuario_logged_in']) && $_SESSION['usuario_logged_in'] === true;
    }
}
?>