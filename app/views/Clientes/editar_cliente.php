<?php
// app/views/clientes/editar_cliente.php

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?page=clientes');
    exit();
}

$id_cliente = intval($_GET['id']);
$clienteController = new ClienteController($db);

// Obtener los datos del cliente
$cliente = $clienteController->obtener($id_cliente);

// Si no se encuentra el cliente, redirigir
if (!$cliente) {
    header('Location: index.php?page=clientes');
    exit();
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_cliente' => trim($_POST['nombre_cliente']),
        'identificacion' => trim($_POST['identificacion']),
        'telefono' => trim($_POST['telefono']),
        'correo' => trim($_POST['correo']),
        'direccion' => trim($_POST['direccion']),
        'ciudad' => trim($_POST['ciudad'])
    ];
    
    // Validar campos requeridos
    if (empty($datos['nombre_cliente'])) {
        $error = "El nombre del cliente es obligatorio.";
    } else {
        // Verificar si la identificación ya existe en otro cliente
        if (!empty($datos['identificacion'])) {
            $clienteExistente = $clienteController->buscarPorIdentificacion($datos['identificacion']);
            if ($clienteExistente && $clienteExistente['id_cliente'] != $id_cliente) {
                $error = "Ya existe un cliente con esta identificación.";
            }
        }
        
        if (!isset($error)) {
            // Actualizar el cliente
            $resultado = $clienteController->actualizar($id_cliente, $datos);
            
            if ($resultado) {
                header('Location: index.php?page=clientes&success=Cliente actualizado correctamente');
                exit();
            } else {
                $error = "Error al actualizar el cliente. Por favor, intenta nuevamente.";
            }
        }
    }
}
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Cliente</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=clientes" class="boton3 text-decoration-none">
                <div class="boton-top3"><i class="fas fa-arrow-left me-2"></i>Volver a Clientes</div>
                <div class="boton-bottom3"></div>
                <div class="boton-base3"></div>
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_GET['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=editar_cliente&id=<?= $id_cliente ?>">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3 text-white py-3">
                                    <label for="nombre_cliente" class="form-label">Nombre del Cliente *</label>
                                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" 
                                           value="<?= htmlspecialchars($cliente['nombre_cliente']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="identificacion" class="form-label">Identificación</label>
                                    <input type="text" class="form-control" id="identificacion" name="identificacion"
                                           value="<?= htmlspecialchars($cliente['identificacion']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono"
                                           value="<?= htmlspecialchars($cliente['telefono']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3 text-white">
                                    <label for="correo" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="correo" name="correo"
                                           value="<?= htmlspecialchars($cliente['correo']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion"
                                           value="<?= htmlspecialchars($cliente['direccion']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad"
                                           value="<?= htmlspecialchars($cliente['ciudad']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3 text-white">
                                    <label class="form-label">Fecha de Registro</label>
                                    <input type="text" class="form-control" 
                                           value="<?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?>" 
                                           readonly disabled>
                                    <small class="form-text text-muted">Este campo es informativo y no puede ser modificado.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-2">
                            <button type="submit" class="boton1 text-decoration-none">
                                <div class="boton-top1"><i class="fas fa-save me-2"></i>Actualizar Cliente</div>
                                <div class="boton-bottom1"></div>
                                <div class="boton-base1"></div>
                            </button>
                            <a href="index.php?page=clientes" class="boton2 text-decoration-none">
                                <div class="boton-top2">Cancelar</div>
                                <div class="boton-bottom2"></div>
                                <div class="boton-base2"></div>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>