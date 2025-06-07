<?php
header('Content-Type: application/json');
require_once '../classi/Prodotto.php';

try {
    $prodotti = Prodotto::all();
    echo json_encode($prodotti);
} catch (Exception $e) {
    echo json_encode(['error' => 'Errore nel recupero prodotti']);
}

?>