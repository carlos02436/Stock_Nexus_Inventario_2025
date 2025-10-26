</main>
<!-- Footer -->
<footer class="py-4 footer" style="color: white; background-color: #1a1a1a;">
    <div class="container">
        <div class="row text-start"> <!-- Cambiado a text-start para alinear todo a la izquierda -->
            <!-- LOGO DEL PROYECTO (PRIMERA COLUMNA) -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <div class="text-start"> <!-- Alineado a la izquierda -->
                    <img src="public/img/StockNexus.png" alt="StockNexus" style="width: 80px; height: auto; margin-bottom: 10px;">
                    <div class="navbar-brand mb-0">
                        <small style="color: #ccc; display: block;">
                            Rol: <?= htmlspecialchars($rol) ?><br>
                            Usuario: <?= htmlspecialchars($nombreUsuario) ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- MÓDULOS PRINCIPALES -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <p class="navbar-brand mb-2">MÓDULOS</p>
                <ul class="list-unstyled text-start"> <!-- Alineado a la izquierda -->
                    <li class="mb-1"><a href="index.php?page=dashboard" style="color: white; text-decoration: none; font-size: 0.9rem;">Dashboard</a></li>
                    <li class="mb-1"><a href="index.php?page=inventario" style="color: white; text-decoration: none; font-size: 0.9rem;">Inventario</a></li>
                    
                    <!-- Finanzas -->
                    <?php if (!in_array($rol, ['Vendedor', 'Compras', 'Contador', 'Bodeguero'])): ?>
                        <li class="mb-1"><a href="index.php?page=finanzas" style="color: white; text-decoration: none; font-size: 0.9rem;">Finanzas</a></li>
                    <?php endif; ?>

                    <!-- Proveedores -->
                    <?php if (in_array($rol, ['Administrador', 'Compras', 'Contador'])): ?>
                        <li class="mb-1"><a href="index.php?page=proveedores" style="color: white; text-decoration: none; font-size: 0.9rem;">Proveedores</a></li>
                    <?php endif; ?>

                    <!-- Reportes -->
                    <?php if (!in_array($rol, ['Vendedor', 'Compras', 'Contador', 'Bodeguero'])): ?>
                        <li class="mb-1"><a href="index.php?page=reportes" style="color: white; text-decoration: none; font-size: 0.9rem;">Reportes</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- CONFIGURACIÓN / OPERACIONES -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <?php if ($rol === 'Administrador'): ?>
                    <p class="navbar-brand mb-2">CONFIGURACIÓN</p>
                    <ul class="list-unstyled text-start"> <!-- Alineado a la izquierda -->
                        <li class="mb-1"><a href="index.php?page=usuarios" style="color: white; text-decoration: none; font-size: 0.9rem;">Usuarios</a></li>
                        <li class="mb-1"><a href="index.php?page=roles" style="color: white; text-decoration: none; font-size: 0.9rem;">Roles</a></li>
                        <li class="mb-1"><a href="index.php?page=parametros" style="color: white; text-decoration: none; font-size: 0.9rem;">Parámetros</a></li>
                        <li class="mb-1"><a href="index.php?page=categorias" style="color: white; text-decoration: none; font-size: 0.9rem;">Categorías</a></li>
                        <li class="mb-1"><a href="index.php?page=permisos" style="color: white; text-decoration: none; font-size: 0.9rem;">Permisos</a></li>
                    </ul>
                <?php else: ?>
                    <p class="navbar-brand mb-2">OPERACIONES</p>
                    <ul class="list-unstyled text-start"> <!-- Alineado a la izquierda -->
                        <!-- Compras -->
                        <?php if (in_array($rol, ['Administrador', 'Compras', 'Contador'])): ?>
                            <li class="mb-1"><a href="index.php?page=compras" style="color: white; text-decoration: none; font-size: 0.9rem;">Compras</a></li>
                        <?php endif; ?>

                        <!-- Ventas (visible para todos) -->
                        <li class="mb-1"><a href="index.php?page=ventas" style="color: white; text-decoration: none; font-size: 0.9rem;">Ventas</a></li>

                        <!-- Clientes (solo admin) -->
                        <?php if ($rol === 'Administrador'): ?>
                            <li class="mb-1"><a href="index.php?page=clientes" style="color: white; text-decoration: none; font-size: 0.9rem;">Clientes</a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- CONTACTO / SOPORTE -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <p class="navbar-brand mb-2">CONTACTO</p>
                <ul class="list-unstyled text-start"> <!-- Alineado a la izquierda -->
                    <li class="mb-1">
                        <a href="tel:+57 312 473 2236" style="color: white; text-decoration: none; font-size: 0.9rem;">
                            (+57) 312 473 22 36
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="mailto:soporte@stocknexus.com" style="color: white; text-decoration: none; font-size: 0.9rem;">
                            soporte@stocknexus.com
                        </a>
                    </li>
                    <li class="mb-1">
                        <span style="color: white; font-size: 0.9rem;">
                            La Jagua de Ibirico, Cesar, Colombia
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- LÍNEA Y COPYRIGHT -->
        <div class="row">
            <div class="col-12">
                <div class="text-center"> <!-- Contenedor centrado para la línea -->
                    <hr style="border-top: 1px solid #00ff00; margin: 20px auto; max-width: 95%;">
                </div>
                <div class="text-center">
                    <p class="navbar-brand mb-0" style="font-size: 0.72rem;">
                        &copy; 2025 StockNexus. | Todos los Derechos Reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="public/JavaScript/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>