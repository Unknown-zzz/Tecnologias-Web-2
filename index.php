<?php
declare(strict_types=1);

// === CONFIGURACIÓN DE ERRORES (solo para desarrollo) ===
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// NO poner ningún echo, espacio en blanco ni texto antes de session_start()
session_start();

$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

$config = require __DIR__ . '/config/config.php';

// Establecer zona horaria consistente para toda la aplicación
if (!empty($config['app']['timezone'])) {
    date_default_timezone_set($config['app']['timezone']);
}

require __DIR__ . '/app/core/Database.php';
require __DIR__ . '/app/core/Controller.php';
require __DIR__ . '/app/core/helpers.php';

require __DIR__ . '/app/models/Account.php';
require __DIR__ . '/app/models/Client.php';
require __DIR__ . '/app/models/Product.php';
require __DIR__ . '/app/models/Category.php';
require __DIR__ . '/app/models/Brand.php';
require __DIR__ . '/app/models/Industry.php';
require __DIR__ . '/app/models/Branch.php';
require __DIR__ . '/app/models/Sale.php';
require __DIR__ . '/app/models/QrPayment.php';          // ← NUEVO

require __DIR__ . '/app/controllers/AuthController.php';
require __DIR__ . '/app/controllers/StoreController.php';
require __DIR__ . '/app/controllers/ProductController.php';
require __DIR__ . '/app/controllers/CartController.php';
require __DIR__ . '/app/controllers/CheckoutController.php';
require __DIR__ . '/app/controllers/AdminController.php';
require __DIR__ . '/app/controllers/QrPaymentController.php'; // ← NUEVO

$db = Database::getInstance($config['db']);

$route = $_GET['r'] ?? 'home';

$auth      = new AuthController($db, $config);
$store     = new StoreController($db, $config);
$product   = new ProductController($db, $config);
$cart      = new CartController($db, $config);
$checkout  = new CheckoutController($db, $config);
$admin     = new AdminController($db, $config);
$qrPayment = new QrPaymentController($db, $config);     // ← NUEVO

$routes = [
    'home'                  => [$store, 'index'],
    'store'                 => [$store, 'index'],
    'product'               => [$product, 'show'],
    'account/purchases'     => [$store, 'purchases'],

    'cart'                  => [$cart, 'index'],
    'cart/modal'            => [$cart, 'modal'],
    'cart/count'            => [$cart, 'count'],
    'cart/add'              => [$cart, 'add'],
    'cart/remove'           => [$cart, 'remove'],
    'cart/quantity'         => [$cart, 'quantity'],
    'cart/clear'            => [$cart, 'clear'],

    'checkout'              => [$checkout, 'index'],
    'checkout/process'      => [$checkout, 'process'],
    'checkout/success'      => [$checkout, 'success'],

    // ── Pago por QR ── NUEVAS RUTAS ───────────────────────
    'qr/generate'           => [$qrPayment, 'generate'],  // POST - genera token
    'qr/status'             => [$qrPayment, 'status'],    // GET  - polling estado
    'qr/scan'               => [$qrPayment, 'scan'],      // GET  - página del escáner
    'qr/confirm'            => [$qrPayment, 'confirm'],   // POST - confirmar pago
    // ──────────────────────────────────────────────────────

    'login'                 => [$auth, 'login'],
    'authenticate'          => [$auth, 'authenticate'],
    'register'              => [$auth, 'register'],
    'register/process'      => [$auth, 'registerProcess'],
    'logout'                => [$auth, 'logout'],

    'admin/login'           => [$admin, 'login'],
    'admin/authenticate'    => [$admin, 'authenticate'],
    'admin/dashboard'       => [$admin, 'dashboard'],
    'admin/products'        => [$admin, 'products'],
    'admin/products/create' => [$admin, 'create'],
    'admin/products/store'  => [$admin, 'store'],
    'admin/products/edit'   => [$admin, 'edit'],
    'admin/products/update' => [$admin, 'update'],
    'admin/products/delete' => [$admin, 'delete'],

    'admin/brands'          => [$admin, 'brands'],
    'admin/brands/create'   => [$admin, 'createBrand'],
    'admin/brands/store'    => [$admin, 'storeBrand'],
    'admin/brands/edit'     => [$admin, 'editBrand'],
    'admin/brands/update'   => [$admin, 'updateBrand'],
    'admin/brands/delete'   => [$admin, 'deleteBrand'],

    'admin/industries'        => [$admin, 'industries'],
    'admin/industries/create' => [$admin, 'createIndustry'],
    'admin/industries/store'  => [$admin, 'storeIndustry'],
    'admin/industries/edit'   => [$admin, 'editIndustry'],
    'admin/industries/update' => [$admin, 'updateIndustry'],
    'admin/industries/delete' => [$admin, 'deleteIndustry'],

    'admin/categories'        => [$admin, 'categories'],
    'admin/categories/create' => [$admin, 'createCategory'],
    'admin/categories/store'  => [$admin, 'storeCategory'],
    'admin/categories/edit'   => [$admin, 'editCategory'],
    'admin/categories/update' => [$admin, 'updateCategory'],
    'admin/categories/delete' => [$admin, 'deleteCategory'],

    'admin/sales'            => [$admin, 'sales'],
    'admin/sales/show'       => [$admin, 'showSale'],

    'admin/logout'           => [$admin, 'logout'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    echo '<h1>404 - Ruta no encontrada</h1>';
    exit;
}

call_user_func($routes[$route]);