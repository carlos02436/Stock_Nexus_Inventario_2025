<?php
// app/views/clientes/eliminar_cliente.php

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?page=clientes&error=ID de cliente no válido');
    exit();
}

$id_cliente = intval($_GET['id']);
$clienteController = new ClienteController($db);

$resultado = $clienteController->eliminar($id_cliente);

if ($resultado) {
    header('Location: index.php?page=clientes&success=Cliente marcado como inactivo correctamente');
} else {
    header('Location: index.php?page=clientes&error=Error al marcar el cliente como inactivo');
}
exit();
?>