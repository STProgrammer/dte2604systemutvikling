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

    // GET ALL TASKS
    public function getAllTasks($hasSubtask = null, $projectName = null, $phaseID = null, $groupID = null, $status = null,
                                $mainResponsible = null, $parentTask = null, $orderBy = null) : array {
        $tasks = array();
        $query = 'SELECT Tasks.*, CONCAT(mainResponsible.firstName, " ", mainResponsible.lastName, " (", mainResponsible.username, ")") as mainResponsibleName, 
groupID.groupName as groupName, phaseID.phaseName as phaseName, parentTasks.taskName as parentTaskName
FROM Tasks
LEFT JOIN Users as mainResponsible on mainResponsible.userID = Tasks.mainResponsible
LEFT JOIN Groups as groupID on groupID.groupID = Tasks.groupID
LEFT JOIN Phases as phaseID on phaseID.phaseID = Tasks.phaseID
LEFT JOIN Tasks as parentTasks on parentTasks.taskID = Tasks.parentTask WHERE 1';
        $params = array();
        if (!is_null($hasSubtask)) {
            $query .= " AND Tasks.hasSubtask = :hasSubtask";
            $params[':hasSubtask'] = $hasSubtask;
        }
        if (!is_null($projectName)) {
            $query .= " AND Tasks.projectName = :projectName";
            $params[':projectName'] = $projectName;
        }
        if (!is_null($phaseID)) {
            $query .= " AND Tasks.phaseID = :phaseID";
            $params[':phaseID'] = $phaseID;
        }
        if (!is_null($groupID)) {
            $query .= " AND Tasks.groupID = :groupID";
            $params[':groupID'] = $groupID;
        }
        if (!is_null($status)) {
            $query .= " AND Tasks.status = :status";
            $params[':status'] = $status;
        }
        if (!is_null($mainResponsible)) {
            $query .= " AND Tasks.mainResponsible = :mainResponsible";
            $params[':mainResponsible'] = $mainResponsible;
        }
        if (!is_null($parentTask)) {
            $query .= " AND Tasks.parentTask = :parentTask";
            $params[':parentTask'] = $parentTask;
        }
        if (!is_null($orderBy)) {
            $query .= " ORDER BY ".$orderBy;
        }
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            if( $tasks = $stmt->fetchAll(PDO::FETCH_CLASS, "Task")) {
                return $tasks;
            }
            else {
                $this->notifyUser("Oppgaver ble ikke funnet", "Kunne ikke hente oppgaver");
                return $tasks;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllTasks()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $tasks;
        }
    }


    // GET ALL TASKS
    public function getTask($taskId)
    {
        $query = 'SELECT Tasks.*, CONCAT(mainResponsible.firstName, " ", mainResponsible.lastName, " (", mainResponsible.username, ")") as mainResponsibleName, 
groupID.groupName as groupName, phaseID.phaseName as phaseName, parentTasks.taskName as parentTaskName
FROM Tasks
LEFT JOIN Users as mainResponsible on mainResponsible.userID = Tasks.mainResponsible
LEFT JOIN Groups as groupID on groupID.groupID = Tasks.groupID
LEFT JOIN Phases as phaseID on phaseID.phaseID = Tasks.phaseID
LEFT JOIN Tasks as parentTasks on parentTasks.taskID = Tasks.parentTask WHERE Tasks.taskID = :taskID';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
            $stmt->execute();
            if($task = $stmt->fetchObject("Task")) {
                return $task;
            }
            else {
                $this->notifyUser("Oppgaver ble ikke funnet", "Kunne ikke hente oppgaver");
                return null;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getTask()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return null;
        }
    }


    public function addMainTask($projectName): bool //returns boolean value
    {
        $taskName = $this->request->request->get('taskName');
        $phaseId = $this->request->request->get('phaseID', null);
        $groupId = $this->request->request->get('groupID', null);
        try {
            $stmt = $this->db->prepare("INSERT INTO `Tasks` (taskName, projectName, phaseID, groupID, hasSubtask)
              VALUES (:taskName, :projectName, :phaseID, :groupID, 1);");
            $stmt->bindParam(':taskName', $taskName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':phaseID', $phaseId, PDO::PARAM_INT, 100);
            $stmt->bindParam(':groupID', $groupId, PDO::PARAM_INT, 100);
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

    public function editTask(Task $task): bool //returns boolean value
    {
        $taskId = $task->getTaskID();
        $phaseId = $this->request->request->get('phaseID', $task->getPhaseID());
        $mainResponsible = $this->request->request->get('mainResponsible', $task->getMainResponsible());
        try {
            $stmt = $this->db->prepare("UPDATE Tasks SET phaseID = :phaseID, mainResponsible = :mainResponsible WHERE taskID = :taskID;");
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT, 100);
            $stmt->bindParam(':mainResponsible', $mainResponsible, PDO::PARAM_INT, 100);
            $stmt->bindParam(':phaseID', $phaseId, PDO::PARAM_INT, 100);
            if ($stmt->execute()) {
                $this->NotifyUser("Oppgave ble endret");
                return true;
            } else {
                $this->NotifyUser("Oppgave ble ikke endret");
                return false;
            }
        } catch (PDOException $e) {
            $this->NotifyUser("Oppgave ble ikke endret", $e->getMessage());
            return false;
        }
    }


    public function addSubTask($projectName, $parentTask, $groupId): bool //returns boolean value
    {
        $taskName = $this->request->request->get('taskName');
        $estimate = $this->request->request->getInt('estimatedTime', 0);
        try {
            $stmt = $this->db->prepare("INSERT INTO Tasks (taskName, projectName, parentTask, groupID, estimatedTime, hasSubtask)
              VALUES (:taskName, :projectName, :parentTask, :groupID, :estimate, 0);
              UPDATE Tasks SET estimatedTime = (SELECT SUM(estimatedTime) total FROM Tasks WHERE parentTask = :parentTask) WHERE taskID = :parentTask");
            $stmt->bindParam(':taskName', $taskName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':groupID', $groupId, PDO::PARAM_INT, 100);
            $stmt->bindParam(':estimate', $estimate, PDO::PARAM_INT, 100);
            $stmt->bindParam(':parentTask', $parentTask, PDO::PARAM_INT, 100);
            if ($stmt->execute()) {
                $taskId = $this->db->lastInsertId();
                $stmt->closeCursor();
                if ($this->addDependencies($taskId)) {
                    $this->NotifyUser("En deloppgave ble lagt til");
                    return true;
                } else {
                    $this->NotifyUser("En deloppgave ble lagt til");
                    return true;
                }
            } else {
                $this->NotifyUser("Deloppgave ble ikke opprettet");
                return false;
            }
        } catch (PDOException $e) {
            $this->NotifyUser("Deloppgave ble ikke opprettet", $e->getMessage());
            return false;
        }
    }


    public function addDependencies($taskId) : bool
    {
        $tasks = $this->request->request->get('dependentTasks');
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO TaskDependencies (firstTask, secondTask) VALUES (:firstTask, :taskId);");
            if (is_array($tasks)) {
                foreach ($tasks as $task) {
                    $stmt->bindParam(':firstTask', $task, PDO::PARAM_INT);
                    $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt->closeCursor();
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


    public function removeDependencies($taskId) : bool
    {
        $tasks = $this->request->request->get('dependentTasks');
        try {
            $stmt = $this->db->prepare("DELETE FROM TaskDependencies WHERE firstTask = :firstTask and secondTask = :taskId);");
            if (is_array($tasks)) {
                foreach ($tasks as $task) {
                    $stmt->bindParam(':firstTask', $task, PDO::PARAM_INT);
                    $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
                    $stmt->execute();
                }
                $this->notifyUser("Avhengigheter ble fjernet");
                return true;
            } else {
                $this->notifyUser("Fikk ikke fjerne avhengigheter");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke fjerne avhengigheter", $e->getMessage());
            return false;
        }
    }




    // GET FIRST TASKS
    public function getTasksItIsDependentOn($taskId) : array {
        $tasksItIsDependentOn = array();
        try {
            $stmt = $this->db->prepare('SELECT TaskDependencies.*, Tasks.*, CONCAT(mainResponsible.firstName, " ", mainResponsible.lastName, " (", mainResponsible.username, ")") as mainResponsibleName, 
groupID.groupName as groupName, phaseID.phaseName as phaseName, Tasks.parentTask as parentTaskName
FROM TaskDependencies
LEFT JOIN Tasks on TaskDependencies.firstTask = Tasks.taskID
LEFT JOIN Users as mainResponsible on mainResponsible.userID = Tasks.mainResponsible
LEFT JOIN Groups as groupID on groupID.groupID = Tasks.groupID
LEFT JOIN Phases as phaseID on phaseID.phaseID = Tasks.phaseID
LEFT JOIN Tasks as parentTasks on parentTasks.taskID = Tasks.parentTask WHERE TaskDependencies.secondTask = :taskID ORDER BY Tasks.taskName;');
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
            $stmt->execute();
            if($tasksItIsDependentOn = $stmt->fetchAll(PDO::FETCH_CLASS, "Task")) {
                return $tasksItIsDependentOn;
            }
            else {
                $this->notifyUser("Tasks ble ikke funnet", "Kunne ikke hente tasks");
                return $tasksItIsDependentOn;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getTasksItIsDependentOn()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $tasksItIsDependentOn;
        }
    }

    // GET SECOND TASKS
    public function getDependentTasks($taskId) : array {
        $dependentTasks = array();
        try {
            $stmt = $this->db->prepare('SELECT TaskDependencies.*, Tasks.*, CONCAT(mainResponsible.firstName, " ", mainResponsible.lastName, " (", mainResponsible.username, ")") as mainResponsibleName, 
groupID.groupName as groupName, phaseID.phaseName as phaseName, Tasks.parentTask as parentTaskName
FROM TaskDependencies
LEFT JOIN Tasks on TaskDependencies.secondTask = Tasks.taskID
LEFT JOIN Users as mainResponsible on mainResponsible.userID = Tasks.mainResponsible
LEFT JOIN Groups as groupID on groupID.groupID = Tasks.groupID
LEFT JOIN Phases as phaseID on phaseID.phaseID = Tasks.phaseID
LEFT JOIN Tasks as parentTasks on parentTasks.taskID = Tasks.parentTask WHERE TaskDependencies.firstTask = :taskID ORDER BY Tasks.taskName;');
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
            $stmt->execute();
            if($dependentTasks = $stmt->fetchAll(PDO::FETCH_CLASS, "Task")) {
                return $dependentTasks;
            }
            else {
                $this->notifyUser("Tasks ble ikke funnet", "Kunne ikke hente tasks");
                return $dependentTasks;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getDependentTasks()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $dependentTasks;
        }
    }


    // GET SECOND TASKS
    public function getNonDependentTasks($task) : array {
        $projectName = $task->getProjectName();
        $taskId = $task->getTaskID();
        $dependentTasks = array();
        try {
            $stmt = $this->db->prepare('SELECT * FROM Tasks WHERE taskID 
                              NOT IN(SELECT firstTask FROM TaskDependencies WHERE secondTask = :taskID) 
                      AND taskID NOT IN(SELECT secondTask FROM TaskDependencies WHERE firstTask = :taskID) 
                      AND taskID != :taskID AND projectName = :projectName;');
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
            $stmt->bindPAram(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->execute();
            if($dependentTasks = $stmt->fetchAll(PDO::FETCH_CLASS, "Task")) {
                return $dependentTasks;
            }
            else {
                $this->notifyUser("Tasks ble ikke funnet", "Kunne ikke hente tasks");
                return $dependentTasks;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getDependentTasks()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $dependentTasks;
        }
    }

    public function addTasksToPhase($phaseId) : bool
    {
        $tasks = $this->request->request->get('tasks');
        try {
            $stmt = $this->db->prepare("UPDATE Tasks SET Tasks.phaseID = :phaseID WHERE Tasks.taskID = :taskID;");
            if (is_array($tasks)) {
                foreach ($tasks as $task) {
                    $stmt->bindParam(':phaseID', $phaseId, PDO::PARAM_INT);
                    $stmt->bindParam(':taskID', $task, PDO::PARAM_INT);
                    $stmt->execute();
                }
                $this->notifyUser("Oppgave ble lagt til på fasen");
                return true;
            } else {
                $this->notifyUser("Fikk ikke legge til oppgaver på fasen");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke legge til oppgaver på fasen (feil på addTasksToPhase())", $e->getMessage());
            return false;
        }
    }


    public function removeTasksFromPhase($phaseId) : bool
    {
        $tasks = $this->request->request->get('tasks');
        try {
            $stmt = $this->db->prepare("UPDATE Tasks SET Tasks.phaseID = null WHERE Tasks.taskID = :taskID AND Tasks.phaseID = :phaseID;");
            if (is_array($tasks)) {
                foreach ($tasks as $task) {
                    $stmt->bindParam(':phaseID', $phaseId, PDO::PARAM_INT);
                    $stmt->bindParam(':taskID', $task, PDO::PARAM_INT);
                    $stmt->execute();
                }
                $this->notifyUser("Oppgave ble lagt til på fasen");
                return true;
            } else {
                $this->notifyUser("Fikk ikke legge til oppgaver på fasen");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke legge til oppgaver på fasen (feil på removeTasksFromPhase())", $e->getMessage());
            return false;
        }
    }

    public function editStatus($taskId) : bool
    {
        $status = $this->request->request->get('status');
        try {
            $stmt = $this->db->prepare("UPDATE Tasks SET status = :status WHERE taskID = :taskID;
                UPDATE Tasks SET status = :status WHERE parentTask = :taskID");
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $this->notifyUser("Status på oppgaven og alle tilhørende sub-oppgaver ble endret");
                return true;
            } else {
                $this->notifyUser("Fikk ikke endre status til oppgaver");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke endre status til oppgaver (feil på editStatus())", $e->getMessage());
            return false;
        }
    }


    public function changeGroup($taskId): bool //returns boolean value
    {
        $groupId = $this->request->request->get('groupID');
        try {
            $stmt = $this->db->prepare("UPDATE Tasks SET groupID = :groupID WHERE taskID = :taskID;
                UPDATE Tasks SET groupID = :groupID WHERE parentTask = :taskID;");
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT, 100);
            $stmt->bindParam(':groupID', $groupId, PDO::PARAM_INT, 100);
            if ($stmt->execute()) {
                $this->NotifyUser("Gruppe ble endret");
                return true;
            } else {
                $this->NotifyUser("Gruppe ble ikke endret");
                return false;
            }
        } catch (PDOException $e) {
            $this->NotifyUser("Gruppe ble ikke endret", $e->getMessage());
            return false;
        }
    }



    public function reEstimate($taskId, $parentTaskId) : bool
    {
        $estimatedTime = $this->request->request->get('estimatedTime');
        try {
            $stmt = $this->db->prepare("UPDATE Tasks SET estimatedTime = :estimatedTime WHERE taskID = :taskID;
                UPDATE Tasks SET estimatedTime = (SELECT SUM(estimatedTime) total FROM Tasks WHERE parentTask = :parentTaskID) WHERE taskID = :parentTaskID");
            $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
            $stmt->bindParam(':estimatedTime', $estimatedTime, PDO::PARAM_INT);
            $stmt->bindParam(':parentTaskID', $parentTaskId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $this->notifyUser("Estimate på oppgaven ble endret");
                return true;
            } else {
                $this->notifyUser("Fikk ikke endre estimat til oppgaver");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke endre estimat til oppgaver (feil på reEstimate())", $e->getMessage());
            return false;
        }
    }


    public function deleteTask($taskId, $parentTaskId) : bool
    {
        if (!is_null($parentTaskId)) {
            try {
                $stmt = $this->db->prepare("DELETE FROM Tasks Where taskID = :taskID;
                UPDATE Tasks SET estimatedTime = (SELECT SUM(estimatedTime) total FROM Tasks WHERE parentTask = :parentTaskID) WHERE taskID = :parentTaskID");
                $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
                $stmt->bindParam(':parentTaskID', $parentTaskId, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $this->notifyUser("Oppgave ble slettet");
                    return true;
                } else {
                    $this->notifyUser("Fikk ikke slette oppgave");
                    return false;
                }
            } catch (Exception $e) {
                $this->notifyUser("Fikk ikke slette oppgave", $e->getMessage());
                return false;
            }
        } else {
            try {
                $stmt = $this->db->prepare("DELETE FROM Tasks Where taskID = :taskID;");
                $stmt->bindParam(':taskID', $taskId, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $this->notifyUser("Oppgave ble slettet");
                    return true;
                } else {
                    $this->notifyUser("Fikk ikke slette oppgave");
                    return false;
                }
            } catch (Exception $e) {
                $this->notifyUser("Fikk ikke slette oppgave", $e->getMessage());
                return false;
            }
        }
    }



}