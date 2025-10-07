# Stock_Nexus_Inventario_2025

Entrega completa: estructura MVC (models, views, controllers), front controller (`public/index.php`), helpers (Mailer, PdfGenerator), `config/database.php` y script SQL para crear la base de datos y datos iniciales.

Estructura del proyecto:

app/
├── controllers/
│   ├── UsuarioController.php
│   ├── ProductoController.php
│   ├── CategoriaController.php
│   ├── ProveedorController.php
│   ├── CompraController.php
│   ├── VentaController.php
│   ├── ClienteController.php
│   ├── InventarioController.php
│   ├── FinanzaController.php
│   ├── ReporteController.php
│   └── DashboardController.php
├── models/
│   ├── Usuario.php
│   ├── Producto.php
│   ├── Categoria.php
│   ├── Proveedor.php
│   ├── Compra.php
│   ├── Venta.php
│   ├── Cliente.php
│   ├── MovimientoBodega.php
│   ├── Pago.php
│   ├── BalanceGeneral.php
│   └── Dashboard.php
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   ├── forgot_password.php
│   │   └── reset_password.php
│   ├── dashboard/
│   │   └── dashboard.php
│   ├── productos/
│   │   ├── productos.php
│   │   ├── crear_producto.php
│   │   └── editar_producto.php
│   ├── categorias/
│   │   ├── categorias.php
│   │   └── crear_categoria.php
│   ├── proveedores/
│   │   ├── proveedores.php
│   │   └── crear_proveedor.php
│   ├── compras/
│   │   ├── compras.php
│   │   ├── crear_compra.php
│   │   └── detalle_compra.php
│   ├── ventas/
│   │   ├── ventas.php
│   │   ├── crear_venta.php
│   │   └── detalle_venta.php
│   ├── clientes/
│   │   ├── clientes.php
│   │   └── crear_cliente.php
│   ├── movimientos/
│   │   ├── movimientos.php
│   │   └── crear_movimiento.php
│   ├── finanzas/
│   │   ├── finanzas.php
│   │   ├── pagos.php
│   │   ├── crear_pago.php
│   │   └── balance.php
│   ├── reportes/
│   │   ├── reportes.php
│   │   ├── reporte_ventas.php
│   │   ├── reporte_inventario.php
│   │   ├── reporte_finanzas.php
│   │   ├── reporte_compras.php
│   │   ├── generar_pdf.php
│   │   └── generar_excel.php
│   ├── usuarios/
│   │   ├── usuarios.php
│   │   ├── crear_usuario.php
│   │   └── editar_usuario.php
│   └── configuracion/
│       ├── configuracion.php
│       └── perfil.php
└── config/
    └── database.php

**Instrucciones rápidas**
1. Copia la carpeta `Stock_Nexus_Inventario_2025_complete` a tu `htdocs` o la carpeta pública de tu servidor (XAMPP, Laragon, etc.).
2. Ajusta `config/database.php` con tus credenciales MySQL.
3. Ejecuta `composer install` en la raíz del proyecto si quieres usar las librerías (PHPMailer, Dompdf, Dotenv).
4. Importa `sql/schema.sql` en tu servidor MySQL (phpMyAdmin o consola).
5. Accede a `http://localhost/Stock_Nexus_Inventario_2025_complete/public/` y entra con las cuentas del SQL (contraseñas hashed; usa `scripts/generate_password_hash.php` para generar hashes reales si lo prefieres).

**Notas**
- Las contraseñas en `sql/schema.sql` usan hashes de ejemplo. Puedes regenerarlas con `php scripts/generate_password_hash.php miPassword`.
- Por seguridad, revisa y personaliza configuraciones SMTP en `app/helpers/Mailer.php` antes de enviar correos.

Autor: Carlos Enrique Parra Castañeda
