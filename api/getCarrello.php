<?php
header('Content-Type: application/json');

require_once '../classi/Carrello.php';
require_once '../classi/Prodotto.php';

session_start();

if (!isset($_SESSION['carrello'])) {
    $_SESSION['carrello'] = new Carrello();
}

$carrello = $_SESSION['carrello'];
$articoli = $carrello->getItems(); 

$subtotale = $carrello->getSubtotal();
$totale = $carrello->getTotal($subtotale);
$scontato = $carrello->hasDiscount($subtotale);

echo json_encode([
    'success' => true,
    'carrello' => $articoli, 
    'subtotale' => $subtotale,
    'totale' => $totale,
    'scontato' => $scontato
]);
?>