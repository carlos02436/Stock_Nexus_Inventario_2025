<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['user']) && basename(__FILE__) !== 'login.php') {
    header('Location: /login.php');
    exit();
}
$u = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Stock Nexus Inventario</title>
<link href="/public/assets/style.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/public/index.php">Stock Nexus</a>
    <div class="d-flex">
      <?php if ($u): ?>
        <span class="navbar-text text-white me-3">Hola, <?= htmlspecialchars($u['nombre'] ?? $u['usuario']) ?> (<?= htmlspecialchars($u['role'] ?? $u['rol']) ?>)</span>
        <a class="btn btn-sm btn-outline-light" href="/public/index.php?module=auth&action=logout">Cerrar Sesión</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container mt-3">
<div class="row">
<div class="col-md-3">
  <div class="list-group">
    <a href="/public/index.php" class="list-group-item list-group-item-action">Dashboard</a>
    <?php if (($u['role'] ?? $u['rol'] ?? '') === 'admin'): ?>
      <a href="/public/index.php?module=users&action=index" class="list-group-item list-group-item-action">Usuarios</a>
      <a href="/public/index.php?module=roles&action=index" class="list-group-item list-group-item-action">Roles</a>
    <?php endif; ?>
    <?php if (in_array(($u['role'] ?? $u['rol'] ?? ''), ['admin','compras'])): ?>
      <a href="/public/index.php?module=suppliers&action=index" class="list-group-item list-group-item-action">Proveedores</a>
      <a href="/public/index.php?module=products&action=index" class="list-group-item list-group-item-action">Productos</a>
    <?php endif; ?>
    <?php if (in_array(($u['role'] ?? $u['rol'] ?? ''), ['admin','ventas'])): ?>
      <a href="/public/index.php?module=movements&action=create" class="list-group-item list-group-item-action">Registrar Venta/Salida</a>
    <?php endif; ?>
    <?php if (in_array(($u['role'] ?? $u['rol'] ?? ''), ['admin','inventario'])): ?>
      <a href="/public/index.php?module=products&action=index" class="list-group-item list-group-item-action">Inventario</a>
    <?php endif; ?>
    <?php if (in_array(($u['role'] ?? $u['rol'] ?? ''), ['admin','contabilidad'])): ?>
      <a href="/public/index.php?module=reports&action=index" class="list-group-item list-group-item-action">Reportes Financieros</a>
    <?php endif; ?>
    <a href="/public/index.php?module=auth&action=logout" class="list-group-item list-group-item-action">Cerrar Sesión</a>
  </div>
</div>
<div class="col-md-9">

