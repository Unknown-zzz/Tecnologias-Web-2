<?php
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/Client.php';
require_once __DIR__ . '/app/core/helpers.php';

$config = require __DIR__ . '/config/config.php';
$db = Database::getInstance($config['db']);

echo "<h1>Estado de tu sesión</h1>";
echo "<h3>Usuario logueado: " . ($_SESSION['usuario'] ?? 'NO') . "</h3>";
echo "<h3>Rol: " . ($_SESSION['rol'] ?? 'N/A') . "</h3>";
echo "<h3>Carrito: " . (empty($_SESSION['carrito']) ? 'VACÍO' : count($_SESSION['carrito']) . ' items') . "</h3>";

if (!empty($_SESSION['usuario'])) {
    echo "<h3>Datos en sesión:</h3>";
    echo "<pre>";
    var_dump($_SESSION);
    echo "</pre>";
    
    echo "<h3>Buscando cliente para usuario: " . $_SESSION['usuario'] . "</h3>";
    
    $clientModel = new Client($db);
    $cliente = $clientModel->findByUsuario($_SESSION['usuario']);
    
    if ($cliente) {
        echo "<p style='color: green;'><strong>✓ Cliente encontrado</strong></p>";
        echo "<pre>";
        var_dump($cliente);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'><strong>✗ Cliente NO encontrado en BD</strong></p>";
    }
} else {
    echo "<p><strong style='color: red;'>⚠️ No estás logueado. <a href='index.php?r=login'>Ir a login</a></strong></p>";
    echo "<p>Usuarios de prueba: <strong>cliente</strong> o <strong>Jona</strong> (contraseña: password)</p>";
}

echo "<hr>";
echo "<p><a href='test_checkout.php' class='btn btn-warning'>🧪 Probar checkout completo</a></p>";
echo "<p><a href='index.php?r=home' class='btn btn-primary'>Volver a inicio</a></p>";
?>

