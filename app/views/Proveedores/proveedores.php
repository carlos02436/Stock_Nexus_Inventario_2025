<?php
// app/views/proveedores/proveedores.php
$proveedorController = new ProveedorController($db);
$proveedores = $proveedorController->listar();
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-truck me-2"></i>Gestión de Proveedores</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=crear_proveedor" class="btn btn-neon rounded-3 px-3 py-2">
                <i class="fas fa-plus me-2"></i>Nuevo Proveedor
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Lista de Proveedores
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>NIT</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?= $proveedor['nombre_proveedor'] ?></td>
                            <td><?= $proveedor['nit'] ?></td>
                            <td><?= $proveedor['telefono'] ?></td>
                            <td><?= $proveedor['correo'] ?></td>
                            <td><?= $proveedor['ciudad'] ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=editar_proveedor&id=<?= $proveedor['id_proveedor'] ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=eliminar_proveedor&id=<?= $proveedor['id_proveedor'] ?>" 
                                       class="btn btn-danger btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>