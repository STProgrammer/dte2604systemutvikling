<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class TaskManager {
    private $db;
    private $request;
    private $session;

    //CONSTRUCTOR//
    function __construct(PDO $db, Request $request, Session $session) {
        $this->db = $db;
        $this->request = $request;
        $this->session = $session;
    }

    private function NotifyUser($strHeader, $strMessage = "") {
        //$this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    // GET ALL COMMENTS
    public function getAllTasks() : array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Tasks");
            $stmt->execute();
            if( $tasks = $stmt->fetchAll(PDO::FETCH_CLASS, "Task")) {
                return $tasks;
            }
            else {
                $this->notifyUser("Tasks ble ikke funnet", "Kunne ikke hente tasks");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllTasks()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return array();
        }
    }



}