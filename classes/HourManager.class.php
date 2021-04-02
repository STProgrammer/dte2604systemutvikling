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