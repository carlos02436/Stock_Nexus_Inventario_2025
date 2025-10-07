<?php
// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Evitar errores de encabezados enviados
ob_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockNexus - Área 51_Barber Shop</title>
    <!-- Bootstrap y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="public/assets/style.css">
    <!-- Favicon -->
    <link rel="icon" href="public/img/StockNexus.png">
</head>

<body>
    <!-- Navbar para Control de Inventario y Finanzas -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top" style="margin: 15px; border-radius: 25px; border-inline: 2px solid #00ff00;">
        <div class="container-fluid">
            <a class="navbar-brand">
                <img src="public/img/StockNexus.png" alt="Logo StockNexus" class="img-fluid" style="height: 50px; width: 50px;">
                <strong class="text-white">StockNexus</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-4"><a class="nav-link text-white" href="index.php?page=dashboard">Dashboard</a></li>
                    <li class="nav-item me-4"><a class="nav-link text-white" href="index.php?page=inventario">Inventario</a></li>
                    <li class="nav-item me-4"><a class="nav-link text-white" href="index.php?page=finanzas">Finanzas</a></li>
                    <li class="nav-item me-4"><a class="nav-link text-white" href="index.php?page=proveedores">Proveedores</a></li>
                    <li class="nav-item me-4"><a class="nav-link text-white" href="index.php?page=reportes">Reportes</a></li>
                    <li class="nav-item dropdown me-4">
                        <a class="nav-link dropdown-toggle text-white" role="button" data-bs-toggle="dropdown" aria-expanded="false">Configuración</a>
                        <ul class="dropdown-menu dropdown-menu-start bg-dark ms-n2">
                            <li><a class="dropdown-item text-white bg-dark" href="index.php?page=usuarios">Usuarios</a></li>
                            <li><a class="dropdown-item text-white bg-dark" href="index.php?page=roles">Roles</a></li>
                            <li><a class="dropdown-item text-white bg-dark" href="index.php?page=parametros">Parámetros</a></li>
                        </ul>
                    </li>
                    <li class="nav-item me-4"><a class="nav-link text-white" href="index.php?page=logout">Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Botón Scroll hacia arriba -->
    <button id="scrollToTopBtn" class="btn" style="position:fixed; bottom:40px; right:30px; z-index:9999; width: 50px; height:40px; display:none; align-items:center; justify-content:center;">
        <i class="fa-solid fa-chevron-up fa-lg"></i>
    </button>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const btn = document.getElementById("scrollToTopBtn");
            window.addEventListener("scroll", function () {
                btn.style.display = (window.scrollY > 200) ? "flex" : "none";
            });
            btn.addEventListener("click", function () {
                window.scrollTo(0, 0);
            });
        });
    </script>

    <style>
        section { scroll-margin-top: 100px; }
    </style>

<main>