<?php
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/helpers.php';
require_once __DIR__ . '/app/models/Client.php';
require_once __DIR__ . '/app/models/Product.php';
require_once __DIR__ . '/app/models/Sale.php';

$config = require __DIR__ . '/config/config.php';
$db = Database::getInstance($config['db']);

// Simular sesión logueada
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'Jona') {
    $_SESSION['usuario'] = 'Jona';
    $_SESSION['rol'] = 'cliente';
    $_SESSION['carrito'] = [
        ['id' => 4, 'cantidad' => 1],  // Air Jordan
        ['id' => 2, 'cantidad' => 1],  // Vaporesso
    ];
}

echo "<h1>🧪 Prueba de Checkout</h1>";
echo "<h3>Estado de Sesión:</h3>";
echo "<pre>";
echo "Usuario: " . $_SESSION['usuario'] . "\n";
echo "Carrito items: " . count($_SESSION['carrito']) . "\n";
echo "</pre>";

echo "<h3>1. Buscando cliente para usuario 'Jona'...</h3>";
$clientModel = new Client($db);
$cliente = $clientModel->findByUsuario('Jona');

if (!$cliente) {
    echo "<p style='color: red;'><strong>✗ ERROR: Cliente no encontrado</strong></p>";
    exit;
}

echo "<p style='color: green;'><strong>✓ Cliente encontrado:</strong></p>";
echo "<pre>";
echo "CI: " . $cliente['ci'] . "\n";
echo "Nombre: " . $cliente['nombres'] . "\n";
echo "</pre>";

echo "<h3>2. Reconstruyendo carrito con detalles del producto...</h3>";

$productModel = new Product($db);
$items = [];
$total = 0.0;

foreach ($_SESSION['carrito'] as $sessionItem) {
    $productId = (int)$sessionItem['id'];
    $cantidad = (int)$sessionItem['cantidad'];
    
    echo "<p>Buscando producto $productId...</p>";
    
    $product = $productModel->find($productId);
    if ($product === null) {
        echo "<p style='color: red;'><strong>✗ Producto $productId no encontrado</strong></p>";
        exit;
    }
    
    echo "<p style='color: green;'><strong>✓ Producto encontrado: " . $product['nombre'] . "</strong></p>";
    
    // Verificar stock
    $currentStock = $productModel->getStock($productId);
    echo "<p>Stock actual: $currentStock (solicitado: $cantidad)</p>";
    
    if ($currentStock < $cantidad) {
        echo "<p style='color: red;'><strong>✗ Stock insuficiente</strong></p>";
        exit;
    }
    
    $subtotal = (float)$product['precio'] * $cantidad;
    $total += $subtotal;
    
    $items[] = [
        'id' => $productId,
        'cantidad' => $cantidad,
        'product' => $product,
        'subtotal' => $subtotal
    ];
}

echo "<h3>3. Carrito reconstruido:</h3>";
echo "<pre>";
foreach ($items as $item) {
    echo "Producto: " . $item['product']['nombre'] . "\n";
    echo "  Cantidad: " . $item['cantidad'] . "\n";
    echo "  Precio: $" . number_format($item['product']['precio'], 2) . "\n";
    echo "  Subtotal: $" . number_format($item['subtotal'], 2) . "\n";
}
echo "Total: \$" . number_format($total, 2) . "\n";
echo "</pre>";

echo "<h3>4. Intentando crear venta...</h3>";

$saleModel = new Sale($db);
$nroVenta = $saleModel->create($cliente['ci'], $items);

if ($nroVenta) {
    echo "<p style='color: green;'><strong>✓ Venta creada exitosamente!</strong></p>";
    echo "<p><strong>Número de venta:</strong> " . $nroVenta . "</p>";
} else {
    echo "<p style='color: red;'><strong>✗ Error al crear la venta</strong></p>";
    echo "<p>Revisa el archivo php_errors.log en el servidor</p>";
}

// Mostrar datos guardados
if ($nroVenta) {
    echo "<h3>5. Verificando datos en BD:</h3>";
    
    $stmt = $db->prepare("SELECT * FROM DetalleNotaVenta WHERE nroNotaVenta = :nro");
    $stmt->execute(['nro' => $nroVenta]);
    $detalles = $stmt->fetchAll();
    
    echo "<pre>";
    foreach ($detalles as $detalle) {
        echo "Producto: " . $detalle['codProducto'] . " | Cantidad: " . $detalle['cant'] . " | Precio: $" . $detalle['precioUnitario'] . "\n";
    }
    echo "</pre>";
}
?>

<hr>
<p><a href="index.php?r=home" class="btn btn-primary">Volver a inicio</a></p>
