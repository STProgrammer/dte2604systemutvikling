<?php
class Userprofiles
{
    private $db;
    private $request;
    private $session;

    //CONSTRUCTOR//
    function __construct(PDO $db, Request $request, Session $session) {
        $this->db = $db;
        $this->request = $request;
        $this->session = $session;
    }
    public function getUserNames() : array {
        $user = new User();
        $stmt = $this->db-prepare("SELECT firstName, lastName FROM 'Users' ");
        $stmt->bindValue(2, $firstName, $lastName, PDO::PARAM_STR);
    }
}