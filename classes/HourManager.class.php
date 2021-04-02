<?php


class HourManager
{
    private $dbase;
    private $request;
    private $session;

    public function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->dbase = $db;
        $this->request = $request;
        $this->session = $session;
    }


    private function notifyUser($strHeader, $strMessage = "")
    {
        //$this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }


    // GET ALL TASKS
    public function getAllTasks($taskId = null, $whoWorked = null, $phaseId = null, $startTime = null, $endTime = null,
                                $timeWorked = null, $activated = null, $location = null, $absenceType = null,
                                $overtimeType = null, $isChanged = null, $stampingStatus = null, $taskType = null, $orderBy = null) : array {
        $tasks = array();
        $query = 'SELECT Hours.*, CONCAT(workers.firstName, " ", workers.lastName, " (", workers.username, ")") as whoWorkedName, 
hourTasks.taskName as taskName, hourPhases.phaseName as phaseName
FROM Hours
LEFT JOIN Users as workers on workers.userID = Hours.whoWorked
LEFT JOIN Tasks as hourTasks on hourTasks.taskID = Hours.taskID
LEFT JOIN Phases as hourPhases on hourPhases.phaseID = Hours.phaseID WHERE 1';
        $params = array();
        if (!is_null($taskId)) {
            $query .= " AND Tasks.hasSubtask = :hasSubtask";
            $params[':hasSubtask'] = $taskId;
        }
        if (!is_null($whoWorked)) {
            $query .= " AND Tasks.projectName = :projectName";
            $params[':projectName'] = $whoWorked;
        }
        if (!is_null($phaseId)) {
            $query .= " AND Tasks.phaseID = :phaseID";
            $params[':phaseID'] = $phaseId;
        }
        if (!is_null($startTime)) {
            $query .= " AND Tasks.groupID = :groupID";
            $params[':groupID'] = $startTime;
        }
        if (!is_null($endTime)) {
            $query .= " AND Tasks.status = :status";
            $params[':status'] = $endTime;
        }
        if (!is_null($timeWorked)) {
            $query .= " AND Tasks.mainResponsible = :mainResponsible";
            $params[':mainResponsible'] = $timeWorked;
        }
        if (!is_null($activated)) {
            $query .= " AND Tasks.parentTask = :parentTask";
            $params[':parentTask'] = $activated;
        }
        if (!is_null($orderBy)) {
            $query .= " ORDER BY ".$orderBy;
        }
        try {
            $stmt = $this->dbase->prepare($query);
            $stmt->execute($params);
            if( $tasks = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
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


    // GET LAST HOURS FOR LOGGED IN USER
    public function getLastHoursForUser($userID): array
    {
        $lastHoursForUser = null;
        try {
            $stmt = $this->dbase->prepare(query: "SELECT * FROM Hours Where whoWorked= :userID ORDER BY endTime DESC LIMIT 5");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($lastHoursForUser = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $lastHoursForUser;
            } else {
                $this->notifyUser("Ingen timer funnet.", "Kunne ikke hente timene.");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHoursForUser()", $e->getMessage());
            return array();
        }
    }
    //END GET LAST HOURS FOR LOGGED IN USER

    // GET ALL HOURS FOR LOGGED IN USER ------------------------------------------------------------
    public function getAllHoursForUser($userID): array
    {
        //TODO må lage en måneds periode for å skrive kun timer fra siste 30 dager
        //"SELECT * FROM Hours Where whoWorked= :userID
        // and MONTH(endTime) = MONTH(NOW()) and YEAR(endTime)=YEAR(now()) ORDER BY endTime DESC");

        $allHoursForUser = null;
        try {
            $stmt = $this->dbase->prepare(query: "SELECT * FROM Hours Where whoWorked= :userID 
                      and startTime BETWEEN '01.01.2020' and NOW() ORDER BY endTime DESC LIMIT 30");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($allHoursForUser = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $allHoursForUser;
            } else {
                $this->notifyUser("Ingen timer funnet.", "Kunne ikke hente timene.");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHoursForUser()", $e->getMessage());
            return array();
        }
    }
    // GET ALL HOURS FOR LOGGED IN USER ------------------------------------------------------------
    public function getAllHoursForUserWithTask($userID): array
    {
        $allHoursForUserWithTask = null;
        try {
            $stmt = $this->dbase->prepare(query: "SELECT * FROM Hours Where whoWorked= :userID 
                      and startTime BETWEEN '01.01.2020' and NOW() ORDER BY endTime DESC LIMIT 30 
                      LEFT JOIN (SELECT * FROM Tasks WHERE task.getTaskID == hours.taskID) ");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($allHoursForUserWithTask = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $allHoursForUserWithTask;
            } else {
                $this->notifyUser("Ingen timer funnet.", "Kunne ikke hente timene.");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHoursForUser()", $e->getMessage());
            return array();
        }
    }

    // GET ALL HOURS
    public function getAllHours() : array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Hours");
            $stmt->execute();
            if( $hours = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $hours;
            }
            else {
                $this->notifyUser("Comments not found", "Kunne ikke hente kommentarer");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHours()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            //return new Project();
            return array();
        }
    }

    // REGISTER TIME FOR USER
    public function registerTimeForUser($userID)
    {
        try {
            $stmt = $this->dbase->prepare("INSERT INTO Hours (startTime, whoWorked) VALUES (NOW(), :userID)");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();

        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på registerTimeForUser()", $e->getMessage());
            return array();
        }
    }//END REGISTER TIME

    // GET HOUR ---------------------------------------------------------------------------------
    public function getHour($hourID)
    {
        try {
            $stmt = $this->dbase->prepare("SELECT * FROM Hours Where hourID= :hourID");
            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_INT);
            $stmt->execute();
            if( $hour = $stmt->fetchObject("Hour")) {
                return $hour;
            }
            else {
                $this->notifyUser("Comments not found", "Kunne ikke hente kommentarer");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getHour()", $e->getMessage());
            return array();
        }
    }

//CHANGE TIME USER
public
function changeTimeForUser()
{

}
//END CHANGE TIME USER
}