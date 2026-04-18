<?php
// Debug para verificar qué está pasando en el checkout

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/helpers.php';
require_once __DIR__ . '/app/models/Client.php';

$config = require __DIR__ . '/config/config.php';
$db = Database::getInstance($config['db']);

echo "<h1>Debug Checkout</h1>";
echo "<h3>Sesión:</h3>";
echo "<pre>";
echo "Usuario en sesión: " . ($_SESSION['usuario'] ?? 'NO DEFINIDO') . "\n";
echo "Rol en sesión: " . ($_SESSION['rol'] ?? 'NO DEFINIDO') . "\n";
echo "Carrito: " . (empty($_SESSION['carrito'] ?? []) ? 'VACÍO' : 'TIENE ' . count($_SESSION['carrito']) . ' items') . "\n";
echo "</pre>";

if (!empty($_SESSION['usuario'])) {
    echo "<h3>Búsqueda de Cliente:</h3>";
    $clientModel = new Client($db);
    $cliente = $clientModel->findByUsuario($_SESSION['usuario']);
    
    echo "<pre>";
    if ($cliente) {
        echo "Cliente encontrado:\n";
        echo json_encode($cliente, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo "Cliente NO encontrado\n";
        
        // Intentar buscar manualmente en la BD
        echo "\n\nBúsqueda manual en BD:\n";
        $stmt = $db->prepare("SELECT c.* FROM Cliente c JOIN Cuenta cu ON c.usuarioCuenta = cu.usuario WHERE cu.usuario = :usuario");
        $stmt->execute(['usuario' => $_SESSION['usuario']]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "Resultado de consulta manual:\n";
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            echo "Consulta manual también retorna NULL\n";
            
            // Verificar que existe la cuenta
            echo "\n\nVerificando Cuenta:\n";
            $stmt2 = $db->prepare("SELECT * FROM Cuenta WHERE usuario = :usuario");
            $stmt2->execute(['usuario' => $_SESSION['usuario']]);
            $cuenta = $stmt2->fetch();
            echo json_encode($cuenta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            // Listar todos los clientes
            echo "\n\nTodos los clientes:\n";
            $stmt3 = $db->query("SELECT * FROM Cliente");
            $clientes = $stmt3->fetchAll();
            echo json_encode($clientes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
    echo "</pre>";
}
?>
