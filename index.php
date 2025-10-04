<?php
// Public front controller
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Autoload app files if not using composer autoload (safe fallback)
foreach (glob(__DIR__ . '/../app/models/*.php') as $f) require_once $f;
foreach (glob(__DIR__ . '/../app/controllers/*.php') as $f) require_once $f;
foreach (glob(__DIR__ . '/../app/helpers/*.php') as $f) require_once $f;

// Protect routes: require login for all except auth/login
$module = $_GET['module'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$public_routes = [
    'auth' => ['login','doLogin','logout']
];

if (!isset($_SESSION['user'])) {
    // Allow only auth routes
    if (!($module === 'auth' && in_array($action, ['login','doLogin']))) {
        header('Location: /'.basename(__DIR__).'/../login.php');
        exit();
    }
}

// Simple router
switch ($module) {
    case 'auth':
        $ctrl = new App\Controllers\AuthController($pdo);
        switch ($action) {
            case 'login': $ctrl->login(); break;
            case 'doLogin': $ctrl->doLogin(); break;
            case 'logout': $ctrl->logout(); break;
            default: $ctrl->login(); break;
        }
        break;

    case 'users':
        $ctrl = new App\Controllers\UserController($pdo);
        switch ($action) {
            case 'index': $ctrl->index(); break;
            case 'create': $ctrl->create(); break;
            case 'store': $ctrl->store(); break;
            case 'edit': $ctrl->edit($_GET['id'] ?? null); break;
            case 'update': $ctrl->update($_GET['id'] ?? null); break;
            case 'deactivate': $ctrl->deactivate($_GET['id'] ?? null); break;
            default: $ctrl->index(); break;
        }
        break;

    case 'products':
        $ctrl = new App\Controllers\ProductController($pdo);
        switch ($action) {
            case 'index': $ctrl->index(); break;
            case 'create': $ctrl->create(); break;
            case 'store': $ctrl->store(); break;
            case 'edit': $ctrl->edit($_GET['id'] ?? null); break;
            case 'update': $ctrl->update($_GET['id'] ?? null); break;
            case 'deactivate': $ctrl->deactivate($_GET['id'] ?? null); break;
            default: $ctrl->index(); break;
        }
        break;

    case 'categories':
        $ctrl = new App\Controllers\CategoryController($pdo);
        switch ($action) {
            case 'index': $ctrl->index(); break;
            case 'create': $ctrl->create(); break;
            case 'store': $ctrl->store(); break;
            default: $ctrl->index(); break;
        }
        break;

    case 'suppliers':
        $ctrl = new App\Controllers\SupplierController($pdo);
        switch ($action) {
            case 'index': $ctrl->index(); break;
            case 'create': $ctrl->create(); break;
            case 'store': $ctrl->store(); break;
            default: $ctrl->index(); break;
        }
        break;

    case 'movements':
        $ctrl = new App\Controllers\MovementController($pdo);
        switch ($action) {
            case 'create': $ctrl->create(); break;
            case 'store': $ctrl->store(); break;
            default: $ctrl->create(); break;
        }
        break;

    case 'reports':
        $ctrl = new App\Controllers\ReportController($pdo);
        switch ($action) {
            case 'index': $ctrl->index(); break;
            case 'generateInventoryPdf': $ctrl->generateInventoryPdf(); break;
            default: $ctrl->index(); break;
        }
        break;

    case 'home':
    default:
        $ctrl = new App\Controllers\HomeController($pdo);
        $ctrl->index();
        break;
}
