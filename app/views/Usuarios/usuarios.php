<?php
// app/views/usuarios/usuarios.php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$usuarioController = new UsuarioController($db);
$usuarios = $usuarioController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-cog me-2"></i>Gestión de Usuarios</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=crear_usuario" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nuevo Usuario
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Usuarios
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['nombre_completo'] ?></td>
                        <td><?= $usuario['usuario'] ?></td>
                        <td><?= $usuario['correo'] ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $usuario['rol'] == 'Administrador' ? 'danger' : 
                                ($usuario['rol'] == 'Vendedor' ? 'primary' : 'info')
                            ?>">
                                <?= $usuario['rol'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $usuario['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                <?= $usuario['estado'] ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?page=editar_usuario&id=<?= $usuario['id_usuario'] ?>" 
                                   class="btn btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($usuario['id_usuario'] != $_SESSION['usuario_id']): ?>
                                <a href="index.php?page=eliminar_usuario&id=<?= $usuario['id_usuario'] ?>" 
                                   class="btn btn-danger btn-delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>