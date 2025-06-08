<?php
header('Content-Type: application/json');

require_once '../classi/Ordine.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID ordine mancante o non valido'
    ]);
    exit;
}

$ordine_id = intval($_GET['id']);

try {
    $dati = Ordine::getSummary($ordine_id);

    if (!$dati['ordine']) {
        echo json_encode([
            'success' => false,
            'message' => 'Ordine non trovato'
        ]);
        exit;
    }

    // Pulizia dati dell’ordine per evidenziare subtotal, sconto e totale
    $ordineInfo = [
        'id' => $dati['ordine']['id'],
    'subtotale' => (float) $dati['ordine']['subtotale'],
    'sconto_applicato' => (bool) $dati['ordine']['sconto_applicato'],
    'totale' => (float) $dati['ordine']['totale'],
    'prodotti' => $dati['prodotti']
    ];

    echo json_encode([
        'success' => true,
        'ordine' => $ordineInfo,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Errore: ' . $e->getMessage()
    ]);
}
?>