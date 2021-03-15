<?php
/*Koden er tatt fra Knut Collin*/
class Db {
    private static $db = null;
    private $host = "kark.uit.no";
    private $dbname = "stud_v20_keser";
    private $username = "stud_v20_keser";
    private $password = "1rZdFHpmIivUbqyg";
    private $dbh = null;

    private function __construct() {
        try {
            $this->dbh = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();

        }
    }
    public static function getDBConnection() {
        if (Db::$db==null) {
            Db::$db = new self();
        }
        return Db::$db->dbh;
    }

}
