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
require_once __DIR__ . '/app/models/Rol.php';
require_once __DIR__ . '/app/models/PermisoModel.php';
require_once __DIR__ . '/app/models/ModuloModel.php';
require_once __DIR__ . '/app/models/GastoOperativo.php';

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
require_once __DIR__ . '/app/controllers/RolController.php';
require_once __DIR__ . '/app/controllers/PermisoController.php';
require_once __DIR__ . '/app/controllers/ModuloController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/GastoController.php';

// ==================== MIDDLEWARE Y BASE CONTROLLER ====================
require_once __DIR__ . '/app/controllers/BaseController.php';
require_once __DIR__ . '/app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/app/helpers/PermisoHelper.php';

// Instanciar controladores principales
$usuarioController = new UsuarioController($db);
$productoController = new ProductoController($db);
$dashboardController = new DashboardController($db);
$rolController = new RolController($db);
$moduloController = new ModuloController($db);
$permisoController = new PermisoController($db);
$authController = new AuthController($db);
$gastoController = new GastoController($db);

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
        $_SESSION['id_rol'] = $user['id_rol']; // ← IMPORTANTE: Agregar id_rol para permisos
        
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
    'movimientos', 'balance', 'pagos', 'gastos', 'crear_gasto', 'editar_gasto', 'eliminar_gasto',
    'roles', 'permisos', 'modulos'
];

// Verificar acceso a páginas protegidas
if (!in_array($page, $public_pages)) {
    if (!isset($_SESSION['usuario_logged_in']) || $_SESSION['usuario_logged_in'] !== true) {
        header("Location: index.php?page=login");
        exit;
    }
    
    // ========== VERIFICACIÓN DE PERMISOS CON MIDDLEWARE ==============
    // Crear instancia del middleware de autenticación
    $authMiddleware = new AuthMiddleware($db);
    
    // Mapeo de páginas a módulos del sistema
    $pageToModuleMap = [
        'dashboard' => 'Dashboard',
        'inventario' => 'Inventario',
        'productos' => 'Inventario',
        'categorias' => 'Categorías',
        'proveedores' => 'Proveedores',
        'compras' => 'Compras',
        'ventas' => 'Ventas',
        'clientes' => 'Clientes',
        'finanzas' => 'Finanzas',
        'reportes' => 'Reportes',
        'usuarios' => 'Usuarios',
        'movimientos' => 'Inventario',
        'balance' => 'Finanzas',
        'pagos' => 'Finanzas',
        'gastos' => 'Finanzas',
        'crear_gasto' => 'Finanzas',
        'editar_gasto' => 'Finanzas',
        'eliminar_gasto' => 'Finanzas', 
        'roles' => 'Roles',
        'permisos' => 'Permisos',
        'modulos' => 'Módulos'
    ];
    
    // Verificar permiso para la página actual
    if (isset($pageToModuleMap[$page])) {
        $modulo = $pageToModuleMap[$page];
        $authMiddleware->verificarPermiso($modulo, 'ver');
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
        include __DIR__ . '/app/views/Dashboard/dashboard.php';
        break;

    // ==================== GASTOS ====================
    case 'gastos':
        $gastoController = new GastoController($db);
        $gastoController->listar();
        break;

    case 'crear_gasto':
        $authMiddleware->verificarPermiso('Finanzas', 'crear');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gastoController = new GastoController($db);
            $gastoController->crear($_POST);
            header('Location: index.php?page=gastos');
            exit();
        } else {
            include __DIR__ . '/app/views/Finanzas/crear_gasto.php';
        }
        break;

    case 'editar_gasto':
        $authMiddleware->verificarPermiso('Finanzas', 'editar');
        $gastoController = new GastoController($db);
        $id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $gastoController->actualizar($id, $_POST);
        } else if ($id) {
            $gastoController->mostrarEditar($id);
        } else {
            header('Location: index.php?page=gastos');
        }
        break;

    case 'activar_gasto':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $gastoController = new GastoController($db);
            $gastoController->activar($id);
        }
        break;

    case 'inactivar_gasto':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $gastoController = new GastoController($db);
            $gastoController->inactivar($id);
        }
        break;

    // ==================== INVENTARIO ====================
    case 'inventario':
        include __DIR__ . '/app/views/Inventario/inventario.php';
        break;

    case 'productos':
        include __DIR__ . '/app/views/productos/productos.php';
        break;

    case 'crear_producto':
        // Verificar permiso para crear
        $authMiddleware->verificarPermiso('Inventario', 'crear');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->crear($_POST);
            header('Location: index.php?page=productos');
            exit();
        } else {
            include __DIR__ . '/app/views/productos/crear_producto.php';
        }
        break;

    case 'editar_producto':
        // Verificar permiso para editar
        $authMiddleware->verificarPermiso('Inventario', 'editar');
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
        // Verificar permiso para eliminar
        $authMiddleware->verificarPermiso('Inventario', 'eliminar');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $productoController->eliminar($id);
        }
        header('Location: index.php?page=productos');
        exit();

    // ==================== CATEGORÍAS ====================
    case 'categorias':
        include __DIR__ . '/app/views/categorias/categoria.php';
        break;

    case 'crear_categoria':
        $authMiddleware->verificarPermiso('Categorías', 'crear');
        include __DIR__ . '/app/views/categorias/crear_categoria.php';
        break;

    case 'editar_categoria':
        $authMiddleware->verificarPermiso('Categorías', 'editar');
        include __DIR__ . '/app/views/categorias/editar_categoria.php';
        break;

    case 'eliminar_categoria':
        $authMiddleware->verificarPermiso('Categorías', 'eliminar');
        include __DIR__ . '/app/views/categorias/eliminar_categoria.php';
        break;

    // ==================== PROVEEDORES ====================
    case 'proveedores':
        include __DIR__ . '/app/views/proveedores/proveedores.php';
        break;

    case 'crear_proveedor':
        $authMiddleware->verificarPermiso('Proveedores', 'crear');
        include __DIR__ . '/app/views/Proveedores/crear_proveedor.php';
        break;

    case 'editar_proveedor':
        $authMiddleware->verificarPermiso('Proveedores', 'editar');
        include __DIR__ . '/app/views/Proveedores/editar_proveedor.php';
        break;

    case 'eliminar_proveedor':
        $authMiddleware->verificarPermiso('Proveedores', 'eliminar');
        include __DIR__ . '/app/views/Proveedores/eliminar_proveedor.php';
        break;

    // ==================== COMPRAS ====================
    case 'compras':
        include __DIR__ . '/app/views/compras/compras.php';
        break;

    case 'crear_compra':
        $authMiddleware->verificarPermiso('Compras', 'crear');
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

    case 'marcar_compra_pagada':
        $authMiddleware->verificarPermiso('Compras', 'editar');
        include __DIR__ . '/app/views/compras/marcar_compra_pagada.php';
        break;

    case 'reanudar_compra':
        $authMiddleware->verificarPermiso('Compras', 'editar');
        include __DIR__ . '/app/views/Compras/reanudar_compra.php';
        break;

    case 'anular_compra':
        $authMiddleware->verificarPermiso('Compras', 'eliminar');
        include __DIR__ . '/app/views/Compras/anular_compra.php';
        break;

    // ==================== VENTAS ====================
    case 'ventas':
        include __DIR__ . '/app/views/ventas/ventas.php';
        break;

    case 'crear_venta':
        $authMiddleware->verificarPermiso('Ventas', 'crear');
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

    case 'marcar_venta_pagada':
        $authMiddleware->verificarPermiso('Ventas', 'editar');
        include __DIR__ . '/app/views/ventas/marcar_venta_pagada.php';
        break;

    case 'actualizar_estado_venta':
        $authMiddleware->verificarPermiso('Ventas', 'editar');
        include __DIR__ . '/app/views/ventas/actualizar_estado_venta.php';
        break;

    case 'anular_venta':
        $authMiddleware->verificarPermiso('Ventas', 'eliminar');
        include __DIR__ . '/app/views/ventas/anular_venta.php';
        break;

    case 'revertir_anulacion':
        $authMiddleware->verificarPermiso('Ventas', 'editar');
        include __DIR__ . '/app/views/ventas/revertir_anulacion.php';
        break;

    // ==================== CLIENTES ====================
    case 'clientes':
        include __DIR__ . '/app/views/clientes/clientes.php';
        break;

    case 'crear_cliente':
        $authMiddleware->verificarPermiso('Clientes', 'crear');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteController = new ClienteController($db);
            $clienteController->crear($_POST);
            header('Location: index.php?page=clientes');
            exit();
        } else {
            include __DIR__ . '/app/views/Clientes/crear_cliente.php';
        }
        break;

    case 'editar_cliente':
        $authMiddleware->verificarPermiso('Clientes', 'editar');
        include __DIR__ . '/app/views/Clientes/editar_cliente.php';
        break;
        
    case 'eliminar_cliente':
        $authMiddleware->verificarPermiso('Clientes', 'eliminar');
        include __DIR__ . '/app/views/Clientes/eliminar_cliente.php';
        break;  

    case 'activar_cliente':
        $authMiddleware->verificarPermiso('Clientes', 'editar');
        include __DIR__ . '/app/views/Clientes/activar_cliente.php';
        break;  

    case 'clientes_inactivos':
        include __DIR__ . '/app/views/Clientes/clientes_inactivos.php';
        break;        

    // ==================== MOVIMIENTOS DE BODEGA ====================
    case 'movimientos':
        include __DIR__ . '/app/views/movimientos/movimientos.php';
        break;

    case 'crear_movimiento':
        $authMiddleware->verificarPermiso('Inventario', 'crear');
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
        include __DIR__ . '/app/views/Finanzas/finanzas.php';
        break;

    case 'pagos':
        include __DIR__ . '/app/views/Finanzas/pagos.php';
        break;

    case 'crear_pago':
        $authMiddleware->verificarPermiso('Finanzas', 'crear');
        include __DIR__ . '/app/views/Finanzas/crear_pago.php';
        break;

    case 'balance':
        include __DIR__ . '/app/views/Finanzas/balance.php';
        break;

    case 'generar_pdf_balance':
        include __DIR__ . '/app/views/Finanzas/generar_pdf_balance.php';
        break;

    case 'estado_resultado':
        include __DIR__ . '/app/views/Finanzas/estado_resultado.php';
        break;

    case 'generar_pdf_estado_resultado':
        include __DIR__ . '/app/views/Finanzas/generar_pdf_estado_resultado.php';
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

    case 'generar_pdf_reporte':
        include __DIR__ . '/app/views/Reportes/generar_pdf_reporte.php';
        break;

    case 'generar_excel_reporte':
        include __DIR__ . '/app/views/Reportes/generar_excel_reporte.php';
        break;

    case 'generar_pdf_inventario':
        include __DIR__ . '/app/views/Reportes/generar_pdf_inventario.php';
        break;

    case 'generar_excel_inventario':
        include __DIR__ . '/app/views/Reportes/generar_excel_inventario.php';
        break;

    case 'generar_pdf_compras':
        include __DIR__ . '/app/views/Reportes/generar_pdf_compras.php';
        break;

    case 'generar_excel_compras':
        include __DIR__ . '/app/views/Reportes/generar_excel_compras.php';
        break;

    case 'generar_pdf_finanzas':
        include __DIR__ . '/app/views/Reportes/generar_pdf_finanzas.php';
        break;

    case 'generar_excel_finanzas':
        include __DIR__ . '/app/views/Reportes/generar_excel_finanzas.php';
        break;

    // ==================== USUARIOS ====================
    case 'usuarios':
        $authMiddleware->verificarPermiso('Usuarios', 'ver');
        include __DIR__ . '/app/views/usuarios/usuarios.php';
        break;

    case 'crear_usuario':
        $authMiddleware->verificarPermiso('Usuarios', 'crear');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioController->crear($_POST);
            header('Location: index.php?page=usuarios');
            exit();
        } else {
            include __DIR__ . '/app/views/usuarios/crear_usuario.php';
        }
        break;

    case 'editar_usuario':
        $authMiddleware->verificarPermiso('Usuarios', 'editar');
        $id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $usuarioController->actualizar($id, $_POST);
            header('Location: index.php?page=usuarios');
            exit();
        } else {
            include __DIR__ . '/app/views/usuarios/editar_usuario.php';
        }
        break;

    case 'inactivar_usuario':
        $authMiddleware->verificarPermiso('Usuarios', 'eliminar');
        include __DIR__ . '/app/views/Usuarios/inactivar_usuario.php';
        break;

    // ==================== CONFIGURACIÓN ====================
    case 'configuracion':
        include __DIR__ . '/app/views/configuracion/configuracion.php';
        break;

    case 'perfil':
        include __DIR__ . '/app/views/configuracion/perfil.php';
        break;

    // ==================== HELPERS ====================
    case 'Mailer':
        include __DIR__ . '/app/helpers/Mailer.php';
        break;

    case 'PdfGenerator':
        include __DIR__ . '/app/helpers/PdfGenerator.php';
        break;  
        
    // ===================== ROLES =====================
    case 'roles':
        $authMiddleware->verificarPermiso('Roles', 'ver');
        include __DIR__ . '/app/views/roles/roles.php';
        break;

    case 'crear_rol':
        $authMiddleware->verificarPermiso('Roles', 'crear');
        include __DIR__ . '/app/views/roles/crear_rol.php';
        break;

    case 'editar_rol':
        $authMiddleware->verificarPermiso('Roles', 'editar');
        include __DIR__ . '/app/views/roles/editar_rol.php';
        break;

    case 'cambiar_estado_rol':
        $authMiddleware->verificarPermiso('Roles', 'editar');
        include __DIR__ . '/app/views/roles/cambiar_estado_rol.php';
        break;

    // ==================== PERMISOS ====================
    case 'permisos':
        $authMiddleware->verificarPermiso('Permisos', 'ver');
        include __DIR__ . '/app/views/Permisos/permisos.php';
        break;

    case 'crear_permisos':
        $authMiddleware->verificarPermiso('Permisos', 'crear');
        include __DIR__ . '/app/views/Permisos/crear_permisos.php';
        break;

    case 'editar_permisos':
        $authMiddleware->verificarPermiso('Permisos', 'editar');
        include __DIR__ . '/app/views/Permisos/editar_permisos.php';
        break; 

    case 'cambiar_estado_permiso':
        $authMiddleware->verificarPermiso('Permisos', 'eliminar');
        include __DIR__ . '/app/views/Permisos/cambiar_estado_permiso.php';
        break; 

    // ==================== MÓDULOS DEL SISTEMA ====================
    case 'modulos':
        $authMiddleware->verificarPermiso('Módulos', 'ver');
        include __DIR__ . '/app/views/Modulos/modulos.php';
        break;

    case 'crear_modulo':
        $authMiddleware->verificarPermiso('Módulos', 'crear');
        include __DIR__ . '/app/views/Modulos/crear_modulo.php';
        break;

    case 'editar_modulo':
        $authMiddleware->verificarPermiso('Módulos', 'editar');
        include __DIR__ . '/app/views/Modulos/editar_modulo.php';
        break;

    case 'cambiar_estado_modulo':
        $authMiddleware->verificarPermiso('Módulos', 'eliminar');
        include __DIR__ . '/app/views/Modulos/cambiar_estado_modulo.php';
        break;

   // ===================================================

    default:
        echo "<section class='container py-5 text-center' style='margin-top:100px;'>
                <h2 class='text-danger'>404 - Página no encontrada</h2>
                <p>Lo sentimos, la página que buscas no existe.</p>
                <a href='index.php?page=dashboard' class='btn btn-neon mt-3'>Volver al dashboard</a>
              </section>";
        break;
}

// ==================== FOOTER ====================
if ($page !== 'login' && $page !== 'forgot_password' && $page !== 'reset_password') {
    include __DIR__ . '/app/views/plantillas/footer.php';
}