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
            $stmt = $this->db->prepare("SELECT * FROM Tasks ORDER BY taskName;");
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

    public function addTask($phaseId, $projectName): bool //returns boolean value
    {
        $taskName = $this->request->request->get('taskName');
        $estimatedTime = $this->request->request->getInt('estimatedTime', 0);
        try {
            $stmt = $this->db->prepare("INSERT INTO `Tasks` (taskName, estimatedTime, projectName, phaseID)
              VALUES (:taskName, :estimatedTime, :projectName, :phaseId);");
            $stmt->bindParam(':taskName', $taskName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':estimatedTime', $estimatedTime, PDO::PARAM_INT, 100);
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':phaseId', $phaseId, PDO::PARAM_INT, 100);
            if ($stmt->execute()) {
                $taskId = $this->db->lastInsertId();
                if ($this->addDependencies($taskId)) {
                    $this->NotifyUser("En oppgave ble lagt til");
                    return true;
                } else {
                    $this->NotifyUser("En oppgave ble lagt til");
                    return true;
                }
            } else {
                $this->NotifyUser("Oppgave ble ikke oprettet");
                return false;
            }
        } catch (PDOException $e) {
            $this->NotifyUser("Oppgave ble ikke opprettet", $e->getMessage());
            return false;
        }
    }


    public function addDependencies($taskId)
    {
        $tasks = $this->request->request->get('dependentTasks');
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO TaskDependencies (firstTask, secondTask) VALUES (:firstTask, :taskId);");
            if (is_array($tasks)) {
                foreach ($tasks as $task) {
                    $stmt->bindParam(':firstTask', $task, PDO::PARAM_INT);
                    $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
                    $stmt->execute();
                }
                $this->notifyUser("Avhengigheter ble lagt til");
                return true;
            } else {
                $this->notifyUser("Fikk ikke legge til avhengigheter");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke legge til avhengigheter", $e->getMessage());
            return false;
        }
    }



}