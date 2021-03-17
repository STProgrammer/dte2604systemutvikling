<?php
require_once("classhelper.php");

class Userprofiles
{
    private $db;
    private $request;
    private $session;
    private $typeName = "User";
    private $status = "Status";

    //CONSTRUCTOR//
    function __construct(PDO $db) {
        $this->db = $db;

    }

    public function getAllEmployees(): array {
        $stmt = $this->db->prepare("SELECT * FROM Users ORDER BY lastName ASC");
        return getAll($stmt, $this->typeName);
    }
}