<?php
require_once 'DB.php';

class Prodotto {
    public $id;
    public $nome;
    public $prezzo;
    public $quantita;

    public function __construct($_id, $_nome, $_prezzo, $_quantita) {
        $this->id = $_id;
        $this->nome = $_nome;
        $this->prezzo = $_prezzo;
        $this->quantita = $_quantita;
    }

    /**
     * Recupera un prodotto specifico dalla tabella prodotti.
     *
     * @param int $id ID del prodotto da recuperare.
     * @return Prodotto|null Oggetto Prodotto se trovato, altrimenti null.
     */

    public static function find($id) {
        $stmt = DB::conn()->prepare("SELECT * FROM prodotti WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return new Prodotto($result['id'], $result['nome'], $result['prezzo'], $result['quantita']);
        }
        return null;
    }

    /**
     * Preleva tutti i record della tabella prodotti.
     *
     * @return array Lista di array associativi contenenti i dati dei prodotti.
     */
    public static function all() {
        $stmt = DB::conn()->query("SELECT * FROM prodotti");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aggiorna la quantità di un prodotto specifico.
     *
     * @param int $id ID del prodotto da recuperare.
     * @param int $qta Quantità da sottrarre.
     */
    public static function updateQuantity($id, $qta) {
        $stmt = DB::conn()->prepare("UPDATE prodotti SET quantita = quantita - ? WHERE id = ?");
        $stmt->execute([$qta, $id]);
    }
}
?>