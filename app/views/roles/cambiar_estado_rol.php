<?php
session_start();

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/RolController.php';

if (!isset($_POST['id']) || !is_numeric($_POST['id']) || !isset($_POST['accion'])) {
    header("Location: index.php?page=roles");
    exit;
}

$id_rol = (int) $_POST['id'];
$accion = $_POST['accion']; // "activar" o "inactivar"

$rolController = new RolController($db);

if (in_array($accion, ['activar', 'inactivar'])) {
    $rolController->cambiarEstado($id_rol, $accion);
}

header("Location: index.php?page=roles#tabla");
exit;