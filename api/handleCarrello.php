<?php
header('Content-Type: application/json');
require_once '../classi/Carrello.php';
require_once '../classi/Prodotto.php';

session_start();

if (!isset($_SESSION['carrello'])) {
    $_SESSION['carrello'] = new Carrello();
}

$carrello = $_SESSION['carrello'];


// Ricevi dati JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verifica se i dati sono stati inviati correttamente
if (!$input || !isset($input['idProdotto']) || !isset($input['azione'])) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit;
}

$idProdotto = (int)$input['idProdotto'];
$azione = $input['azione'];


try {
    $prodotto = Prodotto::find($idProdotto);
    if (!$prodotto) {
        throw new Exception('Prodotto non trovato');
    }

    // Aggiunge Prodotto
    if ($azione === 'aggiungi') {
        $carrello->addProduct($prodotto, 1);
    // Rimuove Prodotto
    } elseif ($azione === 'rimuovi') {
        $items = $carrello->getItems();
        if (isset($items[$idProdotto])) {
            $quantitaAttuale = $items[$idProdotto]['quantita'];
            if ($quantitaAttuale <= 1) {
                $carrello->removeProduct($idProdotto);
            } else {
                $carrello->decreaseProduct($prodotto, 1); 
            }
        } else {
            throw new Exception('Prodotto non presente nel carrello');
        }
    } else {
        throw new Exception('Azione non valida');
    }

    // Salva carrello in sessione
    $_SESSION['carrello'] = $carrello;

    echo json_encode(['success' => true, 'carrello' => $carrello->getItems()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
