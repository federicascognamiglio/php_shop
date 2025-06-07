<?php
header('Content-Type: application/json');

require_once '../classi/Carrello.php';
require_once '../classi/Prodotto.php';

session_start();

if (!isset($_SESSION['carrello'])) {
    $_SESSION['carrello'] = new Carrello();
}

$carrello = $_SESSION['carrello'];

try {
    $items = $carrello->getItems();

    echo json_encode(['success' => true, 'carrello' => $items]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>