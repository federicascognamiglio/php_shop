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
     * @param Carrello $carrello Il carrello da cui creare l'ordine.
     * 
     * @return int L'ID dell'ordine appena creato.
     * 
     * @throws Exception Se si verifica un errore durante il salvataggio dell'ordine.
     * 
     */
    public static function save(Carrello $carrello) {
        $conn = DB::conn();
        $conn->beginTransaction();
    
        try {
            $subtotale = $carrello->getSubtotal();
            $sconto = $carrello->hasDiscount($subtotale) ? 1 : 0;
            $totale = $carrello->getTotal($subtotale);
    
            // Inserisci ordine
            $stmt = $conn->prepare("INSERT INTO ordini (subtotale, sconto_applicato, totale) VALUES (?, ?, ?)");
            $stmt->execute([$subtotale, $sconto, $totale]);
    
            $ordine_id = $conn->lastInsertId();
    
            // Inserisci ogni prodotto nella tabella ordine_dettagli
            foreach ($carrello->getItems() as $item) {
                $prodotto = $item['prodotto'];
                $quantita = $item['quantita'];
                $prezzo = $prodotto->prezzo;
    
                $stmt = $conn->prepare("INSERT INTO ordine_dettagli (ordine_id, prodotto_id, quantita, prezzo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$ordine_id, $prodotto->id, $quantita, $prezzo]);
    
                // Aggiorna la quantità disponibile del prodotto
                $stmt = $conn->prepare("UPDATE prodotti SET quantita = quantita - ? WHERE id = ?");
                $stmt->execute([$quantita, $prodotto->id]);
            }
    
            $conn->commit();
            return $ordine_id;
    
        } catch (Exception $e) {
            $conn->rollBack();
            throw new Exception("Errore durante il salvataggio dell'ordine: " . $e->getMessage());
        }
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