<?php
class DB {
    /**
     * Crea una connessione al database MySQL.
     *
     * @return PDO Oggetto di connessione al database.
     */
    public static function conn() {
        return new PDO("mysql:host=localhost;dbname=php_shop;charset=utf8", 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
?>