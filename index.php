<?php
session_start();
require_once __DIR__ . '/config/database.php'; // Conexión PDO

// ==================== AUTOLOAD DE MODELOS ====================
require_once __DIR__ . '/app/models/Usuario.php';
require_once __DIR__ . '/app/models/Producto.php';
require_once __DIR__ . '/app/models/Categoria.php';
require_once __DIR__ . '/app/models/Proveedor.php';
require_once __DIR__ . '/app/models/Compra.php';
require_once __DIR__ . '/app/models/Venta.php';
require_once __DIR__ . '/app/models/Cliente.php';
require_once __DIR__ . '/app/models/MovimientoBodega.php';
require_once __DIR__ . '/app/models/Pago.php';
require_once __DIR__ . '/app/models/BalanceGeneral.php';
require_once __DIR__ . '/app/models/Dashboard.php';

// ==================== AUTOLOAD DE CONTROLADORES ====================
require_once __DIR__ . '/app/controllers/UsuarioController.php';
require_once __DIR__ . '/app/controllers/ProductoController.php';
require_once __DIR__ . '/app/controllers/CategoriaController.php';
require_once __DIR__ . '/app/controllers/ProveedorController.php';
require_once __DIR__ . '/app/controllers/CompraController.php';
require_once __DIR__ . '/app/controllers/VentaController.php';
require_once __DIR__ . '/app/controllers/ClienteController.php';
require_once __DIR__ . '/app/controllers/InventarioController.php';
require_once __DIR__ . '/app/controllers/FinanzaController.php';
require_once __DIR__ . '/app/controllers/ReporteController.php';
require_once __DIR__ . '/app/controllers/DashboardController.php';

// Instanciar controladores principales
$usuarioController = new UsuarioController($db);
$productoController = new ProductoController($db);
$dashboardController = new DashboardController($db);

// ==================== LÓGICA DE LOGIN ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['contrasena'])) {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND estado = 'Activo' LIMIT 1");
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && $user['contrasena'] === $contrasena) { // En producción usar password_hash
        $_SESSION['usuario_logged_in'] = true;
        $_SESSION['usuario_id'] = $user['id_usuario'];
        $_SESSION['usuario_nombre'] = $user['nombre_completo'];
        $_SESSION['usuario_rol'] = $user['rol'];
        $_SESSION['usuario_correo'] = $user['correo'];
        
        header("Location: index.php?page=dashboard");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

// =========== DEFINIR PÁGINA PRINCIPAL ==============
$page = $_GET['page'] ?? 'login';

// ========== VERIFICACIÓN DE SESIÓN Y PERMISOS ==============
$public_pages = ['login', 'forgot_password', 'reset_password'];
$protected_pages = [
    'dashboard', 'inventario', 'productos', 'categorias', 'proveedores',
    'compras', 'ventas', 'clientes', 'finanzas', 'reportes', 'usuarios',
    'movimientos', 'balance', 'pagos'
];

// Verificar acceso a páginas protegidas
if (!in_array($page, $public_pages)) {
    if (!isset($_SESSION['usuario_logged_in']) || $_SESSION['usuario_logged_in'] !== true) {
        header("Location: index.php?page=login");
        exit;
    }
    
    // Verificar permisos de rol para páginas administrativas
    $admin_pages = ['usuarios', 'balance', 'reportes_avanzados'];
    if (in_array($page, $admin_pages) && $_SESSION['usuario_rol'] !== 'Administrador') {
        header("Location: index.php?page=dashboard");
        exit;
    }
}

// ========== ENDPOINT AJAX PARA CHECK_SESSION ======
if ($page === 'check_session') {
    header('Content-Type: application/json');
    $logged_in = isset($_SESSION['usuario_logged_in']) && $_SESSION['usuario_logged_in'] === true;
    echo json_encode([
        'logged_in' => $logged_in,
        'usuario' => $_SESSION['usuario_nombre'] ?? '',
        'rol' => $_SESSION['usuario_rol'] ?? ''
    ]);
    exit();
}

// ==================== HEADER =======================
if ($page !== 'login' && $page !== 'forgot_password' && $page !== 'reset_password') {
    include __DIR__ . '/app/views/plantillas/header.php';
}

// ==================== ENRUTADOR PRINCIPAL ====================
switch ($page) {
    
    case 'login':
        include __DIR__ . '/app/views/auth/login.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        exit();

    case 'forgot_password':
        include __DIR__ . '/app/views/auth/forgot_password.php';
        break;

    case 'reset_password':
        include __DIR__ . '/app/views/auth/reset_password.php';
        break;

    // ==================== DASHBOARD ====================
    case 'dashboard':
        include __DIR__ . '/app/views/dashboard/dashboard.php';
        break;

    // ==================== INVENTARIO ====================
    case 'inventario':
        include __DIR__ . '/app/views/Inventario/inventario.php';
        break;

    case 'productos':
        include __DIR__ . '/app/views/productos/productos.php';
        break;

    case 'crear_producto':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->crear($_POST);
            header('Location: index.php?page=productos');
            exit();
        } else {
            include __DIR__ . '/app/views/productos/crear_producto.php';
        }
        break;

    case 'editar_producto':
        $id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $productoController->actualizar($id, $_POST);
            header('Location: index.php?page=productos');
            exit();
        } else {
            include __DIR__ . '/app/views/productos/editar_producto.php';
        }
        break;

    case 'eliminar_producto':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $productoController->eliminar($id);
        }
        header('Location: index.php?page=productos');
        exit();

    // ==================== CATEGORÍAS ====================
    case 'categorias':
        include __DIR__ . '/app/views/categorias/categorias.php';
        break;

    case 'crear_categoria':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaController = new CategoriaController($db);
            $categoriaController->crear($_POST);
            header('Location: index.php?page=categorias');
            exit();
        } else {
            include __DIR__ . '/app/views/categorias/crear_categoria.php';
        }
        break;

    // ==================== PROVEEDORES ====================
    case 'proveedores':
        include __DIR__ . '/app/views/proveedores/proveedores.php';
        break;

    case 'crear_proveedor':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proveedorController = new ProveedorController($db);
            $proveedorController->crear($_POST);
            header('Location: index.php?page=proveedores');
            exit();
        } else {
            include __DIR__ . '/app/views/proveedores/crear_proveedor.php';
        }
        break;

    // ==================== COMPRAS ====================
    case 'compras':
        include __DIR__ . '/app/views/compras/compras.php';
        break;

    case 'crear_compra':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $compraController = new CompraController($db);
            $compraController->crear($_POST);
            header('Location: index.php?page=compras');
            exit();
        } else {
            include __DIR__ . '/app/views/compras/crear_compra.php';
        }
        break;

    case 'detalle_compra':
        include __DIR__ . '/app/views/compras/detalle_compra.php';
        break;

    // ==================== VENTAS ====================
    case 'ventas':
        include __DIR__ . '/app/views/ventas/ventas.php';
        break;

    case 'crear_venta':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ventaController = new VentaController($db);
            $ventaController->crear($_POST);
            header('Location: index.php?page=ventas');
            exit();
        } else {
            include __DIR__ . '/app/views/ventas/crear_venta.php';
        }
        break;

    case 'detalle_venta':
        include __DIR__ . '/app/views/ventas/detalle_venta.php';
        break;

    // ==================== CLIENTES ====================
    case 'clientes':
        include __DIR__ . '/app/views/clientes/clientes.php';
        break;

    case 'crear_cliente':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteController = new ClienteController($db);
            $clienteController->crear($_POST);
            header('Location: index.php?page=clientes');
            exit();
        } else {
            include __DIR__ . '/app/views/clientes/crear_cliente.php';
        }
        break;

    // ==================== MOVIMIENTOS DE BODEGA ====================
    case 'movimientos':
        include __DIR__ . '/app/views/movimientos/movimientos.php';
        break;

    case 'crear_movimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventarioController = new InventarioController($db);
            $inventarioController->crearMovimiento($_POST);
            header('Location: index.php?page=movimientos');
            exit();
        } else {
            include __DIR__ . '/app/views/movimientos/crear_movimiento.php';
        }
        break;

    // ==================== FINANZAS ====================
    case 'finanzas':
        include __DIR__ . '/app/views/finanzas/finanzas.php';
        break;

    case 'pagos':
        include __DIR__ . '/app/views/finanzas/pagos.php';
        break;

    case 'crear_pago':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $finanzaController = new FinanzaController($db);
            $finanzaController->crearPago($_POST);
            header('Location: index.php?page=pagos');
            exit();
        } else {
            include __DIR__ . '/app/views/finanzas/crear_pago.php';
        }
        break;

    case 'balance':
        include __DIR__ . '/app/views/finanzas/balance.php';
        break;

    // ==================== REPORTES ====================
    case 'reportes':
        include __DIR__ . '/app/views/reportes/reportes.php';
        break;

    case 'reporte_ventas':
        include __DIR__ . '/app/views/reportes/reporte_ventas.php';
        break;

    case 'reporte_inventario':
        include __DIR__ . '/app/views/reportes/reporte_inventario.php';
        break;

    case 'reporte_finanzas':
        include __DIR__ . '/app/views/reportes/reporte_finanzas.php';
        break;

    case 'reporte_compras':
        include __DIR__ . '/app/views/reportes/reporte_compras.php';
        break;

    case 'generar_pdf':
        include __DIR__ . '/app/views/reportes/generar_pdf.php';
        break;

    case 'generar_excel':
        include __DIR__ . '/app/views/reportes/generar_excel.php';
        break;

    // ==================== USUARIOS ====================
    case 'usuarios':
        if ($_SESSION['usuario_rol'] !== 'Administrador') {
            header("Location: index.php?page=dashboard");
            exit;
        }
        include __DIR__ . '/app/views/usuarios/usuarios.php';
        break;

    case 'crear_usuario':
        if ($_SESSION['usuario_rol'] !== 'Administrador') {
            header("Location: index.php?page=dashboard");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioController->crear($_POST);
            header('Location: index.php?page=usuarios');
            exit();
        } else {
            include __DIR__ . '/app/views/usuarios/crear_usuario.php';
        }
        break;

    case 'editar_usuario':
        if ($_SESSION['usuario_rol'] !== 'Administrador') {
            header("Location: index.php?page=dashboard");
            exit;
        }
        $id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $usuarioController->actualizar($id, $_POST);
            header('Location: index.php?page=usuarios');
            exit();
        } else {
            include __DIR__ . '/app/views/usuarios/editar_usuario.php';
        }
        break;

    case 'eliminar_usuario':
        if ($_SESSION['usuario_rol'] !== 'Administrador') {
            header("Location: index.php?page=dashboard");
            exit;
        }
        $id = $_GET['id'] ?? null;
        if ($id) {
            $usuarioController->eliminar($id);
        }
        header('Location: index.php?page=usuarios');
        exit();

    // ==================== CONFIGURACIÓN ====================
    case 'configuracion':
        include __DIR__ . '/app/views/configuracion/configuracion.php';
        break;

    case 'perfil':
        include __DIR__ . '/app/views/configuracion/perfil.php';
        break;     

    default:
        echo "<section class='container py-5 text-center' style='margin-top:100px;'>
                <h2 class='text-danger'>404 - Página no encontrada</h2>
                <p>Lo sentimos, la página que buscas no existe.</p>
                <a href='index.php?page=dashboard' class='btn btn-primary mt-3'>Volver al dashboard</a>
              </section>";
        break;
}

// ==================== FOOTER ====================
if ($page !== 'login' && $page !== 'forgot_password' && $page !== 'reset_password') {
    include __DIR__ . '/app/views/plantillas/footer.php';
}