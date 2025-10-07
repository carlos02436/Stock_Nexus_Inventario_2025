</main>
<!-- Footer -->
<footer class="py-4 footer" style="color: white; background-color: #1a1a1a;">
    <div class="container">
        <div class="row text-center text-md-start">
            <!-- MÓDULOS PRINCIPALES -->
            <div class="col-md-3 mb-3">
                <p class="navbar-brand mb-0">MÓDULOS</p>
                <ul class="list-unstyled">
                    <li><a href="index.php?page=dashboard" style="color: white; text-decoration: none;">Dashboard</a></li>
                    <li><a href="index.php?page=inventario" style="color: white; text-decoration: none;">Inventario</a></li>
                    <li><a href="index.php?page=finanzas" style="color: white; text-decoration: none;">Finanzas</a></li>
                    <li><a href="index.php?page=proveedores" style="color: white; text-decoration: none;">Proveedores</a></li>
                    <li><a href="index.php?page=reportes" style="color: white; text-decoration: none;">Reportes</a></li>
                </ul>
            </div>

            <!-- CONFIGURACIÓN -->
            <div class="col-md-3 mb-3">
                <p class="navbar-brand mb-0">CONFIGURACIÓN</p>
                <ul class="list-unstyled">
                    <li><a href="index.php?page=usuarios" style="color: white; text-decoration: none;">Usuarios</a></li>
                    <li><a href="index.php?page=roles" style="color: white; text-decoration: none;">Roles</a></li>
                    <li><a href="index.php?page=parametros" style="color: white; text-decoration: none;">Parámetros</a></li>
                </ul>
            </div>

            <!-- CONTACTO / SOPORTE -->
            <div class="col-md-3 mb-3">
                <p class="navbar-brand mb-0">CONTACTO</p>
                <ul class="list-unstyled">
                    <li><a href="tel:+57 312 473 2236" style="color: white; text-decoration: none;">(+57) 312 473 22 36</a></li>
                    <li><a href="mailto:soporte@stocknexus.com" style="color: white; text-decoration: none;">soporte@stocknexus.com</a></li>
                    <li><span style="color: white;">La Jagua de Ibirico, Cesar, Colombia</span></li>
                </ul>
            </div>

            <!-- LOGO DEL PROYECTO -->
            <div class="col-md-3 mb-3 text-center">
                <img src="public/img/StockNexus.png" alt="StockNexus" style="width: 40%; height: inherit; margin-bottom: 15px;">
            </div>

            <hr style="border-top: 1px solid #00ff00;">
            <div class="text-center mt-3">
                <p class="navbar-brand mb-0" style="font-size: 0.55rem;">
                    &copy; 2025 StockNexus. | Todos los Derechos Reservados.
                </p>
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