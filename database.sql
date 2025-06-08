-- Creazione DB
CREATE DATABASE IF NOT EXISTS php_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE php_shop;

-- Creazione tabella Prodotti
CREATE TABLE prodotti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255),
  prezzo FLOAT,
  quantita INT UNSIGNED NOT NULL
);

-- Creazione tabella Ordini
CREATE TABLE ordini (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subtotale FLOAT NOT NULL,
  sconto_applicato BOOLEAN NOT NULL DEFAULT 0,
  totale FLOAT NOT NULL,
  data TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Creazione tabella dettagli Ordine
CREATE TABLE ordine_dettagli (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ordine_id INT,
  prodotto_id INT,
  quantita INT UNSIGNED NOT NULL,
  prezzo DECIMAL(10,2),
  FOREIGN KEY (ordine_id) REFERENCES ordini(id),
  FOREIGN KEY (prodotto_id) REFERENCES prodotti(id)
);

-- Inserimento dati di esempio nella tabella Prodotti
INSERT INTO prodotti (nome, prezzo, quantita) VALUES
('Maglietta', 25.50, 3),
('Pantaloni', 45.00, 8),
('Scarpe', 80.00, 15),
('Cappello', 15.00, 20),
('Zaino', 60.00, 7);