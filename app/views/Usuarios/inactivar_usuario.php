<?php
// app/views/usuarios/inactivar_usuario.php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $accion = $_POST['accion'];
    
    $nuevo_estado = $accion === 'inactivar' ? 'Inactivo' : 'Activo';
    
    try {
        $stmt = $db->prepare("UPDATE usuarios SET estado = :estado WHERE id_usuario = :id");
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
        
        $_SESSION['mensaje_exito'] = "Usuario {$accion}do correctamente";
    } catch (PDOException $e) {
        $_SESSION['mensaje_error'] = "Error al {$accion} el usuario: " . $e->getMessage();
    }
    
    header("Location: index.php?page=usuarios");
    exit;
}
?>