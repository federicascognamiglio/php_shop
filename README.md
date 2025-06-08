# PHP Shop – Esercizio Gestione Ordini

Questo progetto è un **esercizio pratico** sviluppato come parte di un **colloquio tecnico**. L'obiettivo è creare un'applicazione PHP e JavaScript (Vue.js) che simuli un sistema di gestione ordini per un piccolo negozio online.

## Tecnologie Utilizzate

- **PHP** (senza framework)
- **Vue.js**
- **Bootstrap** (per la UI)
- **MySQL** (per la persistenza dei dati)
- **AJAX** (per la comunicazione asincrona client-server)

---

## Funzionalità Implementate

### 1. Catalogo Prodotti

- Elenco di almeno 5 prodotti, ognuno con:
  - `id`
  - `nome`
  - `prezzo`
  - `quantità disponibile`
- Dati memorizzati in una tabella `prodotti` del database MySQL.
- Caricamento dinamico dal backend tramite chiamata PHP.

### 2. Aggiunta al Carrello

- Possibilità di aggiungere o rimuovere più quantità di ogni prodotto al carrello.
- La quantità richiesta viene validata rispetto alla disponibilità.

### 3. Calcolo Totale + Sconto

- Calcolo automatico del **subtotale** e del **totale dell’ordine**.
- Sconto del **10%** applicato se il totale supera i **100€**.

### 4. Riepilogo Ordine

- Visualizzazione chiara dell'ordine:
  - Nome prodotto x Quantità – Subtotale
  - Totale ordine con eventuale sconto applicato

### 5. Salvataggio Ordine

- Salvataggio ordine in database (tabella `ordini` e `ordine_dettagli`)
- Aggiornamento automatico dello **stock dei prodotti**
- Comunicazione asincrona tramite AJAX

---

## Struttura del Progetto

```bash
php_shop/
├── classi/
│   ├── Carrello.php
│   ├── DB.php
│   ├── Ordine.php
│   └── Prodotto.php
├── api/
│   ├── getCarrello.php
│   ├── getDettagliOrdine.php
│   ├── getProdotti.php
│   ├── handleCarrello.php
│   └── salvaOrdine.php
├── database.sql
├── index.html
└── vue-script.js
```

---

## Come Eseguire il Progetto

1. **Clona la repo**  

```bash
git clone https://github.com/federicascognamiglio/php_shop.git
```

2. **Importa il database**  
- Esegui lo script SQL nel file `database.sql` per creare un database MySQL, le tabelle e popolare i dati iniziali.

3. **Configura le credenziali DB**  
Modifica il file `classi/DB.php` per usare le credenziali corrette del tuo database.

4. **Avvia un server locale** (es. con PHP)

```bash
php -S localhost:8000
```

5. **Apri il browser**  

---

## Autrice

**Federica Scognamiglio**  
🔗 [GitHub](https://github.com/federicascognamiglio)
