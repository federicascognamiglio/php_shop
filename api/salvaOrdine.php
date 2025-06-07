<?php
header('Content-Type: application/json');
require_once '../Carrello.php';
require_once '../Ordine.php';
require_once '../Prodotto.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['articoli']) || !is_array($data['articoli'])) {
        throw new Exception("Dati ordine non validi");
    }

    $carrello = new Carrello();

    foreach ($data['articoli'] as $articolo) {
        if (!isset($articolo['prodotto_id'], $articolo['quantita'])) {
            throw new Exception("Dati articolo incompleti");
        }
        $prodotto = Prodotto::find($articolo['prodotto_id']);
        if (!$prodotto) {
            throw new Exception("Prodotto ID {$articolo['prodotto_id']} non trovato");
        }
        // Verifica disponibilità
        if ($articolo['quantita'] > $prodotto->quantita) {
            throw new Exception("Quantità richiesta non disponibile per prodotto {$prodotto->nome}");
        }
        $carrello->addProduct($prodotto, (int)$articolo['quantita']);
    }

    $order_id = Ordine::save($carrello);

    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>