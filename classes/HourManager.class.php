<?php


class HourManager
{
    private $dbase;
    private $request;
    private $session;

    // CONSTRUCTOR -------------------------------------------------
    public function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->dbase = $db;
        $this->request = $request;
        $this->session = $session;
    }

    // FEILMELDINGER -------------------------------------------------
    private function notifyUser($strHeader, $strMessage = "")
    {
        //$this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    // GET LAST HOURS FOR LOGGED IN USER -------------------------------------------------
    public function getLastHoursForUser($userID): array
    {
        $lastHoursForUser = null;
        try {
            $stmt = $this->dbase->prepare(query: "SELECT * FROM Hours Where whoWorked= :userID 
                                            ORDER BY endTime DESC LIMIT 5");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($lastHoursForUser = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $lastHoursForUser;
            } else {
                $this->notifyUser("Ingen timer funnet.", "getLastHoursForUser");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHoursForUser()", $e->getMessage());
            return array();
        }
    }

    // GET ALL TASKS -------------------------------------------------------------------------------------
    public function getHours($taskId = null, $whoWorked = null, $phaseId = null, $startTime = null, $endTime = null,
                                $timeWorked = null, $activated = null, $location = null, $absenceType = null,
                                $overtimeType = null, $isChanged = null, $stampingStatus = null, $taskType = null,
                                $orderBy = null, $offset = null, $limit = null) : array {
        $hours = array();
        $query = 'SELECT Hours.*, CONCAT(workers.firstName, " ", workers.lastName, " (", workers.username, ")") as whoWorkedName, 
                    hourTasks.taskName as taskName, hourPhases.phaseName as phaseName
                    FROM Hours
                    LEFT JOIN Users as workers on workers.userID = Hours.whoWorked
                    LEFT JOIN Tasks as hourTasks on hourTasks.taskID = Hours.taskID
                    LEFT JOIN Phases as hourPhases on hourPhases.phaseID = Hours.phaseID WHERE 1';
        $params = array();
        if (!is_null($taskId)) {
            $query .= " AND Hours.taskID = :taskID";
            $params[':taskID'] = $taskId;
        }
        if (!is_null($whoWorked)) {
            $query .= " AND Hours.whoWorked = :whoWorked";
            $params[':whoWorked'] = $whoWorked;
        }
        if (!is_null($phaseId)) {
            $query .= " AND Hours.phaseID = :phaseID";
            $params[':phaseID'] = $phaseId;
        }
        if (!is_null($startTime)) {
            $query .= " AND Hours.startTime > :startTime";
            $params[':startTime'] = $startTime;
        }
        if (!is_null($endTime)) {
            $query .= " AND Hours.endTime < :endTime";
            $params[':endTime'] = $endTime;
        }
        if (!is_null($timeWorked)) {
            $query .= " AND Hours.timeWorked = :timeWorked";
            $params[':timeWorked'] = $timeWorked;
        }
        if (!is_null($activated)) {
            $query .= " AND Hours.activated = :activated";
            $params[':activated'] = $activated;
        }
        if (!is_null($location)) {
            $query .= " AND Hours.location = :location";
            $params[':location'] = $location;
        }
        if (!is_null($absenceType)) {
            $query .= " AND Hours.absenceType = :absenceType";
            $params[':absenceType'] = $absenceType;
        }
        if (!is_null($overtimeType)) {
            $query .= " AND Hours.overtimeType = :overtimeType";
            $params[':overtimeType'] = $overtimeType;
        }
        if (!is_null($isChanged)) {
            $query .= " AND Hours.isChanged = :isChanged";
            $params[':isChanged'] = $isChanged;
        }
        if (!is_null($stampingStatus)) {
            $query .= " AND Hours.stampingStatus = :stampingStatus";
            $params[':stampingStatus'] = $stampingStatus;
        }
        if (!is_null($taskType)) {
            $query .= " AND Hours.activated = :taskType";
            $params[':taskType'] = $taskType;
        }
        if (!is_null($orderBy)) {
            $query .= " ORDER BY ".$orderBy;
        }
        if (!is_null($limit)) {
            $limit = intval($limit);
            if (!is_null($offset)) {
                $offset = intval($offset);
                $query .= " LIMIT ".$offset.", ".$limit;
//                $params[':offset'] = $offset;
 //               $params[':limit'] = $limit;
            } else {
                $query .= " LIMIT ".$limit;
//                $params[':limit'] = $limit;
            }
        }
        try {
            $stmt = $this->dbase->prepare($query);
            $stmt->execute($params);
            if($tasks = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $hours;
            }
            else {
                $this->notifyUser("Timer ble ikke funnet", "Kunne ikke hente oppgaver");
                return $hours;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHours()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $hours;
        }
    }


//    // GET LAST HOURS FOR LOGGED IN USER
//    public function getLastHoursForUser($userID): array
//{
//    $lastHoursForUser = null;
//    try {
//        $stmt = $this->dbase->prepare(query: "SELECT * FROM Hours Where whoWorked= :userID ORDER BY endTime DESC LIMIT 5");
//        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
//        $stmt->execute();
//        if ($lastHoursForUser = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
//            return $lastHoursForUser;
//        } else {
//            $this->notifyUser("Ingen timer funnet.", "getLastHoursForUser");
//            return array();
//        }
//    } catch (Exception $e) {
//        $this->NotifyUser("En feil oppstod, på getAllHoursForUser()", $e->getMessage());
//        return array();
//    }
//}

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
                $this->notifyUser("Ingen timer funnet.", "getAllHoursForUser");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHoursForUser()", $e->getMessage());
            return array();
        }
    }

    // GET ALL HOURS FOR LOGGED IN USER ------------------------------------------------------------
    public function getAllHoursForUserWithTask($taskId = null, $whoWorked = null, $phaseId = null, $startTime = null, $endTime = null,
                             $timeWorked = null, $activated = null, $location = null, $absenceType = null,
                             $overtimeType = null, $isChanged = null, $stampingStatus = null, $taskType = null,
                             $orderBy = null, $offset = null, $limit = null) : array {
        $hours = array();
        $query = 'SELECT Hours.*, CONCAT(workers.firstName, " ", workers.lastName, " (", workers.username, ")") as whoWorkedName, 
                    hourTasks.taskName as taskName, hourPhases.phaseName as phaseName
                    FROM Hours
                    LEFT JOIN Users as workers on workers.userID = Hours.whoWorked
                    LEFT JOIN Tasks as hourTasks on hourTasks.taskID = Hours.taskID
                    LEFT JOIN Phases as hourPhases on hourPhases.phaseID = Hours.phaseID WHERE 1';
        $params = array();
        if (!is_null($taskId)) {
            $query .= " AND Hours.taskID = :taskID";
            $params[':taskID'] = $taskId;
        }
        if (!is_null($whoWorked)) {
            $query .= " AND Hours.whoWorked = :whoWorked";
            $params[':whoWorked'] = $whoWorked;
        }
        if (!is_null($phaseId)) {
            $query .= " AND Hours.phaseID = :phaseID";
            $params[':phaseID'] = $phaseId;
        }
        if (!is_null($startTime)) {
            $query .= " AND Hours.startTime > :startTime";
            $params[':startTime'] = $startTime;
        }
        if (!is_null($endTime)) {
            $query .= " AND Hours.endTime < :endTime";
            $params[':endTime'] = $endTime;
        }
        if (!is_null($timeWorked)) {
            $query .= " AND Hours.timeWorked = :timeWorked";
            $params[':timeWorked'] = $timeWorked;
        }
        if (!is_null($activated)) {
            $query .= " AND Hours.activated = :activated";
            $params[':activated'] = $activated;
        }
        if (!is_null($location)) {
            $query .= " AND Hours.location = :location";
            $params[':location'] = $location;
        }
        if (!is_null($absenceType)) {
            $query .= " AND Hours.absenceType = :absenceType";
            $params[':absenceType'] = $absenceType;
        }
        if (!is_null($overtimeType)) {
            $query .= " AND Hours.overtimeType = :overtimeType";
            $params[':overtimeType'] = $overtimeType;
        }
        if (!is_null($isChanged)) {
            $query .= " AND Hours.isChanged = :isChanged";
            $params[':isChanged'] = $isChanged;
        }
        if (!is_null($stampingStatus)) {
            $query .= " AND Hours.stampingStatus = :stampingStatus";
            $params[':stampingStatus'] = $stampingStatus;
        }
        if (!is_null($taskType)) {
            $query .= " AND Hours.activated = :taskType";
            $params[':taskType'] = $taskType;
        }
        if (!is_null($orderBy)) {
            $query .= " ORDER BY ".$orderBy;
        }
        if (!is_null($limit)) {
            $limit = intval($limit);
            if (!is_null($offset)) {
                $offset = intval($offset);
                $query .= " LIMIT ".$offset.", ".$limit;
//                $params[':offset'] = $offset;
                //               $params[':limit'] = $limit;
            } else {
                $query .= " LIMIT ".$limit;
//                $params[':limit'] = $limit;
            }
        }
        try {
            $stmt = $this->dbase->prepare($query);
            $stmt->execute($params);
            if($tasks = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $hours;
            }
            else {
                $this->notifyUser("Timer ble ikke funnet", "getAllHoursForUserWithTask");
                return $hours;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHoursForUserWithTask()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $hours;
        }
    }

    // GET ALL Hours With All Users --------------------------------------------------------------------------------
    public function getAllHours() {
        $hoursAll = array();
        try {
            $stmt = $this->dbase->prepare("SELECT * FROM Hours ORDER BY startTime DESC");
            $stmt->execute();
            if( $hoursAll = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $hoursAll;
            }
            else {
                $this->notifyUser("Kunne ikke hente kommentarer, ", "getAllHours()");
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

    // REGISTER TIME FOR USER ---------------------------------------------------------------------------
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
    }

    // GET HOUR ---------------------------------------------------------------------------------
    public function getHour($userID)
    {
        try {
            $stmt = $this->dbase->prepare("SELECT * FROM Hours Where whoWorked= :userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
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

    //EDIT COMMENT --------------------------------------------------------------------------------
    public function editComment($hour): bool
    {
        $hourID = $hour->getHourID();
        $comment = $this->request->request->get('comment', $hour->getComment());
//        $hourID = $this->request->request->get('hourID');
//        $comment = $this->request->request->get('comment');
        try {
            $stmt = $this->dbase->prepare(query: "UPDATE Hours SET comment = :comment WHERE hourID = :hourID;");
            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment);
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $this->notifyUser('Comment changed', 'editComment()');
                return true;
            } else {
                $this->notifyUser('Comment not changed, failed!', 'editComment()');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change comment, exeption on 'editComment()': ", $e->getMessage());
            return false;
        }
    }

    //CHANGE TIME USER ----------------------------------------------------------------------------------------
    public function changeTimeForUser()
    {

    }

}