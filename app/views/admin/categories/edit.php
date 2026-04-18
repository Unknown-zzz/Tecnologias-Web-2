<?php
// Redirigir al index con modal abierto
header('Location: index.php?r=admin/categories&modal=edit&id=' . (int)($_GET['id'] ?? 0));
exit;
?>

