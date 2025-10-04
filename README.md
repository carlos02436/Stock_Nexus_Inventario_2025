# Stock_Nexus_Inventario_2025

Entrega completa: estructura MVC (models, views, controllers), front controller (`public/index.php`), helpers (Mailer, PdfGenerator), `config/database.php` y script SQL para crear la base de datos y datos iniciales.

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
