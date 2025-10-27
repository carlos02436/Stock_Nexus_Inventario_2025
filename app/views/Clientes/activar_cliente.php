<?php
// app/views/clientes/activar_cliente.php

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?page=clientes&error=ID de cliente no válido');
    exit();
}

$id_cliente = intval($_GET['id']);
$clienteController = new ClienteController($db);

$resultado = $clienteController->activar($id_cliente);

if ($resultado) {
    header('Location: index.php?page=clientes&success=Cliente activado correctamente');
} else {
    header('Location: index.php?page=clientes&error=Error al activar el cliente');
}
exit();
?>