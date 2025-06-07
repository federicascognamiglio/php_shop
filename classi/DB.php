<?php
class DB {
    /**
     * Crea una connessione al database MySQL.
     *
     * @return PDO Oggetto di connessione al database.
     */
    public static function conn() {
        return new PDO("mysql:host=127.0.0.1;dbname=php_shop;charset=utf8", 'root', 'Federica', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
?>