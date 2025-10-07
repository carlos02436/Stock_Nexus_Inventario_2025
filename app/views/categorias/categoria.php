<?php
// app/views/categorias/categorias.php
$categoriaController = new CategoriaController($db);
$categorias = $categoriaController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tags me-2"></i>Gestión de Categorías</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=crear_categoria" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nueva Categoría
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Categorías
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= $categoria['nombre_categoria'] ?></td>
                        <td><?= $categoria['descripcion'] ?: 'Sin descripción' ?></td>
                        <td>
                            <span class="badge bg-<?= $categoria['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                <?= $categoria['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?page=editar_categoria&id=<?= $categoria['id_categoria'] ?>" 
                                   class="btn btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?page=eliminar_categoria&id=<?= $categoria['id_categoria'] ?>" 
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