<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once '../classi/Carrello.php';
require_once '../classi/Ordine.php';
require_once '../classi/Prodotto.php';

try {
    $input = file_get_contents('php://input');
    error_log("Dati ricevuti: " . $input);
    $data = json_decode($input, true);

    // Validazione dei dati
    if (!isset($data['articoli']) || !is_array($data['articoli'])) {
        throw new Exception("Dati ordine non validi.");
    }

    $carrello = new Carrello();

    foreach ($data['articoli'] as $articolo) {
        if (!isset($articolo['prodotto_id'], $articolo['quantita'])) {
            throw new Exception("Dati articolo incompleti.");
        }

        $prodotto_id = (int)$articolo['prodotto_id'];
        $quantita = (int)$articolo['quantita'];

        $prodotto = Prodotto::find($prodotto_id);
        if (!$prodotto) {
            throw new Exception("Prodotto ID {$prodotto_id} non trovato.");
        }

        if ($quantita > $prodotto->quantita) {
            throw new Exception("Quantità richiesta non disponibile per il prodotto: {$prodotto->nome}.");
        }

        $carrello->addProduct($prodotto, $quantita);
    }

    // Salva l'ordine e ottieni l'ID
    $order_id = Ordine::save($carrello);

    echo json_encode([
        'success' => true,
        'order_id' => $order_id
    ]);
    exit;

} catch (Exception $e) {
    error_log("Errore in salvaOrdine.php: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>