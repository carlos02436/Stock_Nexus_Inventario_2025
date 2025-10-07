<?php
// app/views/clientes/clientes.php
$clienteController = new ClienteController($db);
$clientes = $clienteController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users me-2"></i>Gestión de Clientes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=crear_cliente" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nuevo Cliente
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Clientes
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Identificación</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Ciudad</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= $cliente['nombre_cliente'] ?></td>
                        <td><?= $cliente['identificacion'] ?></td>
                        <td><?= $cliente['telefono'] ?></td>
                        <td><?= $cliente['correo'] ?></td>
                        <td><?= $cliente['ciudad'] ?></td>
                        <td><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?page=editar_cliente&id=<?= $cliente['id_cliente'] ?>" 
                                   class="btn btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?page=eliminar_cliente&id=<?= $cliente['id_cliente'] ?>" 
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