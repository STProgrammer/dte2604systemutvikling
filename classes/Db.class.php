<?php
/*Koden er tatt fra Knut Collin*/
class Db {
    private static $db = null;
    private $host = "kark.uit.no";
    private $dbname = "stud_v20_keser";
    private $username = "stud_v20_keser";
    private $password = "1rZdFHpmIivUbqyg";
    private $dbh = null;
    private static String $errorMsg;

    private function __construct() {
        try {
            $this->dbh = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            self::$errorMsg = $e->getMessage();
        }
    }
    public static function getDBConnection() {
        if (Db::$db==null) {
            Db::$db = new self();
        }
        return Db::$db->dbh;
    }

    public static function getErrorMsg(): string
    {
        return self::$errorMsg;
    }

}
