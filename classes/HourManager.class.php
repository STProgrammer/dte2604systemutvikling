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

    // CALCULATE TIME IN PHASE -------------------------------------------------
    public function spentTimePhase($starTime, $endTime) { //blir brukt fra twig. Kalles fra user_contribution.twig
        $startTime = new DateTime($starTime);
        $endTime = new DateTime($endTime);

        $hourCount = $endTime->diff($startTime);
        return $hourCount->format('%H:%I');
    }

    // GET ALL Hours With All Users --------------------------------------------------------------------------------
    public function getAllHours()
    {
        $hoursAll = array();
        $query = 'SELECT Hours.*, hourTasks.*, CONCAT(workers.firstName, " ", workers.lastName) as whoWorkedName, 
                    hourTasks.taskName as taskName, hourPhases.phaseName as phaseName
                    FROM Hours
                    LEFT JOIN Users as workers on workers.userID = Hours.whoWorked
                    LEFT JOIN Tasks as hourTasks on hourTasks.taskID = Hours.taskID
                    LEFT JOIN Phases as hourPhases on hourPhases.phaseID = Hours.phaseID 
                    ORDER BY Hours.startTime DESC';
        try {
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if ($hoursAll = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $hoursAll;
            } else {
                $this->notifyUser("Timer ble ikke funnet", "Kunne ikke hente oppgaver");
                return $hoursAll;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHours()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $hoursAll;
        }
    }

    // GET ALL TASKS -------------------------------------------------------------------------------------
    public function getHours($taskId = null, $whoWorked = null, $phaseId = null, $startTime = null, $endTime = null,
                             $timeWorked = null, $activated = null, $location = null, $absenceType = null,
                             $overtimeType = null, $isChanged = null, $stampingStatus = null, $taskType = null,
                             $orderBy = null, $offset = null, $limit = null, $projectName = null): array
    {
        $hours = array();
        $query = 'SELECT Hours.*, hourTasks.*, CONCAT(workers.firstName, " ", workers.lastName) as whoWorkedName, 
                    hourTasks.taskName as taskName, hourPhases.phaseName as phaseName
                    FROM Hours
                    LEFT JOIN Users as workers on workers.userID = Hours.whoWorked
                    LEFT JOIN Tasks as hourTasks on hourTasks.taskID = Hours.taskID
                    LEFT JOIN Phases as hourPhases on hourPhases.phaseID = Hours.phaseID WHERE workers.userID = Hours.whoWorked';
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
        if (!is_null($projectName)) {
            $query .= " AND hourTasks.projectName = :projectName";
            $params[':projectName'] = $projectName;
        }
        if (!is_null($orderBy)) {
            $query .= " ORDER BY " . $orderBy;
        }else{
            $query .= " ORDER BY Hours.startTime DESC"; //hvis null så DESC på startime
        }
        if (!is_null($limit)) {
            $limit = intval($limit);
            if (!is_null($offset)) {
                $offset = intval($offset);
                $query .= " LIMIT " . $offset . ", " . $limit;
            } else {
                $query .= " LIMIT " . $limit;
            }
        }
        try {
            $stmt = $this->dbase->prepare($query);
            $stmt->execute($params);
            if ($hours = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $hours;
            } else {
                $this->notifyUser("Timer ble ikke funnet", "Kunne ikke hente oppgaver");
                return $hours;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllHours()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $hours;
        }
    }

//    // GET ALL HOURS FOR LOGGED IN USER ------------------------------------------------------------
//    public function getAllHoursForUser($userID): array
//    {
//        //TODO må lage en måneds periode for å skrive kun timer fra siste 30 dager
//        //"SELECT * FROM Hours Where whoWorked= :userID
//        // and MONTH(endTime) = MONTH(NOW()) and YEAR(endTime)=YEAR(now()) ORDER BY endTime DESC");
//
//        $allHoursForUser = null;
//        try {
//            $stmt = $this->dbase->prepare(query: "SELECT * FROM Hours Where whoWorked= :userID
//                      ORDER BY startTime DESC");
//            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
//            $stmt->execute();
//            if ($allHoursForUser = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
//                return $allHoursForUser;
//            } else {
//                $this->notifyUser("Ingen timer funnet.", "getAllHoursForUser");
//                return array();
//            }
//        } catch (Exception $e) {
//            $this->NotifyUser("En feil oppstod, på getAllHoursForUser()", $e->getMessage());
//            return array();
//        }
//    }

    // REGISTER TIME FOR USER ---------------------------------------------------------------------------
    public function registerTimeForUser($userID, ?Task $task): bool
    {
        if (!is_null($task)) {
            $taskID = $task->getTaskID();
            $phaseID = $task->getPhaseID();
        } else {
            $taskID = null;
            $phaseID = null;
        }
        $startTime = date("Y-m-d H:i:s");
        $timeWorked = 0;
        $activated = 1;
        $location = $this->request->request->get('Lokasjon');
        $phaseID = NULL;
        $absenceType = NULL;
        $overtimeType = NULL;
        $comment = NULL;
        $commentBoss = NULL;
        $isChanged = 0;
        $stampingStatus = 0;
        $taskType = $this->request->request->get('Kategori');

        try {
            $stmt = $this->dbase->prepare("INSERT INTO Hours (`taskID`, `whoWorked`, `startTime`, 
                   `endTime`, `timeWorked`, `activated`, `location`, `phaseID`, `absenceType`, `overtimeType`, 
                   `comment`, `commentBoss`, `isChanged`, `stampingStatus`, `taskType`)
                   VALUES (:taskID, :userID, :startTime, 0, :timeWorked, :activated, 
                           :location, :phaseID, :absenceType, :overtimeType, :comment, 
                           :commentBoss, :isChanged, :stampingStatus, :taskType)");

            $stmt->bindParam(':taskID', $taskID, PDO::PARAM_INT);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->bindParam(':startTime', $startTime, PDO::PARAM_STR);
            $stmt->bindParam(':timeWorked', $timeWorked, PDO::PARAM_STR);
            $stmt->bindParam(':activated', $activated, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':phaseID', $phaseID, PDO::PARAM_STR);
            $stmt->bindParam(':absenceType', $absenceType, PDO::PARAM_STR);
            $stmt->bindParam(':overtimeType', $overtimeType, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':commentBoss', $commentBoss, PDO::PARAM_STR);
            $stmt->bindParam(':isChanged', $isChanged, PDO::PARAM_STR);
            $stmt->bindParam(':stampingStatus', $stampingStatus, PDO::PARAM_STR);
            $stmt->bindParam(':taskType', $taskType, PDO::PARAM_STR);

            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $this->notifyUser("Ny timereg ble registrert", "");
                return true;
            } else {
                $this->notifyUser("Failed to register time!", "");
                return false;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på registerTimeForUser()", $e->getMessage());
            return false;
        }
    }

    public function activeTimeregForUser($userID)
    {
        try {
            $stmt = $this->dbase->prepare("SELECT hourID FROM Hours WHERE whoWorked = :userID AND stampingStatus = 0");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($hourID = $stmt->fetch()) {
                return $hourID;
            } else {
                $this->notifyUser("Kunne ikke hente aktiv timeregistrering, ", "activeTimeregForUser()");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på activeTimeregForUser()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            //return new Project();
            return array();
        }
    }
    public function totalTimeWorked($projectName) {
        try {
            $stmt = $this->dbase->prepare("SELECT Hours.*, TIME_FORMAT(SEC_TO_TIME(SUM(timeWorked)), '%H:%i') as sumTW, hourTasks.*, CONCAT(workers.firstName, ' ', workers.lastName) as whoWorkedName, 
                hourTasks.taskName as taskName FROM Hours 
                    LEFT JOIN Users as workers on workers.userID = Hours.whoWorked
                    LEFT JOIN Tasks as hourTasks on hourTasks.taskID = Hours.taskID WHERE hourTasks.projectName = :projectName GROUP BY Hours.whoWorked ORDER BY whoWorkedName;");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->execute();
            if ($totalTimeWorked = $stmt->fetchAll()) {
                return $totalTimeWorked;
            } else {
                $this->notifyUser("", "");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            //return new Project();
            return array();
        }
    }


    // STOP TIME FOR USER ---------------------------------------------------------------------------
    public function stopTimeForUser(?Hour $hour, ?Task $task): bool
    {
        $hourID = $hour->getHourID();
        if (!is_null($task)) {
            $taskId = $task->getTaskID();
            $parentTask = $task->getParentTask();
        } else {
            $taskId = null;
            $parentTask = null;
        }
        try {
            $stmt = $this->dbase->prepare("UPDATE Hours SET endTime = NOW(), 
                 stampingStatus = 1 WHERE hourID = :hourID");
            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            if ($stmt->rowCount() == 1) {
                $this->notifyUser("Timeregistrering stoppet");
                $hour = $this->getHour($hourID);
                $timeStr = $hour->getTimeWorked();
                if (!is_null($taskId)) {
                    $this->updateTimeWorkedOnTask($taskId, $timeStr, $parentTask);
                }
                return true;
            } else {
                $this->notifyUser("Failed to stop timereg", "stopTimeForUser()");
                return false;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på stopTimeForUser()", $e->getMessage());
            return false;
        }
    }

    private function updateTimeWorkedOnTask($taskID, $timeStr, $parentTaskID) {
        $timestamp = strtotime($timeStr);
        $hours = date('h', $timestamp) - 12;
        try {
            $stmt = $this->dbase->prepare("UPDATE Tasks SET timeSpent = timeSpent + :hours WHERE taskID = :taskID;
                                 UPDATE Tasks SET timeSpent = (SELECT SUM(timeSpent) total FROM Tasks WHERE parentTask = :parentTaskID) WHERE taskID = :parentTaskID;");
            $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
            $stmt->bindParam(':taskID', $taskID, PDO::PARAM_INT);
            $stmt->bindParam(':parentTaskID',  $parentTaskID, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $this->notifyUser("Tidsbrukt på task oppdatert");
                return true;
            } else {
                $this->notifyUser("Tidsbrukt på task ble ikke oppdatert", "updateTimeWorkedOnTask()");
                return false;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod på updateTimeWorkedOnTask()", $e->getMessage());
            return false;
        }
    }

    // GET HOUR ---------------------------------------------------------------------------------
    public function getHour($hourID)
    {
        try {
            $stmt = $this->dbase->prepare("SELECT * FROM Hours Where hourID = :hourID");
            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_INT);
            $stmt->execute();
            if ($hour = $stmt->fetchObject("Hour")) {
                return $hour;
            } else {
                $this->notifyUser("Comments not found", "Kunne ikke hente kommentarer");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getHour()", $e->getMessage());
            return array();
        }
    }

    //EDIT COMMENT USER --------------------------------------------------------------------------------
    public function editComment($hourID): bool
    {
        $hourID = $this->request->request->get('hourID');
        $comment = $this->request->request->get('comment');
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

    //EDIT COMMENT BOSS--------------------------------------------------------------------------------
    public function editCommentBoss($hourId): bool
    {
        $hourID = $this->request->request->get('hourID');
        $commentBoss = $this->request->request->get('commentBoss');
        try {
            $stmt = $this->dbase->prepare(query: "UPDATE Hours SET commentBoss = :commentBoss WHERE hourID = :hourID;");
            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_INT);
            $stmt->bindParam(':commentBoss', $commentBoss);
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $this->notifyUser('Boss comment changed', 'editCommentBoss()');
                return true;
            } else {
                $this->notifyUser('Comment not changed, failed!', 'editCommentBoss()');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change comment, exeption on 'editCommentBoss()': ", $e->getMessage());
            return false;
        }
    }

    public function checkIfActiveTimereg($userID): array
    {
        try {
            $stmt = $this->dbase->prepare(query: "SELECT stampingStatus, hourID FROM Hours WHERE whoWorked = :userID ;");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($timeregCheck = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $timeregCheck;
            } else {
                $this->notifyUser("Kunne ikke hente status, ", "checkIfActiveTimereg()");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på checkIfActiveTimereg()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            //return new Project();
            return array();
        }
    }

    //Edit the hour, deactivate old
    public function changeTimeForUser($hours, $startTime, $endTime) : bool
    {
        $hours = $hours[0];
        $hourID = $hours->getHourID();
        $isChanged = 1;
        try {
            $stmt = $this->dbase->prepare(query: "UPDATE Hours SET isChanged = :isChanged, startTime = :startTime, endTime = :endTime WHERE hourID = :hourID");
            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_INT);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':endTime', $endTime);
            $stmt->bindParam(':isChanged', $isChanged);
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $this->notifyUser('Timeregistrering forandret!', 'editHour()');
                return true;
            } else {
                $this->notifyUser('Timeregistrering ikke forandret!', 'editHour()');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Endring misslykkes, feil på 'editHour()': ", $e->getMessage());
            return false;
        }
    }
    public function duplicateToLog($hours): bool
    {
        $hours = $hours[0];
        $hourID = $hours->getHourID();
        $taskID = $hours->getTaskID();
        $userID = $hours->getWhoworked();
        $startTime = $hours->getStartTime();
        $endTime = $hours->getEndTime();
        $timeWorked = $hours->getTimeWorked();
        $activated = $hours->isActivated();
        $location = $hours->getLocation();
        $phaseID = $hours->getPhaseID();
        $absenceType = $hours->getAbsenceType();
        $overtimeType = $hours->getOvertimeType();
        $comment = $hours->getComment();
        $commentBoss = $hours->getCommentBoss();
        $isChanged = 1;
        $stampingStatus = $hours->isStampingStatus();
        $taskType = $hours->getTaskType();

        try {
            $stmt = $this->dbase->prepare("INSERT INTO HoursLogs (`hourID`, `taskID`, `whoWorked`, `startTime`, 
                   `endTime`, `timeWorked`, `activated`, `location`, `phaseID`, `absenceType`, `overtimeType`, 
                   `comment`, `commentBoss`, `isChanged`, `stampingStatus`, `taskType`)
                   VALUES (:hourID, :taskID, :userID, :startTime, :endTime, :timeWorked, :activated, 
                           :location, :phaseID, :absenceType, :overtimeType, :comment, 
                           :commentBoss, :isChanged, :stampingStatus, :taskType)");

            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_STR);
            $stmt->bindParam(':taskID', $taskID, PDO::PARAM_STR);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
            $stmt->bindParam(':startTime', $startTime, PDO::PARAM_STR);
            $stmt->bindParam(':endTime', $endTime, PDO::PARAM_STR);
            $stmt->bindParam(':timeWorked', $timeWorked, PDO::PARAM_STR);
            $stmt->bindParam(':activated', $activated, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':phaseID', $phaseID, PDO::PARAM_STR);
            $stmt->bindParam(':absenceType', $absenceType, PDO::PARAM_STR);
            $stmt->bindParam(':overtimeType', $overtimeType, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':commentBoss', $commentBoss, PDO::PARAM_STR);
            $stmt->bindParam(':isChanged', $isChanged, PDO::PARAM_STR);
            $stmt->bindParam(':stampingStatus', $stampingStatus, PDO::PARAM_STR);
            $stmt->bindParam(':taskType', $taskType, PDO::PARAM_STR);

            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $this->notifyUser("Timeregistreringen ble lagret i log.", "");
                return true;
            } else {
                $this->notifyUser("En feil oppstod ved lagring i log.", "");
                return false;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på duplicateToLog()", $e->getMessage());
            return false;
        }
    }

    //Deaktivere en timereg.
    public function deleteTimeForUser($hours) : bool
    {
        $hours = $hours[0];
        $hourID = $hours->getHourID();
        try {
            $stmt = $this->dbase->prepare(query: "DELETE FROM Hours WHERE hourID = :hourID");
            $stmt->bindParam(':hourID', $hourID, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $this->notifyUser('Timeregistrering deaktivert!', 'editHour()');
                return true;
            } else {
                $this->notifyUser('Timeregistrering ikke deaktivert!', 'editHour()');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Endring misslykkes, feil på 'deleteTimeForUser()': ", $e->getMessage());
            return false;
        }
    }

    public function getDeletedHours()
    {
        $deletedHours = array();
        $query = 'SELECT HoursLogs.*, CONCAT(workers.firstName, " ", workers.lastName) as whoWorkedName
                    FROM HoursLogs
                    LEFT JOIN Users as workers on workers.userID = HoursLogs.whoWorked      
                    ORDER BY HoursLogs.whoWorked DESC';
        try {
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if ($deletedHours = $stmt->fetchAll(PDO::FETCH_CLASS, "Hour")) {
                return $deletedHours;
            } else {
                $this->notifyUser("Logger ble ikke funnet", "Kunne ikke hente oppgaver");
                return $deletedHours;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getDeletedHours()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return $deletedHours;
        }
    }
}