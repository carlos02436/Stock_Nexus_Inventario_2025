<?php
// app/views/reportes/reportes.php
?>
<div class="container-fluid px-4 mb-5" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-bar me-2"></i>Módulo de Reportes</h1>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                    <h5 class="card-title text-white">Reporte de Ventas</h5>
                    <p class="card-text text-white">Genera reportes detallados de ventas por período.</p>
                    <a href="index.php?page=reporte_ventas" class="boton5 text-decoration-none d-inline-block position-relative">
                        <div class="boton-top5">Generar Reporte</div>
                        <div class="boton-bottom5"></div>
                        <div class="boton-base5"></div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-boxes fa-3x text-success mb-3"></i>
                    <h5 class="card-title text-white">Reporte de Inventario</h5>
                    <p class="card-text text-white">Estado actual del inventario y productos con stock bajo.</p>
                    <a href="index.php?page=reporte_inventario" class="boton1 text-decoration-none d-inline-block position-relative">
                        <div class="boton-top1">Generar Reporte</div>
                        <div class="boton-bottom1"></div>
                        <div class="boton-base1"></div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                    <h5 class="card-title text-white">Reporte Financiero</h5>
                    <p class="card-text text-white">Análisis de ingresos, egresos y utilidades.</p>
                    <a href="index.php?page=reporte_finanzas" class="boton6 text-decoration-none d-inline-block position-relative">
                        <div class="boton-top6">Generar Reporte</div>
                        <div class="boton-bottom6"></div>
                        <div class="boton-base6"></div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-3x text-warning mb-3"></i>
                    <h5 class="card-title text-white">Reporte de Compras</h5>
                    <p class="card-text text-white">Historial de compras y análisis de proveedores.</p>
                    <a href="index.php?page=reporte_compras" class="boton4 text-decoration-none d-inline-block position-relative">
                        <div class="boton-top4">Generar Reporte</div>
                        <div class="boton-bottom4"></div>
                        <div class="boton-base4"></div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                    <h5 class="card-title text-white">Exportar a PDF</h5>
                    <p class="card-text text-white">Genera reportes en formato PDF para impresión.</p>
                    <a href="index.php?page=generar_pdf" class="boton2 text-decoration-none d-inline-block position-relative">
                        <div class="boton-top2">Exportar PDF</div>
                        <div class="boton-bottom2"></div>
                        <div class="boton-base2"></div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                    <h5 class="card-title text-white">Exportar a Excel</h5>
                    <p class="card-text text-white">Exporta datos a Excel para análisis avanzado.</p>
                    <a href="index.php?page=generar_excel" class="boton1 text-decoration-none d-inline-block position-relative">
                        <div class="boton-top1">Exportar Excel</div>
                        <div class="boton-bottom1"></div>
                        <div class="boton-base1"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>