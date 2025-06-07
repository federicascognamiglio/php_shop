<?php
require_once 'DB.php';
require_once 'Prodotto.php';
require_once 'Carrello.php';

class Ordine {
    private $subtotale;
    private $scontoApplicato;
    private $totale;

    /**
     * Salva un nuovo ordine nel database.
     * 
     * @param Carrello $carrello Il carrello con i dati da salvare come ordine.
     * 
     * @return int L'ID dell'ordine appena creato.
     * 
    */
    public static function save(Carrello $carrello) {
        $subtotale = $carrello->getSubtotal();
        $scontoApplicato = $carrello->hasDiscount($subtotale);
        $totale = $carrello->getTotal($subtotale);

        // Salva ordine
        $stmt = DB::conn()->prepare("INSERT INTO ordini (subtotale, sconto_applicato, totale) VALUES (?, ?, ?)");
        $stmt->execute([$subtotale, $scontoApplicato ? 1 : 0, $totale]);
        $ordine_id = DB::conn()->lastInsertId();

        // Salva dettagli ordine
        foreach ($carrello->getItems() as $item) {
            $prodotto = $item['prodotto'];
            $quantita = $item['quantita'];
            $prezzo_unitario = $prodotto->prezzo;

            $stmt = DB::conn()->prepare("INSERT INTO ordine_dettagli (ordine_id, prodotto_id, quantita, prezzo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$ordine_id, $prodotto->id, $quantita, $prezzo_unitario]);

            // Aggiorna disponibilità prodotto
            Prodotto::updateQuantity($prodotto->id, $quantita);
        }

        return $ordine_id;
    }

    /**
     * Recupera i dettagli di un ordine specifico.
     * 
     * @param int $ordine_id L'ID dell'ordine da recuperare.
     * 
     * @return array I dettagli dell'ordine e dei prodotti associati.
     * 
    */
    public static function getSummary($ordine_id) {
        $stmt = DB::conn()->prepare("SELECT * FROM ordini WHERE id = ?");
        $stmt->execute([$ordine_id]);
        $ordine = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = DB::conn()->prepare("
            SELECT p.nome, od.quantita, od.prezzo 
            FROM ordine_dettagli od 
            JOIN prodotti p ON p.id = od.prodotto_id 
            WHERE ordine_id = ?
        ");
        $stmt->execute([$ordine_id]);
        $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'ordine' => $ordine,
            'prodotti' => $prodotti
        ];
    }
}
?>