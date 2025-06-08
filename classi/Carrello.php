<?php
require_once 'DB.php';
require_once 'Prodotto.php';

class Carrello {
    private $articoli = []; 

    /**
     * Aggiunge un prodotto al carrello.
     *
     * @param Prodotto $prodotto Il prodotto da aggiungere.
     * @param int $quantita La quantità del prodotto da aggiungere.
     * 
     * @throws Exception Se la quantità richiesta non è disponibile.
     * 
    */
    public function addProduct(Prodotto $prodotto, $quantita) {
        if ($prodotto->quantita < $quantita) {
            throw new Exception("Quantità non disponibile per il prodotto: {$prodotto->nome}");
        }
        
        if (isset($this->articoli[$prodotto->id])) {
            $this->articoli[$prodotto->id]['quantita'] += $quantita;
        } else {
            $this->articoli[$prodotto->id] = ['prodotto' => $prodotto, 'quantita' => $quantita];
        }
    }

    /**
     * Diminuisce la quantità di un prodotto nel carrello.
     *
     * @param Prodotto $prodotto Il prodotto da diminuire.
     * @param int $quantita La quantità da diminuire.
     * 
     * @throws Exception Se il prodotto non è presente nel carrello o se la quantità diventa negativa.
     * 
    */
    public function decreaseProduct(Prodotto $prodotto, $quantita) {
        if (!isset($this->articoli[$prodotto->id])) {
            throw new Exception("Prodotto non presente nel carrello.");
        }
    
        $this->articoli[$prodotto->id]['quantita'] -= $quantita;
        if ($this->articoli[$prodotto->id]['quantita'] <= 1) {
            unset($this->articoli[$prodotto->id]);
        }
    }
    
    /**
     * Rimuove un prodotto dal carrello.
     *
     * @param int $id ID del prodotto da rimuovere.
     * 
     * @throws Exception Se il prodotto non è presente nel carrello.
     * 
    */
    public function removeProduct($id) {
        if (!isset($this->articoli[$id])) {
            throw new Exception("Prodotto non presente nel carrello.");
        }
        unset($this->articoli[$id]);
    }

    /**
     * Calcola il subtotale del carrello.
     * 
     * @return float Il totale del carrello.
     * 
    */
    public function getSubtotal() {
        $subtotale = 0;
        foreach ($this->articoli as $articolo) {
            $subtotale += $articolo['prodotto']->prezzo * $articolo['quantita'];
        }
        return $subtotale;
    }

    /**
     * Verifica se il carrello ha diritto a uno sconto.
     *
     * @param float $subtotale Il subtotale del carrello.
     * @return bool True se il subtotale è maggiore di 100, altrimenti false.
     * 
    */
    public function hasDiscount($subtotale) {
        if ($subtotale > 100) {
            return true; 
        } else {
            return false; 
        }
    }

    /**
     * Calcola totale applicando eventuale sconto.
     *
     * @param float $subtotale 
    */
    public function getTotal($subtotale) {
        if ($subtotale > 100) {
            $totale = $subtotale * 0.9; 
            return $totale;
        }
        return $subtotale;
    }

    /**
     * Ripulisce il carrello nella sessione.
     *
     * @return void
     */
    public static function clear() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        unset($_SESSION['carrello']);
    }
    
    /**
     * Restituisce tutti gli articoli nel carrello.
     *
     * @return array Un array di articoli nel carrello, ciascuno contenente il prodotto e la quantità.
     * 
     */
    public function getItems() {
        return $this->articoli;
    }
}
?>