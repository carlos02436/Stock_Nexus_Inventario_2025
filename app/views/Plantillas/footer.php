</main>

<?php
$rol = $_SESSION['usuario_rol'] ?? 'Invitado';
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Usuario';

$rol = htmlspecialchars($rol);
$nombreUsuario = htmlspecialchars($nombreUsuario);
?>
<footer class="py-4 footer" style="color: white; background-color: #1a1a1a;">
    <div class="container">
        <div class="row text-start">
            
            <!-- LOGO DEL PROYECTO -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <div class="text-start">
                    <img src="public/img/StockNexus.png" 
                        alt="StockNexus" 
                        style="width: 80px; height: auto; margin-bottom: 10px;">
                        
                    <div class="navbar-brand mb-0">
                        <small style="color: #ccc; display: block;">
                            Rol: <?= $rol ?><br>
                            Usuario: <?= $nombreUsuario ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- MÓDULOS PRINCIPALES -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <p class="navbar-brand mb-2">MÓDULOS</p>
                <ul class="list-unstyled text-start">
                    <li><a href="index.php?page=dashboard" style="color:white;text-decoration:none;font-size:0.9rem;">Dashboard</a></li>
                    <li><a href="index.php?page=inventario" style="color:white;text-decoration:none;font-size:0.9rem;">Inventario</a></li>
                    <li><a href="index.php?page=finanzas" style="color:white;text-decoration:none;font-size:0.9rem;">Finanzas</a></li>
                    <li><a href="index.php?page=proveedores" style="color:white;text-decoration:none;font-size:0.9rem;">Proveedores</a></li>
                    <li><a href="index.php?page=reportes" style="color:white;text-decoration:none;font-size:0.9rem;">Reportes</a></li>
                </ul>
            </div>

            <!-- CONFIGURACIÓN -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <p class="navbar-brand mb-2">CONFIGURACIÓN</p>
                <ul class="list-unstyled text-start">
                    <li><a href="index.php?page=usuarios" style="color:white;text-decoration:none;font-size:0.9rem;">Usuarios</a></li>
                    <li><a href="index.php?page=roles" style="color:white;text-decoration:none;font-size:0.9rem;">Roles</a></li>
                    <li><a href="index.php?page=parametros" style="color:white;text-decoration:none;font-size:0.9rem;">Parámetros</a></li>
                    <li><a href="index.php?page=categorias" style="color:white;text-decoration:none;font-size:0.9rem;">Categorías</a></li>
                    <li><a href="index.php?page=permisos" style="color:white;text-decoration:none;font-size:0.9rem;">Permisos</a></li>
                    <li><a href="index.php?page=modulos" style="color:white;text-decoration:none;font-size:0.9rem;">Módulos</a></li>
                </ul>
            </div>

            <!-- CONTACTO -->
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <p class="navbar-brand mb-2">CONTACTO</p>
                <ul class="list-unstyled text-start">
                    <li><a href="tel:+57 312 473 2236" style="color:white;text-decoration:none;font-size:0.9rem;">(+57) 312 473 22 36</a></li>
                    <li><a href="mailto:soporte@stocknexus.com" style="color:white;text-decoration:none;font-size:0.9rem;">soporte@stocknexus.com</a></li>
                    <li><span style="color:white;font-size:0.9rem;">La Jagua de Ibirico, Cesar, Colombia</span></li>
                </ul>
            </div>
        </div>

        <div class="text-center mt-4">
            <hr style="border-top: 1px solid #00ff00; max-width: 95%;">
            <p class="navbar-brand mb-0" style="font-size:0.72rem;">
                &copy; 2025 StockNexus | Todos los Derechos Reservados.
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="public/JavaScript/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>